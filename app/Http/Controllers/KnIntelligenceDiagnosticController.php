<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AI\Services\InventoryIntelligenceService;
use App\AI\Services\SalesIntelligenceService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class KnIntelligenceDiagnosticController extends Controller
{
    public function estoque(Request $request, InventoryIntelligenceService $inventoryService, SalesIntelligenceService $salesService)
    {
        $dataInicial = $request->input('data_inicial', Carbon::now()->subDays(30)->toDateString());
        $dataFinal = $request->input('data_final', Carbon::now()->toDateString());
        $filtro = $request->input('filtro', 'todos');

        // 1. Obter Inteligência do Service
        $intelligence = $inventoryService->getEstoqueGerencial($dataInicial, $dataFinal);
        $serviceProducts = collect($intelligence['produtos'])->keyBy('chave_id');

        // 2. Consulta Direta no Banco para Comparação (Somente Leitura)
        // Busca a venda validada para cruzar
        $bancoVendasRaw = DB::table('loja_vendas_produtos')
            ->join('loja_vendas', 'loja_vendas.id', '=', 'loja_vendas_produtos.venda_id')
            ->whereBetween(DB::raw('DATE(loja_vendas.created_at)'), [$dataInicial, $dataFinal])
            ->where('loja_vendas_produtos.troca', false) // Regra de exclusão de trocas
            ->select('loja_vendas_produtos.produto_id', 'loja_vendas_produtos.variacao_id', DB::raw('SUM(loja_vendas_produtos.quantidade) as qtd_vendida'))
            ->groupBy('loja_vendas_produtos.produto_id', 'loja_vendas_produtos.variacao_id')
            ->get()
            ->keyBy(function($item) {
                return $item->variacao_id ? $item->variacao_id : 'p_'.$item->produto_id;
            });

        // 3. Mesclar e Validar
        $dadosTabela = [];
        $divergencias = 0;
        $comVendas = 0;
        $semVendas = 0;
        
        foreach ($serviceProducts as $chaveId => $itemService) {
            $qtdService = $itemService['quantidade_vendida_periodo'];
            $qtdBanco = isset($bancoVendasRaw[$chaveId]) ? (int) $bancoVendasRaw[$chaveId]->qtd_vendida : 0;
            
            $resultado = 'OK';
            if ($qtdService !== $qtdBanco) {
                $resultado = 'DIVERGÊNCIA';
                $divergencias++;
            }

            if ($qtdService > 0 || $qtdBanco > 0) {
                $comVendas++;
            } else {
                $semVendas++;
            }

            // Aplicar filtros visuais
            if ($filtro === 'com_vendas' && $qtdService == 0 && $qtdBanco == 0) continue;
            if ($filtro === 'sem_vendas' && ($qtdService > 0 || $qtdBanco > 0)) continue;
            if ($filtro === 'sem_estoque' && $itemService['estoque_atual'] > 0) continue;
            if ($filtro === 'estoque_negativo' && $itemService['estoque_atual'] >= 0) continue;
            if ($filtro === 'sem_custo' && $itemService['custo_unitario'] > 0) continue;
            if ($filtro === 'com_custo' && $itemService['custo_unitario'] <= 0) continue;

            $dadosTabela[] = [
                'variacao_id' => $itemService['variacao_id'] ?? 'N/A',
                'codigo_produto' => $itemService['codigo_produto'],
                'subcodigo' => $itemService['subcodigo'] ?? '',
                'descricao' => $itemService['descricao'] ?? '',
                'variacao' => $itemService['variacao'],
                'estoque_atual' => $itemService['estoque_atual'],
                'estoque_minimo' => $itemService['estoque_minimo'],
                'qtd_service' => $qtdService,
                'qtd_banco' => $qtdBanco,
                'resultado' => $resultado,
                'velocidade' => $itemService['velocidade_media_diaria'],
                'cobertura' => $itemService['cobertura_dias'],
                'custo_unitario' => $itemService['custo_unitario'],
                'capital_imobilizado' => $itemService['capital_imobilizado'],
                'parado' => $itemService['produto_parado'] ? 'SIM' : 'NÃO',
                'sem_estoque' => $itemService['sem_estoque'] ? 'SIM' : 'NÃO',
                'estoque_negativo' => $itemService['estoque_negativo'] ? 'SIM' : 'NÃO',
            ];
        }

        // Ordenação Básica: Divergências primeiro, depois quem vendeu mais
        usort($dadosTabela, function ($a, $b) {
            if ($a['resultado'] !== $b['resultado']) {
                return $a['resultado'] === 'DIVERGÊNCIA' ? -1 : 1;
            }
            return $b['qtd_service'] <=> $a['qtd_service'];
        });

        // Exportação CSV
        if ($request->has('export') && $request->input('export') == 'csv') {
            return $this->exportCsv($dadosTabela, $intelligence['resumo'], $dataInicial, $dataFinal, $divergencias);
        }

        // Resumo complementado
        $resumo = $intelligence['resumo'];
        $resumo['com_vendas'] = $comVendas;
        $resumo['sem_vendas'] = $semVendas;
        $resumo['divergencias'] = $divergencias;
        $resumo['data_inicial'] = $dataInicial;
        $resumo['data_final'] = $dataFinal;
        $resumo['dias_periodo'] = $intelligence['periodo']['dias_analisados'] ?? 30;
        $resumo['total_variacoes_analisadas'] = count($serviceProducts);
        $resumo['variacoes_sem_estoque'] = $intelligence['resumo']['produtos_sem_estoque'] ?? 0;

        // Paginação manual para não travar o navegador na produção
        $perPage = 100;
        $page = Paginator::resolveCurrentPage() ?: 1;
        $items = collect($dadosTabela);
        
        $dadosPaginados = new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('admin.kn_intelligence.diagnostico_estoque', compact('dadosPaginados', 'resumo', 'filtro'));
    }

    private function exportCsv($dadosTabela, $resumo, $dataInicial, $dataFinal, $divergencias)
    {
        $fileName = "diagnostico_kn_intelligence_" . date('Y-m-d_H-i-s') . ".csv";
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($dadosTabela) {
            $file = fopen('php://output', 'w');
            
            // BOM UTF-8 for Excel
            fputs($file, "\xEF\xBB\xBF");
            
            fputcsv($file, [
                'Variação ID', 'Código', 'Descrição', 'Variação', 'Estoque Atual', 
                'Estoque Mínimo', 'Qtd Vendida (Service)', 'Qtd Vendida (Banco)', 
                'Resultado', 'Velocidade/Dia', 'Cobertura (Dias)', 'Custo Unitário', 
                'Capital Imobilizado', 'Parado?', 'Sem Estoque?', 'Estoque Negativo?'
            ], ';');

            foreach ($dadosTabela as $row) {
                fputcsv($file, [
                    $row['variacao_id'], $row['codigo_produto'], $row['descricao'], 
                    $row['variacao'], $row['estoque_atual'], $row['estoque_minimo'], 
                    $row['qtd_service'], $row['qtd_banco'], $row['resultado'], 
                    number_format($row['velocidade'], 2, ',', ''), 
                    $row['cobertura'] === 99999 ? 'ILIMITADA' : $row['cobertura'], 
                    number_format($row['custo_unitario'], 2, ',', ''), 
                    number_format($row['capital_imobilizado'], 2, ',', ''), 
                    $row['parado'], $row['sem_estoque'], $row['estoque_negativo']
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
