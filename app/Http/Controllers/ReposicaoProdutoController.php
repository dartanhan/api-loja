<?php

namespace App\Http\Controllers;

use App\Http\Models\Vendas;
use App\Http\Models\VendasProdutos;
use App\Http\Models\VendasProdutosDesconto;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\http\Request;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\DataTables;
use NumberFormatter;

class ReposicaoProdutoController extends Controller
{
    protected $request,$vendasProduto,$formatter;

    public function __construct(Request $request, VendasProdutos $vendasProduto){
        $this->request = $request;
        $this->vendasProduto = $vendasProduto;
        $this->formatter = new NumberFormatter('pt_BR',  NumberFormatter::CURRENCY);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::check() === true){
            return view('admin.resposicao');
        }
        return redirect()->route('admin.login');
    }

   

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        dd($this->request->all());

        // Defina o período desejado
        //$startDate = Carbon::createFromFormat('Y-m-d', '2024-07-01')->startOfDay();
        //$endDate = Carbon::createFromFormat('Y-m-d', '2024-07-02')->endOfDay();

        // Recebe os parâmetros de data
            $startDate = $this->request->input('startDate');
            $endDate = $this->request->input('endDate');
           
            // Verifica se as datas foram fornecidas
            if ($startDate && $endDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
                $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

               // $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
                //$endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
            } else {
                // Se as datas não forem fornecidas, define um período padrão
               // $startDate = Carbon::now()->subMonth()->startOfDay(); // Um mês atrás
                $startDate = Carbon::now()->subDays('5')->startOfDay();
                $endDate = Carbon::now()->endOfDay(); // Hoje
            }
