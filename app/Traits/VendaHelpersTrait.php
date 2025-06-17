<?php

namespace App\Traits;

use App\Http\Models\MovimentacaoEstoque;
use App\Http\Models\ProdutoVariation;

trait VendaHelpersTrait
{
    public function baixarEstoque(array $produto): array
    {
        $variacao = ProdutoVariation::find($produto["variacao_id"]);

        if (!$variacao) {
            throw new \Exception("Variação de produto não encontrada.");
        }

        $antes = $variacao->quantidade;
        $variacao->decrement("quantidade", $produto["quantidade"]);

        return [
            "antes" => $antes,
            "movimentada" => $produto["quantidade"],
            "depois" => $antes - $produto["quantidade"]
        ];
    }

    public function reporEstoque(array $produto): array
    {
        $variacao = ProdutoVariation::find($produto["variacao_id"]);

        if (!$variacao) {
            throw new \Exception("Variação de produto não encontrada.");
        }

        $antes = $variacao->quantidade;
        $variacao->increment("quantidade", $produto["quantidade"]);

        return [
            "antes" => $antes,
            "movimentada" => $produto["quantidade"],
            "depois" => $antes + $produto["quantidade"]
        ];
    }

    public function registrarMovimentacao(array $produto, $venda, array $dadosEstoque): void
    {
        // Chame aqui o que for necessário para registrar a movimentação, ex:
        MovimentacaoEstoque::create([
            'produto_id' => $produto['codigo_produto'],
            'venda_id' => $venda->id,
            'quantidade' => $dadosEstoque['movimentada'],
            'antes' => $dadosEstoque['antes'],
            'depois' => $dadosEstoque['depois'],
        ]);
    }
}
