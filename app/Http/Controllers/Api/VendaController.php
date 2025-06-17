<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Models\Carts;
use App\Http\Models\Cashback;
use App\Http\Models\ErrorLogs;
use App\Http\Models\Produto;
use App\Http\Models\ProdutoQuantidade;
use App\Http\Models\ProdutoVariation;
use App\Http\Models\TaxaCartao;
use App\Http\Models\Vendas;
use App\Http\Models\VendasCashBack;
use App\Http\Models\VendasCashBackValor;
use App\Http\Models\VendasPdv;
use App\Http\Models\VendasProdutos;
use App\Http\Models\VendasProdutosDesconto;
use App\Http\Models\VendasProdutosEntrega;
use App\Http\Models\VendasProdutosTipoPagamento;
use App\Http\Models\VendasProdutosTroca;
use App\Http\Models\VendasProdutosValorCartao;
use App\Http\Models\VendasProdutosValorDupla;
use App\Http\Models\VendasTroca;
use App\Http\Models\MovimentacaoEstoque;
use App\Service\VendaService;
use App\Traits\MovimentacaoTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;


class VendaController extends Controller
{
    use MovimentacaoTrait;

    protected VendaService $vendaService;

    protected VendasPdv $vendasPdv;
    protected VendasCashBackValor $cashBackValor;
    protected Cashback $cashback;
    protected VendasCashBack $cashbackVendas;
    protected TaxaCartao $taxaCartao;
    protected ProdutoQuantidade $produtoQuantidade;
    protected VendasProdutosValorDupla $valorDuplo;
    protected VendasProdutosValorCartao $valorCartao;
    protected VendasProdutosTipoPagamento $tipoPagamento;
    protected ProdutoVariation $productVariation;
    protected VendasProdutosDesconto $vendasDescontos;
    protected VendasProdutos $vendasProdutos;
    protected ErrorLogs $errorLogs;
    protected Vendas $vendas;
    protected Produto $product;
    protected Request $request;
    protected VendasProdutosEntrega $vendasProdutosEntrega;
    protected VendasProdutosTroca $vendasProdutosTroca;
    protected VendasTroca $vendasTroca;
    protected MovimentacaoEstoque $movimentacaoEstoque;

