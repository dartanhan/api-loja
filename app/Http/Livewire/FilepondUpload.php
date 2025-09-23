<?php

namespace App\Http\Livewire;

use App\Traits\ProdutoTrait;
use Livewire\Component;
use Livewire\WithFileUploads;

class FilepondUpload extends Component
{
    use ProdutoTrait;
    use WithFileUploads;

    // Props configuráveis pelo pai
    public string $context = 'produto'; // 'produto' | 'variacao'
    public ?string $variacaoKey = null; // chave temporária da variação (ex.: subcodigo)
    public bool $multiple = true;
    public $images = []; // arquivos temporários
    public $uploads = [];
    public $produtoId;
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
        //'imagemDeletada'            => 'removerImagem',
        'imagensAtualizadas'        => 'setImagens',
        'deletarImagem'             => 'deletarImagem'
    ];

    /**
     * @param string $context 'produto' ou 'variacao'
     * @param string $multiple
     * @param string|null $variacaoKey chave temporária para mapear variação (ex.: subcodigo)
     * @param array $imagensExistentes urls/paths já salvos (na criação geralmente vazio)
     */
    public function mount(string $context = 'produto',
                          string $multiple, ?string $variacaoKey = null, array $imagensExistentes = [])
    {
        $this->context = $context;
        $this->variacaoKey = $variacaoKey;
        $this->imagensExistentes = $imagensExistentes ?? [];
        $this->multiple = $multiple;

      //  dump($this->multiple,  $this->context);
    }


   /* public function setPastasImagensProduto($pastas)
    {
        $this->pastasImagensProduto = $pastas ?? [];

        // avisa o PAI (ProdutoCreate) que a lista mudou
        $this->emitUp('pastasAtualizadasProduto', $this->pastasImagensProduto);
        return $this->skipRender();
    }

    /**
     * payload: ['variacao_key' => string, 'pastas' => array]
     */
    /*public function setPastasImagensVariacao(array $payload)
    {
        $key = $payload['variacao_key'] ?? $this->variacaoKey;
        $pastas = $payload['pastas'] ?? [];

        if (!$key) return;

        $this->pastasImagensVariacoes[$key] = $pastas;

        // avisa o PAI (ProdutoCreate) com o mapa completo de variações => pastas
        $this->emitUp('pastasAtualizadasVariacao', $this->pastasImagensVariacoes);
        return $this->skipRender();
    }*/

    /** 🔹 Remove imagem deletada
     * @param $data
     */
  /*  public function removerImagem($id): void
    {
        $this->imagensExistentes = array_filter(
            $this->imagensExistentes,
            fn($img) => $img['id'] != $id
        );
    }*/

 /*   public function carregarImagens($data)
    {
       // dd($data); // 👈 aqui você já vai ver o array
        $this->images = $data ?? [];
        //$this->produto = $data;
    }*/
/*
    public function emitirParaOPai($uploadedFile = null)
    {
        $payload = [
            'file' => $uploadedFile,
            'context' => $this->context,
            'variacaoKey' => $this->variacaoKey,
        ];
        // Aqui passamos só o nome do arquivo temporário para o pai
        $this->emitUp('imagensAtualizadas', $payload);
    }*/



    public function render()
    {
        return view('livewire.produto-filepond');
    }
}
