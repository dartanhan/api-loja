<?php

namespace App\Http\Controllers;

use App\AI\Providers\GeminiProvider;
use App\AI\Services\InventoryIntelligenceService;
use App\AI\Services\MarginIntelligenceService;
use App\AI\Services\SalesIntelligenceService;
use App\Models\AiConversation;
use App\Models\AiInsight;
use App\Models\AiMessage;
use App\Models\ConfiguracaoIa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnIntelligenceController extends Controller
{
    protected $salesService;
    protected $inventoryService;
    protected $marginService;
    protected $geminiProvider;

    public function __construct(
        SalesIntelligenceService $salesService,
        InventoryIntelligenceService $inventoryService,
        MarginIntelligenceService $marginService,
        GeminiProvider $geminiProvider
    ) {
        $this->salesService = $salesService;
        $this->inventoryService = $inventoryService;
        $this->marginService = $marginService;
        $this->geminiProvider = $geminiProvider;
    }

    public function index(Request $request)
    {
        $periodo = $request->get('periodo', '30dias');

        switch ($periodo) {
            case 'hoje':
                $inicio = now()->startOfDay()->toDateTimeString();
                $fim    = now()->endOfDay()->toDateTimeString();
                break;
            case '7dias':
                $inicio = now()->subDays(6)->startOfDay()->toDateTimeString();
                $fim    = now()->endOfDay()->toDateTimeString();
                break;
            case 'personalizado':
                $inicio = $request->get('inicio', now()->startOfMonth()->toDateTimeString());
                $fim    = $request->get('fim',    now()->endOfDay()->toDateTimeString());
                break;
            default:
                $inicio = now()->startOfMonth()->toDateTimeString();
                $fim    = now()->endOfDay()->toDateTimeString();
                break;
        }

        $vendas       = $this->salesService->getPerformanceComercial($inicio, $fim);
        $estoqueRisco = $this->inventoryService->getEstoqueRisco();
        $margem       = $this->marginService->getMargemGerencial();
        $insights     = AiInsight::orderBy('created_at', 'desc')->take(5)->get();

        // Período anterior (mesmo intervalo, janela anterior) para comparação
        $diffDias  = \Carbon\Carbon::parse($inicio)->diffInDays(\Carbon\Carbon::parse($fim)) + 1;
        $inicioAnt = \Carbon\Carbon::parse($inicio)->subDays($diffDias)->toDateTimeString();
        $fimAnt    = \Carbon\Carbon::parse($inicio)->subSecond()->toDateTimeString();
        $vendasAnt = $this->salesService->getPerformanceComercial($inicioAnt, $fimAnt);

        return view('admin.kn_intelligence.dashboard', compact(
            'vendas', 'vendasAnt', 'estoqueRisco', 'margem', 'insights', 'periodo', 'inicio', 'fim'
        ));
    }

    public function assistant(Request $request)
    {
        $conversations = AiConversation::where('user_id', Auth::id())->orderBy('updated_at', 'desc')->get();
        
        $currentConversation = null;
        $messages = [];
        if ($request->has('c')) {
            $currentConversation = AiConversation::where('user_id', Auth::id())->where('id', $request->input('c'))->first();
            if ($currentConversation) {
                $messages = AiMessage::where('conversation_id', $currentConversation->id)->orderBy('id', 'asc')->get();
            }
        }

        return view('admin.kn_intelligence.assistant', compact('conversations', 'currentConversation', 'messages'));
    }

    public function ask(Request $request)
    {
        $request->validate(['pergunta' => 'required|string']);

        $pergunta = $request->input('pergunta');
        $conversationId = $request->input('conversation_id');

        if (!$conversationId) {
            $conversation = AiConversation::create([
                'user_id' => Auth::id(),
                'titulo' => substr($pergunta, 0, 40) . '...'
            ]);
            $conversationId = $conversation->id;
        }

        // Salva mensagem do usuário
        AiMessage::create([
            'conversation_id' => $conversationId,
            'role' => 'user',
            'content' => $pergunta
        ]);

        // Coleta dados reais do sistema para contextualizar a resposta da IA
        $vendas = $this->salesService->getPerformanceComercial();
        $estoque = $this->inventoryService->getEstoqueRisco();
        $margem = $this->marginService->getMargemGerencial();

        $detalhesEstoque = "";
        if (!empty($estoque['produtos'])) {
            $detalhesEstoque = " - Itens específicos com estoque baixo:\n";
            foreach($estoque['produtos'] as $item) {
                $desc = is_array($item) ? $item['descricao'] : $item->descricao;
                $qtd = is_array($item) ? $item['quantidade'] : $item->quantidade;
                $min = is_array($item) ? $item['quantidade_minima'] : $item->quantidade_minima;
                $detalhesEstoque .= "   * {$desc} (Qtd: {$qtd} / Mín: {$min})\n";
            }
        }

        $detalhesMargem = "";
        if (!empty($margem['ranking_menor_margem'])) {
            $detalhesMargem = " - Produtos com MENORES Margens de Lucro:\n";
            $count = 0;
            foreach($margem['ranking_menor_margem'] as $item) {
                if($count++ >= 5) break; // Mostra apenas os 5 piores
                $prod = is_array($item) ? $item['produto'] : $item->produto;
                $perc = is_array($item) ? $item['margem_percentual'] : $item->margem_percentual;
                $detalhesMargem .= "   * {$prod} (Margem: {$perc}%)\n";
            }
        }

        $totalProdutosMargem = count($margem['produtos'] ?? []);

        $contexto = "Você é o assistente inteligente da loja KN Cosméticos (KN Intelligence).\n"
            ."Abaixo estão os DADOS REAIS da loja para você responder com precisão:\n"
            ."1. Vendas/Faturamento Atual: R$ {$vendas['faturamento']} | Pedidos: {$vendas['pedidos']} | Ticket Médio: R$ {$vendas['ticket_medio']} | Descontos: R$ {$vendas['total_desconto']}\n"
            ."2. Risco de Estoque Baixo: {$estoque['total_itens_criticos']} produtos com estoque abaixo do mínimo.\n"
            .$detalhesEstoque
            ."3. Análise de Margem: {$totalProdutosMargem} itens vendidos analisados.\n"
            .$detalhesMargem."\n"
            ."Pergunta do usuário: \"{$pergunta}\"\n\n"
            ."Responda de forma profissional, direta, com formatação limpa e sugestões estratégicas práticas para a loja baseando-se nos itens acima se solicitado.";

        $respostaIa = $this->geminiProvider->generateContent($contexto);

        // Salva mensagem do assistente
        AiMessage::create([
            'conversation_id' => $conversationId,
            'role' => 'assistant',
            'content' => $respostaIa
        ]);

        return response()->json([
            'success' => true,
            'conversation_id' => $conversationId,
            'resposta' => $respostaIa
        ]);
    }

    public function configuracoes()
    {
        $config = ConfiguracaoIa::where('provedor', 'gemini')->first();
        return view('admin.kn_intelligence.configuracoes', compact('config'));
    }

    public function salvarConfiguracoes(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'modelo' => 'required|string',
        ]);

        ConfiguracaoIa::updateOrCreate(
            ['provedor' => 'gemini'],
            [
                'api_key' => $request->input('api_key'),
                'modelo' => $request->input('modelo'),
                'ativo' => $request->has('ativo') ? true : false,
            ]
        );

        return redirect()->back()->with('success', 'Configurações de IA do Gemini atualizadas com sucesso!');
    }

    public function gerarInsights()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('kn-intelligence:generate-insights');
            return redirect()->back()->with('success', 'Insights de IA atualizados com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao gerar novos insights: ' . $e->getMessage());
        }
    }
}
