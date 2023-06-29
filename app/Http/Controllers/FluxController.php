<?php

namespace App\Http\Controllers;

use App\Http\Models\FluxoModel;
use App\Http\Models\Vendas;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use NumberFormatter;
use Throwable;

class FluxController extends Controller
{

    protected $request,$flux, $sales,$formatter;

    public function __construct(Request $request, FluxoModel $flux, Vendas $sales){

        $this->request = $request;
        $this->flux = $flux;
        $this->sales = $sales;
        $this->formatter = new NumberFormatter('pt_BR',  NumberFormatter::CURRENCY);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
      //  dd( $fluxo = $this->fluxo::all());

        //$flux = $this->flux::all();
        $flux = $this->flux->orderBy('created_at', 'DESC')->get();

        return view('admin.flux', compact('flux'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
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
     // dd($id);
        try {

            foreach ($this->flux::all()->where("loja_id",$id) as $value){

                //Busaca o total do DIA
                $sales = $this->sales::
                leftjoin('loja_vendas_produtos_tipo_pagamentos as tp', 'tp.venda_id', '=', 'loja_vendas.id')
                    ->leftjoin('loja_forma_pagamentos as fp', 'tp.forma_pagamento_id', '=', 'fp.id')
                    ->leftjoin('loja_taxa_cartoes as lc', 'lc.forma_id', '=', 'fp.id')
                    ->select(
                        "fp.nome as name",
                        "fp.id as id_payment",
                        // (DB::raw("FORMAT(SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)),2) AS orderTotal")))
                        (DB::raw("SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)) AS orderTotal")))
                    ->where('loja_vendas.loja_id', $id)
                    ->where('fp.id', 1)
                    //->whereDate('loja_vendas.created_at', Carbon::now()->subDay('1'))
                    // ->whereDate('loja_vendas.created_at', Carbon::today())
                    ->whereDate('loja_vendas.created_at', $value->created_at)
                    ->groupBy('fp.id')->first();

                if ($sales) {
                  //  $retorno['orderTotal'] = $vendas->orderTotal
                    $data['total_caixa'] =  $this->formatter->formatCurrency($sales->orderTotal,'BRL');
                } else {
                    //$retorno['orderTotal'] = 0;
                    $data['total_caixa'] =  $this->formatter->formatCurrency(0,'BRL');
                }
                //Fim totaL DIA

                //Monta o retorno para a tabela de fluxo caixa
                $data['id'] =  $value->id;
                $data['valor_caixa'] =  $this->formatter->formatCurrency($value->valor_caixa, 'BRL') ;
                $data['valor_sangria'] =  $this->formatter->formatCurrency($value->valor_sangria, 'BRL') ;
                $data['descricao'] =  $value->descricao;
                $data['loja_id'] =  $value->loja_id == 1 ? "FEIRA" : "BARÃO";
                $data['confirme'] =  $value->confirme;

                $data['created_at'] =  date('d/m/Y H:i:s', strtotime($value->created_at));
                $data['updated_at'] =  date('d/m/Y H:i:s', strtotime($value->updated_at));

               $dat[] = $data;
            }

            if(!empty($dat))
                return Response()->json($dat);

        } catch (Throwable $e) {
            return Response::json(['error' => $e], 500);
        }
        return Response()->json(array('data'=>''));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param FluxoModel $flux
     * @return void
     */
    public function edit(FluxoModel $flux)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $int
     * @return JsonResponse
     */
    public function update(int $int)
    {
        try {
           // dd($int);

            $flux =  $this->flux::find($int);
            $flux->confirme = true;

            $flux->save();

            return Response::json(array('success' => true, 'message' => 'Fluxo atualizada com sucesso!'), 200);

        } catch (Throwable $e) {
            //return Response::json(['error' => $e], 500);
            return Response::json(array('success' => false, 'message' => $e->getMessage()), 500);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param FluxoModel $flux
     * @return void
     */
    public function destroy(FluxoModel $flux)
    {
        //
    }
}
