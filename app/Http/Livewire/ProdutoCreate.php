<?php

namespace App\Http\Livewire;

use App\Http\Models\Fornecedor;
use App\Http\Models\Produto;
use App\Http\Models\ProdutoImagem;
use App\Http\Models\ProdutoVariation;
use App\Traits\ProdutoTrait;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProdutoCreate extends Component
{

    use WithFileUploads;
    use ProdutoTrait;

    public $produto = [
        'codigo_produto' => '',
        'descricao' => '',
        'valor_produto' => '',
        'status' => 0,
        'gtin' => 0
    ];

    public $variacoes = [];
    public $produtoImages = [];
    public $imagemDestaque = null;
    public $fornecedores = [];

    public function mount()
    {
        $lastCodigo = Produto::max('codigo_produto') ?? 0;
        $this->produto['codigo_produto'] = $lastCodigo + 1;

        $this->fornecedores = Fornecedor::select('id', 'nome')->where('status', 1)->get()->toArray();
    }

    public function adicionarVariacao()
    {
        $seq = count($this->variacoes) + 1;
        $subcodigo = $this->produto['codigo_produto'] . str_pad($seq, 2, '0', STR_PAD_LEFT);

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
    public function salvar()
    {
        $produto = Produto::create($this->produto);

        foreach ($this->variacoes as $v) {
            $variacao = ProdutoVariation::create([
                'produto_id' => $produto->id,
                'subcodigo' => $v['subcodigo'],
                'variacao' => $v['variacao'],
                'quantidade' => $v['quantidade'],
                'valor_varejo' => $v['valor_varejo'],
                'status' => $v['status'],
            ]);

            if (!empty($v['imagens'])) {
                foreach ($v['imagens'] as $imagem) {
                    $path = $imagem->store('produtos', 'public');
                    ProdutoImagem::create([
                        'produto_variation_id' => $variacao->id,
                        'path' => $path,
                    ]);
                }
            }
        }

        if ($this->imagemDestaque) {
            $path = $this->imagemDestaque->store('produtos', 'public');
            $produto->imagem_destaque = $path;
            $produto->save();
        }

        session()->flash('success', 'Produto cadastrado com sucesso!');
        return redirect()->route('produtos.produtos_livewire');
    }

    public function render()
    {
        return view('livewire.produto-create')->layout('layouts.layout');;
    }
}
