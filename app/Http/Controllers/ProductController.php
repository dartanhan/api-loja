<?php

namespace App\Http\Controllers;

use App\Http\Models\Cor;
use App\Http\Models\Fornecedor;
use App\Http\Models\Product;
use App\Http\Models\ProdutoImagem;
use App\Http\Models\ProdutoQuantidade;
use App\Http\Models\Categoria;
use App\Http\Models\Produto;
use App\Http\Models\ProdutoCodigo;
use App\Http\Models\TemporaryFile;
use App\Http\Models\Usuario;
use App\Imports\ProductImport;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class  ProductController extends Controller
{

    protected $request,$produto,$produtoQuantidade,$categoria, $produtoCodigo,$cor,$produtoImagem,$fornecedor;

    public function __construct(Request $request, Produto $produto,
                                    ProdutoQuantidade $produtoQuantidade,
                                    Fornecedor $fornecedor, Categoria $categoria,
                                    ProdutoCodigo $produtoCodigo,
                                    Cor $cor, ProdutoImagem $produtoImagem){

        $this->request = $request;
        $this->produto = $produto;
        $this->produtoQuantidade = $produtoQuantidade;
        $this->fornecedor = $fornecedor;
        $this->categoria = $categoria;
        $this->produtoCodigo = $produtoCodigo;
        $this->cor = $cor;
        $this->produtoImagem = $produtoImagem;
    }


    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('admin.product');

        //$produtos =  $this->produto::with('produtos', 'images')
        // $produtos =  $this->produto::with('produtos')
        //     ->leftJoin('loja_fornecedores','loja_produtos.fornecedor_id','=' ,'loja_fornecedores.id')
        //     ->leftJoin('loja_categorias','loja_produtos.categoria_id','=' ,'loja_categorias.id')
        //     ->select(
        //         'loja_produtos.*',
        //         'loja_fornecedores.nome as nome_fornecedor',
        //         'loja_categorias.nome as nome_categoria'
        //     )->orderBy('id', 'DESC')
        //     ->get();

        // $user_data = Usuario::where("user_id",auth()->user()->id)->first();

        // $fornecedors = $this->fornecedor->where('status',true)->orderBy('nome', 'ASC')->get();
        // $categorias = $this->categoria->where('status',true)->orderBy('nome', 'ASC')->get();
        // $cores = $this->cor->where('status',true)->orderBy('nome', 'ASC')->get();

        // return view('admin.produto', compact('produtos','fornecedors','categorias','cores','user_data'));
    }

    /**
     * Retorna o código do produto sequencial
     *
     * */
    public function code()
    {
       // dd($this->produto::all());
        //$produto = $this->produto::all();
        try {

            $maxId = DB::table($this->produtoCodigo->table)->max('codigo')  ;

            return Response()->json(['success' => true , 'message' => $maxId == null ? 1000 : $maxId +1]);

        } catch (Throwable $e) {
            return Response::json(['success' => false , 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create()
    {

        try {
            //$formatter = new NumberFormatter('pt_BR',  NumberFormatter::CURRENCY);

            $products = $this->produto::with('produtos','produtos.lojas')
                ->leftJoin('loja_fornecedores','loja_produtos.fornecedor_id','=' ,'loja_fornecedores.id')
                ->leftJoin('loja_categorias','loja_produtos.categoria_id','=' ,'loja_categorias.id')
                ->leftJoin('loja_cores','loja_produtos.cor_id','=' ,'loja_cores.id')
                ->leftJoin('loja_produtos_quantidade as tblQtd','loja_produtos.id','=' ,'tblQtd.produto_id')
                ->select(
                    'loja_produtos.*',
                    'loja_fornecedores.nome as nome_fornecedor',
                    'loja_categorias.nome as nome_categoria',
                    'loja_cores.nome as nome_cor',
                    'tblQtd.quantidade as qtdBarao',
                    'tblQtd.quantidade_minima as qtdMinBarao',
                    DB::raw("(DATE_FORMAT(loja_produtos.created_at,'%d/%m/%Y %H:%i:%s')) as dataCriacao"),
                    DB::raw("(DATE_FORMAT(loja_produtos.updated_at,'%d/%m/%Y %H:%i:%s')) as dataAtualizacao")
                )
                ->where('block',0)
                ->where('tblQtd.loja_id',2)
                ->orderBy('id', 'DESC');

            /*

            foreach ($productsList as $value){
                $saida['id'] = $value->id;
                $saida['codigo_produto'] = $value->codigo_produto;
                $saida['descricao'] = $value->descricao;
                $saida['status'] = $value->status == 1 ? 'ATIVO' : 'INATIVO';
                $saida['valor_produto'] = $formatter->formatCurrency($value->valor_produto, 'BRL') ;
                $saida['valor_cartao'] =  $formatter->formatCurrency($value->valor_cartao, 'BRL') ;
                $saida['valor_dinheiro'] =  $formatter->formatCurrency($value->valor_dinheiro, 'BRL') ;
                $saida['percentual'] = $value->percentual;
                $saida['created_at'] = date('d/m/Y H:i:s', strtotime($value->created_at));
                $saida['updated_at'] = date('d/m/Y H:i:s', strtotime($value->updated_at));
                $saida['nome_fornecedor'] = $value->nome_fornecedor;
                $saida['nome_categoria'] = $value->nome_categoria;
                $saida['nome_cor'] = $value->nome_cor;

                foreach ($value->produtos as $val) {

                    if ($val->loja_id == 1) {
                        $saida['qtdFeira'] = $val->quantidade;
                        $saida['qtdMinFeira'] = $val->quantidade_minima;
                    } else {
                        $saida['qtdBarao'] = $val->quantidade;
                        $saida['qtdMinBarao'] = $val->quantidade_minima;
                    }
                }
                $products[] = $saida;
            }*/

            return  DataTables::of($products)->make(true);
            /*
            if(!empty($products)) {
                return \response()->json($products);
            }  else {
                return \response()->json(array('data'=>''));
            }*/

        } catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage() ), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store()
    {
//        try {
//            //dd($this->request->allFiles());
//            $produtoId  = $this->request->input("productId") !== "" ? $this->request->input("productId") : $this->request->input("variacaoId");
//
//            //Se productId for preenchido sei que é produto PAI salva na pasta product caso não pasta produtos(variações)
//            $destino = $this->request->input("productId") !== "" ? 'product/' : 'produtos/';
//
//            //busca a foto temporario que foi feita upload pelo UploadController
//            $temp_file = TemporaryFile::where('folder',$this->request->image)->first();
//
//            //faz a logica de deletar caso enha a foto e inserir nova, caso a foto seja ok no upload temporário
//            if($temp_file){
//                if ($this->request->input("imagemName")) {
//                     // Exclua a foto antiga do armazenamento
//                     Storage::delete($destino.$produtoId ."/".$this->request->input("imagemName"));
//                }
//
//                Storage::copy($destino.'/tmp/'.$temp_file->folder.'/'.$temp_file->file,$destino.$produtoId ."/".$temp_file->file);
//
//                //delete a imagem temporaria
//                Storage::deleteDirectory($destino.'/tmp/'.$temp_file->folder);
//                $temp_file->delete();
//            }
//            //Atualiza a tabela de produtos_new com a imagem
//            Produto::where('id', $produtoId)->update(['imagem' => $temp_file->file]);
//
//
//        } catch (Throwable $e) {
//            return Response::json(array('success' => false, 'message' => $e->getMessage(), 'cod_retorno' => 500), 500);
//        }
//        //return redirect()->route('product.index');
//        return Response::json(array('success' => true, 'message' => 'Produto atualizado/cadastrado com sucesso!'), 200);

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        $images =  $this->produtoImagem::where('produto_id' , $id)->get();

        return Response::json(array('success' => true, "dados" => $images), 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function edit(int $id)
    {

        try {

            $produtos =  $this->produto::with('produtos','produtos.lojas')
                ->leftJoin('loja_fornecedores','loja_produtos.fornecedor_id','=' ,'loja_fornecedores.id')
                ->select(
                    'loja_produtos.*',
                    'loja_fornecedores.nome as nome_fornecedor'
                    )
                 ->where('loja_produtos.id', $id)
                 ->orderBy('id', 'DESC')
                ->get();

                // dd($produto[0]->produtos[0]->quantidade);

                return Response::json(array('success' => true, "dados" => $produtos), 200);


        } catch (\Exception $e) {
            return Response::json(array('success' => false, 'message' => $e), 500);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function update(int $id)
    {
        //dd($id);
        try{
            $produto =$this->produto::where('id', $id)->update(['block' => 1]);

            if(!$produto)
                return Response::json(array("success" => false, "message" => utf8_encode("Produto não bloqueado id: [ {$id} ]")), 400);

        }catch(QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1451') {
                return Response::json(array('success' => false, 'message' => 'Produto não pode ser removido, ele está sendo usado no sistema!'), 400);
            }
        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage() ), 500);
        }
        return Response::json(array("success" => true, "message" => "Produto bloqueado com sucesso!"),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        // dd($this->request->fornecedor_id);
        try{
            $produto = $this->produto::find($id)->delete();

            if(!$produto){
                return Response::json(array("success" => false, "message" => utf8_encode("Produto não localizado para deleção com o id: [ {$id} ]")), 400);
            }
        }catch(QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1451') {
                return Response::json(array('success' => false, 'message' => 'Produto não pode ser removido, ele está sendo usado no sistema!'), 400);
            }

        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage() ), 500);

        }
        return Response::json(array("success" => true, "message" => "Produto  deletado com sucesso!"),200);
    }

    public function importProduct()
    {
      //  dd($this->request->all());
        // try {

        //     Excel::import(new ProductImport, $this->request->file('fileUpload'));
        //     return redirect('/admin/produto');

        // } catch (Throwable $e) {

        //     return Response::json(array('success' => false, 'message' => $e->getMessage() ), 500);
        // }
    }
}
