<?php

namespace App\AI\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MarginIntelligenceService
{
    /**
     * Calcula métricas gerenciais de margem cruzando vendas e custos de produtos.
     */
    public function getMargemGerencial(string $dataInicio = null, string $dataFim = null): array
    {
        $inicio = $dataInicio ? Carbon::parse($dataInicio)->startOfDay() : Carbon::now()->startOfMonth();
        $fim = $dataFim ? Carbon::parse($dataFim)->endOfDay() : Carbon::now()->endOfDay();

        $atual = $this->calcularPeriodo($inicio, $fim);

        $diffDias  = $inicio->diffInDays($fim) + 1;
        $inicioAnt = (clone $inicio)->subDays($diffDias);
        $fimAnt    = (clone $inicio)->subSecond();

        $anterior = $this->calcularPeriodo($inicioAnt, $fimAnt, false); // false = sem carregar lista de produtos

        return [
            'faturamento_total' => [
                'valor' => $atual['faturamento_total'],
                'periodo_anterior' => $anterior['faturamento_total'],
                'variação' => $this->calcularVariacao($atual['faturamento_total'], $anterior['faturamento_total'])
            ],
            'custo_total' => [
                'valor' => $atual['custo_total'],
                'periodo_anterior' => $anterior['custo_total'],
                'variação' => $this->calcularVariacao($atual['custo_total'], $anterior['custo_total'])
            ],
            'lucro_bruto' => [
                'valor' => $atual['lucro_bruto'],
                'periodo_anterior' => $anterior['lucro_bruto'],
                'variação' => $this->calcularVariacao($atual['lucro_bruto'], $anterior['lucro_bruto'])
            ],
            'margem_bruta_percentual' => [
                'valor' => $atual['margem_bruta_percentual'],
                'periodo_anterior' => $anterior['margem_bruta_percentual'],
                'diferenca_pp' => round($atual['margem_bruta_percentual'] - $anterior['margem_bruta_percentual'], 2)
            ],
            'produtos' => $atual['produtos'],
            'ranking_maior_lucro' => $this->ordenarProdutos($atual['produtos'], 'lucro_bruto', 'desc'),
            'ranking_menor_margem' => $this->ordenarProdutos($atual['produtos'], 'margem_percentual', 'asc')
        ];
    }

    private function calcularPeriodo(Carbon $inicio, Carbon $fim, bool $incluirProdutos = true): array
    {
        // Cruzamento: loja_vendas -> loja_vendas_produtos -> loja_produtos_new -> loja_produtos_controle
        // Nota: O desconto global (loja_vendas_produtos_descontos) não é rateado aqui para não inventar regras.
        // Utiliza-se valor_produto gravado na loja_vendas_produtos que é o valor final da unidade.
        // O custo é pego de loja_produtos_controle via variacao_id.
        $query = DB::table('loja_vendas_produtos as vp')
            ->join('loja_vendas as v', 'vp.venda_id', '=', 'v.id')
            ->join('loja_produtos_new as p', 'vp.produto_id', '=', 'p.id')
            ->leftJoin('loja_produtos_controle as pc', 'vp.variacao_id', '=', 'pc.products_variation_id')
            ->whereBetween('v.created_at', [$inicio, $fim])
            ->selectRaw('
                vp.codigo_produto,
                MAX(p.descricao) as descricao,
                SUM(vp.quantidade) as quantidade_vendida,
                SUM(vp.valor_produto * vp.quantidade) as faturamento,
                MAX(pc.valor_custo) as custo_unitario_verificado,
                SUM(COALESCE(pc.valor_custo, 0) * vp.quantidade) as custo,
                SUM(vp.percentual_desconto) as soma_descontos_unitarios
            ')
            ->groupBy('vp.codigo_produto');

        $rows = $query->get();

        $faturamentoTotal = 0;
        $faturamentoComCusto = 0;
        $custoTotal = 0;
        $produtosComCusto = 0;
        $produtosSemCusto = 0;
        $produtos = [];

        foreach ($rows as $row) {
            $faturamentoItem = (float) $row->faturamento;
            $faturamentoTotal += $faturamentoItem;
            
            $custoDisponivel = !is_null($row->custo_unitario_verificado);

            if ($custoDisponivel) {
                $custoItem = (float) $row->custo; 
                $lucroItem = $faturamentoItem - $custoItem;
                $margemItem = $faturamentoItem > 0 ? ($lucroItem / $faturamentoItem) * 100 : 0.0;
                
                $faturamentoComCusto += $faturamentoItem;
                $custoTotal += $custoItem;
                $produtosComCusto++;
            } else {
                $custoItem = null;
                $lucroItem = null;
                $margemItem = null;
                $produtosSemCusto++;
            }

            if ($incluirProdutos) {
                $produtos[] = [
                    'codigo_produto' => $row->codigo_produto,
                    'descricao' => $row->descricao,
                    'quantidade_vendida' => (int) $row->quantidade_vendida,
                    'faturamento' => round($faturamentoItem, 2),
                    'custo_estimado' => $custoItem !== null ? round($custoItem, 2) : null,
                    'lucro_bruto' => $lucroItem !== null ? round($lucroItem, 2) : null,
                    'margem_percentual' => $margemItem !== null ? round($margemItem, 2) : null,
                    'custo_disponivel' => $custoDisponivel
                ];
            }
        }

        $lucroTotal = $faturamentoComCusto - $custoTotal;
        $margemBrutaPercentual = $faturamentoComCusto > 0 
            ? ($lucroTotal / $faturamentoComCusto) * 100 
            : 0.0;

        return [
            'faturamento_total' => round($faturamentoTotal, 2),
            'custo_total' => round($custoTotal, 2),
            'lucro_bruto' => round($lucroTotal, 2),
            'margem_bruta_percentual' => round($margemBrutaPercentual, 2),
            'faturamento_com_custo' => round($faturamentoComCusto, 2),
            'indicador_cobertura' => [
                'produtos_com_custo' => $produtosComCusto,
                'produtos_sem_custo' => $produtosSemCusto
            ],
            'produtos' => $produtos,
        ];
    }

    private function calcularVariacao(float $atual, float $anterior): float
    {
        if ($anterior == 0) {
            return $atual > 0 ? 100.0 : 0.0;
        }
        return round((($atual - $anterior) / $anterior) * 100, 2);
    }

    private function ordenarProdutos(array $produtos, string $chave, string $direcao): array
    {
        $produtosValidos = array_filter($produtos, function ($p) {
            return $p['custo_disponivel'] === true;
        });

        usort($produtosValidos, function ($a, $b) use ($chave, $direcao) {
            if ($a[$chave] == $b[$chave]) return 0;
            if ($direcao === 'asc') {
                return ($a[$chave] < $b[$chave]) ? -1 : 1;
            }
            return ($a[$chave] > $b[$chave]) ? -1 : 1;
        });

        return array_slice($produtosValidos, 0, 15);
    }
}
