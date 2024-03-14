<?php

namespace App\Http\Controllers;

use App\Http\Models\Fornecedor;
use App\Http\Models\Produto;
use App\Http\Models\ProdutoQuantidade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;


/**
 * @property Fornecedor fornecedor
 */
class  ProductMinController extends Controller
{

    protected $request,$produto,$produtoQtd;

    public function __construct(Request $request, Produto $produto, ProdutoQuantidade $produtoQtd){
        $this->request = $request;
        $this->produto = $produto;
        $this->produtoQtd = $produtoQtd;
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        return view('admin.produto_min');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        try{
          /*  $quantity = DB::table('loja_produtos_quantidade')->select('quantidade_minima')
                                ->distinct()
                                ->get();

            foreach ($quantity as $pay) {
                //if(!in_array($pay->id, [1])) //diferentes desse(s) ids de pagamentos [1 = dinheiro]
                    $paymentType[] = $pay->quantidade_minima;
            }*/

            $products =  $this->produto::Join('loja_produtos_quantidade as q','q.produto_id','=' ,'loja_produtos.id')
                                            ->whereRaw('q.quantidade <= q.quantidade_minima')
                                            ->where('q.loja_id',  2)
                                            ->where('loja_produtos.block',  0)
                                           // ->groupBy("loja_produtos.id")
                                            ->orderBy("q.quantidade_minima",'desc')
                                            ->get();

           /* $products =  $this->produto::select('id',
                                                'codigo_produto',
                                                'descricao',
                                                DB::raw('(CASE WHEN block = 1 THEN "BLOQUEADO" ELSE "LIBERADO" END) AS status'),
                                                'valor_produto',
                                                'valor_cartao',
                                                'valor_dinheiro'
                                            )->where("block",1)->get();*/

            if(!empty($products)) {
                return \response()->json($products);
            }  else {
                return \response()->json(array('data'=>''));
            }

        }catch (\Exception $e) {
            return Response::json(array('success' => false, 'message' => 'ProductMinController' . $e ), 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function update(int $id): JsonResponse
    {
        try{
            $produto =$this->produto::where('id', $id)->update(['block' => 0]);

            if($produto){
                return Response::json(array("success" => true, "message" => "Produto desbloqueado com sucesso!"),200);
            }else {
                return Response::json(array("success" => false, "message" => utf8_encode("Produto não desbloqueado id: [ {$id} ]")), 400);
            }
        }catch (\Exception $e) {
            return Response::json(array('success' => false, 'message' => 'ProductBlockController -> update()' . $e ), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
      //

    }
}
