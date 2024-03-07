<?php

namespace App\Http\Controllers;

use App\Http\Models\Produto;
use App\Http\Models\ProdutoVariation;
use App\Http\Models\Reposicao;
use App\Http\Models\VendasProdutos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Throwable;

class ReposicaoController extends Controller
{
    protected $request,$produto;

    public function __construct(Request $request, Produto $produto){
        $this->request = $request;
        $this->produto = $produto;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|\Illuminate\View\View
     */
    public function index()
    {

        return view('admin.resposicao_trimestre');
    }

    public function store(){
        
        // try {
        //     $data = $this->produtoImage::select('id','produto_variacao_id','path')->where('produto_variacao_id',$id)->get();
 
        //  } catch (Throwable $e) {
        //      return Response::json(['error' => $e->getMessage()], 500);
        //  }

    }

    public function show(int $id)  {
       
        try {

            $informacoes = DB::table('loja_vendas_produtos as vp')
                ->leftJoin('loja_produtos_variacao as va', 'vp.codigo_produto', '=', 'va.subcodigo')
                ->leftJoin('loja_produtos_imagens as pi', 'va.id', '=', 'pi.produto_variacao_id')
                    ->select(
                        'pi.path as imagem',
                        'vp.codigo_produto as subcodigo',
                        'vp.descricao as variacao',
                        'va.valor_produto as valor_pago',
                        'va.estoque',
                        DB::raw('CAST(SUM(IF(vp.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY), vp.quantidade, 0)) AS UNSIGNED) AS qtd_total_venda_30d'),
                        DB::raw('CAST(SUM(IF(vp.created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY), vp.quantidade, 0)) AS UNSIGNED) AS qtd_total_venda_60d'),
                        DB::raw('CAST(SUM(IF(vp.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY), vp.quantidade, 0)) AS UNSIGNED) AS qtd_total_venda_90d'),
                        DB::raw('CAST(SUM(vp.quantidade) AS UNSIGNED) AS qtd'),
                        DB::raw('IF((va.status = 1), "ATIVO", "INATIVO") as status')
                )
                ->groupBy('vp.codigo_produto')
                ->where('va.products_id',$id)->get();
            
        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
        return Response::json(array('success' => true, "data" => $informacoes), 200);
    }
}
