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
use App\Http\Models\VendasProdutosTipoPagamento;
use App\Http\Models\VendasProdutosValorCartao;
use App\Http\Models\VendasProdutosValorDupla;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;


class VendaController extends Controller
{
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
                                ErrorLogs $errorLogs){
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
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
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
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create()
    {

        try {
            $pro = [];
            $code_store = $this->request->header('store-id');

            /**
            PEGA A ÚLTIMA VENDA DA LOJA ESPECIFICA
             */
            $store =  DB::table('loja_vendas')->where('loja_id',$code_store)->orderBy('id', 'DESC')->first();

            if( $store != null) {

                $total_store = $store->valor_total;
                $code_store = $store->codigo_venda; //Pega o código venda KNxxx

                $store = DB::table('loja_vendas')
                    ->join('loja_vendas_produtos', 'loja_vendas.id', '=', 'loja_vendas_produtos.venda_id')
                    ->where('loja_vendas.codigo_venda', '=', $code_store)
                    ->select('loja_vendas.*', 'loja_vendas_produtos.quantidade',
                        'loja_vendas_produtos.descricao',
                        'loja_vendas_produtos.codigo_produto',
                        'loja_vendas_produtos.valor_produto')->get();

                if (count($store) > 0) {
                    //Desconto
                    $discount = DB::table('loja_vendas')
                        ->join('loja_vendas_produtos_descontos', 'loja_vendas.id', '=', 'loja_vendas_produtos_descontos.venda_id')
                        ->where('loja_vendas.codigo_venda', '=', $code_store)
                        ->select('loja_vendas_produtos_descontos.valor_desconto',
                            'loja_vendas_produtos_descontos.valor_percentual',
                            'loja_vendas_produtos_descontos.valor_recebido',
                            DB::raw('loja_vendas_produtos_descontos.valor_recebido - loja_vendas.valor_total as valor_troco'),
                            DB::raw('loja_vendas.valor_total + loja_vendas_produtos_descontos.valor_desconto as sub_total'))->first();

                    //Forma de pagamento
                    $payment = DB::table('loja_vendas')
                        ->join('loja_vendas_produtos_tipo_pagamentos', 'loja_vendas.id', '=', 'loja_vendas_produtos_tipo_pagamentos.venda_id')
                        ->join('loja_forma_pagamentos', 'loja_forma_pagamentos.id', '=', 'loja_vendas_produtos_tipo_pagamentos.forma_pagamento_id')
                        ->where('loja_vendas.codigo_venda', '=', $code_store)
                        //->select('loja_forma_pagamentos.id','loja_forma_pagamentos.nome')->first();
                        ->select('loja_forma_pagamentos.id', 'loja_forma_pagamentos.nome')->get();

                    $clienteCashBack = DB::table('loja_vendas')
                        ->leftJoin('loja_vendas_cashback', 'loja_vendas.id', '=',  'loja_vendas_cashback.venda_id')
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
            }

        } catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage(), 'code' => 500), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * SALVA A VENDA alterado para a possibilidade de venda offline
     * @return JsonResponse
     */
    /*public function store()
    {
        DB::beginTransaction();

        try {
            $requestAll  = $this->request->all();

           // dd($this->request->all());

            // Validação detalhada
            $erros = [];

            // Verificar se "codigo_venda" está vazio
            if (empty($requestAll['codigo_venda'])) {
                $erros[] = "O campo 'codigo_venda' é obrigatório e não pode estar vazio.";
            }

            // Verificar se "produtos" está definido e não vazio
            if (!isset($requestAll['produtos']) || count($requestAll['produtos']) === 0) {
                $erros[] = "O campo 'produtos' é obrigatório e deve conter pelo menos um item.";
            }

            // Verificar outros campos, se necessário
            if (empty($requestAll['loja_id'])) {
                $erros[] = "O campo 'loja_id' é obrigatório.";
            }

            // Retornar erro se houver problemas
            if (!empty($erros)) {
                throw new \Exception('Erro nos dados enviados: ' . implode(' | ', $erros));
            }

            //Salvo a venda
            $sale = $this->vendas->create(["codigo_venda" =>  $requestAll["codigo_venda"],
                                           "loja_id" =>  $requestAll["loja_id"],
                                           "valor_total" =>  $requestAll["valor_total"],
                                            "usuario_id" => $requestAll["usuario_id"] ?? 3,
                                            "cliente_id" =>  $requestAll["clienteModel"]["id"] !== 0 ? $requestAll["clienteModel"]["id"] : null,
                                            "tipo_venda_id" => $requestAll["tipoEntregaCliente"]]);

            if (!$sale) {
                throw new \Exception('Erro ao salvar venda.');
            }


            // Processa os produtos da venda
            foreach ($requestAll["produtos"] as $produto) {
                $this->vendasProdutos->create([
                    "venda_id" => $sale->id,
                    "codigo_produto" => $produto["codigo_produto"],
                    "descricao" => $produto["descricao"],
                    "valor_produto" => $produto["valor_produto"],
                    "quantidade" => $produto["quantidade"],
                    "troca" => $produto["troca"],
                    "fornecedor_id" => $produto["fornecedor_id"],
                    "categoria_id" => $produto["categoria_id"]
                ]);

                // Atualiza estoque
                $productVariation = $this->productVariation
                    ->where('products_id', $produto["id"])
                    ->where('subcodigo', $produto["codigo_produto"])
                    ->where('status', true)
                    ->first();

                if (!$productVariation) {
                    throw new \Exception("Produto não encontrado: {$produto['codigo_produto']}");
                }

                if (!$produto["troca"]) {
                    if ($productVariation->quantidade < $produto["quantidade"]) {
                        throw new \Exception("Estoque insuficiente para o produto: {$produto['codigo_produto']}");
                    }
                    $productVariation->quantidade -= $produto["quantidade"];
                } else {
                    $productVariation->quantidade += $produto["quantidade"];
                }

                $productVariation->save();
            }


            // Salva desconto, valor recebido e percentual
            $this->vendasDescontos->create([
                "venda_id" => $sale->id,
                "valor_desconto" => $requestAll["valor_desconto"],
                "valor_recebido" => $requestAll["valor_recebido"],
                "valor_percentual" => $requestAll["percentual"]
            ]);

            // Salva os tipos de pagamento
            foreach ($requestAll["listTipoPagamento"] as $index => $pagamento) {
                $this->tipoPagamento->create([
                    "venda_id" => $sale->id,
                    "forma_pagamento_id" => $pagamento["id"],
                    "valor_pgto" => $requestAll["listValorRecebido"][$index],
                    "taxa" => $this->buscaTaxa($pagamento["id"])
                ]);
            }

            // Processa cashback
            if ($sale->cliente_id) {
                $cashbackUsado = $requestAll["clienteModel"]["cashback"] ?? 0;
                if ($cashbackUsado > 0) {
                    $this->cashbackVendas
                        ->where('cliente_id', $sale->cliente_id)
                        ->update(['status' => 1]);
                }

                $taxaCashback = 0.05; // Taxa padrão
                $valorCashback = ($sale->valor_total * $taxaCashback);

                if ($valorCashback > 0) {
                    $this->cashbackVendas->create([
                        "cliente_id" => $sale->cliente_id,
                        "venda_id" => $sale->id,
                        "valor" => $valorCashback
                    ]);
                }
            }

            DB::commit();

            //Pega o total de produtos no array
            //$total = count($dados["produtos"]);
            //$totalPayment = count($dados["listTipoPagamento"]);

            //Salva os produtos da venda
 //           if ($sale->exists) {
//                for ($i = 0; $i < $total; $i++) {
//                    $this->vendasProdutos = new VendasProdutos();
//                    $this->vendasProdutos->venda_id = $sale->id;
//                    $this->vendasProdutos->codigo_produto = $dados["produtos"][$i]["codigo_produto"];
//                    $this->vendasProdutos->descricao = $dados["produtos"][$i]["descricao"];
//                    $this->vendasProdutos->valor_produto = $dados["produtos"][$i]["valor_produto"];
//                    $this->vendasProdutos->quantidade = $dados["produtos"][$i]["quantidade"];
//                    $this->vendasProdutos->troca = $dados["produtos"][$i]["troca"];
//                    $this->vendasProdutos->fornecedor_id = $dados["produtos"][$i]["fornecedor_id"];
//                    $this->vendasProdutos->categoria_id = $dados["produtos"][$i]["categoria_id"];
//                    $this->vendasProdutos->save();
//                }

                //Salva o desconto, valor percentual e valor recebido da venda
//                $this->vendasDescontos = new VendasProdutosDesconto();
//                $this->vendasDescontos->venda_id = $sale->id;
//                $this->vendasDescontos->valor_desconto = $dados["valor_desconto"];
//                $this->vendasDescontos->valor_recebido = $dados["valor_recebido"];
//                $this->vendasDescontos->valor_percentual = $dados["percentual"];
//                $this->vendasDescontos->save();


//                for ($i = 0; $i < $totalPayment; $i++) {
//                    $this->tipoPagamento = new VendasProdutosTipoPagamento();
//                    $this->tipoPagamento->venda_id = $sale->id;
//                    $this->tipoPagamento->forma_pagamento_id = $dados["listTipoPagamento"][$i]["id"];
//                    $this->tipoPagamento->valor_pgto = $dados["listValorRecebido"][$i];
//                    $this->tipoPagamento->taxa = $this->buscaTaxa($dados["listTipoPagamento"][$i]["id"]);
//                    $this->tipoPagamento->save();
//                }

                //Realizar baixa do produto
//                for ($i = 0; $i < $total; $i++) {
//                    $id = $dados["produtos"][$i]["id"]; // id do produto pai
//                    $sub_codigo = $dados["produtos"][$i]["codigo_produto"]; // subcodigo do produto
//                    //$loja_id = $dados["loja_id"]; //id da loja
//
//                    $productVariation = $this->productVariation
//                        ->where('products_id', '=', $id)
//                        ->where('subcodigo', '=', $sub_codigo)
//                        ->select('id', 'quantidade')->first();
//
//                    if ($dados["produtos"][$i]["troca"] === false) {
//                        $productVariation->quantidade -= $dados["produtos"][$i]["quantidade"];
//                    } else {
//                        $productVariation->quantidade += $dados["produtos"][$i]["quantidade"];
//                    }
//
//                    $affected = $productVariation->save();
//                }

                //Salva valor cashback
//                if ($sale->cliente_id) {
//                    //Se tiver valor de cashback, entendo que foi usado, seta status true
//                    if ($dados["clienteModel"]["cashback"] > 0) {
//                        $this->cashbackVendas
//                            ->where('cliente_id', '=', $sale->cliente_id)
//                            ->update(['status' => 1]);
//                    }
//
//                    $cashbacks = $this->cashback::all();
//                    $taxa = 0.05;
//                    foreach ($cashbacks as $valor) {
//                        if ($valor->valor < $sale->valor_total) {
//                            $taxa = $valor->taxa;
//                        }
//                    }
//                    $valor_cashback = ($sale->valor_total * $taxa) / 100;
//
//                    //Salva o cashback caso tenha valor acima de 0
//                    if ($valor_cashback > 0) {
//                        $this->cashbackVendas = new VendasCashBack();
//                        $this->cashbackVendas->cliente_id = $sale->cliente_id;
//                        $this->cashbackVendas->venda_id = $sale->id;
//                        $this->cashbackVendas->valor = $valor_cashback;
//                        $this->cashbackVendas->save();
//                    }
//
//                }
//
//                if ($affected > 0) {
//                    return Response::json(array('success' => true), 200);
//                } else {
//                    return Response::json(array('success' => false, 'message' => 'Ocorreu um erro no fechamento da venda!!'), 400);
//                }
//            }
            return Response::json(['success' => true,'message' => 'Venda processada com sucesso.'], 200);

    } catch (Throwable $e) {
        //return Response::json(array('success' => false, 'message' => $e->getMessage(), 'cod_retorno' => 500), 500);
            DB::rollback();

            // Salva o erro na tabela
            $this->errorLogs->create([
                'codigo_venda' => $requestAll['codigo_venda'] ?? null,
                'mensagem' => $e->getMessage(),
                'dados' => json_encode($requestAll),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Responde ao cliente
            return Response::json([
                'success' => false,
                'message' => 'Erro ao processar venda. O problema foi registrado para análise.'
            ], 500);
    }
}
*/
    public function store()
    {

        DB::beginTransaction();
        try {
            $dados = $this->request->all();

            // Validação detalhada
            $erros = [];

            // Verificar se "codigo_venda" está vazio
            if (empty($dados['codigo_venda'])) {
                $erros[] = "O campo 'codigo_venda' é obrigatório e não pode estar vazio.";
            }

            // Verificar se "produtos" está definido e não vazio
            if (!isset($dados['produtos']) || count($dados['produtos']) === 0) {
                $erros[] = "O campo 'produtos' é obrigatório e deve conter pelo menos um item.";
            }

            // Verificar outros campos, se necessário
            if (empty($dados['loja_id'])) {
                $erros[] = "O campo 'loja_id' é obrigatório.";
            }

            // Retornar erro se houver problemas
            if (!empty($erros)) {
                throw new \Exception('Erro nos dados enviados: ' . implode(' | ', $erros));
            }

            //monta array com dados da venda
            $dados_venda = [
                "codigo_venda" => $dados["codigo_venda"],
                "loja_id" => $dados["loja_id"],
                "valor_total" => $dados["valor_total"],
                "usuario_id" => $dados["usuario_id"] ?? 3,
                "cliente_id" => $dados["clienteModel"]["id"] !== 0 ? $dados["clienteModel"]["id"] : null,
                "tipo_venda_id" => $dados["tipoEntregaCliente"]
            ];

            // Só adiciona "created_at" se "data" existir
            if (isset($dados["data"])) {
                $dados_venda["created_at"] = $dados["data"];
            }

            // Cria a venda
            $sale = $this->vendas->create($dados_venda);

            if (!$sale) {
                throw new \Exception('Erro ao salvar venda.');
            }

            // Processa os produtos da venda
            foreach ($dados["produtos"] as $produto) {
                $this->vendasProdutos->create([
                    "venda_id" => $sale->id,
                    "codigo_produto" => $produto["codigo_produto"],
                    "descricao" => $produto["descricao"],
                    "valor_produto" => $produto["valor_produto"],
                    "quantidade" => $produto["quantidade"],
                    "troca" => $produto["troca"],
                    "fornecedor_id" => $produto["fornecedor_id"],
                    "categoria_id" => $produto["categoria_id"]
                ]);

                // Atualiza estoque
                $productVariation = $this->productVariation
                    ->where('products_id', $produto["id"])
                    ->where('subcodigo', $produto["codigo_produto"])
                    ->where('status', 1) //true
                    ->first();

                if (!$productVariation) {
                    throw new \Exception("Produto não encontrado: {$produto['codigo_produto']}");
                }

                if (!$produto["troca"]) {
                    if ($productVariation->quantidade < $produto["quantidade"]) {
                        throw new \Exception("Estoque insuficiente para o produto: {$produto['codigo_produto']}");
                    }
                    $productVariation->quantidade -= $produto["quantidade"];
                } else {
                    $productVariation->quantidade += $produto["quantidade"];
                }

                $productVariation->save();
            }

            // Salva os tipos de pagamento
            foreach ($dados["listTipoPagamento"] as $index => $pagamento) {
                $this->tipoPagamento->create([
                    "venda_id" => $sale->id,
                    "forma_pagamento_id" => $pagamento["id"],
                    "valor_pgto" => $dados["listValorRecebido"][$index],
                    "taxa" => $this->buscaTaxa($pagamento["id"])
                ]);
            }

            // Salva desconto, valor recebido e percentual
            $this->vendasDescontos->create([
                "venda_id" => $sale->id,
                "valor_desconto" => $dados["valor_desconto"],
                "valor_recebido" => $dados["valor_recebido"],
                "valor_percentual" => $dados["percentual"]
            ]);

            // Processa cashback
            if ($sale->cliente_id) {
                $cashbackUsado = $dados["clienteModel"]["cashback"] ?? 0;
                if ($cashbackUsado > 0) {
                    $this->cashbackVendas
                        ->where('cliente_id', $sale->cliente_id)
                        ->update(['status' => 1]);
                }

                $taxaCashback = 0.08; // Taxa padrão
                $valorCashback = ($sale->valor_total * $taxaCashback)/100;

                if ($valorCashback > 0) {
                    $this->cashbackVendas->create([
                        "cliente_id" => $sale->cliente_id,
                        "venda_id" => $sale->id,
                        "valor" => $valorCashback
                    ]);
                }
            }

            DB::commit();

            return Response::json([
                'success' => true,
                'message' => 'Venda processada com sucesso.'
            ], 200);


        } catch (Throwable $e) {
            DB::rollback();

            // Salva o erro na tabela
            $this->errorLogs->create([
                'codigo_venda' => $dados['codigo_venda'] ?? null,
                'mensagem' => $e->getMessage(),
                'dados' => json_encode($dados),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Responde ao cliente
            return Response::json([
                'success' => false,
                'message' => 'Erro ao processar venda. O problema foi registrado para análise.'
            ], 500);
        }
    }

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
