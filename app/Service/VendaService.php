<?php


namespace App\Service;

use Throwable;
use App\Http\Models\ProdutoVariation;
use App\Http\Models\TaxaCartao;
use App\Http\Models\Vendas;
use App\Http\Models\VendasProdutos;
use App\Http\Models\VendasProdutosDesconto;
use App\Http\Models\VendasProdutosTipoPagamento;
use App\Http\Models\VendasCashBack;
use App\Http\Models\ErrorLogs;
use App\Http\Models\VendasProdutosTroca;
use App\Http\Models\VendasTroca;
use App\Traits\MovimentacaoTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Exception;

class VendaService
{
    use MovimentacaoTrait;

    public function criarVenda(array $data)
    {
        return Vendas::create([
            "codigo_venda"     => $data["codigo_venda"],
            "loja_id"          => $data["loja_id"],
            "valor_total"      => $data["valor_total"],
            "cliente_id"       => $data["clienteModel"]["id"] != 0 ? $data["clienteModel"]["id"] : null,
            "usuario_id"       => $data["usuario_id"] ?? null,
            "tipo_venda_id"    => $data["tipoEntregaCliente"],
            "forma_entrega_id" => $data["forma_entrega_id"],
            "created_at"       => $data["data"],
        ]);
    }


    public function processarProdutosVenda(array $products, Vendas $venda, array $dados)
    {
        foreach ($products as $product) {
            $produtoVenda = VendasProdutos::create([
                'venda_id' => $venda->id,
                'codigo_produto' => $product['codigo_produto'],
                'descricao' => $product['descricao'],
                'valor_produto' => $product['valor_produto'],
                'quantidade' => $product['quantidade'],
                'troca' => $product['troca'] ?? false,
                'fornecedor_id' => $product['fornecedor_id'] ?? null,
                'categoria_id' => $product['categoria_id'] ?? null,
                'variacao_id' => $product['variacao_id'] ?? null,
            ]);

            $variacao = ProdutoVariation::find($product['variacao_id']);
            $quantidade = $product['quantidade'];
            $data = [];

            if (!empty($product['troca']) && isset($dados['venda_id_original'])) {
                // Troca
                $troca = VendasTroca::updateOrCreate(
                    ['venda_id_original' => $dados['venda_id_original']],
                    [
                        'nova_venda_id' => $venda->id,
                        'valor_total_troca' => $venda->valor_total
                    ]
                );

                VendasProdutosTroca::create([
                    'troca_id' => $troca->id,
                    'produto_id' => $produtoVenda->id,
                    'codigo_produto' => $product['codigo_produto'],
                    'descricao' => $product['descricao'],
                    'valor_produto' => $product['valor_produto'],
                    'quantidade' => $product['quantidade'],
                ]);

                VendasProdutos::where('venda_id', $dados['venda_id_original'])
                    ->where('codigo_produto', $product['codigo_produto'])
                    ->update([
                        'troca' => true,
                        'descricao' => DB::raw("CONCAT(descricao, ' (Trocado)')")
                    ]);

                if ($variacao) {
                    $data['antes'] = $variacao->quantidade;
                    $variacao->increment('quantidade', $quantidade);
                }

            } else {
                // Venda normal
                if ($variacao) {
                    $data['antes'] = $variacao->quantidade;
                    $variacao->decrement('quantidade', $quantidade);
                }
            }

            // Registro de movimentação
            if ($variacao) {
                try {
                    $data['movimentada'] = $quantidade;
                    $data['depois'] = $data['antes'] - $quantidade;
                    $this->movimentacaoSaida($product, $venda, $data);
                } catch (Throwable $e) {
                    throw new Exception("Registro de movimentação." . $e->getMessage());
                }
            }
        }
    }


    public function processarPagamentos(array $pagamentos, int $vendaId)
    {
        foreach ($pagamentos as $pagamento) {
            VendasProdutosTipoPagamento::create([
                "venda_id" => $vendaId,
                "forma_pagamento_id" => $pagamento["id"],
                "valor_pgto" => $pagamento["valor_pagamento"],
                "taxa" => $this->buscaTaxa($pagamento["id"]),
            ]);
        }
    }

    public function registrarDesconto(array $desconto, int $vendaId)
    {
        VendasProdutosDesconto::create([
            "venda_id" => $vendaId,
            "valor_percentual" => $desconto["percentual"] ?? 0,
            "valor_recebido"   => $desconto["valor_recebido"] ?? 0,
            "valor_desconto"   => $desconto["valor_desconto"] ?? 0,
        ]);
    }

    public function registrarCashback(array $cliente, $venda)
    {
        if (!empty($cliente["id"]) && $cliente["id"] != 0) {
            $cashbackUsado = $cliente["cashback"] ?? 0;

            if ($cashbackUsado > 0) {
                VendasCashBack::where('cliente_id', $cliente["id"])->update(['status' => 1]);
            }

            $valorCashback = ($venda->valor_total * 0.08) / 100;

            if ($valorCashback > 0) {
                VendasCashBack::create([
                    "cliente_id" => $venda->cliente_id,
                    "venda_id" => $venda->id,
                    "valor" => $valorCashback
                ]);
            }
        }
    }

    public function logarErro(string $codigoVenda, \Throwable $e, array $dados)
    {
        $sqlErrorCode = null;

        if (property_exists($e, 'errorInfo') && is_array($e->errorInfo) && isset($e->errorInfo[1])) {
            $sqlErrorCode = $e->errorInfo[1];
        } else {
            $sqlErrorCode = $e->getCode();
        }

        $jaExiste = ErrorLogs::where('codigo_venda', $codigoVenda)
            ->where('codigo_erro', $sqlErrorCode)
            ->exists();

        if (!$jaExiste) {
            ErrorLogs::create([
                'codigo_venda' => $codigoVenda,
                'codigo_erro' => $sqlErrorCode,
                'mensagem' => $e->getMessage(),
                'dados' => json_encode($dados),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    //pega o valor da taxa e associa ao tipo de
    // pagmaento da venda, para no futuro alterar a taxa não influenciar nos relatórios
    public function buscaTaxa(int $paymentId): ?float
    {
        return Cache::remember("taxa_cartao_{$paymentId}", 60, function () use ($paymentId) {
            return optional(TaxaCartao::select('valor_taxa')
                ->where('forma_id', $paymentId)
                ->first())->valor_taxa;
        });
    }
}
