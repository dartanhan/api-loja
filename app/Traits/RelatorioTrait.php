<?php


namespace App\Traits;

use App\Http\Models\Vendas;
use Illuminate\Support\Facades\DB;

trait RelatorioTrait
{
    function totaisPorDia($store_id,$dateOne, $dateTwo){
        //totais por dia
        return Vendas::leftjoin('loja_vendas_produtos_tipo_pagamentos as tp', 'tp.venda_id', '=', 'loja_vendas.id')
            ->leftjoin('loja_forma_pagamentos as fp', 'tp.forma_pagamento_id', '=', 'fp.id')
            ->leftjoin('loja_taxa_cartoes as lc', 'lc.forma_id', '=', 'fp.id')
            ->select(
                "fp.nome as name",
                "fp.id as id_payment",
                //(DB::raw("FORMAT(SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)),2) AS orderTotal")))
                (DB::raw("SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)) AS orderTotal")))
            // ->where('loja_vendas.loja_id', $store->id)
            // ->whereDate('loja_vendas.created_at', Carbon::now()->subDay('4'))
            //->whereDate('loja_vendas.created_at', Carbon::today())
            ->where('loja_vendas.loja_id', $store_id)
            ->whereBetween(DB::raw('DATE(loja_vendas.created_at)'), array($dateOne, $dateTwo))
            ->groupBy('fp.id')
            ->get();
    }

}
