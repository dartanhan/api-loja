<?php

namespace App\AI\Services;

use Illuminate\Support\Facades\DB;

class InventoryIntelligenceService
{
    /**
     * Analisa risco de ruptura e produtos com baixo estoque na loja.
     */
    public function getEstoqueRisco(): array
    {
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
}
