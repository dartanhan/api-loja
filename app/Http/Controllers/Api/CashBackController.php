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

class CashBackController extends Controller
{

    protected  $request, $vendasCashBackModel;

    public function __construct(Request $request, VendasCashBack $vendasCashBackModel){
        $this->request = $request;
        $this->vendasCashBackModel = $vendasCashBackModel;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {

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
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param $param
     * @return JsonResponse
     */
    public function show($param)
    {

        $cashBackTotal = $this->vendasCashBackModel::where('cliente_id', $param)->where( 'status', 0)->sum('valor');

        $this->vendasCashBackModel = new VendasCashBack();

        $this->vendasCashBackModel->cliente_id = $param;
        $this->vendasCashBackModel->valor_total = $cashBackTotal;

        if ($this->vendasCashBackModel) {
            return Response::json($this->vendasCashBackModel, 200, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\\ClienteModel  $clienteModel
     * @return JsonResponse
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\\ClienteModel  $clienteModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClienteModel $clienteModel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\\ClienteModel  $clienteModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClienteModel $clienteModel)
    {
        //
    }
}
