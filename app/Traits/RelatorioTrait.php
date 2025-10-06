<?php


namespace App\Traits;

use App\Http\Models\Vendas;
use Illuminate\Support\Facades\DB;

trait RelatorioTrait
{
    function totaisPorDia($store_id,$dateOne, $dateTwo){
        //totais por dia
        return Vendas::leftjoin('loja_vendas_produtos_tipo_pagamentos as tp', 'tp.venda_id', '=', 'loja_vendas.id')
            ->join('loja_vendas_produtos as lvp', 'lvp.venda_id' , '=', 'loja_vendas.id')
            ->leftjoin('loja_forma_pagamentos as fp', 'tp.forma_pagamento_id', '=', 'fp.id')
            ->leftjoin('loja_taxa_cartoes as lc', 'lc.forma_id', '=', 'fp.id')
            ->select(
                "fp.nome as name",
                "fp.id as id_payment",
                //(DB::raw("FORMAT(SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)),2) AS orderTotal")))
                //(DB::raw("SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)) AS orderTotal")))
                (DB::raw("SUM((lvp.valor_produto * lvp.quantidade) - ((lvp.valor_produto * lvp.quantidade) * tp.taxa/100)) AS orderTotal")))
            ->where('lvp.troca', '!=' ,1)
            ->where('loja_vendas.loja_id', $store_id)
            ->whereBetween(DB::raw('DATE(loja_vendas.created_at)'), array($dateOne, $dateTwo))
            ->groupBy('fp.id')
            ->get();
    }


    public function buscaTaxas($dataIinicio, $dataFim)
    {
       return DB::table('loja_vendas as lv')
            ->join('loja_vendas_produtos as p', 'p.venda_id', '=', 'lv.id')
            ->leftJoin(DB::raw('(
                SELECT venda_id, AVG(taxa) AS taxa
                FROM loja_vendas_produtos_tipo_pagamentos
                GROUP BY venda_id
            ) as tp'), 'tp.venda_id', '=', 'lv.id')
            ->where('lv.loja_id', 2)
            ->where('p.troca', 0)
            ->whereBetween(DB::raw('DATE(p.created_at)'), [$dataIinicio, $dataFim])
            ->select(DB::raw("ROUND(SUM((p.valor_produto * p.quantidade) * COALESCE(tp.taxa, 0) / 100), 2) as total_taxas"))
            ->value('total_taxas');
    }

}
