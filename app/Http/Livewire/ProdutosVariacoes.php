<?php

namespace App\Http\Livewire;

use App\Http\Models\Categoria;
use App\Http\Models\Fornecedor;
use App\Http\Models\Produto;
use App\Http\Models\ProdutoVariation;
use App\Traits\ProdutoTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Livewire\WithPagination;

class ProdutosVariacoes extends Component
{
    use WithPagination;
    use ProdutoTrait;

    public $expanded = [];
    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    protected $paginationTheme = 'bootstrap';
    protected $queryString = ['search'];
    public $variacoesCarregadas = [];
    public $loadingProdutoId = null;
    public $loadingVariaId = null;
    public $fornecedores = [];
    public $categorias = [];


    protected $listeners = ['atualizarCampoValor','atualizarCampo','alterarStatusConfirmado'];

    public function mount()
    {
        $this->fornecedores = collect(); // esvazia antes
        $this->fornecedores = Fornecedor::select('id', 'nome')->where('status',1)->orderBy('nome','asc')->get();
        $this->categorias = collect(); // esvazia antes
        $this->categorias = Categoria::select('id', 'nome')->where('status',1)->orderBy('nome','asc')->get();

    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function isExpanded($produtoId)
    {
        return in_array($produtoId, $this->expanded);
    }

    public function toggleExpand($produtoId)
    {
        if ($this->isExpanded($produtoId)) {
            $this->expanded = array_filter($this->expanded, fn($id) => $id != $produtoId);
        } else {
            $this->expanded[] = $produtoId;

            // Sempre recarrega as variações ao expandir
            $this->variacoesCarregadas[$produtoId] = ProdutoVariation::with('images')->where('products_id', $produtoId)
                ->where('status', 1)
                ->get()
                ->filter(); // Remove nulls, objetos vazios etc.
        }
    }

    public function incrementar($variacaoId, $campo = 'quantidade')
    {
        $this->loadingVariaId = $variacaoId;

        $variacao = ProdutoVariation::findOrFail($variacaoId);
        $valorAtual = $variacao->$campo ?? 0;
        $variacao->$campo = $valorAtual + 1;
        $variacao->save();

        $this->refreshVariacao($variacao);
        $this->loadingVariaId = null;
    }

    public function decrementar($variacaoId, $campo='quantidade')
    {
        $this->loadingVariaId = $variacaoId;

        $variacao = ProdutoVariation::findOrFail($variacaoId);
        $valorAtual = $variacao->$campo ?? 0;
        $variacao->$campo = max(0, $valorAtual - 1); // Garante que não fique negativo;
        $variacao->save();

        $this->refreshVariacao($variacao);
        $this->loadingVariaId = null;
    }


    public function atualizarCampo($variacaoId, $campo, $valor)
    {
        // Limpeza básica do valor
        $valor = str_replace(['R$', '.', ' '], '', $valor);
        $valor = str_replace(',', '.', $valor);

        $variacao = ProdutoVariation::findOrFail($variacaoId);

        if ($campo === 'quantidade') {
            $valor = (float) $valor;

            // Ignora valores zerados ou negativos
            if ($valor > 0) {
                $variacao->quantidade += $valor;
                //$variacao->quantidade = $valor;
            }
        } else {
            $variacao->$campo = $valor;
        }

        $variacao->save();

        $this->refreshVariacao($variacao);

        $this->dispatchBrowserEvent('status-generico',
            [
                'title' => 'Suecesso!',
                'text' => 'Informações atualizadas com sucesso!',
                'icon' => 'success'
            ]);
    }


    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    private function refreshVariacao($variacao)
    {
        if (!$variacao || !isset($variacao->products_id)) return;

        $produtoId = $variacao->products_id;

        if (isset($this->variacoesCarregadas[$produtoId])) {
            $this->variacoesCarregadas[$produtoId] = ProdutoVariation::where('products_id', $produtoId)
                ->where('status', 1)
                ->get()
                ->filter();
        }
    }

    /**
     * Redireciona para  tela de editar a variação
     * @param $id
     * @return RedirectResponse
     */
    public function editarVariacao($id)
    {
        return Redirect::route('variacao.edit', ['variacao' => $id]);
    }

    public function render()
    {

        $searchTerms = collect(explode(' ', strtoupper(trim($this->search))))->filter(); // remove termos vazios

        $produtos = Produto::with('produtoImagens')->where('status', 1)
            ->where(function ($query) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $query->where(function ($sub) use ($term) {
                        $sub->whereRaw('UPPER(descricao) LIKE ?', ["%{$term}%"])
                            ->orWhereHas('variacoes', function ($q) use ($term) {
                                $q->whereRaw('UPPER(variacao) LIKE ?', ["%{$term}%"])
                                    ->orWhereRaw('subcodigo LIKE ?', ["%{$term}%"]);
                            });
                    });
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);


        return view('livewire.produtos-variacoes', [
            'produtos' => $produtos,
            'expanded' => $this->expanded,
            'loadingProdutoId' => $this->loadingProdutoId,
            'fornecedores' => $this->fornecedores,
            'categorias' => $this->categorias,
        ])->layout('layouts.layout');
    }

}
