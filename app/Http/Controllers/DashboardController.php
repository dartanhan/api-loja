<?php

namespace App\Http\Controllers;

use App\Http\Models\Payments;
use App\Http\Models\Usuario;
use App\Http\Models\Vendas;
use App\Http\Models\VendasCashBack;
use App\Traits\RelatorioTrait;
use Illuminate\Http\Request;
use NumberFormatter;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Support\Facades\Response;
use function PHPUnit\Framework\isNull;

class DashboardController extends Controller
{
    use RelatorioTrait;
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
       // dd($this->request->all());
        try {
            $data = null;
            $return = [];
            $imposto_total = 0;
            $total_mc = 0;
            $total_precentual_mc =0;
            $startDate = CarbonImmutable::parse($this->request->input('dataIni'));
            $endDate = CarbonImmutable::parse($this->request->input('dataFim'));
            $store_id = $this->request->id;
            $orderTotal =0;


            /**
             * Semana agrupado por dia
             */
            $vendas = Vendas::with('formaPgto.PaymentsList','loja','descontos',
                'tipoVenda','produtos.produtoVariation','cliente','cashback.cliente','usuario.users','frete')
            ->from('loja_vendas as lv')
                ->where('lv.loja_id', $store_id)
               // ->where('lv.codigo_venda','KN378406')
                ->whereBetween(DB::raw('DATE(lv.created_at)'), array($startDate, $endDate))
               //->groupBy('lv.codigo_venda')
                ->orderBy('lv.created_at', 'asc')
                ->get();
//return Response::json($vendas, 400);

          /*  $listSales = $this->vendas->with('formaPgto')->select(
                "lv.valor_total AS total",
                DB::raw('DATE_FORMAT(lv.created_at, "%d/%m/%Y %H:%i:%s") as data'),
                "loja_lojas.nome as loja",
                "lv.codigo_venda",
                "lv.id as venda_id",
                "lv.usuario_id as usuario_id",
                "u.name as nome",
                "lvpd.valor_desconto",
                DB::raw("lv.valor_total + lvpd.valor_desconto as sub_total"),
                //"loja_forma_pagamentos.nome as nome_pgto",
               // "tp.taxa as taxa_pgto",
                "lv.loja_id as taxa_pgto",
               // "loja_forma_pagamentos.id as id_pgto",
                "loja_tipo_vendas.descricao as tipo_venda",
                "loja_vendas_produtos.quantidade as quantidade",
                "loja_produtos_variacao.valor_produto",
                DB::raw('SUM(CAST(loja_produtos_variacao.valor_produto AS DECIMAL(9, 2)) * loja_vendas_produtos.quantidade) as valor_total_produtos'),
               // DB::raw('(' . $valorTotalProdutos->getQuery()->toSql() . ') as valor_total_produtos'),
                "loja_clientes.nome as nome_cli"
            )->from('loja_vendas as lv')
              ->Join('loja_lojas', 'loja_lojas.id', '=', 'lv.loja_id')
              ->Join('loja_vendas_produtos_descontos as lvpd', 'lv.id' , '=','lvpd.venda_id')
             // ->Join('loja_vendas_produtos_tipo_pagamentos as tp', 'tp.venda_id', '=', 'lv.id')
             // ->leftJoin('loja_forma_pagamentos', 'loja_forma_pagamentos.id', '=', 'tp.forma_pagamento_id')
              ->leftJoin('loja_usuarios as lu', 'lv.usuario_id', '=', 'lu.id')
              ->leftJoin('users as u', 'lu.user_id', '=', 'u.id')
              ->leftJoin('loja_tipo_vendas', 'loja_tipo_vendas.id', '=', 'lv.tipo_venda_id')
              ->leftJoin('loja_vendas_produtos', 'loja_vendas_produtos.venda_id', '=', 'lv.id')
              ->leftJoin('loja_produtos_variacao', 'loja_produtos_variacao.subcodigo', '=', 'loja_vendas_produtos.codigo_produto')
              ->leftJoin('loja_clientes', 'loja_clientes.id', '=', 'lv.cliente_id')
              ->where('lv.loja_id', $store_id)
                ->where('lv.codigo_venda','KN378406')
              ->whereBetween(DB::raw('DATE(lv.created_at)'), array($dataIni, $dataFim))
                ->groupBy('lv.codigo_venda')
                ->orderBy('lv.created_at', 'asc')
                ->get();

            */

            if(count($vendas) > 0){

                foreach ($vendas as $venda) {
                    $forma_pgto_nome = [];
                    $forma_pgto_id =[];
                    $valor_total_produtos =0;
                    $taxa = 0;
                    $imposto =0;
                   // return Response::json($vendas, 400);
                    $cashback = !$venda['cashback'] ? $venda['cashback'][0]->valor : 0; //se tiver cashback pega o valor
                    $valor_desconto = $venda->descontos[0]->valor_desconto;

                    //total o valor subtraido de "desconto e cashback"
                    $total = $venda->valor_total - $valor_desconto - $cashback;
                    $data['total'] = $total;

                    $frete = 0;
                    if(count($venda->frete) > 0){
                        $frete = $venda->frete[0]->valor_entrega - $valor_desconto - $cashback;
                    }
                    $data['total_geral'] = $venda->valor_total + $frete;
                    $data['data'] =  Carbon::parse($venda->created_at)->format('d/m/Y H:i:s');
                    $data['loja'] =  $venda->loja->nome;
                    $data['codigo_venda'] =  $venda->codigo_venda;
                    $data['venda_id'] =  $venda->id;
                    $data['valor_desconto'] = $this->formatter->formatCurrency($valor_desconto, 'BRL');
                    //valor total que seria da venda sem descontos
                    $data['sub_total'] =  $this->formatter->formatCurrency($total, 'BRL');

                    $taxa_cliente = $venda->cliente !== null ? $venda->cliente->taxa : 0;
                    $taxa_cliente_final =0;
                    foreach ($venda->formaPgto as $forma) {
                        array_push($forma_pgto_id, $forma['id']);
                        foreach ($forma->paymentsList as $payment) {
                            array_push($forma_pgto_nome, $payment['nome']);

                            if( $forma->taxa > 0){
                                $taxa += ($total * $forma->taxa) / 100;
                                $taxa_cliente_final  += $taxa_cliente - ($taxa_cliente * $forma->taxa) / 100;
                            }

                            //se igual a forma de pagamento DINHEIRO não calcula imposto
                            if($payment->slug !== 'dinheiro'){
                                $imposto += ($total * 4)/100;
                            }

                        }
                    }
                    $data['nome_pgto'] =  $forma_pgto_nome; //$listSale->nome_pgto;
                    $data['taxa_pgto'] = $this->formatter->formatCurrency($taxa, 'BRL');
                    $data['moto_taxa'] = $this->formatter->formatCurrency($taxa_cliente_final, 'BRL');
                    $data['id_pgto'] =  $forma_pgto_id;
                    $data['usuario'] =  $venda->usuario->nome;
                    $data['cashback'] = $this->formatter->formatCurrency($cashback, 'BRL');
                    $data['tipo_venda'] = $venda->tipoVenda->descricao;

                    //se igual a forma de pagamento DINHEIRO não calcula imposto
                    // $imposto = $listSale->id_pgto !== 1 ? ($total * 4)/100 : 0;
                    $formattedImposto = 'R$ '.number_format(floor($imposto * 100) / 100, 2, ',', '');

                    $data['imposto'] = $formattedImposto;// $this->formatter->formatCurrency($imposto, 'BRL');
                    $imposto_total += $imposto;

                    //array_push($teste,$listSale->valor_total_produtos, $listSale->valor_produto,$listSale->quantidade, $listSale->codigo_venda, $listSale->valor_produto*$listSale->quantidade);

                    foreach ($venda->produtos as $produto) {
                        foreach ($produto->produtoVariation as $variation) {
                            $valor_total_produtos += $variation->valor_produto * $produto->quantidade;
                        }
                    }
                    $data['valor_produto'] = $this->formatter->formatCurrency($valor_total_produtos, 'BRL');

                    //total liquido da venda
                    $total_final = $total - $taxa - $imposto;
                    $orderTotal += $total_final; //total dia

                    //MC = SUBTOTAL - DESCONTO -  CASHBACK - TAXA DE CARTÃO - IMPOSTO- VALOR DO PRODUTO
                    $mc = $total_final - $valor_total_produtos;
                    $total_mc += $mc;
                    $data['mc'] = $this->formatter->formatCurrency($mc, 'BRL');

                    if($valor_total_produtos > 0){
                        //$data['percentual_mc'] =  number_format($mc/$listSale->valor_total_produtos * 100, 2) . '%';
                        $pmc =  $mc/$valor_total_produtos * 100;
                        $data['percentual_mc'] =  number_format($pmc, 2) . '%';
                        $total_precentual_mc += $pmc;
                    }

                    $data['nome_cli'] = !$venda->cliente ? "Cliente não Identificado" : $venda->cliente->nome;

                    $data['total_final'] = $this->formatter->formatCurrency($total_final, 'BRL');
                    array_push($return,$data);
                }
                //return Response::json($data, 400);
                /**
                 * Calcula o Total da Margem contribuição do DIA
                 */
                $orderTotal = ($total_mc / $orderTotal)*100;
            }

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
        return Response::json(array("data" => $return,
                                "total_imposto" => $this->formatter->formatCurrency($imposto_total, 'BRL'),
                                "total_mc" =>  $this->formatter->formatCurrency($total_mc , 'BRL'),
                                "total_precentual_mc" => number_format($orderTotal,2).'%' ));
    }

