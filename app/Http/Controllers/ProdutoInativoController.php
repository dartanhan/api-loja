<?php

namespace App\Http\Controllers;

use App\Http\Models\Produto;
use App\Http\Models\ProdutoImagem;
use App\Http\Models\ProdutoVariation;
use App\Http\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Throwable;

class ProdutoInativoController extends Controller
{
    protected $request,$produto,$produtoImage,$produtoVariation;

    public function __construct(Request $request, Produto $produto, ProdutoImagem $produto_image,
                                    ProdutoVariation $produtoVariation){
        $this->request = $request;
        $this->produto = $produto;
        $this->produtoImage = $produto_image;
        $this->produtoVariation = $produtoVariation;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        if(Auth::check() === true){
            $user_data = Usuario::where("user_id",auth()->user()->id)->first();

            return view('admin.product_inactive', compact('user_data'));
        }

        return redirect()->route('admin.login');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create()
    {
        try {

            //$ret =  $this->produto::with('products')
            $ret =  $this->produto->leftJoin('loja_fornecedores','loja_produtos_new.fornecedor_id','=' ,'loja_fornecedores.id')
                ->leftJoin('loja_categorias','loja_produtos_new.categoria_id','=' ,'loja_categorias.id')
                //->leftJoin('loja_produtos_variacao','loja_produtos_new.id','=' ,'loja_produtos_variacao.products_id')
                ->select(
                    'loja_produtos_new.id',
                    'loja_produtos_new.codigo_produto',
                    'loja_produtos_new.descricao',
                    'loja_categorias.nome as categoria',
                    (DB::raw('IF((loja_produtos_new.status = 1), \'ATIVO\', \'INATIVO\') as status')),
                    (DB::raw("DATE_FORMAT(loja_produtos_new.created_at, '%d/%m/%Y %H:%i:%s') as created")),
                    (DB::raw("DATE_FORMAT(loja_produtos_new.updated_at, '%d/%m/%Y %H:%i:%s') as updated"))


                    /*(DB::raw("FORMAT(loja_produtos_variacao.valor_varejo, 2) as valor_varejo")),

                    'loja_produtos_variacao.valor_atacado',
                    'loja_produtos_variacao.valor_produto',
                    'loja_produtos_variacao.subcodigo',
                    'loja_produtos_variacao.variacao',
                    'loja_produtos_variacao.quantidade',
                    'loja_produtos_variacao.quantidade_minima',
                    'loja_produtos_variacao.estoque',
                    'loja_produtos_variacao.percentage',
                    'loja_produtos_variacao.id as id_variacao',
                    'loja_produtos_variacao.subcodigo',
                    'loja_produtos_variacao.status as status_variacao',
                    (DB::raw("DATE_FORMAT(loja_produtos_variacao.validade, '%d/%m/%Y') as data_validade_variacao")),
                    (DB::raw('IF((loja_produtos_new.status = 1), \'ATIVO\', \'INATIVO\') as status_produto')),
                    'loja_fornecedores.nome as fornecedor',
                    'loja_categorias.nome as categoria',
                    'loja_fornecedores.id as fornecedor_id',
                    'loja_categorias.id as categoria_id'*/

                )->where('block',0)
                ->where('loja_produtos_new.status',1) //somente ativos
                ->orderBy('loja_produtos_new.id', 'DESC')
                ->get();

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
     * @return JsonResponse
     */
    public function store()
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $data = $this->produto::select('codigo_produto')->orderBy("id", "desc")->first();
        $id = $this->produto::max('id')+1; //pega próximo ID para criar  a variação do produto

        if($data == null)
            return Response::json(array('success' => true, "data" => 1000, "id" => "01"), 200);

        return Response::json(array('success' => true, "data" => $data->codigo_produto + 1, "id" => "01"), 200);
    }

    /**
     * Retorna os produtos inativos
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        try {

            $ret =  Produto::leftJoin('loja_categorias','loja_produtos_new.categoria_id','=' ,'loja_categorias.id')
                ->leftJoin('loja_produtos_variacao','loja_produtos_new.id','=' ,'loja_produtos_variacao.products_id')
                ->select(
                    'loja_produtos_new.id',
                    'loja_produtos_new.codigo_produto',
                    'loja_produtos_new.descricao',
                    'loja_categorias.nome as categoria',
                    'loja_produtos_new.status as  sta',
                    (DB::raw('IF((loja_produtos_new.status = 1), \'ATIVO\', \'INATIVO\') as status')),
                    (DB::raw("DATE_FORMAT(loja_produtos_new.created_at, '%d/%m/%Y %H:%i:%s') as created")),
                    (DB::raw("DATE_FORMAT(loja_produtos_new.updated_at, '%d/%m/%Y %H:%i:%s') as updated"))

                )->where('loja_produtos_variacao.status',$id)
                ->groupBy('loja_produtos_new.codigo_produto')
                ->orderByDesc('loja_produtos_new.id')
                ->get();

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
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request,$id)
    {

        try {
            if($request->input('tipo') == 'variacao'){
                $produto =  ProdutoVariation::find($request->input('id'));
            }else{
                $produto =  Produto::find($request->input('id'));
            }
            $produto->status = $request->input('flag');
            $produto->update(); // Salve as alterações no banco de dados

            $msg = $request->input('tipo') == 'variacao' ? "Variação do Produto Ativado com Sucesso!" : "Produto Ativado com Sucesso!";

            return Response::json(array('success' => true, "message" => $msg), 200);

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
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
     * Retorna imagens do produto
     * Recebe o Id do produto
     * @param int $id
     * @return JsonResponse
     */
    public function pictures(int $id){

        try {
           $data = $this->produtoImage::select('id','produto_variacao_id','path')->where('produto_variacao_id',$id)->get();

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
        return Response::json(array('success' => true, "data" => $data), 200);
    }

    /***
     * Retonar os produtos inativos (variações) pelo ID do PAI
     *
    */
    public function getProdutoInativos(int $id){
        try {
            $produto = Produto::find($id);

            $ret = $produto->variances()->where('status', 0)->get();

           //$ret =  $this->produto::with('variances')->where('status', 0)->get();

            /*
            $ret =  $this->produto::with('products')
                ->select("id","codigo_produto","descricao","status","block","fornecedor_id","categoria_id")
                ->where('id',$id)->first();
    */

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
        return Response::json(array('success' => true, "data" => $ret), 200);
    }

}
