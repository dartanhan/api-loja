<?php

namespace App\Console\Commands;

use App\AI\Providers\GeminiProvider;
use App\AI\Services\InventoryIntelligenceService;
use App\AI\Services\MarginIntelligenceService;
use App\AI\Services\SalesIntelligenceService;
use App\Models\AiInsight;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateAiInsightsCommand extends Command
{
    protected $signature = 'kn-intelligence:generate-insights';
    protected $description = 'Gera insights analíticos de vendas, estoque e margem usando a IA Gemini';

    public function handle(
        SalesIntelligenceService $salesService,
        InventoryIntelligenceService $inventoryService,
        MarginIntelligenceService $marginService,
        GeminiProvider $geminiProvider
    ) {
        $this->info('Iniciando geração de insights KN Intelligence...');

        try {
            $vendas = $salesService->getPerformanceComercial();
            $estoque = $inventoryService->getEstoqueRisco();
            $margem = $marginService->getAnaliseMargem();

            $prompt = "Você é um especialista em e-commerce e inteligência de negócios da loja KN Cosméticos.\n"
                . "Analise os dados abaixo e gere exatamente 3 insights estratégicos curtos e práticos.\n"
                . "DADOS DA LOJA:\n"
                . "- Faturamento do Mês: R$ {$vendas['faturamento']} | Pedidos: {$vendas['pedidos']} | Ticket Médio: R$ {$vendas['ticket_medio']}\n"
                . "- Total Descontos Concedidos: R$ {$vendas['total_desconto']}\n"
                . "- Produtos com Risco de Estoque Baixo: {$estoque['total_itens_criticos']} itens\n"
                . "- Itens com Altos Descontos Analisados: {$margem['total_analisado']} vendas\n\n"
                . "Responda exclusivamente em formato JSON VÁLIDO (sem markdown ```json), como uma lista de objetos com a seguinte estrutura:\n"
                . "[\n"
                . "  {\n"
                . "    \"tipo\": \"vendas\"|\"estoque\"|\"margem\"|\"geral\",\n"
                . "    \"severidade\": \"baixa\"|\"media\"|\"alta\"|\"critica\",\n"
                . "    \"titulo\": \"Título conciso\",\n"
                . "    \"descricao\": \"Descrição detalhada da recomendação prática\"\n"
                . "  }\n"
                . "]";

            $rawResponse = $geminiProvider->generateContent($prompt);

            // Limpa formatação markdown se houver
            $cleanJson = preg_replace('/^```json\s*|\s*```$/i', '', trim($rawResponse));
            $insightsData = json_decode($cleanJson, true);

            if (is_array($insightsData)) {
                foreach ($insightsData as $item) {
                    AiInsight::create([
                        'tipo'       => $item['tipo'] ?? 'geral',
                        'severidade' => $item['severidade'] ?? 'media',
                        'titulo'     => $item['titulo'] ?? 'Insight de IA',
                        'descricao'  => $item['descricao'] ?? '',
                        'lido'       => false,
                    ]);
                }
                $this->info('Insights gerados com sucesso!');
            } else {
                // Fallback caso a resposta da IA venha em texto corrido
                AiInsight::create([
                    'tipo'       => 'geral',
                    'severidade' => 'media',
                    'titulo'     => 'Análise Geral da Loja',
                    'descricao'  => $rawResponse,
                    'lido'       => false,
                ]);
                $this->info('Insight criado como texto livre.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao gerar insights KN Intelligence: ' . $e->getMessage());
            $this->error('Erro: ' . $e->getMessage());
        }

        return 0;
    }
}