    /****
     * Pega o cashback da venda
     * @param int $venda_id
     * @return int
     */
//    public function cashback(int $venda_id)
//    {
//        $cashback = $this->cashbackVendas
//            ->select(
//                "valor AS total_cashback",
//            )->where('venda_id', $venda_id)
//           // ->where('status', 1)
//            ->first();
//
//        if ($cashback) {
//            return $cashback->total_cashback;
//        } else {
//            return 0;
//        }
//    }


    /**
     * Retona o nome da forma de pagamento
     * @param $id
     * @return false|string
     */
/*    public function formName(int $id)
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
    }*/


    /***
     * Retonar o total dos produtos no valor de compra do dia em cima das vendas do dia
     */
    /*public function totalProdutoVenda(){

        try{
            //  dd($this->request->input('dataOne'));
            $totalValorProduto = 0;
            $dataOneRequest = $this->request->input('dataOne');
            $dataTwoRequest = $this->request->input('dataTwo');
            $idLojaRequest = $this->request->input('idLoja');

            $dataOne = ($dataOneRequest)
                        ? CarbonImmutable::createFromFormat('d/m/Y', $dataOneRequest)->format('Y-m-d')
                        : CarbonImmutable::now()->format('Y-m-d');

            $dataTwo = ($dataTwoRequest)
                        ? CarbonImmutable::createFromFormat('d/m/Y', $dataTwoRequest)->format('Y-m-d')
                        : CarbonImmutable::now()->format("Y-m-d");

            $listSales = $this->vendas::with('VendasProdutos.produtoVariation')
                        ->from('loja_vendas as lv')
                        ->where('lv.loja_id', $idLojaRequest)
                        ->whereBetween(DB::raw('DATE(lv.created_at)'), array($dataOne, $dataTwo))
                            ->groupBy('lv.codigo_venda')
                            ->orderBy('lv.created_at', 'asc')
                            ->get();

            $json = Response::json(["data" => $listSales])->getContent();

            $data = json_decode($json, true);

            foreach ($data['data'] as $venda) {
                foreach ($venda['vendas_produtos'] as $produto) {
                    foreach ($produto['produto_variation'] as $variacao) {
                        $totalValorProduto += (float) $variacao['valor_produto'];
                    }
                }
            }
            return Response::json(["data" => $this->formatter->formatCurrency($totalValorProduto, 'BRL')]);

        }catch(Throwable $e){
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }*/

}
