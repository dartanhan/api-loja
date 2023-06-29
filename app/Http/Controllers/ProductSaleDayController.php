<?php

namespace App\Http\Controllers;

use App\Http\Models\Vendas;
use App\Http\Models\VendasProdutos;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\DataTables;

class ProductSaleDayController extends Controller
{

    protected $request,$sales,$salesProduct;

    public function __construct(Request $request, Vendas $sales, VendasProdutos $salesProduct){
        $this->request = $request;
        $this->sales = $sales;
        $this->salesProduct = $salesProduct;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.produto_sale_day');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse $response
     */
    public function store(Request $request): JsonResponse
    {
        try {
            if($request->dating == ""){
                $data =  Date("Y-m-d");
            }else{
                $data_array = explode("/",$request->dating );

                //$dateini = $dataini[1]."-".$dataini[0];
                $data =  $data_array[2]."-".$data_array[1]."-".$data_array[0];
            }
//dd($data);
            $sales =  $this->sales::Join('loja_vendas_produtos as vp','vp.venda_id','=' ,'loja_vendas.id')
                //->Join('loja_produtos as p','p.codigo_produto','=' ,'vp.codigo_produto')
                ->select(
                    DB::raw('DATE_FORMAT(loja_vendas.created_at, "%d/%m/%Y") as data'),
                    DB::raw('SUM(vp.valor_produto * vp.quantidade) AS total,sum(vp.quantidade) AS quantidade'),
                    'vp.descricao as descricao',
                    'vp.codigo_produto as codigo_produto',
                    'vp.valor_produto as valor_produto'
                )
                ->where('loja_id', 2)
                ->whereDate(DB::raw('DATE_FORMAT(loja_vendas.created_at, "%Y-%m-%d")'),$data)
                ->groupBy('codigo_produto','descricao')
                //->orderBy(DB::raw('DATE_FORMAT(loja_vendas.created_at, "%Y-%m-%d")'), 'DESC')
                ->orderBy('quantidade', 'desc');

            return  DataTables::of($sales)->make(true);

        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => 'ProductBestSellersController' . $e ), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
