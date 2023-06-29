<?php

namespace App\Http\Controllers;

use App\Http\Models\Vendas;
use App\Http\Models\VendasProdutos;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Throwable;


class EstoqueController extends Controller
{
    protected $request,$vendas,$vProducts;


    public function __construct(Request $request, Vendas $vendas, VendasProdutos $vProducts){
        $this->request = $request;
        $this->vendas = $vendas;
        $this->vProducts = $vProducts;
    }


    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('admin.estoque');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create()
    {
        //DB::raw("DATE_FORMAT(created_at, '%Y-%m')")
        $ret = $this->vendas::join('loja_vendas_produtos as prod','prod.venda_id','=' ,'loja_vendas.id')
            ->join('loja_categorias as cat','cat.id','=' ,'prod.categoria_id')
            ->select(
                DB::raw("sum(valor_produto * prod.quantidade) as total"),
                "descricao",
                DB::raw("sum(prod.quantidade) as qtd"),
                "cat.nome as categoria",
                "codigo_produto"
               // DB::raw("DATE_FORMAT(loja_vendas.created_at, '%m/%Y') as data"),
               // DB::raw("DATE_FORMAT(loja_vendas.created_at, '%Y-%m') as data_sort")
            )->take(100)
            ->groupBy("codigo_produto","categoria")
           ->orderBy('qtd', 'desc')
            ->get();

        return Response::json($ret);

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
       //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function edit($id)
    {

        try {
            $ret = $this->vProducts::
            select(
                'descricao',
                'codigo_produto',
                DB::raw("sum(quantidade) as quantidade"),
                DB::raw(	"CONCAT('R$ ',FORMAT(valor_produto ,2 , 'pt_BR'))  as valor"),
                DB::raw("DATE_FORMAT(created_at, '%m/%Y') as data")
            )->take(30)
                ->where("codigo_produto",$id)
                ->groupBy('data')
                ->orderBy('quantidade','desc')
                ->get();


        }catch (Throwable $e){
            return Response()->json(["success" =>false, 'message' => $e->getMessage()]);
        }
        return Response()->json(["success" =>true, 'data' => $ret]);
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
