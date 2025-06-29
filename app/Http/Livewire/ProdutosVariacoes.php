<?php

namespace App\Http\Livewire;

use App\Http\Models\Produto;
use App\Http\Models\ProdutoVariation;
use Livewire\Component;
use Livewire\WithPagination;

class ProdutosVariacoes extends Component
{
    use WithPagination;

    public $expanded = [];
    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    protected $paginationTheme = 'bootstrap';
    protected $queryString = ['search'];
    public $variacoesCarregadas = [];
    public $loadingProdutoId = null;
    public $loadingVariaId = null;


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
            $this->variacoesCarregadas[$produtoId] = ProdutoVariation::where('products_id', $produtoId)
                ->where('status', 1)
                ->get()
                ->filter(); // Remove nulls, objetos vazios etc.
        }
    }

    public function incrementar($variacaoId)
    {
        $this->loadingVariaId = $variacaoId;

        $variacao = ProdutoVariation::findOrFail($variacaoId);
        $variacao->quantidade += 1;
        $variacao->save();

        $this->refreshVariacao($variacao);
        $this->loadingVariaId = null;
    }

    public function decrementar($variacaoId)
    {
        $this->loadingVariaId = $variacaoId;

        $variacao = ProdutoVariation::findOrFail($variacaoId);
        $variacao->quantidade = max(0, $variacao->quantidade - 1);
        $variacao->save();

        $this->refreshVariacao($variacao);
        $this->loadingVariaId = null;
    }

    public function atualizarCampo($variacaoId, $campo, $valor)
    {
        // Remove R$, pontos e substitui vírgula por ponto
        $valor = str_replace(['R$', '.', ' '], '', $valor);
        $valor = str_replace(',', '.', $valor);

        $variacao = ProdutoVariation::findOrFail($variacaoId);
        $variacao->$campo = $valor;
        $variacao->save();

        $this->refreshVariacao($variacao);
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

    public function render()
    {

        $searchTerms = collect(explode(' ', strtoupper(trim($this->search))))
            ->filter(); // remove termos vazios

        $produtos = Produto::where('status', 1)
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
            'loadingProdutoId' => $this->loadingProdutoId
        ])->layout('layouts.layout');
    }

}
