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
        $this->loadingProdutoId = $produtoId;

        if ($this->isExpanded($produtoId)) {
            $this->expanded = array_values(array_diff($this->expanded, [$produtoId]));
        } else {
            $this->expanded[] = $produtoId;

            if (!isset($this->variacoesCarregadas[$produtoId])) {
                $this->variacoesCarregadas[$produtoId] = ProdutoVariation::where('products_id', $produtoId)
                    ->where('status', 1)
                    ->get();
            }
        }

        $this->loadingProdutoId = null;
    }

    public function incrementar($variacaoId)
    {
        $variacao = ProdutoVariation::findOrFail($variacaoId);
        $variacao->quantidade += 1;
        $variacao->save();

        $this->refreshVariacao($variacao);
    }

    public function decrementar($variacaoId)
    {
        $variacao = ProdutoVariation::findOrFail($variacaoId);
        $variacao->quantidade = max(0, $variacao->quantidade - 1);
        $variacao->save();

        $this->refreshVariacao($variacao);
    }

    public function atualizarCampo($variacaoId, $campo, $valor)
    {
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
        $produtoId = $variacao->products_id;

        if (isset($this->variacoesCarregadas[$produtoId])) {
            $this->variacoesCarregadas[$produtoId] = ProdutoVariation::where('products_id', $produtoId)
                ->where('status', 1)
                ->get();
        }
    }

    public function render()
    {
        $produtos = Produto::where('status', 1)
            ->where(function ($query) {
                $query->where('descricao', 'like', "%{$this->search}%");
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
