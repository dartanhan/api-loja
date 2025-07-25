<?php

namespace App\Http\Controllers;

use App\Http\Models\Categoria;
use App\Http\Models\Cor;
use App\Http\Models\Fornecedor;
use App\Http\Models\MovimentacaoEstoque;
use App\Http\Models\OrigemNfce;
use App\Http\Models\Produto;
use App\Http\Models\ProdutoControle;
use App\Http\Models\ProdutoImagem;
use App\Http\Models\ProdutoVariation;
use App\Http\Models\Usuario;
use App\Traits\MovimentacaoTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use NumberFormatter;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class ProdutoController extends Controller
{
    use MovimentacaoTrait;

    protected Request $request;
    protected OrigemNfce $origem_nfce;
    protected ProdutoControle $produtoControle;
    protected ProdutoVariation $produtoVariation;
    protected ProdutoImagem $produtoImage;
    protected Cor $cor;
    protected Categoria $category;
    protected Fornecedor $fornecedor;
    protected Produto $produto;
    protected MovimentacaoEstoque $movimentacaoEstoque;

    public function __construct(Request $request, Produto $produto, Fornecedor $fornecedor, Categoria $category,
                                Cor $cor, ProdutoImagem $produto_image, ProdutoVariation $produtoVariation,
                                ProdutoControle $produtoControle, OrigemNfce $origem_nfce,MovimentacaoEstoque $movimentacaoEstoque)
    {
        $this->request = $request;
        $this->produto = $produto;
        $this->fornecedor = $fornecedor;
        $this->category = $category;
        $this->cor = $cor;
        $this->produtoImage = $produto_image;
        $this->produtoVariation = $produtoVariation;
        $this->produtoControle = $produtoControle;
        $this->origem_nfce = $origem_nfce;
        $this->movimentacaoEstoque = $movimentacaoEstoque;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        if (Auth::check() === true) {
            $suppliers = $this->fornecedor->where('status', true)->orderBy('nome', 'ASC')->get();
            $categories = $this->category->where('status', true)->orderBy('nome', 'ASC')->get();
            $cores = $this->cor->where('status', true)->orderBy('nome', 'ASC')->get();
            $origem_nfces = $this->origem_nfce->orderBy('codigo', 'ASC')->get();

            $user_data = Usuario::where("user_id", auth()->user()->id)->first();

            return view('admin.produto', compact('origem_nfces', 'categories', 'cores', 'user_data'));
        }

        return redirect()->route('admin.login');

    }

    /**
     * Livewire
    */
    public function produtos_ativos()
    {
        return view('admin.produtos.ativos');
    }

    /**
     * Livewire
     */
    public function produtos_inativos()
    {
        return view('admin.produtos.inativos');
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
            $query = $this->produto::with('produtoImagens')
                ->leftJoin('loja_fornecedores', 'lpn.fornecedor_id', '=', 'loja_fornecedores.id')
                ->leftJoin('loja_categorias', 'lpn.categoria_id', '=', 'loja_categorias.id')
                ->select(
                    'lpn.id',
                    'lpn.codigo_produto',
                    'lpn.descricao',
                    'loja_categorias.nome as categoria',
                    'lpn.status',
                    (DB::raw("DATE_FORMAT(lpn.created_at, '%d/%m/%Y %H:%i:%s') as created")),
                    (DB::raw("DATE_FORMAT(lpn.updated_at, '%d/%m/%Y %H:%i:%s') as updated"))

                )->from('loja_produtos_new as lpn')
                ->where('lpn.status', 1) //somente ativos
                ->groupBy('lpn.id')
                ->orderBy('lpn.id', 'DESC');

            if (!empty($query)) {
                return DataTables::of($query)->make(true);
            } else {
                return Response()->json(array('data' => ''));
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
        try {
            DB::beginTransaction();
            // dd($this->request->all());
            //   dd(count($this->request->allFiles()['images0']));

            $produto_id = $this->request->input("produto_id");

            $rules = $this->getValidationRules($produto_id);
            $validated = Validator::make($this->request->all(), $rules, $this->getValidationMessages());

            if ($validated->fails()) {
                return Response::json(['success' => false, 'message' => $validated->errors()->first()], 400);
            }

            /**
             * Salvar produto
            */
            $produto = $this->salvarProduto($produto_id);

            /**
             * Salvar as variações
             */

            $this->salvarVariacoes($produto->id);

//            $data["codigo_produto"] = $this->request->input("codigo_produto");
//            $data["descricao"] = $this->request->input("descricao");
//            $data["status"] = $this->request->input("status");
//            $data["origem_id"] = $this->request->input("origem");
//            $data["ncm"] = $this->request->input("ncm");
//            $data["cest"] = $this->request->input("cest");
//            $data["categoria_id"] = $this->request->input("categoria_id");
//            $data["cor_id"] = 1;
//
//
//            //Cria o produto
//            //$products = $this->produto::create($data);
//            $matchThese = array('id' => $produto_id);
//            $products = $this->produto::updateOrCreate($matchThese, $data);

            /**
             * Quantidade de variações
             */
 //           $qtd_lines = count($this->request->input("variacao"));
            //     dd($qtd_lines);

            /**
             * Exibindo os dados
             */
      //      $data["products_id"] = $products->id;

            //dd($data);
            /**
             *   Formata em decimal par o banco
             */
    //        $formatter = new NumberFormatter('pt_BR', NumberFormatter::DECIMAL);

            /**
             * Salva a variação vinculada ao produto PAI
            */
//            for ($i = 0; $i < $qtd_lines; $i++) {
//                $dateString = $this->request->input("validade")[$i];
//
//                if ($dateString === "00/00/0000") {
//                    $formattedDate = "0000-00-00";
//                } else {
//                    $formattedDate = Carbon::createFromFormat('d/m/Y', $dateString)->format('Y-m-d');
//                }
//
//                $data["subcodigo"] = $data["codigo_produto"] . $this->request->input("subcodigo")[$i];
//                $data["variacao"] = $this->request->input("variacao")[$i];
//                $data["valor_varejo"] = $formatter->parse(str_replace(['R$', ' '], '', $this->request->input("valor_varejo")[$i]));
//                $data["valor_atacado"] = $formatter->parse(str_replace(['R$', ' '], '', $this->request->input("valor_atacado_10un")[$i]));
//                $data["valor_atacado_10un"] = $formatter->parse(str_replace(['R$', ' '], '', $this->request->input("valor_atacado_10un")[$i]));
//                $data["valor_produto"] = $formatter->parse(str_replace(['R$', ' '], '', $this->request->input("valor_produto")[$i]));
//                $data["quantidade"] = $this->request->input("quantidade")[$i];
//                $data["quantidade_minima"] = $this->request->input("quantidade_minima")[$i];
//                //se o status do Pai for INATIVO (0), seta 0 para os filhos
//                $data["status"] = $this->request->input("status") == 0 ? 0 : $this->request->input("status_variacao")[$i];
//                $data["percentage"] = $formatter->parse($this->request->input("percentage")[$i]);
//                $data["validade"] = $formattedDate;
//                $data["fornecedor"] = $this->request->input("fornecedor")[$i];
//                $data["estoque"] = $this->request->input("estoque")[$i];
//                $data["gtin"] = $this->request->input("gtin")[$i];
//                $data["variacao_id"] = $this->request->input("variacao_id")[$i];
//
//                /**
//                 *Salva a movimentação do estoque
//                 */
//                $variacao = ProdutoVariation::where('id',  $data["variacao_id"])->first();
//                if($variacao){
//                    $data["antes"] = $variacao->quantidade;
//                    $data["movimentada"] = $data["quantidade"];
//                    $data["depois"] = $data["antes"] + $data["movimentada"];
//                }else{
//                    $data["antes"] = 0;
//                    $data["movimentada"] = $data["quantidade"];
//                    $data["depois"] = $data["antes"] + $data["movimentada"];
//                }
//
//                $this->movimentacaoEntrada($data);
//
//                /**
//                 * Cria ou Atualiza a variação do produto
//                 */
//                $matchThese = array('id' => $this->request->input("variacao_id")[$i]);
//                $controle = ProdutoVariation::updateOrCreate($matchThese, $data);
//            }
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack(); // Faz rollback em caso de erro
            return Response::json(array('success' => false, 'message' => $e->getMessage(),'trace' => $e->getTraceAsString(), 'cod_retorno' => 500), 500);
        }

        $msg = $produto_id ? "Produto Atualizado com sucesso!" : "Produto Cadastrado com sucesso!";
        return Response::json(['success' => true, 'message' => $msg], 201);
    }

    /**
     * Método: validação de regras
     * @param $produto_id
     * @return string[]
     */
    private function getValidationRules($produto_id)
    {
        $table = $this->produto->getTable();
        if (!$produto_id) {
            return [
                'codigo_produto' => "required|unique:$table|max:15",
                'descricao' => 'required|max:155',
                'origem' => 'required|max:5',
                'cest' => 'required|max:15',
                'ncm' => 'required|max:15',
                'categoria_id' => 'required|max:5',
            ];
        }

        return [
            'codigo_produto' => "required|max:15|unique:$table,codigo_produto,$produto_id",
            'descricao' => 'required|max:155',
            'origem' => 'required|max:5',
            'cest' => 'required|max:15',
            'ncm' => 'required|max:15',
            'categoria_id' => 'required|max:5',
        ];
    }

    /**
     *  Mensagens de validação
    */
    private function getValidationMessages()
    {
        return [
            'codigo_produto.required' => 'Código do produto é obrigatório!',
            'codigo_produto.unique' => 'Código do produto já cadastrado!',
            'codigo_produto.max' => 'Código do produto deve ter no máximo 15 caracteres!',
            'descricao.required' => 'Descrição do produto é obrigatória!',
            'descricao.max' => 'Descrição limitada a 155 caracteres!',
            'origem.required' => 'A origem é obrigatória!',
            'cest.required' => 'O CEST é obrigatório!',
            'ncm.required' => 'O NCM é obrigatório!',
            'categoria_id.required' => 'A categoria é obrigatória!',
        ];
    }

    /**
     * 🧩 Salvar produto
     * @param $produto_id
     * @return
     * @throws \Exception
     */
    private function salvarProduto($produto_id)
    {
        try {
            $data = [
                "codigo_produto" => $this->request->input("codigo_produto"),
                "descricao" => $this->request->input("descricao"),
                "status" => $this->request->input("status"),
                "origem_id" => $this->request->input("origem"),
                "ncm" => $this->request->input("ncm"),
                "cest" => $this->request->input("cest"),
                "categoria_id" => $this->request->input("categoria_id"),
                "cor_id" => 1,
            ];

            return $this->produto::updateOrCreate(['id' => $produto_id], $data);
        } catch (Throwable $e) {
            throw new \Exception("Erro em salvarProduto: " . $e->getMessage(), 500, $e);
        }
    }

    /**
     * Salvar variações
     * @param $produto_id
     * @throws \Exception
     */
    private function salvarVariacoes($produto_id)
    {
        try{
                $formatter = new NumberFormatter('pt_BR', NumberFormatter::DECIMAL);
                $qtd = count($this->request->input("variacao"));

                for ($i = 0; $i < $qtd; $i++) {
                    $dateString = $this->request->input("validade")[$i];
                    $validade = $this->formatarData($dateString);

                    $valorVarejo = $this->formatarValor($this->request->input("valor_varejo")[$i], $formatter);
                    $valorAtacado = $this->formatarValor($this->request->input("valor_atacado_10un")[$i], $formatter);
                    $valorProduto = $this->formatarValor($this->request->input("valor_produto")[$i], $formatter);
                    $percentage = $this->formatarValor($this->request->input("percentage")[$i], $formatter);
                    $statusPai = $this->request->input("status");
                    $statusVariacao = $statusPai == 0 ? 0 : $this->request->input("status_variacao")[$i];

                    $data = [
                        "products_id" => $produto_id,
                        "subcodigo" => $this->request->input("codigo_produto") . $this->request->input("subcodigo")[$i],
                        "variacao" => $this->request->input("variacao")[$i],
                        "valor_varejo" => $valorVarejo,
                        "valor_atacado" => $valorAtacado,
                        "valor_atacado_10un" => $valorAtacado,
                        "valor_produto" => $valorProduto,
                        "quantidade" => $this->request->input("quantidade")[$i],
                        "quantidade_minima" => $this->request->input("quantidade_minima")[$i],
                        "status" => $statusVariacao,
                        "percentage" => $percentage,
                        "validade" => $validade,
                        "fornecedor" => (int)$this->request->input("fornecedor")[$i],
                        "estoque" => (int)$this->request->input("estoque")[$i],
                        "gtin" => $this->request->input("gtin")[$i],
                        "variacao_id" => (int)$this->request->input("variacao_id")[$i],
                    ];

                    $variationId = $this->request->input("variacao_id")[$i] ?? null;
                    $variacaoExistente = ProdutoVariation::find($variationId); // retorna null se não existir
                    $quantidadeAntes = $variacaoExistente ? $variacaoExistente->quantidade : 0;

                    //array_push($var,  $this->registrarMovimentacaoEstoque($data));
                    $variacao = ProdutoVariation::updateOrCreate(['id' => $data["variacao_id"]], $data);

                    // Agora que temos a variação salva com ID e quantidade, registramos a movimentação
                    // Movimentação
                    $this->registrarMovimentacaoEstoque([
                        "variacao_id" => $variacao->id,
                        "quantidade" => $data["quantidade"],
                        "antes" => $quantidadeAntes,
                    ]);
                }

            } catch (Throwable $e) {
            throw new \Exception("Erro em salvarVariacoes: " . $e->getMessage(), 500, $e);
        }
    }

    /**
     *  Registrar movimentação de estoque
     * @param $data
     * @throws \Exception
     */
    private function registrarMovimentacaoEstoque(array $data)
    {
        try {
            $variacao = ProdutoVariation::find($data["variacao_id"]);
            if (!$variacao) {
                throw new \Exception("Variação não encontrada para ID " . $data["variacao_id"]);
            }

            // Usa o valor passado ou assume 0
            $antes = $data["antes"] ?? 0;
            $depois = $data["quantidade"];
            $movimentada = $depois - $antes;

            $dataMovimentacao = [
                "variacao_id" => $variacao->id,
                "antes" => $antes,
                "movimentada" => $movimentada,
                "depois" => $depois,
                "quantidade" => $movimentada,
            ];

//            $dataMovimentacao["variacao_id"] = $variacao->id;
//            $dataMovimentacao["antes"] = $variacao ? $variacao->quantidade : 0;
//            $dataMovimentacao["movimentada"] = $data["quantidade"] - $dataMovimentacao["antes"];
//            $dataMovimentacao["depois"] = $dataMovimentacao["antes"] + $dataMovimentacao["movimentada"];
//            $dataMovimentacao["quantidade"] = $dataMovimentacao["movimentada"];

            $this->movimentacaoEntrada($dataMovimentacao);

           // return $dataMovimentacao;
        } catch (\Throwable $e) {
            throw new \Exception("Erro em registrarMovimentacaoEstoque: " . $e->getMessage(), 500, $e);
        }
    }



    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $data = $this->produto::select('codigo_produto')->orderBy("id", "desc")->first();
        $id = $this->produto::max('id') + 1; //pega próximo ID para criar  a variação do produto

        if ($data == null)
            return Response::json(array('success' => true, "data" => 1000, "id" => "01"), 200);

        return Response::json(array('success' => true, "data" => $data->codigo_produto + 1, "id" => "01"), 200);
    }

    /**
     * Retorna os produtos inativos
     *
     * @param Produto $produto
     * @return void
     */
    public function edit(Produto $produto)
    {
      //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update($id)
    {
        try {
            $produto = $this->produto->find($id);
            $produto->status = 1;
            $produto->update(); // Salve as alterações no banco de dados

            return Response::json(array('success' => true, "message" => 'Produto ativado com sucesso!'), 200);

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
    public function pictures(int $id)
    {

        try {
            $data = $this->produtoImage::select('id', 'produto_variacao_id', 'path')->where('produto_variacao_id', $id)->get();

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
        return Response::json(array('success' => true, "data" => $data), 200);
    }

    /***
     *
     * @param int $id
     */
    public function getProducts(int $id)
    {
        try {

            $ret = $this->produto::with('products')
                ->select("id", "codigo_produto", "descricao", "status", "fornecedor_id", "categoria_id", "ncm", 'cest', "origem_id")
                ->where('id', $id)->first();


        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
        return Response::json(array('success' => true, "data" => $ret), 200);
    }


    public function indexNew()
    {
        if (Auth::check() === true) {
            $suppliers = $this->fornecedor->where('status', true)->orderBy('nome', 'ASC')->get();
            $categories = $this->category->where('status', true)->orderBy('nome', 'ASC')->get();
            $cores = $this->cor->where('status', true)->orderBy('nome', 'ASC')->get();
            $origem_nfces = $this->origem_nfce->orderBy('codigo', 'ASC')->get();

            $user_data = Usuario::where("user_id", auth()->user()->id)->first();

            return view('admin.produto-new', compact('origem_nfces', 'categories', 'cores', 'user_data'));
        }

        return redirect()->route('admin.login');

    }


    public function fornecedoresProdutosBaixoEstoque()
    {
//        $inicioPeriodo = now()->subDays(30);
//        $fimPeriodo = now();
//
//        $fornecedores = Fornecedor::with(['variacoes' => function ($query) use ($inicioPeriodo, $fimPeriodo) {
//            $query
//                ->where('quantidade', '<=', 5)
//                ->where('status', 1) //ativo
//                ->withCount(['vendas as total_vendido' => function ($vendaQuery) use ($inicioPeriodo, $fimPeriodo) {
//                    $vendaQuery->whereBetween('created_at', [$inicioPeriodo, $fimPeriodo]);
//                }])
//                ->orderBy('total_vendido', 'desc') // Ordena pelo total vendido
//                ->with(['produtoPai', 'images']);
//        }])->where('status', 1)->get();
//
//        return view('admin.baixoEstoque', compact('fornecedores','inicioPeriodo', 'fimPeriodo'));


        return view('admin.produtosEstourados');

    }

    public function getProdutosEstourados(Request $request)
    {
        $inicio = $request->input('data_inicio', now()->startOfMonth());
        $fim = $request->input('data_fim', now()->endOfMonth());

        $dados = DB::table('loja_vendas_produtos as vp')
            ->join('loja_produtos_variacao as pv', 'vp.codigo_produto', '=', 'pv.subcodigo')
            ->join('loja_fornecedores as f', 'pv.fornecedor', '=', 'f.id')
            ->leftJoin('loja_produtos_imagens as i', 'pv.id', '=', 'i.produto_variacao_id')
            ->select(
                'f.nome as fornecedor',
                'vp.codigo_produto',
                'vp.descricao',
                DB::raw('SUM(vp.quantidade) as quantidade_vendida'),
                'pv.quantidade',
                'i.path as imagem'
            )
            ->whereBetween('vp.created_at', [$inicio, $fim])
            ->groupBy('vp.codigo_produto', 'vp.descricao', 'pv.quantidade', 'f.nome', 'i.path')
            ->havingRaw('pv.quantidade < SUM(vp.quantidade)')
            ->get()
            ->groupBy('fornecedor');

        $resultado = $dados->map(function ($produtos, $fornecedor) {
            return [
                'fornecedor' => $fornecedor,
                'produtos' => $produtos->values()
            ];
        })->values();

        return response()->json(['data' => $resultado]);
    }
}
