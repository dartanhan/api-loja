<?php

namespace App\Http\Controllers;

use App\Http\Models\Fornecedor;
use App\Http\Models\Produto;
use App\Http\Models\ProdutoImagem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use NumberFormatter;


/**
 * @property Fornecedor fornecedor
 */
class  ProductBlockController extends Controller
{

    protected $request,$produto;

    public function __construct(Request $request, Produto $produto){
        $this->request = $request;
        $this->produto = $produto;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.produto_block');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        try{

            $products =  $this->produto::select('id',
                                                'codigo_produto',
                                                'descricao',
                                                DB::raw('(CASE WHEN block = 1 THEN "BLOQUEADO" ELSE "LIBERADO" END) AS status'),
                                                'valor_produto',
                                                'valor_cartao',
                                                'valor_dinheiro'
                                            )->where("block",1)->get();

            if(!empty($products)) {
                return \response()->json($products);
            }  else {
                return \response()->json(array('data'=>''));
            }

        }catch (\Exception $e) {
            return Response::json(array('success' => false, 'message' => 'ProductBlockController' . $e ), 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(): \Illuminate\Http\Response
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): \Illuminate\Http\Response
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
    public function destroy(int $id): \Illuminate\Http\Response
    {
      //

    }
}
