<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Http\Models\Produto;
use App\Http\Models\ProdutoVariation;
use App\Http\Models\Fornecedor;
use App\Http\Models\Categoria;


class ProdutoEditar extends Component
{
    public $produto;
    public $variacoes = [];
    public $fornecedores = [];

    public $produtoId;

    public function mount(ProdutoVariation $produto)
    {

        $this->produto = $produto->produtoPai; // acessa o pai pela relação
        dd($this->produto);
        $this->variacoes = [
            [
                'id' => $produto->id,
                'subcodigo' => $produto->subcodigo,
                'variacao' => $produto->variacao,
                'quantidade' => $produto->quantidade,
                'valor_varejo' => $produto->valor_varejo,
                'fornecedor_id' => $produto->fornecedor_id,
            ]
        ];

        $this->fornecedores = Fornecedor::select('id', 'nome')
            ->where('status', 1)
            ->get()
            ->toArray();
    }

    public function salvar()
    {
        $produto = Produto::findOrFail($this->produtoId);
        $produto->descricao = $this->produto['descricao'] ?? '';
        $produto->status = $this->produto['status'] ?? 0;
        $produto->save();

        foreach ($this->variacoes as $dados) {
            $variacao = ProdutoVariation::find($dados['id']);
            if ($variacao) {
                $variacao->variacao = $dados['variacao'] ?? '';
                $variacao->quantidade = $dados['quantidade'] ?? 0;
                $variacao->valor_varejo = $dados['valor_varejo'] ?? 0;
                $variacao->fornecedor_id = $dados['fornecedor_id'] ?? null;
                $variacao->save();
            }
        }

        session()->flash('success', 'Produto e variações atualizados com sucesso!');
    }

    public function render()
    {
        return view('livewire.produto-editar')->layout('layouts.layout');
    }
}
