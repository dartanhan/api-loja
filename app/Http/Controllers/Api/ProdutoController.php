<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Throwable;

class ProdutoController extends Controller
{
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
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        //dd($product);
        try {
       /* $product =  DB::table('loja_produtos')
            ->join('loja_produtos_quantidade', 'loja_produtos.id', '=','loja_produtos_quantidade.produto_id')
            ->join('loja_lojas', 'loja_lojas.id', '=','loja_produtos_quantidade.loja_id')
            ->where('loja_produtos_quantidade.loja_id', '=', $id)
            ->where('loja_produtos.block','=',1)
            ->select('loja_produtos.*', 'loja_produtos_quantidade.quantidade', 'loja_lojas.nome as nome_loja')
            ->get();*/

            $product =  DB::table('loja_produtos_new')
                ->join('loja_produtos_variacao', 'loja_produtos_new.id', '=','loja_produtos_variacao.products_id')
                ->select("loja_produtos_variacao.subcodigo as codigo_produto",
                        DB::raw("Concat(loja_produtos_new.descricao, ' - ', loja_produtos_variacao.variacao) as descricao"),
                        "loja_produtos_new.status",
                        "loja_produtos_variacao.products_id",
                        "loja_produtos_variacao.id",
                        "loja_produtos_variacao.valor_varejo",
                        "loja_produtos_variacao.valor_atacado",
                        "loja_produtos_variacao.quantidade")
                ->where('loja_produtos_new.status',1)
                ->get();

            if($product){
                return Response::json(array('success' => true, 'produtos' => $product), 200,[],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }else{
                return Response::json(array('success' => false, 'message' => 'NENHUM PRODUTO LOCALIZADO!'), 204,[],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

        } catch (Throwable $e) {
             return Response::json(array('success' => false, 'message' => $e->getMessage(), 'cod_retorno' => 500), 500);
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
        //
    }
}
