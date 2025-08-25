<?php

namespace App\Http\Livewire;

use App\Http\Models\Produto;
use App\Traits\ProdutoTrait;
use Livewire\Component;

class ProdutoVariacoesForm extends Component
{
    use ProdutoTrait;

    public $variacoes = [];
    public $fornecedores = [];
    public $produtoId;
    public $produtoCodigo;
    public $produto = [
        'codigo_produto' => '',
        'descricao' => '',
        'valor_produto' => '',
        'status' => 1,
        'categoria_id' => ''
    ];

    public function mount($variacoes = [], $fornecedores = [], $produtoId = null)
    {
        $lastCodigo = Produto::max('codigo_produto') ?? 0;
        $this->produto['codigo_produto'] = $lastCodigo + 1;

        $this->variacoes = $variacoes;
        $this->fornecedores = $fornecedores;
        $this->produtoId = $produtoId;

    }



    public function adicionarVariacao()
    {
        $seq = count($this->variacoes) + 1;
        $subcodigo = $this->produtoId . str_pad($seq, 2, '0', STR_PAD_LEFT);

        $this->variacoes[] = [
            'subcodigo' => $subcodigo,
            'variacao' => '',
            'quantidade' => 0,
            'valor_varejo' => 0,
            'status' => 1,
            'imagens' => [],
            'validade' => '',
            'fornecedor_id' => ''
        ];
    }

    public function render()
    {
        return view('livewire.produto-variacoes-form');
    }
}
