<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Models\FluxoModel;
use App\Http\Models\Vendas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use NumberFormatter;

class FluxoController extends Controller
{

    protected  $request, $fluxoModel, $vendas;

    public function __construct(Request $request, FluxoModel $fluxoModel, Vendas $vendas){
        $this->request = $request;
        $this->fluxoModel = $fluxoModel;
        $this->vendas = $vendas;
        $this->formatter = new NumberFormatter('pt_BR',  NumberFormatter::CURRENCY);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = $this->request->header('store-id');

        $model = $this->fluxoModel::
            where('loja_id', $id)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'DESC')->get();

        if(!$model->isEmpty())
            return Response::json(array('success' => true, "dados" => $model), 200, [],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        else
            return Response::json(array('success' => false, "message" => "Nenhuma informação para o fluxo de caixa!"), 400, [],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $request = $this->request->all();

            /**
                VALIDA SE JÁ TEM A DATA CADASTRADA DO FLUXO, POIS FLUXO É DIÁRIO APENAS 1 POR DIA
             */
            $created_at = ['created_at' => date('Y-m-d', strtotime($request['created_at']))];

            $dateExists = $this->fluxoModel::whereDate('created_at', $created_at)->where('loja_id',$request['loja_id'])->first();

            //dd($dateExists);

            if($dateExists){
                return Response::json(array('success' => false, 'status' => 400,
                    'message' => 'Fluxo já cadastrado para essa data [ ' . date('d/m/Y', strtotime($request['created_at'])) .']'), 400);
            }else {

                /* $rules = ['created_at' => 'required|date_format:Y-m-d|after:now|unique:'.$this->fluxoModel->table];

                 $validator = Validator::make($created_at, $rules);
                 if ($validator->fails()) {
                     $errors = $validator->errors();

                     return Response::json(array('success' => false, 'status' => 400,
                         'message' => 'Fluxo já cadastrado para essa data [ ' . date('d/m/Y', strtotime($request['created_at'])) .']', 'errors' => $errors), 400);
                 }*/


                $this->fluxoModel = new FluxoModel();
                $this->fluxoModel->valor_caixa = $request['valor_caixa'];
                $this->fluxoModel->valor_sangria = $request['valor_sangria'];
                $this->fluxoModel->descricao = $request['descricao'];
                $this->fluxoModel->created_at = $request['created_at'];
                $this->fluxoModel->updated_at = $request['updated_at'];
                $this->fluxoModel->loja_id = $request['loja_id'];
                $this->fluxoModel->total_caixa = $request['total_caixa'];

                $return = $this->fluxoModel->save();

                if ($return) {
                    return Response::json(array('success' => true, "message" => 'Fluxo Caixa cadastrado com sucesso!'), 200, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                } else {
                    return Response::json(array('success' => false, "message" => 'Fluxo Caixa não cadastrado!'), 400, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }
            }
        } catch (\Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage(), 'code' => 500), 500);
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
        //$id = $this->request->header('store-id');
        try {
            $totalsDay = $this->vendas::
            leftjoin('loja_vendas_produtos_tipo_pagamentos as tp', 'tp.venda_id', '=', 'loja_vendas.id')
                ->leftjoin('loja_forma_pagamentos as fp', 'tp.forma_pagamento_id', '=', 'fp.id')
                ->leftjoin('loja_taxa_cartoes as lc', 'lc.forma_id', '=', 'fp.id')
                ->select(
                    "fp.created_at",
                    "fp.nome as name",
                    "fp.id as id_payment",
                    // (DB::raw("FORMAT(SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)),2) AS orderTotal")))
                    (DB::raw("SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)) AS orderTotal")))
                ->where('loja_vendas.loja_id', $id)
                ->where('fp.id', 1)
                 //->whereDate('loja_vendas.created_at', Carbon::now()->subDay('1'))
                ->whereDate('loja_vendas.created_at', Carbon::today())
                ->groupBy('fp.id')
                ->get();

            if (count($totalsDay) > 0) {
                $total = $this->formatter->formatCurrency($totalsDay[0]->orderTotal,'BRL');
            }else{
                $total = $this->formatter->formatCurrency(0,'BRL');
            }

            return Response::json(array('success' => true, "message"=> "ok","total_caixa" => number_format($totalsDay[0]['orderTotal'], 2)), 200, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        } catch (\Throwable $e) {
                return Response::json(array('success' => false, 'message' => $e->getMessage(), 'code' => 500), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update()
    {

        try{
            $request = $this->request->all();

            $this->fluxoModel = $this->fluxoModel::find($request['id']);
            $this->fluxoModel->valor_caixa = $request['valor_caixa'];
            $this->fluxoModel->valor_sangria = $request['valor_sangria'];
            $this->fluxoModel->descricao = $request['descricao'];
            //$this->fluxoModel->created_at = $request['created_at'];
            $this->fluxoModel->updated_at = $request['updated_at'];
            $this->fluxoModel->loja_id = $request['loja_id'];
            $this->fluxoModel->total_caixa = $request['total_caixa'];

            $return = $this->fluxoModel->save();

            if($return){
                return Response::json(array('success' => true, "message" => 'Fluxo caixa atualizado com sucesso!'), 200, [],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }else{
                return Response::json(array('success' => false, "message" => 'Fluxo caixa não atualizado!'), 400, [],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        } catch (\Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage(), 'code' => 500), 500);
        }
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
