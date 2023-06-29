<?php

namespace App\Http\Controllers;


use App\Http\Models\Calendario;
use App\Http\Models\Payments;
use App\Http\Models\Lojas;
use App\Http\Models\Vendas;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use NumberFormatter;

class CalendarioController extends Controller
{

    protected $request,$calendar,$database,$vendas, $lojas, $payments;

    public function __construct(Request $request, Calendario $calendar, Vendas $vendas, Lojas $lojas, Payments $payments){

        $this->request = $request;
        $this->calendar = $calendar;
        $this->vendas = $vendas;
        $this->lojas = $lojas;
        $this->payments = $payments;
        $this->formatter = $formatter = new NumberFormatter('pt_BR',  NumberFormatter::CURRENCY);
        //$this->database = $this->request->header('database');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('admin.calendario');
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.

     */
    public function show()
    {
       //Todas a vendas realizadas
        $totalsDay = $this->vendas::
        leftjoin('loja_vendas_produtos_tipo_pagamentos as tp', 'tp.venda_id', '=', 'loja_vendas.id')
            ->leftjoin('loja_forma_pagamentos as fp', 'tp.forma_pagamento_id', '=', 'fp.id')
            ->leftjoin('loja_taxa_cartoes as lc', 'lc.forma_id', '=', 'fp.id')
            ->select(
            (DB::raw('(CASE WHEN loja_vendas.loja_id = 1 THEN "Total Feira: " ELSE "Total Barão: " END) AS titulo')),
            (DB::raw('(CASE WHEN loja_vendas.loja_id = 1 THEN "#ff00ff" ELSE "#d468ff" END) AS color')),
            (DB::raw("DATE_FORMAT(loja_vendas.created_at, '%Y-%m-%d') as start")),

            //(DB::raw("SUM(valor_total) AS valor")))
            (DB::raw("SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)) AS valor")))
            //  ->where('loja_vendas.loja_id', $store->id)
            // ->whereDate('loja_vendas.created_at', Carbon::now()->subDay('4'))
            //->whereDate('loja_vendas.created_at', Carbon::today())
            ->groupBy('start','loja_vendas.loja_id')
            ->get();


        foreach ($totalsDay as $value){

            $saida['title'] = $value->titulo . $this->formatter->formatCurrency($value->valor, 'BRL') ;
            $saida['start'] = $value->start;
            $saida['color'] = $value->color;

            $retorno[] = $saida;
        }

        //Todos os anos
        $years = $this->vendas::select((DB::raw("DATE_FORMAT(loja_vendas.created_at, '%Y') as year")))->groupBy('year')->get();

        //Chama a função domingos, que retorna os domingos de cada mês de cada ano
        foreach ($years as $key => $year){
            $anos[$year->year] = $this->domingos($year->year);
        }

        $index = 0;
        foreach ($anos as $key => $ano){

            foreach ($ano as $keys => $values){

                $totalItens = count($values);
                for ($i=0; $i < $totalItens;$i++){
                    $saida['start'] = $values[$i];
                    $saida['title'] = "Total Semana: " . $this->totalSemana($values[$i]);
                    $saida['color'] = "#008080";
                    $retorno[] = $saida;
                }
              //  $saida['title'] = $value->titulo . $this->formatter->formatCurrency($value->valor, 'BRL') ;
                //$saida['start'] = $values[$index];
               // $saida['color'] = $value->color;

            }
        }


        return Response::json($retorno, 200,[],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }


    public function totalSemana($data){
        $totalsOrdersWeek = null;
        $en = CarbonImmutable::parse($data);
        $startDate = $en->startOfWeek();
        $endDate = $en->endOfWeek();
        //dd($en->startOfWeek() . $en->endOfWeek());

        $totalsDayWeek = $this->vendas::Join('loja_lojas as ll', 'll.id', '=', 'loja_vendas.loja_id')
                ->join('loja_vendas_produtos_tipo_pagamentos as tp', 'tp.venda_id', '=', 'loja_vendas.id')
                ->join('loja_forma_pagamentos as fp', 'tp.forma_pagamento_id', '=', 'fp.id')
                ->join('loja_taxa_cartoes as ltc', 'ltc.forma_id', '=', 'fp.id')
                ->select(
                    (DB::raw("DATE_FORMAT(loja_vendas.created_at, '%Y') as year")),
                    (DB::raw("SUM(tp.valor_pgto - (tp.valor_pgto * tp.taxa/100)) AS orderTotalWeek")))
                ->whereBetween(DB::raw('DATE(loja_vendas.created_at)'), array($startDate, $endDate))
                ->whereIn('ll.id', array(1,2))
                ->groupBy('ll.id')
                ->get();

        //total semana
       // if(count($totalsDayWeek) > 0) {
         /*   foreach ($totalsDayWeek as $keys => $item) {
                $sumWeek = 0;
                foreach ($item as $key => $tot) {
                    dd($tot);
                    $sumWeek = $tot->orderTotalWeek;
                }

                $totalsOrdersWeek[$keys] = array("totalWeek" => $this->formatter->formatCurrency($sumWeek, 'BRL'));
            }
        }*/
       return count($totalsDayWeek) > 0 ? $this->formatter->formatCurrency($totalsDayWeek[0]->orderTotalWeek, 'BRL') : "R$ 0,00";
    }

    public function domingos($year){
        $week_day = 1; // The desired week day (sunday: 1, monday:2 .... saturday: 0)
        //$year = $year; // Year, 4 digits

        $rs = array();
        $month = 1;
        while ($month <= 12) {
            $day = 1;
            $date = new \DateTime($year . '-' . $month . '-' . $day);
            $day_last = $date->format('t');
            $i = 1;
            while ($day <= $day_last) {
                $date->add(new \DateInterval('P' . $i . 'D'));
                //$date->add(date_interval_create_from_date_string($i.' days'));
                $day_week = $date->format('w');
                if ($day_week == $week_day) {
                    $rs[$month][] = $year.'-'.$this->formata($month).'-'.$this->formata($day);
                    $i = 7;
                }
                $day += $i;
            }
            $month++;
        }
        return $rs;
    }

    function formata($valor){
        if($valor < 10)
            return "0".$valor;
        else
            return $valor;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Calendario $calendario
     * @return void
     */
    public function edit(Calendario $calendario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Calendario $calendario
     * @return void
     */
    public function update(Request $request, Calendario $calendario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Calendario $calendario
     * @return void
     */
    public function destroy(Calendario $calendario)
    {
        //
    }
}