    public function __construct(Request $request,
                                Produto $product,
                                Vendas $vendas,
                                VendasProdutos $vendasProdutos,
                                VendasProdutosDesconto $vendasDescontos,
                                VendasProdutosTipoPagamento $tipoPagamento,
                                VendasProdutosValorCartao $valorCartao,
                                VendasProdutosValorDupla $valorDuplo,
                                ProdutoQuantidade $produtoQuantidade,
                                TaxaCartao $taxaCartao,
                                ProdutoVariation $productVariation,
                                VendasCashBack $cashbackVendas,
                                Cashback $cashback,
                                VendasCashBackValor $cashBackValor,
                                VendasPdv $vendasPdv,
                                ErrorLogs $errorLogs,
                                VendasProdutosEntrega $vendasProdutosEntrega,
                                VendasProdutosTroca $vendasProdutosTroca,
                                VendasTroca $vendasTroca,
                                MovimentacaoEstoque $movimentacaoEstoque,
                                VendaService $vendaService){
        $this->request = $request;
        $this->product = $product;
        $this->vendas = $vendas;
        $this->vendasProdutos = $vendasProdutos;
        $this->vendasDescontos = $vendasDescontos;
        $this->tipoPagamento = $tipoPagamento;
        $this->valorCartao = $valorCartao;
        $this->valorDuplo  = $valorDuplo;
        $this->produtoQuantidade  = $produtoQuantidade;
        $this->taxaCartao = $taxaCartao;
        $this->productVariation = $productVariation;
        $this->cashbackVendas = $cashbackVendas;
        $this->cashback = $cashback;
        $this->cashBackValor = $cashBackValor;
        $this->vendasPdv = $vendasPdv;
        $this->errorLogs = $errorLogs;
        $this->vendasProdutosEntrega = $vendasProdutosEntrega;
        $this->vendasProdutosTroca = $vendasProdutosTroca;
        $this->vendasTroca = $vendasTroca;
        $this->movimentacaoEstoque = $movimentacaoEstoque;
        $this->vendaService = $vendaService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $codigo_produto = $this->request->header('product-code');
        $tipo_pgto = intval($this->request->header('tipo-id'));

        $variations = $this->productVariation::with('produtoPai', 'images')->where('subcodigo', $codigo_produto)->first();

        if (!$variations) {
            return response()->json(['success' => false, 'message' => 'Produto não encontrado!'], 201);
        }

        if ($variations->status == 0) {
            return response()->json(['success' => false, 'message' => 'Produto Inativado para Venda!'], 201);
        }

        if ($variations->quantidade == 0) {
            return response()->json(['success' => false, 'message' => 'Produto sem Estoque para Venda!'], 201);
        }

        $product = $variations->produtoPai;

        if (!$product || $product->status == 0) {
            return response()->json(['success' => false, 'message' => 'Produto Bloqueado para venda!'], 201);
        }

        // Construção do JSON de resposta
        $storage = url('public','storage');

        $imagePath = !empty($variations->images) && isset($variations->images[0]->path) && Storage::exists($variations->images[0]->path)
            ? $storage . '/' . $variations->images[0]->path
            : $storage . '/produtos/not-image.png';

        return response()->json([
            'success' => true,
            'id' => $product->id, //id do pai
            'codigo_produto' => $codigo_produto,
            'descricao' => "{$product->descricao} - {$variations->variacao}",
            'status' => $variations->status,
            'fornecedor_id' => $variations->fornecedor,
            'categoria_id' => $product->categoria_id,
            'variacao_id' => $variations->id,
            'id_forma_pgto' => $tipo_pgto,
            'quantidade' => 1,
            'valor_produto' => $variations->valor_produto,
            'valor_atacado' => $variations->valor_atacado_10un,
            'valor_varejo' => $variations->valor_varejo,
            'valor_venda' => $variations->valor_varejo,
            'percentual' => $variations->percentage,
            'qtdestoque' => $variations->quantidade,
            'loja_id' => 2,
            'troca' => false,
            'path' => $imagePath,
            'path_image' => $imagePath
        ], 200);
    }

    /*public function index()
    {
        //dd($this->request->header('product-code'));
        $codigo_produto = $this->request->header('product-code');
       // $codigo_loja = $this->request->header('store-id');
        $tipo_pgto = intval($this->request->header('tipo-id'));
        $flag = $this->request->header('flag');

       // if($flag == 0) {
            $variations = DB::table('loja_produtos_variacao')
                ->leftJoin('loja_produtos_imagens', 'loja_produtos_imagens.produto_variacao_id', '=', 'loja_produtos_variacao.id')
               ->where('loja_produtos_variacao.subcodigo', '=', $codigo_produto)->first();

            if ($variations) {
                if ($variations->status == 0) {
                    return Response::json(array('success' => false, 'message' => 'Produto Inativado para Venda!'),
                        201, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }else if ($variations->quantidade == 0) {
                    return Response::json(array('success' => false, 'message' => 'Produto sem Estoque para Venda!'),
                       201, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }

                $products = DB::table('loja_produtos_new')->where('loja_produtos_new.id', '=', $variations->products_id)->first();

                if ($products) {
                    if ($products->status == 0) {
                        return Response::json(array('success' => false, 'message' => 'Produto Bloqueado para venda!'), 201, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    } else {
                        $data['success'] = true;
                        $data['id'] = $products->id;
                        $data['codigo_produto'] = $codigo_produto;
                        $data['descricao'] = $products->descricao . " - " . $variations->variacao;
                        //$data['variacao'] = $variations->variacao;
                        $data['status'] = $variations->status;
                        $data['fornecedor_id'] = $variations->fornecedor;
                        $data['categoria_id'] = $products->categoria_id;
                        $data['id_forma_pgto'] = $tipo_pgto;
                        $data['quantidade'] = 1;
                        $data['valor_produto'] = $variations->valor_produto;
                        $data['valor_atacado'] = $variations->valor_atacado_10un;//$variations->valor_atacado;
                        $data['valor_varejo'] =  $variations->valor_varejo;
                        $data['valor_venda'] =  $data['valor_varejo'];//$variations->valor_varejo;
                        //$data['valor_atacado_5un'] = $variations->valor_atacado_5un;
                       // $data['valor_atacado_10un'] = $variations->valor_atacado_10un;
                       // $data['valor_lista'] = $variations->valor_lista;
                        $data['percentual'] = $variations->percentage;
                        $data['qtdestoque'] = $variations->quantidade;
                        $data['loja_id'] = 2;
                        $data['troca'] = false;
                        $data['path'] = $variations->path;


                        //Verifica qual o host para pegar a imagem no destino certo
                        $storage = $this->request->secure() === true ? 'https://'.$this->request->getHttpHost() :
                                                                        'http://'.$this->request->getHttpHost().$this->request->getBasePath();

                        //concatena o pasta publica
                        $storage = $storage."/public/storage/";

                        //Verifica se existe o diretorio
                        $result =  Storage::exists($variations->path);

                        if ($result) {
                            $data['path_image'] = $storage.$variations->path;
                        } else {
                            $data['path_image'] = $storage."produtos/not-image.png";
                        }

                        return Response::json($data, 200, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    }
                }
            } else {
                return Response::json(array('success' => false, 'message' => 'Produto não encontrado!'), 201, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
    }*/

