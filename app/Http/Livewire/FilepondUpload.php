<?php

namespace App\Http\Livewire;

use App\Traits\ProdutoTrait;
use Livewire\Component;

class FilepondUpload extends Component
{
    use ProdutoTrait;
    // Props configuráveis pelo pai
    public string $context = 'produto'; // 'produto' | 'variacao'
    public ?string $variacaoKey = null; // chave temporária da variação (ex.: subcodigo)
    public bool $multiple = true;

    // Estado interno
    public array $pastasImagensProduto = [];   // imagens do produto pai
    public array $pastasImagensVariacoes = []; // imagens por key de variação
    public array $imagensExistentes = [];      // opcional (criação costuma vir vazio)

    /**
     * @var string[]
     */
    protected $listeners = [
        'pastasAtualizadasProduto'  => 'setPastasImagensProduto',
        'pastasAtualizadasVariacao' => 'setPastasImagensVariacao',
        'imagemDeletada'            => 'removerImagem',
        'imagemAtualizada' => 'carregarImagens',
        'deletarImagem' => 'deletarImagem'
    ];

    /**
     * @param string $context 'produto' ou 'variacao'
     * @param string|null $variacaoKey chave temporária para mapear variação (ex.: subcodigo)
     * @param array $imagensExistentes urls/paths já salvos (na criação geralmente vazio)
     */
    public function mount(string $context = 'produto', ?string $variacaoKey = null, array $imagensExistentes = [])
    {
        $this->context = $context;
        $this->variacaoKey = $variacaoKey;
        $this->imagensExistentes = $imagensExistentes ?? [];
    }


    public function setPastasImagensProduto($pastas): void
    {
        $this->pastasImagensProduto = $pastas ?? [];

        // avisa o PAI (ProdutoCreate) que a lista mudou
        $this->emitUp('pastasAtualizadasProduto', $this->pastasImagensProduto);
    }

    /**
     * payload: ['variacao_key' => string, 'pastas' => array]
     */
    public function setPastasImagensVariacao(array $payload): void
    {
        $key = $payload['variacao_key'] ?? $this->variacaoKey;
        $pastas = $payload['pastas'] ?? [];

        if (!$key) return;

        $this->pastasImagensVariacoes[$key] = $pastas;

        // avisa o PAI (ProdutoCreate) com o mapa completo de variações => pastas
        $this->emitUp('pastasAtualizadasVariacao', $this->pastasImagensVariacoes);
    }

    /** 🔹 Remove imagem deletada
     * @param $data
     */
    public function removerImagem($id): void
    {
        $this->imagensExistentes = array_filter(
            $this->imagensExistentes,
            fn($img) => $img['id'] != $id
        );
    }

    public function render()
    {
        return view('livewire.produto-filepond');
    }
}