dd($startDate,$endDate);
           $saida = [];
            // Consulta para obter vendas e produtos agrupados pelo código do produto no período especificado
            $listSales = DB::table('loja_vendas_produtos as lv')
            ->leftJoin('loja_produtos_variacao as v', 'lv.codigo_produto', '=', 'v.subcodigo')
            ->leftJoin('loja_produtos_imagens as i', 'v.id', '=', 'i.produto_variacao_id')
            
            ->select(
                'lv.descricao',
                'lv.codigo_produto',
                DB::raw("DATE_FORMAT(lv.created_at, '%d/%m/%Y') AS venda_data"),
                DB::raw('SUM(lv.quantidade) AS quantidade'),
                'i.path AS imagem'
                
            )
            ->whereBetween('lv.created_at', [$startDate, $endDate])
            ->groupBy('lv.codigo_produto', 'lv.descricao', 'i.path')
            ->orderBy('quantidade', 'DESC')
            ->orderBy('lv.descricao')
            ->get();

           

            /*$listSales = Vendas::with(['produtos'])->
            from('loja_vendas_produtos as lvp')
            ->whereDate('lvp.created_at', Carbon::now()->subDays('3'))
            ->groupBy('lvp.created_at','lvp.codigo_produto')
            ->get(); 

            return  DataTables::of($listSales)->make(true);

            $listSales = Vendas::with(['produtos.produtoVariation.images','descontos','cashback','formaPgto'])
            ->from('loja_vendas as lv')
                ->whereDate('lv.created_at', C)
                ->groupBy('lv.created_at')
            ->get(); 
           // return  DataTables::of($listSales)->make(true);

            foreach ($listSales as $sale) {
                //foreach ($sale->produtos as $produto) {//produto da venda
                    $path = $sale->produtos[0]->produtoVariation[0]->images[0]->path ?? null;

                    $data['imagem'] =   $path ? asset('storage/' .  $path) : null;
                    $data['descricao'] =  $sale->produtos[0]->descricao;
                    $data['quantidade'] =  $sale->produtos[0]->quantidade;
                    $data['valor_produto'] = $sale->produtos[0]->valor_produto;
                    $data['codigo_produto'] = $sale->produtos[0]->codigo_produto;
                    $data['valor_produto_compra'] = $this->formatter->formatCurrency($sale->produtos[0]->produtoVariation[0]->valor_produto, 'BRL');
                    $data['valor_total_produto'] = $this->formatter->formatCurrency($sale->produtos[0]->quantidade * $sale->produtos[0]->valor_produto, 'BRL');

                    //$data['valor_total_produtos'] = $sale->produtos[0]->quantidade * $sale->produtos[0]->produtoVariation[0]->valor_produto;
                //}
                $data['codigo_venda'] =  $sale->codigo_venda;
            
                
                //se igual a forma de pagamento DINHEIRO não calcula imposto
                $imposto = $sale->formaPgto[0]->forma_pagamento_id !== 1 ? ($sale->valor_total * 4)/100 : 0;
                $data['imposto'] = $this->formatter->formatCurrency($imposto, 'BRL');

                $valor_desconto = $sale->descontos[0]->valor_desconto ?? 0;
                $data['valor_desconto'] = $this->formatter->formatCurrency($valor_desconto, 'BRL');

                $taxa_cartao = ($sale->valor_total * $sale->formaPgto[0]->taxa)/100;
                $data['valor_taxa_cartao'] = $this->formatter->formatCurrency($taxa_cartao, 'BRL');

                $cashback = $sale->cashback[0]->valor ?? 0;
                $data['valor_cashback'] = $this->formatter->formatCurrency($cashback, 'BRL');

                //$total_ficou =  $sale->valor_total - $imposto - $taxa_cartao;
                
                //MC = SUBTOTAL - DESCONTO -  CASHBACK - TAXA DE CARTÃO - IMPOSTO- VALOR DO PRODUTO
            // $mc = $valor_desconto - $cashback -  -  $sale->valor_total_produtos;
                //$mc = $total_ficou - $data['valor_total_produtos'];

                //$data['mc'] = $this->formatter->formatCurrency($mc, 'BRL');
               // if($data['valor_total_produtos'] > 0){
                //    $data['percentual_mc'] =  number_format($mc/$data['valor_total_produtos'] * 100, 2) . '%';
                //}
                

                $data['total'] =  $this->formatter->formatCurrency($sale->valor_total, 'BRL');
                $data['data'] = Carbon::parse($sale->created_at)->format('d/m/Y H:i:s');
            
                array_push($saida, $data);
            }*/
            
            return  DataTables::of($listSales)->make(true);
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    { 
        dd($this->request->all());
        // Recebe as datas do request
        $startDate = $this->request->input('startDate');
        $endDate = $this->request->input('endDate');

        if ($startDate && $endDate) {
            $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

           // $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            //$endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
        } else {
            // Se as datas não forem fornecidas, define um período padrão
           // $startDate = Carbon::now()->subMonth()->startOfDay(); // Um mês atrás
            $startDate = Carbon::now()->subDays('5')->startOfDay();
            $endDate = Carbon::now()->endOfDay(); // Hoje
        }

        // Verifique se as datas estão sendo recebidas corretamente
        if ($startDate && $endDate) {
            // Utilize as datas recebidas na sua consulta
            $listSales = DB::table('loja_vendas_produtos as lv')
            ->leftJoin('loja_produtos_variacao as v', 'lv.codigo_produto', '=', 'v.subcodigo')
            ->leftJoin('loja_produtos_imagens as i', 'v.id', '=', 'i.produto_variacao_id')
            
            ->select(
                'lv.descricao',
                'lv.codigo_produto',
                DB::raw("DATE_FORMAT(lv.created_at, '%d/%m/%Y') AS venda_data"),
                DB::raw('SUM(lv.quantidade) AS quantidade'),
                'i.path AS imagem'
                
            )
            ->whereBetween('lv.created_at', [$startDate, $endDate])
            ->groupBy('lv.codigo_produto', 'lv.descricao', 'i.path')
            ->orderBy('quantidade', 'DESC')
            ->orderBy('lv.descricao')
            ->get();


            return response()->json([
                'data' => $listSales
            ]);
        }

        // Caso as datas não sejam fornecidas, retorne um erro ou dados padrão
        return response()->json(['data' => []]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
