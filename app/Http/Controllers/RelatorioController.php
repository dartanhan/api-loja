<?php

namespace App\Http\Controllers;

use App\Http\Models\Payments;
use App\Http\Models\Lojas;
use App\Http\Models\TaxaCartao;
use App\Http\Models\Vendas;
use App\Http\Models\VendasCashBack;
use App\Http\Models\VendasProdutosDesconto;
use App\Http\Models\VendasProdutosTipoPagamento;
use App\Traits\RelatorioTrait;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use NumberFormatter;
use Throwable;
use Yajra\DataTables\DataTables;

class RelatorioController extends Controller
{
    use RelatorioTrait;
    protected $request, $vendas, $payments, $lojas, $salePayments,$formatter, $taxaCartao,$vendasCashBack,$vendasProdutosDesconto;

    public function __construct(Request $request, Vendas $vendas,
                                Lojas $lojas, Payments $payments,
                                VendasProdutosTipoPagamento $salePayments,
                                TaxaCartao $taxaCartao, VendasCashBack $cashbackVendas,
                                VendasProdutosDesconto $vendasProdutosDesconto){
        $this->request = $request;
        $this->vendas = $vendas;
        $this->lojas = $lojas;
        $this->payments = $payments;
        $this->salePayments = $salePayments;
        $this->taxaCartao = $taxaCartao;
        $this->formatter = new NumberFormatter('pt_BR',  NumberFormatter::CURRENCY);
        $this->vendasCashBack = $cashbackVendas;
        $this->vendasProdutosDesconto = $vendasProdutosDesconto;

    }
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|\Illuminate\View\View
     */
    public function index()
    {

        return view('admin.relatorio');
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
     * @return JsonResponse
     */
    public function store()
    {
        //$dataCarbon = CarbonImmutable::now()->locale('en_US'); //default

        //Data Automatica
        $dataCarbon = CarbonImmutable::parse(CarbonImmutable::now()->format("Y-m-d")); // use en_US as default locale

        $inicioDiaSemana = $dataCarbon->startOfWeek()->format('Y-m-d');
        $fimDiaSemana = $dataCarbon->endOfWeek()->format('Y-m-d');

        //Mês
        if($this->request->dataini != "" and $this->request->datafim != ""){
            $dateini = Date("Y-m-01",strtotime(str_replace('/', '-',$this->request->dataini)));
            $datefim = Date("Y-m-t",strtotime(str_replace('/', '-',$this->request->datafim)));
        }else{
            $dateini =  Date("Y-m-01");
            $datefim = Date("Y-m-t");
        }

        DB::statement("SET lc_time_names = 'pt_BR'");
        $sales_mes =  $this->vendas::
        //Join('loja_vendas_produtos','loja_vendas_produtos.venda_id','=' ,'loja_vendas.id')
            join('loja_vendas_produtos_descontos','loja_vendas_produtos_descontos.venda_id','=' ,'loja_vendas.id')
            ->join('loja_vendas_produtos_tipo_pagamentos','loja_vendas_produtos_tipo_pagamentos.venda_id','=' ,'loja_vendas.id')
            ->join('loja_forma_pagamentos','loja_vendas_produtos_tipo_pagamentos.forma_pagamento_id','=' ,'loja_forma_pagamentos.id')
            ->join('loja_taxa_cartoes','loja_taxa_cartoes.forma_id','=' ,'loja_forma_pagamentos.id')
            ->select(
              //  DB::raw("Concat('R$ ', format( ((sum( valor_produto) * quantidade)-valor_desconto)-((((sum( valor_produto) * quantidade)-valor_desconto) * valor_taxa)/100),2,'de_DE')) as total")
                DB::raw("Concat('R$ ', format( sum( valor_total)-(valor_total * valor_taxa/100),2,'de_DE')) as total")
                //format('sum( valor_produto - valor_desconto) * quantidade,2,'de_DE')) as total'
            )->whereBetween(DB::raw('DATE(loja_vendas.created_at)'), array($dateini, $datefim))
            ->where('loja_id', $this->request->loja_id)
        ->groupBy(DB::raw("DATE_FORMAT(loja_vendas.created_at, '%Y-%m')"))
        ->first();

        //se informado a data pega e converte ,senão pega data do dia
        $dateini = Date("Y-m-d",strtotime(str_replace('/', '-',$this->request->dataini != "" ? $this->request->dataini : Date("Y-m-d"))));
        $datefim = Date("Y-m-d",strtotime(str_replace('/', '-',$this->request->datafim != "" ? $this->request->datafim : Date("Y-m-d"))));

       // dd($dateini . " - " . $datefim);
        $sales_dia =  $this->vendas::Join('loja_vendas_produtos','loja_vendas_produtos.venda_id','=' ,'loja_vendas.id')
            ->join('loja_vendas_produtos_descontos','loja_vendas_produtos_descontos.venda_id','=' ,'loja_vendas.id')
            ->join('loja_vendas_produtos_tipo_pagamentos','loja_vendas_produtos_tipo_pagamentos.venda_id','=' ,'loja_vendas.id')
            ->join('loja_forma_pagamentos','loja_vendas_produtos_tipo_pagamentos.forma_pagamento_id','=' ,'loja_forma_pagamentos.id')
            ->join('loja_taxa_cartoes','loja_taxa_cartoes.forma_id','=' ,'loja_forma_pagamentos.id')
            ->select(
                DB::raw("Concat('R$ ', format( ((sum( valor_produto) * quantidade)-valor_desconto)-((((sum( valor_produto) * quantidade)-valor_desconto) * valor_taxa)/100),2,'de_DE')) as total")
            //format('sum( valor_produto - valor_desconto) * quantidade,2,'de_DE')) as total'
            )->whereBetween(DB::raw('DATE(loja_vendas.created_at)'), array($dateini, $datefim))
            ->where('loja_id', $this->request->loja_id)
            ->groupBy(DB::raw("DATE_FORMAT(loja_vendas.created_at, '%Y-%m-%d')"))
            ->first();

        //echo $inicioDiaSemana . " - " . $fimDiaSemana;
        $sales_semana =  $this->vendas::
            //Join('loja_vendas_produtos','loja_vendas_produtos.venda_id','=' ,'loja_vendas.id')
            join('loja_vendas_produtos_descontos','loja_vendas_produtos_descontos.venda_id','=' ,'loja_vendas.id')
            ->join('loja_vendas_produtos_tipo_pagamentos','loja_vendas_produtos_tipo_pagamentos.venda_id','=' ,'loja_vendas.id')
            ->join('loja_forma_pagamentos','loja_vendas_produtos_tipo_pagamentos.forma_pagamento_id','=' ,'loja_forma_pagamentos.id')
            ->join('loja_taxa_cartoes','loja_taxa_cartoes.forma_id','=' ,'loja_forma_pagamentos.id')
            ->select('loja_vendas.created_at as data',
              //  DB::raw("Concat('R$ ', format( ((sum( valor_produto) * quantidade)-valor_desconto)-((((sum( valor_produto) * quantidade)-valor_desconto) * valor_taxa)/100),2,'de_DE')) as total")
                DB::raw("Concat('R$ ', format( sum( valor_total)-(valor_total * valor_taxa/100),2,'de_DE')) as total")
            )->whereBetween(DB::raw('DATE(loja_vendas.created_at)'), array($inicioDiaSemana, $fimDiaSemana))
            ->where('loja_id', $this->request->loja_id)
            ->groupBy(DB::raw("DATE_FORMAT(loja_vendas.created_at, '%Y-%m')"))
            ->first();



        return Response::json(array("mes" => $sales_mes, "semana" => $sales_semana , "dia" => $sales_dia));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
       // dd($request->all());
        $sales =  $this->vendas::Join('loja_vendas_produtos','loja_vendas_produtos.venda_id','=' ,'loja_vendas.id')
            ->join('loja_vendas_produtos_descontos','loja_vendas_produtos_descontos.venda_id','=' ,'loja_vendas.id')
            ->join('loja_vendas_produtos_tipo_pagamentos','loja_vendas_produtos_tipo_pagamentos.venda_id','=' ,'loja_vendas.id')
            ->select(
                'loja_vendas.id as venda_id',
                'codigo_venda',
                'valor_percentual',
                DB::raw('sum( valor_produto ) * quantidade as sub_total'),
                DB::raw('valor_total as total'),
                (DB::raw("(SELECT u1.nome FROM loja_forma_pagamentos u1 WHERE u1.id = forma_pagamento_id) AS tipo_pgto")),
                (DB::raw('DATE_FORMAT(created_at, "%d/%m/%Y") as data')),
                (DB::raw('DATE_FORMAT(created_at, "%H:%i:%s") as hora'))
            )
            ->where('loja_id', $id)
            ->whereDate('created_at', Carbon::today())
            ->groupBy('loja_vendas.codigo_venda')
            ->orderBy('data', 'DESC')
            ->get();

        return Response::json($sales);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function edit(int $id)
    {
        dd('edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        try {
            //return $request->all();
            $payments = intval($request->input('payments'));
            $payments_sale = intval($request->input('payments_sale'));
            $new_tax = $request->input('new_taxa');


            //return ['id' => $payments_sale,'forma_pagamento_id' => $payments,'taxa' => $new_tax];
            $this->salePayments->where(['id' => $payments_sale])->update(['forma_pagamento_id' => $payments,'taxa' => $new_tax]);

        }catch (Throwable $e){
            return Response::json(array('success' => false, 'message' => $e->getMessage()), 500);
        }
        return Response::json(array('success' => true, 'message' => 'Venda alterada com sucesso!'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param $dateOne
     * @param $dateTwo
     * @param $store_id
     * @return JsonResponse
     */
    public function chartDay($dateOne,$dateTwo,$store_id)
    {

        //todas as lojas
        //$stores = $this->lojas::where("status" , true)->get();

        //Tipos de pagamentos
        //$paymentType = [2,3,4,5,6,7];
        $payments = $this->payments::all();
        $paymentType = [];
        foreach ($payments as $pay) {
            if(!in_array($pay->id, [1])) //diferentes desse(s) ids de pagamentos [1 = dinheiro]
                $paymentType[] = $pay->id;
        }

       $dateOne = CarbonImmutable::parse($dateOne);
       $dateTwo = CarbonImmutable::parse($dateTwo);

        $iniDayWeek = $dateOne->startOfWeek()->format('Y-m-d');
        $endDayWeek = $dateOne->endOfWeek()->format('Y-m-d');
        //dd( [$iniDayWeek,  $endDayWeek]);

        $iniDayMonth = $dateOne->startOfMonth();
        $endDayMonth = $dateTwo->endOfMonth();

        //$iniDayMonth = Carbon::now()->startOfMonth();
        //$endDayMonth = Carbon::now()->endOfMonth();

        //foreach ($stores as $key => $store) {

            //semana agrupado por dia
            $chart = $this->vendas->Join('loja_lojas', 'loja_lojas.id', '=', 'loja_vendas.loja_id')
                ->leftjoin('loja_vendas_produtos_tipo_pagamentos as tp', 'tp.venda_id', '=', 'loja_vendas.id')
                ->select(
                    //(DB::raw("SUM(loja_vendas.valor_total) AS total")),
                    (DB::raw("SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)) AS total")),
                    (DB::raw('DATE_FORMAT(loja_vendas.created_at, "%d/%m/%Y") as data')),
                    "loja_lojas.nome as loja"
                )
                //->where('loja_vendas.loja_id', $store->id)
                //->whereDate('loja_vendas.created_at', Carbon::today())
                //->whereBetween(DB::raw('DATE(loja_vendas.created_at)'), array($iniDayWeek, $endDayWeek))
				//->whereBetween(DB::raw('DATE(loja_vendas.created_at)'), array($iniDayMonth, $endDayMonth))
                ->where('loja_vendas.loja_id', $store_id)
                ->whereBetween(DB::raw('DATE(loja_vendas.created_at)'), array($iniDayMonth, $endDayMonth))
                ->groupBy((DB::raw('DATE_FORMAT(loja_vendas.created_at, "%Y-%m-%d"),loja_id')))
                ->orderBy('loja_vendas.created_at', 'asc')
                ->get();


            //totais por dia
            $totalsDay = $this->totaisPorDia($store_id,$dateOne, $dateTwo);


            //totais descontos por dia
            $totalsDayDiscount = $this->vendas->join('loja_vendas_produtos_descontos', 'loja_vendas_produtos_descontos.venda_id', '=', 'loja_vendas.id')
                ->select(
                    (DB::raw("FORMAT(SUM(loja_vendas_produtos_descontos.valor_desconto),2) AS orderTotalDiscount"))
                )
                ->where('loja_vendas.loja_id', $store_id)
                ->whereBetween(DB::raw('DATE(loja_vendas.created_at)'), array($dateOne, $dateTwo))
                ->groupBy('loja_vendas.loja_id')
                ->get();

            //total no mês por loja
            //$totalMes[$store->id] = $this->vendas::Join('loja_lojas', 'loja_lojas.id', '=', 'loja_vendas.loja_id')
            $totalMes = $this->vendas->Join('loja_lojas', 'loja_lojas.id', '=', 'loja_vendas.loja_id')
                ->join('loja_vendas_produtos_tipo_pagamentos as tp', 'tp.venda_id', '=', 'loja_vendas.id')
                ->join('loja_forma_pagamentos as fp', 'tp.forma_pagamento_id', '=', 'fp.id')
                ->join('loja_taxa_cartoes', 'loja_taxa_cartoes.forma_id', '=', 'fp.id')
                ->select(
                    //(DB::raw("SUM(loja_vendas.valor_total - (loja_vendas.valor_total * loja_taxa_cartoes.valor_taxa/100)) AS totalMes")),
                    (DB::raw("SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)) AS totalMes")),
                    (DB::raw('DATE_FORMAT(loja_vendas.created_at, "%m/%Y") as data')),
                    "loja_lojas.nome as loja"
                )
                //->where('loja_vendas.loja_id', $store->id)
                ->where('loja_vendas.loja_id', $store_id)
                ->whereYear('loja_vendas.created_at', '=', $dateOne->year)
                ->whereMonth('loja_vendas.created_at', '=',$dateOne->month)
                ->groupBy((DB::raw('DATE_FORMAT(loja_vendas.created_at, "%Y-%m"),loja_id')))
                ->orderBy('loja_vendas.created_at', 'asc')
                ->get();

            //totais por semana
            //$totalsDayWeek[$store->id] = $this->vendas::Join('loja_lojas as ll', 'll.id', '=', 'loja_vendas.loja_id')
            $totalsDayWeek = $this->vendas->Join('loja_lojas as ll', 'll.id', '=', 'loja_vendas.loja_id')
                ->join('loja_vendas_produtos_tipo_pagamentos as tp', 'tp.venda_id', '=', 'loja_vendas.id')
                ->join('loja_forma_pagamentos as fp', 'tp.forma_pagamento_id', '=', 'fp.id')
                ->join('loja_taxa_cartoes as ltc', 'ltc.forma_id', '=', 'fp.id')
                ->select(
                    //(DB::raw("SUM(loja_vendas.valor_total - (loja_vendas.valor_total * ltc.valor_taxa/100)) AS orderTotalWeek")),
                    "ll.nome as loja",
                    "fp.nome as name",
                    "fp.id as id_payment",
                    (DB::raw("SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)) AS orderTotalWeek"))

                )
                //->whereBetween(DB::raw('DATE(loja_vendas.created_at)'), array($dataCarbon, $dataCarbonFim))
                //->where('loja_vendas.loja_id', $store->id)
                ->whereBetween(DB::raw('DATE(loja_vendas.created_at)'), array($iniDayWeek, $endDayWeek))
                ->where('loja_vendas.loja_id', $store_id)
                ->groupBy('ll.id')
                ->get();

        //}
     //  $totalsDayWeek['data'] = array($iniDayWeek, $endDayWeek);
        //dd($totalsDayWeek);
       // if(!empty($totalsDay)){
            //foreach ($totalsDay as $keys => $total) {
                $sumDinner = 0;
                $sumCart = 0;
                    //foreach ($total as $key => $tot){
                    foreach ($totalsDay as $key => $tot){
                        if ($tot->id_payment == 1) {
                            $sumDinner = $tot->orderTotal;
                        }

                        if (in_array($tot->id_payment, $paymentType)) {
                            $sumCart += $tot->orderTotal;
                        }
                    }
                //$totalOrders[$keys] = array(
                $totalOrders = array(
                                        "orderTotalDiner" => $this->formatter->formatCurrency($sumDinner, 'BRL'),
                                        "orderTotalCart" => $this->formatter->formatCurrency($sumCart, 'BRL'));

                //$sumOrdersDay[$keys] = array(
                $sumOrdersDay = array(
                    "orderTotalDay" => $this->formatter->formatCurrency($sumDinner + $sumCart, 'BRL'));
           // }
       // }
       // if(!empty($totalsDayDiscount)){
            //foreach ($totalsDayDiscount as $keys => $item) {
                $sumDisc = 0;
                //foreach ($item as $key => $tot){
                foreach ($totalsDayDiscount as $key => $tot){
                    $sumDisc = $tot->orderTotalDiscount;
                }
                //$totalOrderDiscount[$keys] = array("totalDiscount" => $this->formatter->formatCurrency($sumDisc, 'BRL'));
                $totalOrderDiscount = array("totalDiscount" => $this->formatter->formatCurrency($sumDisc, 'BRL'));
          // }
        //}

        //total por mês
        //if(!empty($totalMes)) {
           // foreach ($totalMes as $keys => $item) {
                $sum = 0;
               // foreach ($item as $key => $tot) {
                foreach ($totalMes as $key => $tot) {
                    $sum = $tot->totalMes;
                }
                //$totalOrderMonth[$keys] = array("totalMes" => $this->formatter->formatCurrency($sum, 'BRL'));
                $totalOrderMonth = array("totalMes" => $this->formatter->formatCurrency($sum, 'BRL'));
            //}
       // }

        //total semana
       // if(!empty($totalsDayWeek)) {
            //foreach ($totalsDayWeek as $keys => $item) {
                $sumWeek = 0;
                //foreach ($item as $key => $tot) {
                foreach ($totalsDayWeek as $key => $tot) {
                    $sumWeek = $tot->orderTotalWeek;
                }

                //$totalsOrdersWeek[$keys] = array("totalWeek" => $this->formatter->formatCurrency($sumWeek, 'BRL'));
                $totalsOrdersWeek = array("totalWeek" => $this->formatter->formatCurrency($sumWeek, 'BRL'));
           // }
       // }

        return Response::json(array("chart"=> $chart,
                                    "totals" => $totalsDay,
                                    "totalOrders" => $totalOrders ,
                                    "totalOrderDiscount" => $totalOrderDiscount,
                                    "totalOrderDay" => $sumOrdersDay,
                                    "totalsOrderWeek" => $totalsOrdersWeek,
                                    "totalOrderMonth" => $totalOrderMonth));

    }

    public function chartLineGroupYear($year){

        try {
            Carbon::setLocale('pt_BR');

            $values = $this->vendas::select(DB::raw("sum(valor_total) as total"))
                                    ->whereYear('created_at',$year)
                                    ->where('loja_id', 2)
                                    ->groupBy(DB::raw("Month(created_at)"))
                                    ->pluck('total');

            $months = $this->vendas::select(DB::raw("MONTHNAME(created_at) as month"))
                                    ->whereYear('created_at',$year)
                                    ->where('loja_id', 2)
                                    ->groupBy(DB::raw("Month(created_at)"))
                                    ->pluck('month');

            $m = array();
            foreach ($months as $index => $month){
                $m[] = ucfirst(strtolower(Carbon::parse($month)->translatedFormat('F')));
               //$datas[$month] = $values[$index];
            }
        }catch (Throwable $e){
            return Response::json(['error' => $e], 400);
        }

        return Response::json(array("values" => $values,"months" => $m ));
    }

    /***
     *
     * Linhas multiplas no gráfico
     *
     * */


    public function chartLineMultiGroupYear(){

        try {
            Carbon::setLocale('pt_BR');

            $years = $this->vendas::select(DB::raw('YEAR(created_at) year'), DB::raw("sum(valor_total) as total"))->groupby('year')->get();

            foreach ($years as $index => $year){

                $color = $this->dynamicColors($index,1);

                $datasets['label'] = $year->year;
                $datasets['lineTension'] = "0.3";
                $datasets["backgroundColor"] = $this->dynamicColors($index,0.2);//"rgba(2,117,216,0.2)";
                $datasets["borderColor"] =  $color;//"rgba(2,117,216,1)";
                $datasets["pointRadius"] = "5";
                $datasets["pointBackgroundColor"] = $color;
                $datasets["pointBorderColor"] = $color;
                $datasets["pointHoverRadius"] = "5";
                $datasets["pointHoverBackgroundColor"] = $color;
                $datasets["pointHitRadius"] = "50";
                $datasets["pointBorderWidth"] = "2";
                $datasets["data"] = $this->retornaValoresPorAno($year->year);

                $saida[] = $datasets;
            }

            $months = $this->vendas::select(DB::raw("MONTHNAME(created_at) as month"))
               // ->whereYear('created_at',$year)
                ->where('loja_id', 2)
                ->groupBy(DB::raw("Month(created_at)"))
                ->pluck('month');

            $m = array();
            foreach ($months as $index => $month){
                $m[] = ucfirst(strtolower(Carbon::parse($month)->translatedFormat('F')));
            }
        }catch (Throwable $e){
            return Response::json(['error' => $e->getMessage()], 400);
        }

        return Response::json(['data' =>  $saida, 'months' => $m ,'years' => $years]);
    }

    public function dynamicColors($index,$alpha) {
        // Definir um limite mínimo para os valores de cor
        $minColorValue = 100;

      $r = floor(rand(0,$index) * $minColorValue);
      $g = floor(rand(0,$index) * $minColorValue);
      $b = floor(rand(0,$index) * $minColorValue);

        return "rgba($r,$g,$b,$alpha)" ;
    }

    public function retornaValoresPorAno($ano){
        $totals = $this->vendas::select(DB::raw("sum(valor_total) as total")
        //DB::raw("DATE_FORMAT(created_at, '%m-%Y') new_date"),
        // DB::raw('YEAR(created_at) year'),
        // DB::raw('MONTH(created_at) month')
        )->whereYear('created_at',$ano)->groupby(DB::raw('YEAR(created_at) '),DB::raw('MONTH(created_at) '))->get();

        foreach ($totals as $index => $total){
            $values[] = intval($total->total);
        }
       return  $values;
    }

    /**
     * Retona as vendas diárias
     * @return JsonResponse
     */
    public function dailySalesList()
    {

        try {
            $data = [];
            $return = [];

            $startDate = CarbonImmutable::parse($this->request->input('startDate'));
            $endDate = CarbonImmutable::parse($this->request->input('endDate'));

            /**
             * Semana agrupado por dia
             */
            $listSales = $this->vendas->select(
                "loja_vendas.valor_total AS total",
                (DB::raw('DATE_FORMAT(loja_vendas.created_at, "%d/%m/%Y %H:%i:%s") as data')),
                "loja_lojas.nome as loja",
                "loja_vendas.codigo_venda",
                "loja_vendas.id as venda_id",
                "loja_vendas.usuario_id as usuario_id",
                "loja_usuarios.nome",
                "loja_vendas_produtos_descontos.valor_desconto",
                (DB::raw(("loja_vendas.valor_total + loja_vendas_produtos_descontos.valor_desconto as sub_total"))),
                "loja_forma_pagamentos.nome as nome_pgto",
                "loja_forma_pagamentos.id as id_pgto",
                "loja_tipo_vendas.descricao as tipo_venda"
            )->leftJoin('loja_lojas', 'loja_lojas.id', '=', 'loja_vendas.loja_id')
                ->leftJoin('loja_vendas_produtos_descontos', 'loja_vendas_produtos_descontos.venda_id', '=', 'loja_vendas.id')
                ->leftJoin('loja_vendas_produtos_tipo_pagamentos as tp', 'tp.venda_id', '=', 'loja_vendas.id')
                ->leftJoin('loja_forma_pagamentos', 'loja_forma_pagamentos.id', '=', 'tp.forma_pagamento_id')
                ->leftJoin('loja_usuarios', 'loja_usuarios.id', '=', 'loja_vendas.usuario_id')
                ->leftJoin('loja_tipo_vendas', 'loja_tipo_vendas.id', '=', 'loja_vendas.tipo_venda_id')

                ->where('loja_vendas.loja_id', $this->request->input('id'))
               //  ->whereDate('loja_vendas.created_at', Carbon::today())
               // ->whereDate('loja_vendas.created_at', Carbon::now()->subDay('1'))

                ->whereBetween(DB::raw('DATE(loja_vendas.created_at)'), array($startDate, $endDate))

                //->groupBy((DB::raw('DATE_FORMAT(loja_vendas.created_at, "%Y-%m-%d"),loja_id')))
                ->groupBy('loja_vendas.codigo_venda')
                ->orderBy('loja_vendas.created_at', 'asc')
                ->get();

            foreach ($listSales as $listSale) {

                $data['total'] =  $listSale->total - $this->cashback($listSale->venda_id);
                $data['data'] =  $listSale->data;
                $data['loja'] =  $listSale->loja;
                $data['codigo_venda'] =  $listSale->codigo_venda;
                $data['venda_id'] =  $listSale->venda_id;
                $data['valor_desconto'] =  $listSale->valor_desconto;
                $data['sub_total'] =  $listSale->sub_total;
                $data['nome_pgto'] =  $this->formName($listSale->venda_id);//$listSale->nome_pgto;
                $data['id_pgto'] =  $listSale->id_pgto;
                $data['usuario'] =  ($listSale->usuario_id == "") ? 'Karla' : $listSale->nome;
                $data['cashback'] = $this->cashback($listSale->venda_id);
                $data['tipo_venda'] = $listSale->tipo_venda;

                $return[] = $data;
            }

            return DataTables::of($return)->make(true);

        }catch (Throwable $e){
            return Response::json(['error' => $e], 400);
        }
    }

    /****
     * Pega o cashback da venda
     * @param int $venda_id
     * @return int
     */
    public function cashback(int $venda_id){
        $cashback = $this->vendasCashBack
            ->select(
                "valor AS total_cashback",
        )->where('venda_id', $venda_id)
        ->where('status', 1)
        ->first();

        if ($cashback) {
           return $cashback->total_cashback;
        } else {
            return 0;
        }
    }

    /****
     * Pega o desconto da venda
     * @param int $venda_id
     * @return int
     */
    public function valor_recebido(int $venda_id){
        $data = $this->vendasProdutosDesconto
            ->select(
                "valor_recebido",
        )->where('venda_id', $venda_id)
        ->first();

        if ($data) {
           return $data->valor_recebido;
        } else {
            return 0;
        }

    }

    /**
     * Retona o nome da forma de pagamento
     * @param $id
     * @return false|string
     */
    public function formName(int $id){

        //return "teste nome forma";
        $nomePayments = $this->payments::select('nome','valor_pgto','taxa')
            ->leftjoin('loja_vendas_produtos_tipo_pagamentos as tp','tp.forma_pagamento_id' , '=', 'loja_forma_pagamentos.id')
            ->where('venda_id',$id)->get();

        $saida = "";
        foreach ($nomePayments as $nomePayment) {
            if(strtoupper($nomePayment->nome) == "DINHEIRO"){
                $saida .= $nomePayment->nome.' ('.$this->formatter->formatCurrency($nomePayment->valor_pgto, 'BRL').' - RECEBIDO:  '. $this->formatter->formatCurrency($this->valor_recebido($id), 'BRL') .')';
            }else{
                $saida .= $nomePayment->nome.'('.$this->formatter->formatCurrency($nomePayment->valor_pgto, 'BRL').' - tx.'. $nomePayment->taxa .')';
            }

        }
        return substr($saida,0,-1);

    }

    /***
     * Detalhes da venda
     * @param $sales
     * @return JsonResponse
     */
    public function detailSales()
    {

        try {

         $codigo_venda =  $this->request->input('codigo_venda');
         $items = [];

        //semana agrupado por dia
        $sales = $this->vendas::with([
                                        'descontos',
                                        'produtos' => function ($query) {
                                            $query->where('troca', '!=', 1);
                                        },
                                        'produtos.productsSales'
                                    ])->where(
                                        'loja_vendas.codigo_venda', $codigo_venda)->first();


            // Decode JSON string into PHP array
            $jsonString  = json_decode($sales, true);

            $data["id"] = $jsonString['id'];
            $data["codigo_venda"] = $jsonString['codigo_venda'];
            $data["created_at"] = $jsonString['created_at'];


            foreach ($jsonString['produtos'] as $produto) {
                $data["codigo_produto"] = $produto['codigo_produto'];
                $data["descricao"] = $produto['descricao'];
                $data["quantidade"] = $produto['quantidade'];
                $data["valor_venda"] = $produto['valor_produto'];//valor venda
                $data["valor_total"] = number_format($produto['quantidade'] * $produto['valor_produto'], 2, '.', ',');

                // Accessing nested data (products_sales)
                foreach ($produto['products_sales'] as $product_sale) {
                    $data["valor_produto"] = $product_sale['valor_produto'];//valor real do produto
                }
                array_push($items, $data);
            }

        // Configurar retorno para DataTables
        return DataTables::of($items)->make(true);

        }catch (Throwable $e){
            return Response::json(['error' => $e], 500);
        }
    }

    /***
     * Detalhes das vendas no cartão
     * @return JsonResponse
     */
    public function detailCart()
    {
       // dd($this->request->data['id']);
        try{
            $id = $this->request->input("id");
            $startDate = Carbon::parse($this->request->input("startDate"));
            $endDate = Carbon::parse($this->request->input("endDate"));

            $listDetail = $this->vendas->join('loja_vendas_produtos_tipo_pagamentos as tp', 'tp.venda_id', '=', 'lv.id')
                ->join('loja_forma_pagamentos as fp', 'tp.forma_pagamento_id', '=', 'fp.id')
               // ->join('loja_taxa_cartoes as tx', 'tx.forma_id', '=', 'fp.id')
                ->select(
                   (DB::raw("SUM(tp.valor_pgto)  AS total")),
                    (DB::raw("SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)) AS totalFinal")),
                    'fp.nome',
                    'tp.taxa'
                )->from('loja_vendas as lv')
                ->where('lv.loja_id', $id)
                ->whereNotIn('fp.id', [1]) //sem dinheiro
                //->whereDate('loja_vendas.created_at', Carbon::today())
                //->whereDate('loja_vendas.created_at', Carbon::now()->subDay('4'))
                ->whereBetween(DB::raw('DATE(lv.created_at)'), array($startDate, $endDate))
                ->groupBy('fp.id')
                ->orderBy('fp.id', 'asc')
                ->get();

            return DataTables::of($listDetail)->make(true);
        }catch (Throwable $e){
            return Response::json(['error' => $e], 400);
        }
    }

    /***
     * Detalhes das vendas no dinheiro
     * @return JsonResponse
     */
    public function detailDinner()
    {
        try{
            $id = $this->request->input("id");
            $startDate = Carbon::parse($this->request->input("startDate"));
            $endDate = Carbon::parse($this->request->input("endDate"));

            $listDetail = $this->vendas
                ->join('loja_vendas_produtos_tipo_pagamentos as tp', 'tp.venda_id', '=', 'lv.id')
                ->join('loja_forma_pagamentos as fp', 'tp.forma_pagamento_id', '=', 'fp.id')
                ->join('loja_usuarios as u', 'lv.usuario_id', '=', 'u.id')
                ->select(
                   (DB::raw("SUM(tp.valor_pgto)  AS total")),
                    (DB::raw("SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)) AS totalFinal")),
                    'fp.nome',
                    'u.nome as nome_usu'
                )->from('loja_vendas as lv')
                ->where('lv.loja_id', $id)
                ->whereIn('fp.id', [1]) //dinheiro
                //->whereDate('loja_vendas.created_at', Carbon::now()->subDay('6'))
               // ->whereBetween('loja_vendas.created_at', [$startDate, $endDate])
                ->whereBetween(DB::raw('DATE(lv.created_at)'), array($startDate, $endDate))
                ->groupBy('u.nome')
                ->orderBy('u.nome', 'asc')
                ->get();

            return DataTables::of($listDetail)->make(true);

        }catch (Throwable $e){
            return Response::json(['error' => $e], 400);
        }
    }

    /***
     * Tabela com os totais nos meses
     * @param int $ano
     * @return JsonResponse
     */
    function mes(int $ano){

        DB::statement("SET lc_time_names = 'pt_BR'");
        $sales = $this->vendas::join('loja_vendas_produtos_tipo_pagamentos as tp', 'tp.venda_id', '=', 'loja_vendas.id')
            ->join('loja_forma_pagamentos as fp', 'tp.forma_pagamento_id', '=', 'fp.id')
            // ->join('loja_taxa_cartoes as tx', 'tx.forma_id', '=', 'fp.id')
            ->select(
                DB::raw('MONTHNAME(loja_vendas.created_at) As nome_mes'),
                (DB::raw('DATE_FORMAT(loja_vendas.created_at, "%m/%Y") as mes')),
                (DB::raw("SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)) AS totalFinal"))
            )
            ->where('loja_vendas.loja_id', 2)
            ->where(DB::raw('DATE_FORMAT(loja_vendas.created_at, "%Y")'),$ano)
            ->groupBy((DB::raw('DATE_FORMAT(loja_vendas.created_at, "%Y-%m")')))
            ->orderBy('loja_vendas.created_at', 'asc')
            ->get();

        return Response::json(array('success' => true,"dados" => $sales));
    }

    /***
     * Função para buscar a venda e permitir a alteração da forma de pagamento
     * @param $id
     * @return JsonResponse
     */
    function editSales(int $id){
        try{

            $data = $this->salePayments::with('PaymentsList')->where("venda_id",$id)->get();
            $payments = $this->payments::with('PaymentsTaxes')->where("status",true)->orderBy('nome','asc')->get();

            return Response::json(array('data' =>$data,'payments' =>$payments));


        }catch (Throwable $e) {
            return Response::json(['error' => $e], 500);
        }
    }



    /**
     * Tabela com os totais nos meses apenas cartão
     * @param int $ano
     * @return JsonResponse
     */
    function card(int $ano){

        DB::statement("SET lc_time_names = 'pt_BR'");
        $sales = $this->vendas::join('loja_vendas_produtos_tipo_pagamentos as tp', 'tp.venda_id', '=', 'loja_vendas.id')
            ->join('loja_forma_pagamentos as fp', 'tp.forma_pagamento_id', '=', 'fp.id')
            // ->join('loja_taxa_cartoes as tx', 'tx.forma_id', '=', 'fp.id')
            ->select(
                DB::raw('MONTHNAME(loja_vendas.created_at) As nome_mes'),
                (DB::raw('DATE_FORMAT(loja_vendas.created_at, "%m/%Y") as mes')),
                (DB::raw("SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)) AS totalFinal"))
            )
            ->where('loja_vendas.loja_id', 2)
            ->whereNotIn('fp.id', [1]) //sem dinheiro
            ->where(DB::raw('DATE_FORMAT(loja_vendas.created_at, "%Y")'),$ano)
            ->groupBy((DB::raw('DATE_FORMAT(loja_vendas.created_at, "%Y-%m")')))
            ->orderBy('loja_vendas.created_at', 'asc')->get();

        return Response::json(array("success" => true,"dados" => $sales));
    }

    function buscaTaxa(int $idPagamento){
        return $this->taxaCartao::select('valor_taxa')->where('forma_id', $idPagamento)->first()->valor_taxa;
    }

    /**
     * Gráfico de vendas dos funcionarios
     * @param int $ano
     * @return JsonResponse
     */
    function chartFunc(int $ano){
       // dd("ok");

        // Construir a consulta SQL dinâmica para agrupar por funcionário
        $query = DB::table('loja_vendas')
            ->join('loja_usuarios', 'loja_vendas.usuario_id', '=', 'loja_usuarios.id')
            ->select(
                'loja_usuarios.nome AS funcionario_nome', // Selecionar o nome do funcionário
                DB::raw('MONTH(loja_vendas.created_at) AS mes'), // Extrair o mês da data_venda
                DB::raw('SUM(loja_vendas.valor_total) AS total_vendas') // Calcular a soma das vendas para cada funcionário
            )
            ->whereYear('loja_vendas.created_at', $ano) // Filtrar pelo ano atual
            ->groupBy('funcionario_nome', 'mes'); // Agrupar por funcionário e mês

        // Executar a consulta e obter os resultados
        $salesData = $query->get();

        // Converter os resultados para array e retornar como resposta JSON
        return response()->json($salesData);

        //return Response::json(array("success" => true,"dados" => "ok"));
    }

}
