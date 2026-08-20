<?php

namespace App\AI\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesIntelligenceService
{
    /**
     * Calcula métricas comerciais consolidadas para a loja atual.
     * Método preservado para manter compatibilidade com o Controller atual.
     */
    public function getPerformanceComercial(string $dataInicio = null, string $dataFim = null): array
    {
        $inicio = $dataInicio ? Carbon::parse($dataInicio)->startOfDay() : Carbon::now()->startOfMonth();
        $fim = $dataFim ? Carbon::parse($dataFim)->endOfDay() : Carbon::now()->endOfDay();

        // Tabela loja_vendas — coluna correta é valor_total
        // Descontos ficam em loja_vendas_produtos_descontos (campo valor_desconto, FK venda_id)
        $row = DB::table('loja_vendas')
            ->whereBetween('created_at', [$inicio, $fim])
            ->selectRaw(
                'COALESCE(SUM(loja_vendas.valor_total), 0) AS faturamento,'
                . 'COUNT(*) AS pedidos,'
                . 'COALESCE(('
                .   'SELECT SUM(d.valor_desconto)'
                .   ' FROM loja_vendas_produtos_descontos d'
                .   ' WHERE d.venda_id IN ('
                .       'SELECT id FROM loja_vendas v2'
                .       ' WHERE v2.created_at BETWEEN ? AND ?'
                .   ')'
                . '), 0) AS total_desconto,'
                . 'COALESCE(('
                .   'SELECT SUM(vp.quantidade)'
                .   ' FROM loja_vendas_produtos vp'
                .   ' JOIN loja_vendas v3 ON vp.venda_id = v3.id'
                .   ' WHERE v3.created_at BETWEEN ? AND ?'
                . '), 0) AS unidades_vendidas'
            )
            ->addBinding([$inicio, $fim], 'select')
            ->addBinding([$inicio, $fim], 'select')
            ->first();

        $faturamento = (float) ($row->faturamento ?? 0);
        $pedidos = (int) ($row->pedidos ?? 0);
        $unidades = (int) ($row->unidades_vendidas ?? 0);
        
        $ticketMedio = $pedidos > 0 ? $faturamento / $pedidos : 0.0;
        $upa = $pedidos > 0 ? $unidades / $pedidos : 0.0;

        return [
            'periodo' => [
                'inicio' => $inicio->toIso8601String(),
                'fim' => $fim->toIso8601String(),
            ],
            'faturamento' => round($faturamento, 2),
            'pedidos' => $pedidos,
            'ticket_medio' => round($ticketMedio, 2),
            'total_desconto' => round((float) ($row->total_desconto ?? 0), 2),
            'unidades_vendidas' => $unidades,
            'upa' => round($upa, 2),
        ];
    }

    /**
     * Retorna a consolidação completa das métricas gerenciais, incluindo comparação.
     */
    public function getMetricasGerenciais(string $dataInicio = null, string $dataFim = null): array
    {
        $inicio = $dataInicio ? Carbon::parse($dataInicio)->startOfDay() : Carbon::now()->startOfMonth();
        $fim = $dataFim ? Carbon::parse($dataFim)->endOfDay() : Carbon::now()->endOfDay();

        $atual = $this->getPerformanceComercial($inicio->toDateTimeString(), $fim->toDateTimeString());

        $diffDias  = $inicio->diffInDays($fim) + 1;
        $inicioAnt = (clone $inicio)->subDays($diffDias);
        $fimAnt    = (clone $inicio)->subSecond();

        $anterior = $this->getPerformanceComercial($inicioAnt->toDateTimeString(), $fimAnt->toDateTimeString());

        $produtos = $this->getTopProdutos($inicio->toDateTimeString(), $fim->toDateTimeString(), $atual['faturamento']);

        return [
            'receita' => [
                'valor' => $atual['faturamento'],
                'periodo_anterior' => $anterior['faturamento'],
                'percentual_variacao' => $this->calcularVariacao($atual['faturamento'], $anterior['faturamento'])
            ],
            'pedidos' => [
                'valor' => $atual['pedidos'],
                'periodo_anterior' => $anterior['pedidos'],
                'percentual_variacao' => $this->calcularVariacao($atual['pedidos'], $anterior['pedidos'])
            ],
            'ticket_medio' => [
                'valor' => $atual['ticket_medio'],
                'periodo_anterior' => $anterior['ticket_medio'],
                'percentual_variacao' => $this->calcularVariacao($atual['ticket_medio'], $anterior['ticket_medio'])
            ],
            'upa' => [
                'valor' => $atual['upa'],
                'periodo_anterior' => $anterior['upa'],
                'percentual_variacao' => $this->calcularVariacao($atual['upa'], $anterior['upa'])
            ],
            'produtos_mais_vendidos' => $produtos
        ];
    }

    /**
     * Consulta auxiliar para produtos mais vendidos do período.
     */
    public function getTopProdutos(string $dataInicio, string $dataFim, float $faturamentoTotal = 0): array
    {
        $inicio = Carbon::parse($dataInicio)->startOfDay();
        $fim = Carbon::parse($dataFim)->endOfDay();

        $produtos = DB::table('loja_vendas_produtos')
            ->join('loja_vendas', 'loja_vendas_produtos.venda_id', '=', 'loja_vendas.id')
            ->whereBetween('loja_vendas.created_at', [$inicio, $fim])
            ->selectRaw('
                codigo_produto,
                MAX(descricao) as produto,
                SUM(quantidade) as quantidade_vendida,
                SUM(valor_produto * quantidade) as faturamento_produto
            ')
            ->groupBy('codigo_produto')
            ->orderBy('quantidade_vendida', 'desc')
            ->limit(20)
            ->get();

        return $produtos->map(function ($p) use ($faturamentoTotal) {
            $faturamentoProduto = (float) $p->faturamento_produto;
            $participacao = $faturamentoTotal > 0 ? ($faturamentoProduto / $faturamentoTotal) * 100 : 0.0;
            
            return [
                'codigo_produto' => $p->codigo_produto,
                'produto' => $p->produto,
                'quantidade_vendida' => (int) $p->quantidade_vendida,
                'faturamento' => round($faturamentoProduto, 2),
                'participacao_faturamento' => round($participacao, 2)
            ];
        })->toArray();
    }

    /**
     * Calcula a variação percentual lidando com divisões por zero.
     */
    private function calcularVariacao(float $atual, float $anterior): float
    {
        if ($anterior == 0) {
            return $atual > 0 ? 100.0 : 0.0;
        }
        return round((($atual - $anterior) / $anterior) * 100, 2);
    }
}
