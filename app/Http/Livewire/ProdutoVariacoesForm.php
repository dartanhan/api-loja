<?php

namespace App\Http\Livewire;

use App\Http\Models\Produto;
use App\Traits\ProdutoTrait;
use Illuminate\Support\Str;
use Livewire\Component;

class ProdutoVariacoesForm extends Component
{
    use ProdutoTrait;

    public $variacoes = [];
    public $fornecedores = [];
    public $produtoId;
    public $codigoPai;
    public $produto = [
        'codigo_produto' => '',
        'descricao' => '',
        'valor_produto' => '',
        'status' => 1,
        'categoria_id' => ''
    ];
    // O filho "ouve" uma chamada para sincronizar e salvar
    protected $listeners = ['updatedVariacoes' => 'updatedVariacoes','syncAndSave' => 'syncAndSave',];

    public function mount($variacoes = [], $fornecedores = [], ?int $produtoId = null)
    {
        $lastCodigo = Produto::max('codigo_produto') ?? 0;
        $this->produto['codigo_produto'] = $lastCodigo + 1;

        $this->variacoes = $variacoes;
        $this->fornecedores = $fornecedores;
        $this->codigoPai = $produtoId;

    }

    public function adicionarVariacao()
    {
        $seq = count($this->variacoes) + 1;
        $subcodigo = $this->codigoPai . str_pad($seq, 2, '0', STR_PAD_LEFT);

        // Gerar ID único (pode ser uuid ou contador incremental)
        $idUnico = Str::uuid()->toString(); // se quiser usar UUID
        // $idUnico = count($this->variacoes) + 1; // se quiser sequencial

        $this->variacoes[] = [
            'id_temp'     => $idUnico, // garante que nunca vai conflitar
            'subcodigo' => $subcodigo,
            'variacao' => '',
            'quantidade' => 0,
            'valor_varejo' => '',
            'valor_produto' => '',
            'fornecedor_id' => '',
            'gtin' => '',
            'estoque' => '',
            'quantidade_minima' => '',
            'percentage' => '',
            'status' => true,
            'validade' => '',
            'imagens' => [],
        ];

    }

    // Método chamado PELO PAI (via $emitTo) antes de salvar
    public function syncAndSave()
    {
        // 1) Envia o array completo de variações ao pai
        $this->emitUp('atualizarVariacoes', $this->variacoes);

        // 2) Agora manda o pai salvar (ele já recebeu as variações)
        $this->emitUp('salvar');
    }

    public function render()
    {
        return view('livewire.produto-variacoes-form');
    }
}
