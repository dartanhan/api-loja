<?php

namespace App\AI\Services;

use Illuminate\Support\Facades\DB;

class MarginIntelligenceService
{
    /**
     * Analisa descontos aplicados e margens das vendas.
     */
    public function getAnaliseMargem(): array
    {
        $vendasComDescontoAlto = DB::table('loja_vendas_produtos as vp')
            ->join('loja_vendas as v', 'vp.venda_id', '=', 'v.id')
            ->join('loja_produtos_new as p', 'vp.produto_id', '=', 'p.id')
            ->where('vp.percentual_desconto', '>', 0)
            ->select(
                'v.id as venda_id',
                'p.descricao as produto',
                'vp.valor_produto',
                'vp.percentual_desconto',
                'vp.quantidade',
                'v.created_at'
            )
            ->orderBy('vp.percentual_desconto', 'desc')
            ->limit(10)
            ->get();

        return [
            'total_analisado' => $vendasComDescontoAlto->count(),
            'descontos_altos' => $vendasComDescontoAlto->toArray()
        ];
    }
}
