<?php

namespace App\Http\Controllers;


use App\Http\Models\GastosFixo;
use App\Http\Models\Usuario;
use App\Http\Models\Vendas;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use NumberFormatter;
use Throwable;

class GastosFixoController extends Controller
{

    protected $request,$gastosFixo,$formatter,$vendas;


    public function __construct(Request $request, GastosFixo $gastosFixo, Vendas $vendas){
        $this->request = $request;
        $this->gastosFixo = $gastosFixo;
        $this->vendas = $vendas;
        $this->formatter = new NumberFormatter('pt_BR',  NumberFormatter::CURRENCY);
    }
    /**
     * Display a listing of the resource.
     *

     */
    public function index()
    {
        $user_data = Usuario::where("user_id",auth()->user()->id)->first();

        return view('admin.gastosfixo', compact('user_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        try {

            $gastos = $this->gastosFixo::all();

            foreach ($gastos as $value){
                $retorno['id'] =  $value->id;
                $retorno['nome'] =  $value->nome;
                $retorno['created_at'] =  date('d/m/Y H:i:s', strtotime($value->created_at));
                $retorno['updated_at'] =  date('d/m/Y H:i:s', strtotime($value->updated_at));

                $saida[] = $retorno;
            }

            if(!empty($saida)) {
                return \response()->json($saida);
            }  else {
                return \response()->json(array('data'=>''));
            }

        }catch (\Exception $e) {
            return Response::json(['error' => $e], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse

     */
    public function store()
    {
        try {
            // dd($this->request->all());
            $data = $this->request->all();

            $val['descricao'] = $data['descricao'];

            $valorRecebidoSemMilhar = str_replace('.', '', $data['valor']);
            $valorCorreto = str_replace(',', '.', str_replace('R$ ', '', $valorRecebidoSemMilhar));
            $val['valor'] =$valorCorreto;
            $val['tipo_gasto'] = $data['tipo_gasto'];

            $validator  = Validator::make($val, [
                //'descricao' => 'required|unique:'.$this->gastosFixo->table.'|max:255',
                'descricao' => 'required|max:255',
                'valor' => 'required|numeric',
                'tipo_gasto' => 'required|numeric'
            ],[
                'descricao.required' => 'Descrição do gasto é obrigatória!',
                // 'descricao.unique' => 'Descrição já registrada com esse nome!',
                'valor.required' => 'Valor do gasto é obrigatória!',
                'valor.unique' => 'Valor já registrada com esse nome!',
                'valor.numeric' => 'Valor não é numérico!',
                'tipo_gasto.required' => 'Tipo do gasto é obrigatório!',
                'tipo_gasto.numeric' => 'Tipo do Gasto não é numérico!'
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();

                return Response::json(array(
                    'success' => false,

                    'message' =>  $errors->toArray()
                ), 400);
            }

            //dd($val['descricao']);
            $this->gastosFixo->descricao = $val['descricao'];
            $this->gastosFixo->valor =  $val['valor'];
            $this->gastosFixo->tipo_gasto =  $val['tipo_gasto'];

            $this->gastosFixo::create($val);
            //$this->gastosFixo->save();

            return Response::json(array('success' => true, 'message' => 'Gasto cadastrado com sucesso!'), 201);

        } catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => 'Error!' . $e), 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id)
    {

        try {
            $dataCarbon = CarbonImmutable::parse(CarbonImmutable::now()->format("Y-m-d")); // use en_US as default locale
            //  dd($this->request->all());

            //Mês
            //if($request->dataini != "" and $request->datafim != ""){
            //    $dateini = Date("Y-m-01",strtotime(str_replace('/', '-',$request->dataini)));
            //    $datefim = Date("Y-m-t",strtotime(str_replace('/', '-',$request->datafim)));
            // }else{
            //  $dateini =  Date("Y-m-01");
            //  $datefim = Date("Y-m-t");
            // }

            //Total do Mês
            $totalMes =  $this->vendas::join('loja_lojas as ll', 'll.id', '=', 'loja_vendas.loja_id')
                ->join('loja_vendas_produtos_descontos','loja_vendas_produtos_descontos.venda_id','=' ,'loja_vendas.id')
                ->join('loja_vendas_produtos_tipo_pagamentos as tp','tp.venda_id','=' ,'loja_vendas.id')
                ->join('loja_forma_pagamentos as fp','tp.forma_pagamento_id','=' ,'fp.id')
                ->join('loja_taxa_cartoes as tc','tc.forma_id','=' ,'fp.id')
                ->select(
                    (DB::raw("SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)) AS total"))
                )->whereYear('loja_vendas.created_at', '=', $dataCarbon->year)
                ->whereMonth('loja_vendas.created_at', '=', $dataCarbon->month)
                ->where('ll.id', 2)
                ->groupBy((DB::raw('DATE_FORMAT(loja_vendas.created_at, "%Y-%m"),loja_id')))
                ->first();

            //Soma os gastos por id do tipo do gasto
            $sum = $this->gastosFixo::where("tipo_gasto",$id)
                ->whereYear('loja_gastos_fixos.created_at', '=', $dataCarbon->year)
                ->whereMonth('loja_gastos_fixos.created_at', '=', $dataCarbon->month)
                -> sum("valor");
            $sum =  'R$ '.number_format($sum, 2, ',', '.');

            //Pega todos os gastos por ID do gasto
            $spending = $this->gastosFixo::where("tipo_gasto",$id)
                ->whereYear('loja_gastos_fixos.created_at', '=', $dataCarbon->year)
                ->whereMonth('loja_gastos_fixos.created_at', '=', $dataCarbon->month)
                ->get();

            //Total Gastos
            $sumTotal = $this->gastosFixo::whereYear('loja_gastos_fixos.created_at', '=', $dataCarbon->year)
                ->whereMonth('loja_gastos_fixos.created_at', '=', $dataCarbon->month)
                ->sum("valor");
            $lucro = $totalMes->total - $sumTotal;


            //Formata Total Mes
            $totalMes = $this->formatter->formatCurrency($totalMes->total, 'BRL');
            return Response::json(
                array('data'=> $spending,
                    'totalGasto'=>$sum,
                    'totalMes'=>$totalMes,
                    'totalLucro'=>$this->formatter->formatCurrency($lucro, 'BRL'),
                    'lucro'=>$lucro));

        }catch (Throwable $e) {
            return Response::json(['error' => $e], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param GastosFixo $gastosFixo
     * @return void
     */
    public function edit(GastosFixo $gastosFixo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param GastosFixo $gastosFixo
     * @return JsonResponse
     */
    public function update(Request $request, GastosFixo $gastosFixo)
    {
        // dd($this->request->all());
        try {

            $data = $this->request->all();

            $val['descricao'] = $data['descricao'];

            $valorRecebidoSemMilhar = str_replace('.', '', $data['valor']);
            $valorCorreto = str_replace(',', '.', str_replace('R$ ', '', $valorRecebidoSemMilhar));
            $val['valor'] =$valorCorreto;

            $validator = Validator::make($val, [
                'descricao' => 'required|max:255|unique:' . $this->gastosFixo->table . ',descricao,' . $this->request->id,
                'valor' => 'required|numeric'
            ],
                [
                    'descricao.required' => 'Descrição do gasto é obrigatória!',
                    'descricao.unique' => 'Descrição já registrada com esse nome!',
                    'valor.required' => 'Valor do gasto é obrigatória!',
                    'valor.unique' => 'Valor já registrada com esse nome!',
                    'valor.numeric' => 'Valor não é numérico!'
                ]
            );

            if ($validator->fails()) {
                $errors = $validator->errors();

                return Response::json(array(
                    'success' => false,
                    'message' =>  $errors->toArray()
                ), 400);
            }

            //OK
            $this->gastosFixo = $this->gastosFixo::find($this->request->id);

            $this->gastosFixo->descricao = $this->request->descricao;
            $this->gastosFixo->valor =  $val['valor'];

            $this->gastosFixo->save();

            return Response::json(array('success' => true, 'message' => 'Gasto atualizado com sucesso!'), 200);

        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                return Response::json(array('success' => false, 'message' => 'Gasto já cadastrado!!'), 400);
            }
        }catch (\Exception $e) {
            return Response::json(array('success' => false, 'message' => 'GatFixosController -> update()' . $e ), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        try{
            $gasto = $this->gastosFixo::find($id);

            if($gasto){
                $gasto->delete();
                return Response::json(array("success" => true, "message" => "Gasto Fixo deletado com sucesso!"),200);
            }else {
                return Response::json(array("success" => false, "message" => utf8_encode("Gasto Fixo não localizado para deleção com o id: [ {$id} ]")), 400);
            }
        }catch(\Illuminate\Database\QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1451') {
                return Response::json(array('success' => false, 'message' => 'Gasto Fixo não pode ser removido, ele está sendo usado no sistema!'), 400);
            }

        }catch (\Exception $e) {
            return Response::json(array('success' => false, 'message' => 'GastoFixoController -> delete()' . $e ), 500);
        }
    }

    public function getDataAttribute($value)
    {
        //return CarbonImmutable::parse($value)->format('Y-m-d');
        return Carbon::createFromFormat('d/m/Y',$value)->format('Y-m-d');
    }

    public function getFormGasto(Request $request){
//       // $dados["success"] = true;
        //$dados["message"] = $request->datefim;
        //echo json_encode($dados);

        try {
            $dataCarbon = CarbonImmutable::parse(CarbonImmutable::now()->format("Y-m-d")); // use en_US as default locale

            //Mês
            if($request->dateini == "" and $request->datefim == ""){
                $dateini =  Date("Y-m-01");
                $datefim = Date("Y-m-t");
            }else{
                // $dateini = Date("Y-m-01",strtotime(str_replace('/', '-',$request->dateini)));
                // $datefim = Date("Y-m-t",strtotime(str_replace('/', '-',$request->datefim)));
                $dateini = $this->getDataAttribute($request->dateini);
                $datefim = $this->getDataAttribute($request->datefim);
            }
            // dd($dateini . " " . $datefim);

            //Total do Mês
            $totalMes =  $this->vendas::join('loja_lojas as ll', 'll.id', '=', 'loja_vendas.loja_id')
                ->join('loja_vendas_produtos_descontos','loja_vendas_produtos_descontos.venda_id','=' ,'loja_vendas.id')
                ->join('loja_vendas_produtos_tipo_pagamentos as tp','tp.venda_id','=' ,'loja_vendas.id')
                ->join('loja_forma_pagamentos as fp','tp.forma_pagamento_id','=' ,'fp.id')
                ->join('loja_taxa_cartoes as tc','tc.forma_id','=' ,'fp.id')
                ->select(
                    (DB::raw("SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)) AS total"))
                )
                ->whereYear('loja_vendas.created_at', '=', $dataCarbon->year)
                ->whereMonth('loja_vendas.created_at', '=', $dataCarbon->month)
                ->where('ll.id', 2)
                //->whereBetween(DB::raw('DATE(loja_vendas.created_at)'), array($dateini, $datefim))
                ->groupBy((DB::raw('DATE_FORMAT(loja_vendas.created_at, "%Y-%m"),loja_id')))
                ->first();

            //Soma os gastos por id do tipo do gasto
            $sum = $this->gastosFixo::where("tipo_gasto",1)
                //   ->whereYear('loja_gastos_fixos.created_at', '=', $dataCarbon->year)
                //   ->whereMonth('loja_gastos_fixos.created_at', '=', $dataCarbon->month)
                ->whereBetween(DB::raw('DATE(loja_gastos_fixos.created_at)'), array($dateini, $datefim))
                -> sum("valor");
            $sum =  'R$ '.number_format($sum, 2, ',', '.');

            //Pega todos os gastos por ID do gasto
            $spending = $this->gastosFixo::where("tipo_gasto",1)
                // ->whereYear('loja_gastos_fixos.created_at', '=', $dataCarbon->year)
                // ->whereMonth('loja_gastos_fixos.created_at', '=', $dataCarbon->month)
                ->whereBetween(DB::raw('DATE(loja_gastos_fixos.created_at)'), array($dateini, $datefim))
                ->get();

            //Total Gastos
            $sumTotal = $this->gastosFixo::whereYear('loja_gastos_fixos.created_at', '=', $dataCarbon->year)
                ->whereMonth('loja_gastos_fixos.created_at', '=', $dataCarbon->month)
                ->sum("valor");
            $lucro = $totalMes->total - $sumTotal;


            //Formata Total Mes
            $totalMes = $this->formatter->formatCurrency($totalMes->total, 'BRL');
            return Response::json(
                array('data'=> $spending,
                    'totalGasto'=>$sum,
                    'totalMes'=>$totalMes,
                    'totalLucro'=>$this->formatter->formatCurrency($lucro, 'BRL'),
                    'lucro'=>$lucro));

        }catch (Throwable $e) {
            return Response::json(['error' => $e], 500);
        }
    }
}
