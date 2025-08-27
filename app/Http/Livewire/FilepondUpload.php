<?php

namespace App\Http\Livewire;

use Livewire\Component;

class FilepondUpload extends Component
{
    public $multiple = true;
    public $pastas = [];
    public array $pastasImagensProduto = [];   // imagens do produto pai
    public array $pastasImagensVariacoes = []; // imagens exclusivas de cada variação
    public $imagensExistentes = []; // imagens já salvas no banco (passadas pelo pai)

    protected $listeners = [
        'pastasAtualizadasProduto' => 'setPastasImagensProduto',
        'pastasAtualizadasVariacao' => 'setPastasImagensVariacao',
    ];

    public function mount($imagensExistentes = [])
    {
        $this->imagensExistentes = $imagensExistentes;
    }

    public function setPastasImagensProduto($pastas)
    {
        $this->pastasImagensProduto = $pastas ?? [];

        // emite para o pai (ProdutoEditar, ClienteEditar, etc)
        $this->emitUp('pastasAtualizadasProduto', $this->pastasImagensProduto);
    }

    public function setPastasImagensVariacao($payload)
    {
        // payload = ['variacao_id' => 12, 'pastas' => [...]]
        $this->pastasImagensVariacoes[$payload['variacao_id']] = $payload['pastas'] ?? [];

        // emite para o pai (ProdutoEditar, ClienteEditar, etc)
        $this->emitUp('pastasAtualizadasVariacao', $this->pastasImagensVariacoes);
    }

    public function render()
    {
        return view('livewire.produto-filepond');
    }
}
