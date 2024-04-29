<?php

namespace App\Http\Controllers;

use App\Http\Models\ListaDeCompras;
use App\Http\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Throwable;

class ListaDeComprasController extends Controller
{

    protected $request,$listaCompras,$produto;

    public function __construct(Request $request, ListaDeCompras  $listaCompras ,Produto $produto){
        $this->request = $request;
        $this->listaCompras =  $listaCompras;
        $this->produto = $produto;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.listadecompras');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $ret = $this->produto
                        ->Join('loja_lista_de_compras as lc', 'loja_produtos_new.id', '=', 'lc.produto_new_id')
                        ->leftJoin('loja_categorias', 'loja_produtos_new.categoria_id', '=', 'loja_categorias.id')
            
            ->select(
                'loja_produtos_new.id',
                'loja_produtos_new.codigo_produto',
                'loja_produtos_new.imagem',
                'loja_produtos_new.descricao',
                'loja_categorias.nome as categoria',
                DB::raw('IF((loja_produtos_new.status = 1), \'ATIVO\', \'INATIVO\') as status'),
                DB::raw("DATE_FORMAT(loja_produtos_new.created_at, '%d/%m/%Y %H:%i:%s') as created"),
                DB::raw("DATE_FORMAT(loja_produtos_new.updated_at, '%d/%m/%Y %H:%i:%s') as updated")
            )
            ->where('loja_produtos_new.status', 1) //somente ativos
            ->orderBy('loja_produtos_new.id', 'DESC')
            ->groupBy('loja_produtos_new.id')->get();

            if(!empty($ret)) {
                return Response()->json($ret);
            }  else {
                return Response()->json(array('data'=>''));
            }

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
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
     *
     * @param  \App\ListaDeCompras  $listaDeCompras
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
         try {

            // Buscar o produto com o ID fornecido
            $produtoNew = Produto::findOrFail($id);


             // Buscar todas as listas de compras
            // $listasDeCompras = ListaDeCompras::with('produtoNew', 'produtoVariacao')->where('produto_new_id',$id)->get();


            // Dentro do seu método onde você está buscando as listas de compras
            $listasDeCompras = ListaDeCompras::with('produtoNew', 'produtoVariacao')
            ->where('produto_new_id', $id)
            ->with(['produtoVariacao' => function ($query) {
                $query->select('id', 'subcodigo', 'variacao', 'quantidade', 'estoque', 'valor_produto')
                    ->selectRaw('(SELECT SUM(IF(vp.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY), vp.quantidade, 0)) FROM loja_vendas_produtos as vp WHERE vp.codigo_produto = loja_produtos_variacao.subcodigo) as qtd_total_venda_30d')
                    ->selectRaw('(SELECT SUM(IF(vp.created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY), vp.quantidade, 0)) FROM loja_vendas_produtos as vp WHERE vp.codigo_produto = loja_produtos_variacao.subcodigo) as qtd_total_venda_60d')
                    ->selectRaw('(SELECT SUM(IF(vp.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY), vp.quantidade, 0)) FROM loja_vendas_produtos as vp WHERE vp.codigo_produto = loja_produtos_variacao.subcodigo) as qtd_total_venda_90d')
                    ->selectRaw('(SELECT lpi.path FROM loja_produtos_imagens as lpi WHERE lpi.produto_variacao_id = loja_produtos_variacao.id) as imagem_path')
                    ->selectRaw('IF((loja_produtos_variacao.status = 1), "ATIVO", "INATIVO") as status');
            }])->get();

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
        return Response::json(array('success' => true, "data" => $listasDeCompras), 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ListaDeCompras  $listaDeCompras
     * @return \Illuminate\Http\Response
     */
    public function edit(ListaDeCompras $listaDeCompras)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ListaDeCompras  $listaDeCompras
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ListaDeCompras $listaDeCompras)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ListaDeCompras  $listaDeCompras
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $listaIds = ListaDeCompras::find($id)->delete();

        return Response::json(array("success" => true, "message" => "Produto removido com sucesso da Lista de Compras!"),200);
        
    }
}
