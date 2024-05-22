<?php

namespace App\Http\Controllers;

use App\Http\Models\ListaDeCompras;
use App\Http\Models\Produto;
use App\Http\Models\ProdutoVariation;
use App\Http\Models\Reposicao;
use App\Http\Models\VendasProdutos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Throwable;

class ReposicaoController extends Controller
{
    protected $request,$produto,$listaCompra;

    public function __construct(Request $request, Produto $produto, ListaDeCompras $listaCompra){
        $this->request = $request;
        $this->produto = $produto;
        $this->listaCompra = $listaCompra;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|\Illuminate\View\View
     */
    public function index()
    {

        return view('admin.resposicao_trimestre');
    }

    public function store(){
      //  return $this->request->input();
        try {

             // Verifica se o produto já existe no banco de dados
            $existingProduct = $this->listaCompra::where('produto_new_id',  $this->request->input("produto_new_id"))
                                                    ->where('produto_variacao_id',  $this->request->input("produto_variacao_id"))->first();

            // Se o produto já existir, retorna uma mensagem de aviso
            if ($existingProduct) {
                return Response::json(array('success' => false, 'message'=> 'Produto já cadastrado em lista de compras!', "data" => null ), 200);
            }

             // Se o produto não existir, cria e salva o novo produto
            $data['produto_new_id'] = $this->request->input("produto_new_id");
            $data['produto_variacao_id'] = $this->request->input("produto_variacao_id");

            $data = $this->listaCompra::create($data);

            if( $data){
                return Response::json(array('success' => true, 'message'=> 'Produto cadastrado com sucesso em lista de compras', "data" => $data ), 200);
            }else{
                return Response::json(array('success' => false, 'message'=> 'Ocorreu um erro interno!', "data" => null ), 400);
            }

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(int $id)  {

        try {

            $informacoes = DB::table('loja_vendas_produtos as vp')
                ->leftJoin('loja_produtos_variacao as va', 'vp.codigo_produto', '=', 'va.subcodigo')
                ->leftJoin('loja_produtos_imagens as pi', 'va.id', '=', 'pi.produto_variacao_id')
                ->Join('loja_produtos_new as pn', 'va.products_id', '=', 'pn.id')
                    ->select(
                        'va.id as variacao_id',
                        'pi.path as imagem',
                        'vp.codigo_produto as subcodigo',
                        DB::raw('CONCAT(pn.descricao, " - ", va.variacao) AS variacao'),
                        'va.valor_produto as valor_pago',
                        'va.estoque',
                        DB::raw('CAST(SUM(IF(vp.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY), vp.quantidade, 0)) AS UNSIGNED) AS qtd_total_venda_30d'),
                        DB::raw('CAST(SUM(IF(vp.created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY), vp.quantidade, 0)) AS UNSIGNED) AS qtd_total_venda_60d'),
                        DB::raw('CAST(SUM(IF(vp.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY), vp.quantidade, 0)) AS UNSIGNED) AS qtd_total_venda_90d'),
                        DB::raw('CAST(va.quantidade AS UNSIGNED) AS qtd'),
                        DB::raw('IF((va.status = 1), "ATIVO", "INATIVO") as status')
                )
                ->groupBy('vp.codigo_produto')
                ->where('va.status',true) //somente ativos
                ->where('va.products_id',$id)->get();

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
        return Response::json(array('success' => true, "data" => $informacoes), 200);
    }


    public function create()
    {
        try {

            //$ret =  $this->produto::with('products')
            $ret = $this->produto
            //->leftJoin('loja_fornecedores', 'loja_produtos_new.fornecedor_id', '=', 'loja_fornecedores.id')
            ->leftJoin('loja_categorias', 'loja_produtos_new.categoria_id', '=', 'loja_categorias.id')
            ->leftJoin('loja_produtos_variacao as va', 'loja_produtos_new.id', '=', 'va.products_id')
            ->leftJoin('loja_produtos_imagens as pi', 'loja_produtos_new.id', '=', 'pi.produto_id')
            ->select(
                'loja_produtos_new.id',
                'loja_produtos_new.codigo_produto',
                'pi.path as imagem',
                'loja_produtos_new.descricao',
                'loja_categorias.nome as categoria',
                DB::raw('IF((loja_produtos_new.status = 1), \'ATIVO\', \'INATIVO\') as status'),
                DB::raw("DATE_FORMAT(loja_produtos_new.created_at, '%d/%m/%Y %H:%i:%s') as created"),
                DB::raw("DATE_FORMAT(loja_produtos_new.updated_at, '%d/%m/%Y %H:%i:%s') as updated")
            )
            //->where('block', 0)
            ->where('loja_produtos_new.status', 1) //somente ativos
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('loja_vendas_produtos')
                    ->whereRaw('va.subcodigo = loja_vendas_produtos.codigo_produto')
                    ->whereRaw('DATEDIFF(CURDATE(), loja_vendas_produtos.created_at) <= 90');
            })
            ->orderBy('loja_produtos_new.id', 'DESC')
            ->groupBy('loja_produtos_new.id')
            ->get();

            if(empty($ret)) {
                return Response()->json(array('data'=>''));
            }

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
        return Response()->json($ret);
    }
}
