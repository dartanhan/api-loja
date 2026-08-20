<?php

namespace App\AI\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryIntelligenceService
{
    /**
     * Analisa risco de ruptura e produtos com baixo estoque na loja.
     * Mantido para compatibilidade com partes antigas do sistema (ex: Painel atual).
     */
    public function getEstoqueRisco(): array
    {
        // Esta consulta legada usa loja_produtos_quantidade. 
        // Foi mantida intocada para não quebrar a tela atual.
        $produtosBaixoEstoque = DB::table('loja_produtos_quantidade as q')
            ->join('loja_produtos_new as p', 'q.produto_id', '=', 'p.id')
            ->whereColumn('q.quantidade', '<=', 'q.quantidade_minima')
            ->select(
                'p.id',
                'p.codigo_produto',
                'p.descricao',
                'q.quantidade',
                'q.quantidade_minima',
                'p.valor_produto'
            )
            ->limit(15)
            ->get();

        $totalItensCriticos = $produtosBaixoEstoque->count();

        return [
            'total_itens_criticos' => $totalItensCriticos,
            'produtos' => $produtosBaixoEstoque->toArray(),
        ];
    }

    /**
     * Retorna a consolidação completa das métricas de estoque gerencial.
     * Utiliza a estrutura real de estoque da loja controlada por VARIAÇÃO.
     */
    public function getEstoqueGerencial(string $dataInicio = null, string $dataFim = null): array
    {
        $inicio = $dataInicio ? Carbon::parse($dataInicio)->startOfDay() : Carbon::now()->startOfMonth();
        $fim = $dataFim ? Carbon::parse($dataFim)->endOfDay() : Carbon::now()->endOfDay();

        $diasPeriodo = $inicio->diffInDays($fim) ?: 1;

        // Subquery para obter vendas agrupadas por produto e variação no período
        $subqueryVendas = DB::table('loja_vendas_produtos as vp')
            ->join('loja_vendas as vd', 'vp.venda_id', '=', 'vd.id')
            ->whereBetween('vd.created_at', [$inicio, $fim])
            ->select('vp.produto_id', 'vp.variacao_id', DB::raw('SUM(vp.quantidade) as quantidade_vendida'))
            ->groupBy('vp.produto_id', 'vp.variacao_id');

        // Query principal agregando Produto Pai -> Variação Filho + Vendas
        $rows = DB::table('loja_produtos_new as p')
            ->leftJoin('loja_produtos_variacao as v', 'p.id', '=', 'v.products_id')
            ->leftJoinSub($subqueryVendas, 'vendas', function ($join) {
                $join->on('p.id', '=', 'vendas.produto_id')
                     ->where(function ($q) {
                         $q->on('v.id', '=', 'vendas.variacao_id')
                           ->orWhereNull('v.id'); // Se não tiver variação, pega as vendas atreladas só ao produto pai
                     });
            })
            ->select(
                'p.id as produto_id',
                'p.codigo_produto',
                'p.descricao as produto',
                'p.status as status_pai',
                'v.id as variacao_id',
                'v.variacao as descricao_variacao',
                'v.quantidade as estoque_atual',
                'v.quantidade_minima',
                'v.valor_produto as custo_unitario',
                'v.valor_varejo',
                'v.valor_atacado',
                'vendas.quantidade_vendida as quantidade_vendida_periodo'
            )
            ->get();

        $resumo = [
            'estoque_total_unidades' => 0,
            'produtos_com_estoque' => 0,
            'produtos_sem_estoque' => 0,
            'valor_total_imobilizado' => 0,
        ];
        
        $produtos = [];

        foreach ($rows as $row) {
            $estoqueAtual = (int) ($row->estoque_atual ?? 0);
            $qtdVendida = (int) ($row->quantidade_vendida_periodo ?? 0);
            
            $velocidade = $qtdVendida > 0 ? $qtdVendida / $diasPeriodo : 0;
            
            // Cobertura só pode ser calculada se há velocidade.
            $cobertura = ($velocidade > 0) ? $estoqueAtual / $velocidade : null;

            $custoDisponivel = !is_null($row->custo_unitario) && $row->custo_unitario > 0;
            $custo = $custoDisponivel ? (float) $row->custo_unitario : 0;
            $imobilizado = $custoDisponivel ? ($estoqueAtual * $custo) : 0;

            $semEstoque = $estoqueAtual <= 0;
            $estoqueNegativo = $estoqueAtual < 0;
            $produtoParado = ($estoqueAtual > 0 && $qtdVendida == 0);

            // Consolidação Resumo
            $resumo['estoque_total_unidades'] += $estoqueAtual;
            if ($estoqueAtual > 0) {
                $resumo['produtos_com_estoque']++;
                if ($imobilizado > 0) {
                    $resumo['valor_total_imobilizado'] += $imobilizado;
                }
            } else {
                $resumo['produtos_sem_estoque']++;
            }

            // Identificador único mesclado (para produtos sem filho, usa o id do pai com prefixo)
            $chaveId = $row->variacao_id ? $row->variacao_id : 'p_'.$row->produto_id;

            $produtos[] = [
                'chave_id' => $chaveId,
                'produto_id' => $row->produto_id,
                'variacao_id' => $row->variacao_id,
                'codigo_produto' => $row->codigo_produto,
                'descricao' => $row->produto,
                'variacao' => $row->descricao_variacao ?? 'Produto sem variação',
                'estoque_atual' => $estoqueAtual,
                'estoque_minimo' => (int) ($row->quantidade_minima ?? 0),
                'quantidade_vendida_periodo' => $qtdVendida,
                'dias_periodo' => $diasPeriodo,
                'velocidade_media_diaria' => round($velocidade, 2),
                'cobertura_dias' => $cobertura !== null ? round($cobertura, 1) : null,
                'produto_parado' => $produtoParado,
                'sem_estoque' => $semEstoque,
                'estoque_negativo' => $estoqueNegativo,
                'custo_unitario' => $custo,
                'capital_imobilizado' => $imobilizado,
                'custo_disponivel' => $custoDisponivel
            ];
        }

        $resumo['valor_total_imobilizado'] = round($resumo['valor_total_imobilizado'], 2);

        return [
            'periodo' => [
                'data_inicio' => $inicio->toIso8601String(),
                'data_fim' => $fim->toIso8601String(),
            ],
            'resumo' => $resumo,
            'produtos' => $produtos
        ];
    }
}