    /**
     * Show the form for creating a new resource.
     *
     * imprime a venda pelo seu código ou pega a ultima venda realizada
     *
     * @param codigo_venda
     *
     * @return JsonResponse
     */
    public function create()
    {

        try {
            $store_id = $this->request->header('store-id');
            $code_store = $this->request->header('code-store');

            /**
            PEGA A ÚLTIMA VENDA DA LOJA ESPECIFICA
             */
            $lastSale = $this->vendas
                ->where('loja_id', $store_id)
                ->when(!empty($code_store), function ($query) use ($code_store) {
                    return $query->where('codigo_venda', $code_store);
                })
                ->orderBy('id', 'DESC')
                ->first();


            if (!$lastSale) {
                return Response::json([
                    'success' => false,
                    'message' => "Venda não localizada [ {$code_store} ]"
                ], 200);
            }

            // Busca detalhes da venda e seus produtos
            $products = $this->vendas->where('codigo_venda', $lastSale->codigo_venda)
                ->with([
                    'produtos:id,venda_id,quantidade,descricao,codigo_produto,valor_produto',
                    'pagamentos' => function ($query) {
                        $query->select('id', 'venda_id', 'forma_pagamento_id', 'valor_pgto')->with('formaPagamento:id,nome');
                    },
                    'descontos:valor_desconto,valor_percentual,valor_recebido,venda_id',
                    'cashback.cliente:id,nome',
                    'entregas.formaEntrega:id,nome'
                ])
                ->select('id','codigo_venda', 'loja_id', 'valor_total', 'created_at')
                ->first();

            return Response::json(['success'=>true,"data"=>$products], 200);

            // Monta resposta JSON
            /* return Response::json([
                 'success'        => true,
                 'produtos'       => $products,
                 'percentual'     => $products->descontos[0]->valor_percentual ?? 0,
                 'valor_desconto' => $products->descontos[0]->valor_desconto ?? 0,
                 'valor_recebido' => $products->descontos[0]->valor_recebido ?? 0,
                 'loja_id'        => $products->loja_id,
                 'listTipoPagamento' => $products->pagamentos,
                 'codigo_venda'   => $code_store,
                 'valor_total'    => $lastSale->valor_total,
                 'valor_troco'    => $products->descontos[0]->valor_recebido - $lastSale->valor_total,
                 'valor_sub_total'=> $lastSale->valor_total + $products->descontos[0]->valor_desconto,
 //                'clienteModel'   => [
 //                    'nome'     => $products->cashback[0]->cliente->nome ?? 'Não informado',
 //                    'cashback' => $products->cashback[0]->valor ?? 0,
 //                    //'clientes' => [$products->cashback ?? (object) []],
 //                ],
             ], 200, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);*/



//            return response()->json($products, 201);
//
//            $products = $this->vendas->join('loja_vendas_produtos', 'loja_vendas.id', '=', 'loja_vendas_produtos.venda_id')
//                ->where('loja_vendas.codigo_venda', $code_store)
//                ->select(
//                    'loja_vendas.*',
//                    'loja_vendas_produtos.quantidade',
//                    'loja_vendas_produtos.descricao',
//                    'loja_vendas_produtos.codigo_produto',
//                    'loja_vendas_produtos.valor_produto'
//                )
//                ->get();

//            if ($products->isEmpty()) {
//                return Response::json([
//                    'success' => false,
//                    'message' => "Venda não localizada [ {$code_store} ]"
//                ], 400);
//            }

            /*if( $store != null) {

                $total_store = $store->valor_total;
                $code_store = $store->codigo_venda; //Pega o código venda KNxxx

                $store = $this->vendas->join('loja_vendas_produtos', 'loja_vendas.id', '=', 'loja_vendas_produtos.venda_id')
                    ->where('loja_vendas.codigo_venda', '=', $code_store)
                    ->select('loja_vendas.*', 'loja_vendas_produtos.quantidade',
                        'loja_vendas_produtos.descricao',
                        'loja_vendas_produtos.codigo_produto',
                        'loja_vendas_produtos.valor_produto')->get();

                if (count($store) > 0) {
                    //Desconto
                    $discount = $this->vendas->join('loja_vendas_produtos_descontos', 'loja_vendas.id', '=', 'loja_vendas_produtos_descontos.venda_id')
                        ->where('loja_vendas.codigo_venda', '=', $code_store)
                        ->select('loja_vendas_produtos_descontos.valor_desconto',
                            'loja_vendas_produtos_descontos.valor_percentual',
                            'loja_vendas_produtos_descontos.valor_recebido',
                            DB::raw('loja_vendas_produtos_descontos.valor_recebido - loja_vendas.valor_total as valor_troco'),
                            DB::raw('loja_vendas.valor_total + loja_vendas_produtos_descontos.valor_desconto as sub_total'))->first();

                    //Forma de pagamento
                    $payment = $this->vendas->join('loja_vendas_produtos_tipo_pagamentos', 'loja_vendas.id', '=', 'loja_vendas_produtos_tipo_pagamentos.venda_id')
                        ->join('loja_forma_pagamentos', 'loja_forma_pagamentos.id', '=', 'loja_vendas_produtos_tipo_pagamentos.forma_pagamento_id')
                        ->where('loja_vendas.codigo_venda', '=', $code_store)
                        //->select('loja_forma_pagamentos.id','loja_forma_pagamentos.nome')->first();
                        ->select('loja_forma_pagamentos.id', 'loja_forma_pagamentos.nome')->get();

                    //cashback
                    $clienteCashBack = $this->vendas->leftJoin('loja_vendas_cashback', 'loja_vendas.id', '=',  'loja_vendas_cashback.venda_id')
                        ->leftJoin('loja_clientes', 'loja_vendas_cashback.cliente_id' ,'=', 'loja_clientes.id')
                        ->where('loja_vendas.codigo_venda', '=', $code_store)
                    ->select('loja_clientes.nome', 'loja_vendas_cashback.valor as cashback')->first();


                    // dd($payment);
                    $pro["produtos"] = $store;
                    $pro["percentual"] = $discount->valor_percentual;
                    $pro["valor_desconto"] = $discount->valor_desconto;
                    $pro["valor_recebido"] = $discount->valor_recebido;
                    $pro["loja_id"] = $store[0]->loja_id;
                    //$pro["tipo_pagamento"] = $payment->id;
                    $pro["listTipoPagamento"] = $payment;
                    $pro["codigo_venda"] = $code_store;
                    $pro["valor_total"] = $total_store;
                    $pro["valor_troco"] = $discount->valor_troco;
                    $pro["valor_sub_total"] = $discount->sub_total;
                    $pro["clienteModel"]["nome"] = $clienteCashBack->nome;
                    $pro["clienteModel"]["cashback"] = $clienteCashBack->cashback;
                    $pro["clienteModel"]["clientes"] = array($clienteCashBack);

                    $pro["success"] = true;
                }
            }
            if ($store != null and count($store) > 0) {
                return Response::json($pro, 200,[],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }else{
                return Response::json(array('success' => false, 'message' => 'Venda não localizada [ ' . $code_store . ' ]'), 400);
            }*/

        } catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage(), 'code' => 500), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * SALVA A VENDA alterado para a possibilidade de venda offline
     * @param Request $request
     * @return JsonResponse
     */

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $dados = $request->all();

