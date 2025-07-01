<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Models\ProdutoVariation;

class ProdutoVariacaoController extends Controller
{
    public function update(Request $request, $id)
    {
        $variacao = ProdutoVariation::findOrFail($id);

        // Atualiza nome e valor, se enviados
        $variacao->variacao = $request->input('variacao', $variacao->variacao);
        $variacao->valor_varejo = $request->input('valor_varejo', $variacao->valor_varejo);

        // Atualiza quantidade dependendo do botão clicado
        $quantidadeAtual = $variacao->quantidade ?? 0;
        $quantidadeInput = (int) $request->input('quantidade', $quantidadeAtual);

        switch ($request->input('operacao')) {
            case 'adicionar':
                $variacao->quantidade = $quantidadeAtual + 1;
                break;

            case 'subtrair':
                $variacao->quantidade = max(0, $quantidadeAtual - 1);
                break;

            default:
                //Só somar se o valor digitado for diferente do que já está no banco. Ou seja:
                if ($quantidadeInput > 0 && $quantidadeInput !== $quantidadeAtual) {
                    $variacao->quantidade = $quantidadeAtual + $quantidadeInput;
                }
                break;
        }

        $variacao->save();

        return redirect()->back()->with('success', 'Variação atualizada com sucesso!');
    }


    public function edit(ProdutoVariation $variacao)
    {
        return view('admin.produtos.editar', [
            'produtoId' => $variacao->id
        ]);
    }

}
