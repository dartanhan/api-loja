<?php

namespace App\Http\Controllers;

use App\Http\Models\Payments;
use App\Http\Models\Usuario;
use App\Http\Models\Vendas;
use App\Http\Models\VendasCashBack;
use Illuminate\Http\Request;
use NumberFormatter;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{

    protected $request, $vendas, $formatter, $cashbackVendas,$payments;

    public function __construct(Request $request, Vendas $vendas, VendasCashBack $cashbackVendas, Payments $payments)
    {
        $this->request = $request;
        $this->vendas = $vendas;
        $this->cashbackVendas = $cashbackVendas;
        $this->payments = $payments;
        $this->formatter = new NumberFormatter('pt_BR',  NumberFormatter::CURRENCY);
    }


    public function index()
    {

        if(Auth::check() === true){
            $user_data = Usuario::where("user_id",auth()->user()->id)->first();

            $store_id = $user_data->loja_id;
            $isAdmin = $user_data->admin;

            if($isAdmin){
                return view('admin.dashbord-diario',compact("user_data"));
            }else{
               // return view('admin.pdv',compact("isAdmin"));
                return redirect()->route('admin.pdv');
            }

        }

        return redirect()->route('admin.login');       
    }

    /***
     * Retorna informações de vendas no DIA - Nova Tela
     */
    public function vendasDia()
    {
        try {
            $data = null;
            $return = [];

            $dataOne = ($this->request->dataOne != 0) ?
                CarbonImmutable::parse(
                    Carbon::createFromFormat('dmY', $this->request->dataOne)
                        ->format('Y-m-d')
                ) : CarbonImmutable::parse(CarbonImmutable::now()->format("Y-m-d"));
            $dataTwo = ($this->request->dataTwo != 0) ?
                CarbonImmutable::parse(Carbon::createFromFormat('dmY', $this->request->dataTwo)
                    ->format('Y-m-d')) : CarbonImmutable::parse(CarbonImmutable::now()->format("Y-m-d"));


            //  print_r( $this->request->all());
            /**
             * Semana agrupado por dia
             */
            $listSales = $this->vendas->select(
                "loja_vendas.valor_total AS total",
                DB::raw('DATE_FORMAT(loja_vendas.created_at, "%d/%m/%Y %H:%i:%s") as data'),
                "loja_lojas.nome as loja",
                "loja_vendas.codigo_venda",
                "loja_vendas.id as venda_id",
                "loja_vendas.usuario_id as usuario_id",
                "loja_usuarios.nome",
                "loja_vendas_produtos_descontos.valor_desconto",
                DB::raw("loja_vendas.valor_total + loja_vendas_produtos_descontos.valor_desconto as sub_total"),
                "loja_forma_pagamentos.nome as nome_pgto",
                "tp.taxa as taxa_pgto",
                "loja_forma_pagamentos.id as id_pgto",
                "loja_tipo_vendas.descricao as tipo_venda",
                DB::raw('SUM(loja_produtos_variacao.valor_produto * loja_vendas_produtos.quantidade) as valor_total_produtos'),
                "loja_clientes.nome as nome_cli"
            )->leftJoin('loja_lojas', 'loja_lojas.id', '=', 'loja_vendas.loja_id')
              ->leftJoin('loja_vendas_produtos_descontos', 'loja_vendas_produtos_descontos.venda_id', '=', 'loja_vendas.id')
              ->leftJoin('loja_vendas_produtos_tipo_pagamentos as tp', 'tp.venda_id', '=', 'loja_vendas.id')
              ->leftJoin('loja_forma_pagamentos', 'loja_forma_pagamentos.id', '=', 'tp.forma_pagamento_id')
              ->leftJoin('loja_usuarios', 'loja_usuarios.id', '=', 'loja_vendas.usuario_id')
              ->leftJoin('loja_tipo_vendas', 'loja_tipo_vendas.id', '=', 'loja_vendas.tipo_venda_id')
              ->leftJoin('loja_vendas_produtos', 'loja_vendas_produtos.venda_id', '=', 'loja_vendas.id')
              ->leftJoin('loja_produtos_variacao', 'loja_produtos_variacao.subcodigo', '=', 'loja_vendas_produtos.codigo_produto')
              ->leftJoin('loja_clientes', 'loja_clientes.id', '=', 'loja_vendas.cliente_id')
              ->where('loja_vendas.loja_id', $this->request->id)
            
            
                //  ->whereDate('loja_vendas.created_at', Carbon::today())
                // ->whereDate('loja_vendas.created_at', Carbon::now()->subDay('1'))

                ->whereBetween(DB::raw('DATE(loja_vendas.created_at)'), array($dataOne, $dataTwo))

                //->groupBy((DB::raw('DATE_FORMAT(loja_vendas.created_at, "%Y-%m-%d"),loja_id')))
                ->groupBy('loja_vendas.codigo_venda')
                ->orderBy('loja_vendas.created_at', 'asc')
                ->get();


            foreach ($listSales as $listSale) {

                $imposto = ($listSale->total * 4)/100;

                $data['total'] =  $listSale->total - $this->cashback($listSale->venda_id);
                $data['data'] =  $listSale->data;
                $data['loja'] =  $listSale->loja;
                $data['codigo_venda'] =  $listSale->codigo_venda;
                $data['venda_id'] =  $listSale->venda_id;
                $data['valor_desconto'] =  $listSale->valor_desconto;
                $data['sub_total'] =  $listSale->sub_total;
                $data['nome_pgto'] =  $this->formName($listSale->venda_id); //$listSale->nome_pgto;
                $data['taxa_pgto'] =  $this->formatter->formatCurrency($listSale->taxa_pgto, 'BRL');;
                $data['id_pgto'] =  $listSale->id_pgto;
                $data['usuario'] =  ($listSale->usuario_id == "") ? 'Karla' : $listSale->nome;
                $data['cashback'] = $this->cashback($listSale->venda_id);
                $data['tipo_venda'] = $listSale->tipo_venda;
                $data['imposto'] = $this->formatter->formatCurrency($imposto, 'BRL');
                $data['valor_produto'] = $this->formatter->formatCurrency($listSale->valor_total_produtos, 'BRL');
                //MC = SUBTOTAL - DESCONTO -  CASHBACK - TAXA DE CARTÃO - IMPOSTO- VALOR DO PRODUTO
                $mc = $listSale->sub_total - $listSale->valor_desconto -  $data['cashback'] - $listSale->taxa_pgto - $imposto - $listSale->valor_total_produtos;
                $data['mc'] = $this->formatter->formatCurrency($mc, 'BRL');
                $data['percentual_mc'] =  number_format($mc / $listSale->valor_total_produtos * 100, 2) . '%';
                $data['nome_cli'] = $listSale->nome_cli == "" ? "Cliente não Identificado" : $listSale->nome_cli;

                $return[] = $data;
            }
           // var_dump($return);
        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
        return Response::json(array("data" => $return));
    }

    /****
     * Pega o cashback da venda
     * 
     */
    public function cashback(int $venda_id)
    {
        $cashback = $this->cashbackVendas
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


    /**
     * Retona o nome da forma de pagamento
     * @param $id
     * @return false|string
     */
    public function formName(int $id)
    {

        //return "teste nome forma";
        $nomePayments = $this->payments::select('nome', 'valor_pgto', 'taxa')
            ->leftjoin('loja_vendas_produtos_tipo_pagamentos as tp', 'tp.forma_pagamento_id', '=', 'loja_forma_pagamentos.id')
            ->where('venda_id', $id)->first();

            return $nomePayments->nome;

        // $saida = "";
        // foreach ($nomePayments as $nomePayment) {
        //     if (strtoupper($nomePayment->nome) == "DINHEIRO") {
        //         $saida .= $nomePayment->nome . ' (' . $this->formatter->formatCurrency($nomePayment->valor_pgto, 'BRL') . ' - RECEBIDO:  ' . $this->formatter->formatCurrency($this->valor_recebido($id), 'BRL') . ')' . "<br/>";
        //     } else {
        //         $saida .= $nomePayment->nome . ' (' . $this->formatter->formatCurrency($nomePayment->valor_pgto, 'BRL') . ' - tx.' . $nomePayment->taxa . ')' . "<br/>";
        //     }
        // }
        // return substr($saida, 0, -1);
    }
}
