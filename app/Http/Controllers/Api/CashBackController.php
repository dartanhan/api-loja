<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Models\Cashback;
use App\Http\Models\ClienteModel;
use App\Http\Models\VendasCashBack;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Throwable;


class CashBackController extends Controller
{

    protected  $request, $vendasCashBackModel;

    public function __construct(Request $request, VendasCashBack $vendasCashBackModel){
        $this->request = $request;
        $this->vendasCashBackModel = $vendasCashBackModel;
    }

    /**
     * @OA\Get(
     *      tags={"CashBackAPI"},
     *     path="/api/auth/cashbackapi",
     *     @OA\Response(
     *         response="200",
     *         description="The data"
     *     )
     * )
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {

    }



    /**
     * @OA\Post(
     *      tags={"CashBackAPI"},
     *     path="/api/auth/cashbackapi",
     *     @OA\Response(
     *         response="200",
     *         description="The data"
     *     )
     * )

     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

    }

    /**
     * @OA\Get(
     *      tags={"CashBackAPI"},
     *     path="/api/auth/cashbackapi/{cashbackapi} ",
     *     @OA\Response(
     *         response="200",
     *         description="The data"
     *     )
     * )

     * Display the specified resource.
     *
     * @param $param
     * @return JsonResponse
     */
    public function show($param)
    {
        try{
            // $cashBackTotal = $this->vendasCashBackModel::where('cliente_id', $param)->where( 'status', 1)->sum('valor');
            $cashBackTotal = $this->vendasCashBackModel::where('cliente_id', $param)->sum('valor');

            $this->vendasCashBackModel = new VendasCashBack();

            $this->vendasCashBackModel->cliente_id = $param;
            $this->vendasCashBackModel->valor_total = $cashBackTotal;

            if ($this->vendasCashBackModel) {
                return Response::json($this->vendasCashBackModel, 200, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        }catch (Throwable $th){
            return Response::json(array("success" => false , 'message' => $th->getMessage()), 500, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

    }


    /**
     * @OA\Put(
     *      tags={"CashBackAPI"},
     *     path="/api/auth/cashbackapi/{cashbackapi}",
     *     @OA\Response(
     *         response="200",
     *         description="The data"
     *     )
     * )
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ClienteModel  $clienteModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClienteModel $clienteModel)
    {
        //
    }

 /**
     * @OA\Delete(
     *      tags={"CashBackAPI"},
     *     path="/api/auth/cashbackapi/{cashbackapi}",
     *     @OA\Response(
     *         response="200",
     *         description="The data"
     *     )
     * )
     * Remove the specified resource from storage.
     *
     * @param  ClienteModel  $clienteModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClienteModel $clienteModel)
    {
        //
    }
}
