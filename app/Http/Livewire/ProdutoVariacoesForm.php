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
    public $produto;
    public bool $salvando = false;


    // O filho "ouve" uma chamada para sincronizar e salvar
    protected $listeners = ['updatedVariacoes' => 'updatedVariacoes','syncAndSave' => 'syncAndSave',
        'imagemAtualizada'=>'imagemAtualizada'];

//    protected $rules = [
//        'variacoes.*.descricao' => 'required|string|max:155',
//        'variacoes.*.fornecedor_id' => 'required|exists:loja_fornecedores,id',
//        'variacoes.*.categoria_id' => 'required|exists:loja_categorias,id',
//        'variacoes.*.origem_id' => 'required|exists:loja_produto_origem_nfces,codigo',
//    ];

    public function mount($produto , $variacoes = [], $fornecedores = [], ?int $produtoId = null,$codigoPai)
    {
        $lastCodigo = Produto::max('codigo_produto') ?? 0;
        $this->produto['codigo_produto'] = $lastCodigo + 1;

        $this->variacoes = $variacoes;
        $this->fornecedores = $fornecedores;
        $this->produtoId = $produtoId;
        $this->produto = $produto;
        $this->codigoPai = $codigoPai;

    }

    public function adicionarVariacao()
    {
        $seq = count($this->variacoes) + 1;
        $subcodigo = $this->codigoPai . str_pad($seq, 2, '0', STR_PAD_LEFT);

        // Gerar ID único (pode ser uuid ou contador incremental)
        $idUnico = Str::uuid()->toString(); // se quiser usar UUID
        // $idUnico = count($this->variacoes) + 1; // se quiser sequencial

        $this->variacoes[] = [
            'id'     => $idUnico, // garante que nunca vai conflitar
            'subcodigo' => $subcodigo,
            'variacao' => '',
            'quantidade' => '',
            'valor_varejo' => '',
            'valor_produto' => '',
            'fornecedor_id' => '',
            'gtin' => '',
            'estoque' => '',
            'quantidade_minima' => '',
            'percentage' => '',
            'status' => true,
            'validade' => '',
            'images' => [],
        ];

    }

    // Método chamado PELO PAI (via $emitTo) antes de salvar
    public function syncAndSave()
    {
        $this->validate([
            'variacoes.*.fornecedor_id' => 'required|exists:loja_fornecedores,id',
            'variacoes.*.quantidade' => 'required|numeric|min:1',
            // adicione outras regras conforme necessário
        ]);

        // 1) Envia o array completo de variações ao pai
         $this->emitUp('atualizarVariacoes', $this->variacoes);

        // 2) Agora manda o pai salvar (ele já recebeu as variações)
        $this->emitUp('salvar');
    }


    //deleteadno a ultima imagem, atualiza o componente na tela de edição
    public function imagemAtualizada($data)
    {
        $this->variacoes = $data;
    }


    public function render()
    {
        return view('livewire.produto-variacoes-form');
    }
}
