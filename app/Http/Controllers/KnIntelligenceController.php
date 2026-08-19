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

    public function index()
    {
        $vendas = $this->salesService->getPerformanceComercial();
        $estoqueRisco = $this->inventoryService->getEstoqueRisco();
        $margem = $this->marginService->getAnaliseMargem();
        $insights = AiInsight::orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.kn_intelligence.dashboard', compact('vendas', 'estoqueRisco', 'margem', 'insights'));
    }

    public function assistant()
    {
        $conversations = AiConversation::where('user_id', Auth::id())->orderBy('updated_at', 'desc')->get();
        return view('admin.kn_intelligence.assistant', compact('conversations'));
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
        $margem = $this->marginService->getAnaliseMargem();

        $contexto = "Você é o assistente inteligente da loja KN Cosméticos (KN Intelligence).\n"
            ."Abaixo estão os DADOS REAIS da loja para você responder com precisão:\n"
            ."1. Vendas/Faturamento Atual: R$ {$vendas['faturamento']} | Pedidos: {$vendas['pedidos']} | Ticket Médio: R$ {$vendas['ticket_medio']} | Descontos: R$ {$vendas['total_desconto']}\n"
            ."2. Risco de Estoque Baixo: {$estoque['total_itens_criticos']} produtos com estoque abaixo do mínimo.\n"
            ."3. Vendas com Maiores Descontos: {$margem['total_analisado']} itens analisados.\n\n"
            ."Pergunta do usuário: \"{$pergunta}\"\n\n"
            ."Responda de forma profissional, direta, com formatação limpa e sugestões estratégicas práticas para a loja.";

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
}
