<?php

namespace App\Http\Controllers;

use App\Http\Models\Cor;
use App\Http\Models\Fornecedor;
use App\Http\Models\Product;
use App\Http\Models\ProdutoImagem;
use App\Http\Models\ProdutoQuantidade;
use App\Http\Models\Categoria;
use App\Http\Models\ProdutoCodigo;
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



class  ProductController extends Controller
{

    protected $request,$produto,$produtoQuantidade,$categoria, $produtoCodigo,$cor,$produtoImagem,$fornecedor;

    public function __construct(Request $request, Product $produto,
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

        //$produtos =  $this->produto::with('produtos', 'images')
        $produtos =  $this->produto::with('produtos')
            ->leftJoin('loja_fornecedores','loja_produtos.fornecedor_id','=' ,'loja_fornecedores.id')
            ->leftJoin('loja_categorias','loja_produtos.categoria_id','=' ,'loja_categorias.id')
            ->select(
                'loja_produtos.*',
                'loja_fornecedores.nome as nome_fornecedor',
                'loja_categorias.nome as nome_categoria'
            )->orderBy('id', 'DESC')
            ->get();

        $user_data = Usuario::where("user_id",auth()->user()->id)->first();

        $fornecedors = $this->fornecedor->where('status',true)->orderBy('nome', 'ASC')->get();
        $categorias = $this->categoria->where('status',true)->orderBy('nome', 'ASC')->get();
        $cores = $this->cor->where('status',true)->orderBy('nome', 'ASC')->get();

        return view('admin.produto', compact('produtos','fornecedors','categorias','cores','user_data'));
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

       // dd($this->request->all());
        DB::beginTransaction();
        //OK
        try {
            if($this->request->input("id") == null){
                $rules = ['codigo_produto' => 'required|unique:'.$this->produto->table.'|max:255'];
            }else{
                $rules = ['codigo_produto' => 'required|max:255|unique:'.$this->produto->table.',codigo_produto,'. $this->request->input("id")];
            }
            $validator = Validator::make($this->request->all(), $rules);
               // 'codigo_produto' => 'required|unique:'.$this->produto->table.'|max:255']);
              //  'codigo_produto' => 'required|max:255|unique:' . $this->produto->table . ',nome,' . $this->request->iProduto]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return Response::json(array('success' => false,'message' => $error), 400);
            }


            $data["id"] = $this->request->input("id");

            /***
             * Caso seja diferente de 13 não é código de barras é codigo loja [1000,1001,1002]
             * Então pega o ultimo código da loja e incrementa de 1
             * se qualquer códiog abaixo de 13 for informado , será seguido a sequencia do código loja
             */
            if (strlen($this->request->input("codigo_produto")) < 13 ) {
                if($this->request->input("id") == ""){
                    $maxCodigo = DB::table($this->produtoCodigo->table)->max('codigo')+1;
                   // $this->produtoCodigo = new $this->produtoCodigo();
                    if($maxCodigo == $this->request->input("codigo_produto")){
                        $data["codigo_produto"] = $this->request->input("codigo_produto");
                    }else if(strlen($this->request->input("codigo_produto")) < 13){
                        $data["codigo_produto"] = $maxCodigo;
                    }
                }
            }else{
                $data["codigo_produto"] = $this->request->input("codigo_produto");
            }

            $data["descricao"] = $this->request->input("nome");
            $data["status"] = $this->request->input("status");

            $data["valor_produto"] = str_replace(',', '.', str_replace('R$ ', '', $this->request->input("valorproduto")));

            $data["valor_dinheiro"] = str_replace(',', '.', str_replace('R$ ', '', $this->request->input("valordinheiro")));

            $data["valor_cartao"] = str_replace(',', '.', str_replace('R$ ', '', $this->request->input("valorcartao")));

            $data["percentual"] = str_replace(',', '.', str_replace(' %', '', $this->request->input("valorpercentual")));

            $data["fornecedor_id"] = $this->request->input("fornecedor");

            $data["categoria_id"] = $this->request->input("categoria");

            $data["cor_id"] = $this->request->input("cor");

          //  dd($data);
            //Cria o produto
            $matchThese = array('id' => $this->request->input("id"));
            $produtos = $this->produto::updateOrCreate($matchThese, $data);
           // dd($produtos);

            //monta dados tabela de quantidade do produto
            $dadosqtd["produto_id"] = $produtos->id;
            $dadosqtd["loja_id"] = 1;
            $dadosqtd["quantidade"] = $this->request->qtdfeira;
            $dadosqtd["quantidade_minima"] = $this->request->qtdmin;
            $dadosqtd["status"] = $this->request->status;

            $dadosqtd1["produto_id"] = $produtos->id;
            $dadosqtd1["loja_id"] = 2;
            $dadosqtd1["quantidade"] = $this->request->qtdbarao;
            $dadosqtd1["quantidade_minima"] = $this->request->qtdmin;
            $dadosqtd1["status"] = $this->request->status;


            $dados['valores'] = array($dadosqtd, $dadosqtd1);

            //tranforma em json
            $json = json_encode($dados);

            //converte em array
            $arrJson = json_decode($json, true);

            //Grava na tabela de quantidade para retornar em PIVOT
            if ($produtos->exists) {
                foreach ($arrJson['valores'] as $valor) {

                    $produtosQuantidade = new $this->produtoQuantidade($arrJson['valores']);
                    $produtosQuantidade->produto_id = $produtos->id;
                    $produtosQuantidade->cor_id = $this->request->input("cor");
                    $produtosQuantidade->loja_id = $valor['loja_id'];
                    $produtosQuantidade->quantidade = $valor['quantidade'];
                    $produtosQuantidade->quantidade_minima = $valor['quantidade_minima'];
                    $produtosQuantidade->status = $valor['status'];

                    if ($this->request->input("id") != "") {
                        $this->produtoQuantidade->where(
                            ['produto_id' => $this->request->input("id"), 'loja_id' => $valor['loja_id']])
                            ->update(['produto_id' => $this->request->input("id"),
                                'loja_id' => $valor['loja_id'],
                                'quantidade' => $valor['quantidade'],
                                'quantidade_minima' => $valor['quantidade_minima'],
                                'status' => $valor['status'],
                                'cor_id' => $this->request->input("cor")
                            ]);
                    } else {
                        $produtosQuantidade->save();
                    }
                }

                /***
                 * Caso seja diferente de 13 não é código de barras é codigo loja [1000,1001,1002]
                 * Então pega o ultimo código da loja e incrementa de 1
                 * se qualquer códiog abaixo de 13 for informado , será seguido a sequencia do código loja
                 */
                if (strlen($this->request->input("codigo_produto")) < 13 ) {
                    if($this->request->input("id") == ""){
                        $maxCodigo = DB::table($this->produtoCodigo->table)->max('codigo')+1;
                        $this->produtoCodigo = new $this->produtoCodigo();
                        if($maxCodigo == $this->request->input("codigo_produto")){
                            $this->produtoCodigo->codigo = $this->request->input("codigo_produto");
                           // $this->produtoCodigo->save();
                        }else if(strlen($this->request->input("codigo_produto")) < 13){
                            $this->produtoCodigo->codigo = $maxCodigo;
                            //$this->produtoCodigo->save();
                        }
                       // dd($this->request->codigo_produto);
                        $this->produtoCodigo->save();
                    }
                }

               // dd($this->request->allFiles());
                //UPLOAD DE IMAGENS

                if (count($this->request->allFiles()) > 0) {
                    $total = count($this->request->allFiles()['images']);
                    for ($i = 0; $i < $total; $i++) {
                        $file = $this->request->allFiles()['images'][$i];

                        $this->produtoImagem = new ProdutoImagem();
                        $this->produtoImagem->produto_id = $produtos->id;
                        $this->produtoImagem->path = $file->store('produtos/' . $produtos->id);
                        $this->produtoImagem->save();
                        unset($this->produtoImagem);
                    }
                }
            }
            DB::commit();

        } catch (QueryException $e) {
                $errorCode = $e->errorInfo[1];
                if($errorCode == 1062){
                    return Response::json(array('success' => false, 'message' => 'Produto já cadastrado com o código [ '. $this->request->input("codigo_produto").' ]'), 400);
                }
        } catch (Throwable $e) {
            //   $maxId = DB::table($this->produto->table)->max('id');
            //   DB::statement("ALTER TABLE '.$this->produto->table.' AUTO_INCREMENT=$maxId");
            DB::rollBack();
            return Response::json(array('success' => false, 'message' => $e->getMessage(), 'cod_retorno' => 500), 500);
        }

        if($this->request->input("metodo") === 'PUT')
            return Response::json(array('success' => true, 'message' => 'Produto atualizado com sucesso!'), 200);
        else
            return Response::json(array('success' => true, 'message' => 'Produto cadastrado com sucesso!'), 200);

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
        try {

            Excel::import(new ProductImport, $this->request->file('fileUpload'));
            return redirect('/admin/produto');

        } catch (Throwable $e) {

            return Response::json(array('success' => false, 'message' => $e->getMessage() ), 500);
        }
    }
}