            // Validação básica
            if (empty($dados['codigo_venda']) || empty($dados['loja_id']) || !isset($dados['produtos'])) {
                throw new \Exception("Dados obrigatórios ausentes.");
            }

            // Cria a venda
            $venda = $this->vendaService->criarVenda($dados);

            if (!$venda) {
                throw new \Exception('Erro ao salvar a venda.');
            }

            // Processa produtos (incluindo baixa/entrada no estoque)
            $this->vendaService->processarProdutosVenda($dados["produtos"], $venda, $dados);

            // Pagamentos
            $this->vendaService->processarPagamentos($dados["pagamentos"], $venda->id);

            // Desconto
            $this->vendaService->registrarDesconto($dados["desconto"] ?? [], $venda->id);

            // Cashback
            $this->vendaService->registrarCashback($dados["clienteModel"], $venda);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venda registrada com sucesso.',
                'venda_id' => $venda->id
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            $this->vendaService->logarErro($dados['codigo_venda'] ?? '', $e, $dados);

            return Response::json([
                'success' => false,
                'message' => 'Erro ao processar venda. O problema foi registrado.'
            ], 500);
        }
    }

//    public function store(Request $request)
//    {
//
//        DB::beginTransaction();
//
//        try {
//            $dados = $request->all();
//            $variacao = "";
//
//            if (empty($dados['codigo_venda']) || empty($dados['loja_id']) || !isset($dados['produtos'])) {
//                throw new \Exception("Dados obrigatórios ausentes.");
//            }
//
//            // Criando a venda
//            $venda = $this->vendas::create([
//                "codigo_venda" => $dados["codigo_venda"],
//                "loja_id" => $dados["loja_id"],
//                "valor_total" => $dados["valor_total"],
//                "cliente_id" => $dados["clienteModel"]["id"] != 0 ? $dados["clienteModel"]["id"] : null,
//                "usuario_id" => $dados["usuario_id"] ?? null,
//                "tipo_venda_id" => $dados["tipoEntregaCliente"],
//               "forma_entrega_id" => $dados["forma_entrega_id"],
//                "created_at" => $dados["data"],
//            ]);
//
//            if (!$venda) {
//                throw new \Exception('Erro ao salvar a venda.');
//            }
//            foreach ($dados["produtos"] as $produto) {
//                $produtoVenda = $this->vendasProdutos::create([
//                    "venda_id" => $venda->id,
//                    "codigo_produto" => $produto["codigo_produto"],
//                    "descricao" => $produto["descricao"],
//                    "valor_produto" => $produto["valor_produto"],
//                    "quantidade" => $produto["quantidade"],
//                    "troca" => $produto["troca"] ?? false,
//                    "fornecedor_id" => $produto["fornecedor_id"] ?? null,
//                    "categoria_id" => $produto["categoria_id"] ?? null,
//                    "variacao_id" => $produto["variacao_id"] ?? null
//                ]);
//
//                // Se for troca
//                if (!empty($produto['troca']) && !empty($dados['venda_id_original'])) {
//
//                    $troca = $this->vendasTroca::updateOrCreate(
//                        ['venda_id_original' => $dados['venda_id_original']],
//                        [
//                            'nova_venda_id' => $venda->id,
//                            'valor_total_troca' => $dados["valor_total"]
//                        ]
//                    );
//
//                    $this->vendasProdutosTroca::create([
//                        "troca_id" => $troca->id,
//                        "produto_id" => $produtoVenda->id,
//                        "codigo_produto" => $produto["codigo_produto"],
//                        "descricao" => $produto["descricao"],
//                        "valor_produto" => $produto["valor_produto"],
//                        "quantidade" => $produto["quantidade"],
//                    ]);
//
//                    $this->vendasProdutos::where('venda_id', $dados['venda_id_original'])
//                        ->where('codigo_produto', $produto['codigo_produto'])
//                        ->update([
//                            'troca' => true,
//                            'descricao' => DB::raw("CONCAT(descricao, ' (Trocado)')")
//                        ]);
//
//                    // Repor no estoque o produto devolvido
//                    $variacao = $this->productVariation::where('id', $produto["variacao_id"])->first();
//                    $data["antes"] = $variacao->quantidade;
//                    if ($variacao) {
//                        $variacao->increment('quantidade', $produto["quantidade"]);
//                    }
//                }else {
//                    // Baixa no estoque (Venda normal)
//                    $variacao = $this->productVariation::where('id', $produto["variacao_id"])->first();
//                    $data["antes"] = $variacao->quantidade;
//                    $variacao->decrement('quantidade', $produto["quantidade"]);
//                }
//
//                /**
//                 *Salva a movimentação do estoque
//                 */
//                //$data["antes"] = $variacao->quantidade;
//                $data["movimentada"] = $produto["quantidade"];
//                $data["depois"] =  $data["antes"] - $data["movimentada"];
//
//                $this->movimentacaoSaida($produto,$venda,$data);
//            }
//
//            // Processa pagamentos
//            foreach ($dados["pagamentos"] as $pagamento) {
//                $this->tipoPagamento::create([
//                    "venda_id" => $venda->id,
//                    "forma_pagamento_id" => $pagamento["id"],
//                    "valor_pgto" => $pagamento["valor_pagamento"],
//                    "taxa" => $this->buscaTaxa($pagamento["id"])
//                ]);
//            }
//
//            // Registra desconto
//            $this->vendasDescontos::create([
//                "venda_id" => $venda->id,
//                "valor_percentual" => $dados["desconto"]["percentual"] ?? 0,
//                "valor_recebido" => $dados["desconto"]["valor_recebido"] ?? 0,
//                "valor_desconto" => $dados["desconto"]["valor_desconto"] ?? 0,
//            ]);
//
//            // Registra cashback
//            if (!empty($dados["clienteModel"]["id"]) && $dados["clienteModel"]["id"] != 0) {
//                $cashbackUsado = $dados["clienteModel"]["cashback"] ?? 0;
//                if ($cashbackUsado > 0) {
//                    $this->cashbackVendas->where('cliente_id', $dados["clienteModel"]["id"])->update(['status' => 1]);
//                }
//
//                $valorCashback = ($venda->valor_total * 0.08)/100;
//                if ($valorCashback > 0) {
//                    $this->cashbackVendas->create([
//                        "cliente_id" => $venda->cliente_id,
//                        "venda_id" => $venda->id,
//                        "valor" => $valorCashback
//                    ]);
//                }
//            }
//
//            DB::commit();
//
//            return response()->json([
//                'success' => true,
//                'message' => 'Venda registrada com sucesso.',
//                'venda_id' => $venda->id
//            ], 201);
//
//        } catch (\Throwable $e) {
//            DB::rollBack();
//
//            // Tenta capturar código de erro SQL, se existir
//            $sqlErrorCode = null;
//            if (property_exists($e, 'errorInfo') && is_array($e->errorInfo) && isset($e->errorInfo[1])) {
//                $sqlErrorCode = $e->errorInfo[1]; // Código MySQL (ex: 1452)
//            } else {
//                $sqlErrorCode = $e->getCode(); // Pode retornar 0 ou um código genérico
//            }
//
//            // Evita duplicidade de logs para o mesmo erro e venda
//            $jaExiste = $this->errorLogs
//                ->where('codigo_venda', $dados['codigo_venda'])
//                ->where('codigo_erro', $sqlErrorCode)
//                ->exists();
//
//            if (!$jaExiste) {
//                $this->errorLogs->create([
//                    'codigo_venda' => $dados['codigo_venda'] ?? null,
//                    'codigo_erro' => $sqlErrorCode,
//                    'mensagem'     => $e->getMessage(),
//                    'dados'        => json_encode($dados),
//                    'created_at'   => now(),
//                    'updated_at'   => now()
//                ]);
//            }
//            return Response::json([
//                'success' => false,
//                'message' => 'Erro ao processar venda. O problema foi registrado.'
//            ], 500);
//        }
//    }

    /**
     * Display the specified resource.
     *
     * @param Vendas
     * @return JsonResponse
     */
    public function show()
    {
        $json = json_decode($this->request->header('json'));
        //  dd($json);

        $total = count($json);
        $ret = [];

        for ($i = 0; $i < $total; $i++) {
            $data = DB::table('loja_produtos_variacao')->where('loja_produtos_variacao.subcodigo', '=', $json[$i]->codigo_produto)->first();

            //$product = $this->produtoQuantidade::where('produto_id', '=', $data->id)->where('loja_id', '=', $json[$i]->loja_id)->first();

            //Quantidade vendida maior que estoque, erro
            if($json[$i]->quantidade > $data->quantidade){
                array_push($ret, [
                    'codigo_produto'   => $data->subcodigo,
                    'message'   =>  'Produto ['.$data->variacao .'] acima da quantidade em estoque, TOTAL [ '.$data->quantidade.' ]',
                ]);
            }
        }
        //$produtos = json_encode($saida);
        if(count($ret) > 0)
            return Response::json(array('success' => false,'produtos'=> $ret), 400);


        return Response::json(array('success' => true), 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return void
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return void
     */
    public function update()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id_venda
     * @return JsonResponse
     * ao passar o id da venda pega por injeção a venda
     */
    public function destroy(int $id_venda)
    {
        try {
            //Pega a venda pelo codigo
            $vendas = $this->vendas::where('id',$id_venda)->first();
            if($vendas) {

                //Cria a model
                $this->vendas->id = $vendas->id;

                //retorna os dados da venda com o produto
                $quantityProduct = $this->vendas->quantityProduct()->get();

                //atualiza a tabela com as quatidades retornadas
                if ($quantityProduct) {
                    foreach ($quantityProduct as $key => $value) {
                        //Pega o produto para retornar o ID
                        //$product  = $this->product::where('codigo_produto', $quantityProduct[$key]['codigo_produto'])->first();

                        //Retorna produto da variação
                        $product = $this->productVariation::where('subcodigo', $quantityProduct[$key]['codigo_produto'])->first();

                        //Pega a quantidade em estoque e soma com a que foi vendida
                        $qtd = $product->quantidade + $value->quantidade;

                        $this->productVariation::where('subcodigo', $quantityProduct[$key]['codigo_produto'])
                            ->update(['quantidade' => $qtd]);

                        // dd($this->productVariation);
                        /* $productUpdate =  DB::table('loja_produtos_quantidade')
                             ->where('loja_produtos_quantidade.produto_id', '=', $product->id)
                             ->where('loja_produtos_quantidade.loja_id', '=', $venda->loja_id)
                             ->select('loja_produtos_quantidade.quantidade as qtdestoque')
                             ->first();

                         $qtd = $productUpdate->qtdestoque + $value->quantidade;
                         $affected = DB::table('loja_produtos_quantidade')
                             ->where('loja_produtos_quantidade.produto_id', '=',$product->id)
                             ->where('loja_produtos_quantidade.loja_id', '=', $venda->loja_id)
                             ->update(['loja_produtos_quantidade.quantidade' => $qtd]);*/
                    }
                }


                //$delete = $vendas->delete();

                if($vendas->delete())
                    return Response::json(array('success' => true, "message" => 'Venda deletada com sucesso!'), 200, [],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            }else{
                return Response::json(array('success' => false, "message" => 'Venda não localizada: [' . $id_venda .']'), 400, [],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

        }catch (Throwable $e){
            return Response::json(array('success' => false, "message" => $e->getMessage()), 500, [],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

    }

    /**
     * Nova função para o novo PDV WEB
     */

    public function getProducts(){

        try {

            $request = $this->request->get('term');
            // print_r($request);

            $products = DB::table('loja_produtos_variacao')
                ->join('loja_produtos_new','loja_produtos_variacao.products_id','=','loja_produtos_new.id')
                ->leftJoin('loja_produtos_imagens', 'loja_produtos_imagens.produto_variacao_id', '=', 'loja_produtos_variacao.id')
                ->where('loja_produtos_variacao.subcodigo', 'LIKE', "%$request%")
                ->orWhere('loja_produtos_variacao.variacao', 'LIKE', "%$request%")
                ->get();

            if(count($products) ==0)
                return [[ 'label' => "PRODUTO NÃO LOCALIZADO!", 'value' => $request]];

            $arr = null;
            foreach ($products as $key => $product){
                $arr[] = ['label' => $product->variacao ,
                    'value' => $product->subcodigo,
                    'image' => $product->path,
                    'product' => $product->descricao];
            }


            /*$variations = DB::table('loja_produtos_variacao')
                ->leftJoin('loja_produtos_imagens', 'loja_produtos_imagens.produto_variacao_id', '=', 'loja_produtos_variacao.id')
                ->where('loja_produtos_variacao.subcodigo', '=',  $request)->first();*/

            return Response::json($arr)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                ->header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
        } catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage(), 'code' => 500), 500);
        }
    }

    /**
     * Salva itens da venda
     **/
    public function saveProductSale() {
        try {
            // Validação de dados
            $validated = $this->request->validate([
                'user_id' => 'required|integer',
                'codigo_produto' => 'required|integer',
                'variacao_id' => 'required|integer',
                'descricao' => 'required|string',
                'valor_venda' => 'required|numeric',
                'quantidade' => 'required|integer',
                'imagem' => 'nullable|string',
                'status' => 'required|string'
            ]);

            // Buscar produto no carrinho e suas informações reais
            $cart = Carts::with('variations.images')
                ->where([
                    ['user_id', '=', $validated['user_id']],
                    ['codigo_produto', '=', $validated['codigo_produto']],
                    ['status', '=', $validated['status']]
                ])->first();
            //return response()->json(['success' => true, 'data' => $cart], 200);

            // Se o produto já estiver no carrinho, somar a quantidade
            if ($cart) {
                $cart->quantidade = intval($cart->quantidade) + intval($validated['quantidade']);
                $cart->save();
                $msg = "Produto atualizado com sucesso!";
            } else {
                // Se o produto não estiver no carrinho, criar um novo registro
                Carts::create([
                    'user_id' => $validated['user_id'],
                    'produto_variation_id' => $validated['variacao_id'],
                    'codigo_produto' => $validated['codigo_produto'],
                    'name' => $validated['descricao'],
                    'price' => $validated['valor_venda'],
                    'quantidade' => $validated['quantidade'], // quantidade passada diretamente
                    'imagem' => $validated['imagem'],
                    'status' => $validated['status']
                ]);
                $msg = "Produto adicionado com sucesso!";
            }

            return response()->json(['success' => true, 'message' => $msg], 200);

        }catch (ValidationException $e) {
            // Captura a exceção de validação e retorna o erro em formato JSON
            return response()->json([
                'success' => false,
                'message' => 'Erro: A validação falhou.',
                'errors' => $e->errors() // Detalhes dos erros de validação
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()], 500);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Erro: ' . $e->getMessage()], 500);
        }
    }


    /**
     * @return JsonResponse
     */
    public function carts(){
        try {

            $user_id =  $this->request->input('user_id');
            // $cliente_id =  $this->request->input('cliente_id');
            $status =  $this->request->input('status');

            $carts = Carts::with('variations','clientes','usuario','cashback')
                ->where('user_id', $user_id)
                ->whereIn('status', $status)
                ->orderBy('id','desc')
                ->get();

            if (!$carts->isEmpty())
                return Response::json(['success' => true,'message' => "sucesso", 'data' => $carts], 200,[],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            else
                return Response::json(['success' => false,'message' => "Carrinho cliente não localizado! ", 'data' => $carts], 201,[],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        } catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage(), 'code' => 500), 500);
        }
    }

    public function getItemsCart(){
        try {

        } catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage(), 'code' => 500), 500);
        }
    }

    //pega o valor da taxa e associa ao tipo de
    // pagmaento da venda, para no futuro alterar a taxa não influenciar nos relatórios
    public function buscaTaxa(int $paymentId): ?float
    {
        return Cache::remember("taxa_cartao_{$paymentId}", 60, function () use ($paymentId) {
            return optional($this->taxaCartao::select('valor_taxa')
                ->where('forma_id', $paymentId)
                ->first())->valor_taxa;
        });
    }

}
