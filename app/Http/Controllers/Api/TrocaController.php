<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Models\Produto;
use App\Http\Models\Vendas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class TrocaController extends Controller
{
    protected  $request, $product, $vendas;

    public function __construct(Request $request, Produto $product, Vendas $vendas){
        $this->request = $request;
        $this->product = $product;
        $this->vendas = $vendas;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  String  $code_store
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($code_store)
    {
      // dd($this->vendas::where('loja_vendas.codigo_venda', $code_store)->get());

        $return = $this->vendas::join('loja_vendas_produtos', 'loja_vendas.id', '=','loja_vendas_produtos.venda_id')
                ->join('loja_vendas_produtos_descontos', 'loja_vendas.id', '=','loja_vendas_produtos_descontos.venda_id')
                ->join('loja_vendas_produtos_tipo_pagamentos', 'loja_vendas.id', '=','loja_vendas_produtos_tipo_pagamentos.venda_id')
                ->join('loja_produtos_variacao', 'loja_vendas_produtos.codigo_produto', '=','loja_produtos_variacao.subcodigo')
                ->join('loja_produtos_new', 'loja_produtos_variacao.products_id', '=','loja_produtos_new.id')
                ->select('loja_vendas.id as venda_id',
                    'loja_vendas.loja_id as loja_id',
                    'loja_vendas.valor_total as valor_total',
                    'loja_vendas.created_at as created_at',
                    'loja_vendas.updated_at as updated_at',
                    'loja_vendas_produtos.codigo_produto',
                    'loja_vendas_produtos.descricao as desc_varicacao_venda',
                    'loja_produtos_new.descricao',
                    'loja_vendas_produtos.valor_produto',
                    'loja_vendas_produtos.quantidade',
                    //'loja_vendas_produtos.troca',
                    'loja_vendas_produtos_descontos.valor_desconto',
                    'loja_vendas_produtos_descontos.valor_recebido',
                    'loja_vendas_produtos_descontos.valor_percentual',
                    'loja_vendas_produtos_tipo_pagamentos.forma_pagamento_id',
                    'loja_produtos_variacao.products_id as produto_id',
                    'loja_produtos_variacao.variacao',
                    'loja_vendas_produtos.fornecedor_id',
                    'loja_vendas_produtos.categoria_id')
                ->where('loja_vendas.codigo_venda', $code_store)
                ->get();

     //  print_r($return);darta
      //  $count = count($return);
      //  print_r($count);
      // die();
        //Total de produtos na venda
       // $count = count($return);
        if(count($return) > 0){
            foreach ($return as $key => $value){
                $store['venda_id'] = $value->venda_id;
                $store['descricao'] = $value->desc_varicacao_venda;
                $store['quantidade'] = $value->quantidade;
                $store['codigo_produto'] = $value-> codigo_produto;
                $store['valor_produto'] = $value->valor_produto;
                $store['codigo_venda'] =  $code_store;
                $store['troca'] =  true;
                $store['data'] = date('Y-m-d H:i:s', strtotime(  $return[0]->created_at));
                $store['id'] = $value->produto_id;
                $store['fornecedor_id'] =  $value->fornecedor_id;
                $store['categoria_id'] = $value->categoria_id;

                $ret['produtos'][$key] =  $store;
            }
           // dd($store);

            $ret['valor_total'] =  $return[0]->valor_total;
            $ret['valor_sub_total'] =  $return[0]->valor_total -  $return[0]->valor_desconto;
            $ret['valor_desconto'] =  $return[0]->valor_desconto;
            $ret['valor_recebido'] =  $return[0]->valor_recebido;
            $ret['percentual'] =  $return[0]->valor_percentual;
            $ret['id_forma_pagamento'] =  $return[0]->forma_pagamento_id;
            $ret['loja_id'] = $return[0]->loja_id;
            $ret['venda_id'] = $return[0]->venda_id;
            $ret['success'] =  true;
            $ret['troca'] =  true;

            return Response::json(array($ret), 200, [],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }else{
            return Response::json(array(array('success' => false, "message" => 'Venda não localizada!')), 400, [],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       // dd($id);

       /* $sale = $this->vendas->find($id);

        $delete = $sale->delete();

        if($delete){
            return Response::json(array('success' => true, "message" => 'Venda deletada com sucesso!'), 200, [],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }else{
            return Response::json(array('success' => false, "message" => 'Error ao deleter venda!'), 400, [],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }*/
    }
}
