<?php

namespace App\Http\Livewire;

use App\Http\Models\Despesa;
use App\Http\Models\VendasProdutos;
use App\Traits\RelatorioTrait;
use Carbon\Carbon;
use Livewire\Component;

class DashboardDre extends Component
{
    use RelatorioTrait;

    public $inicio;
    public $fim;
    public $receitaTotal;
    public $despesaTotal;
    public $lucro;
    public $despesas;
    public $taxasAplicadas;

    public $receitaAnterior;
    public $lucroAnterior;
    public $despesaAnterior;
    public $taxasAnteriores;

    public $variacaoReceita;
    public $variacaoLucro;

    protected $listeners = ['periodoAtualizado' => 'atualizarPeriodo'];


    public function mount()
    {
        $this->inicio = Carbon::now()->startOfMonth()->toDateString();
        $this->fim = Carbon::now()->endOfMonth()->toDateString();

        $this->calcularTaxas();
        $this->calcularDre();
        $this->calcularValorMesAnterior();
    }

    public function atualizarPeriodo($periodo)
    {
        if (!$periodo || !str_contains($periodo, ' - ')) {
            return;
        }

        try {
            [$inicioRaw, $fimRaw] = explode(' - ', $periodo);

            $this->inicio = Carbon::createFromFormat('d/m/Y', trim($inicioRaw))->toDateString();
            $this->fim = Carbon::createFromFormat('d/m/Y', trim($fimRaw))->toDateString();

            $this->calcularTaxas();
            $this->calcularDre();
            $this->calcularValorMesAnterior();

        } catch (\Exception $e) {
            // log ou erro silencioso
        }
    }

    public function calcularDre()
    {
        $this->receitaTotal = VendasProdutos::whereBetween('created_at', [$this->inicio, $this->fim])
            ->where('troca', 0)
            ->get()
            ->sum(fn($venda) => $venda->valor_produto * $venda->quantidade);

        $this->despesaTotal = Despesa::whereBetween('created_at', [$this->inicio, $this->fim])->sum('valor');
        $this->despesas = Despesa::whereBetween('created_at', [$this->inicio, $this->fim])->get();
        $this->lucro = $this->receitaTotal - $this->despesaTotal - $this->taxasAplicadas;

        $this->dispatchBrowserEvent('refreshChart', [
            'receita' => $this->receitaTotal,
            'despesa' => $this->despesaTotal,
            'lucro' => $this->lucro
        ]);
    }

    public function calcularTaxas()
    {
        $this->taxasAplicadas = $this->buscaTaxas($this->inicio, $this->fim);
    }

    public function calcularValorMesAnterior()
    {
        $inicioAnterior = Carbon::parse($this->inicio)->subMonth()->startOfMonth()->toDateString();
        $fimAnterior = Carbon::parse($this->inicio)->subMonth()->endOfMonth()->toDateString();

        $this->receitaAnterior = VendasProdutos::whereBetween('created_at', [$inicioAnterior, $fimAnterior])
            ->where('troca', 0)
            ->get()
            ->sum(fn($venda) => $venda->valor_produto * $venda->quantidade);

        $this->despesaAnterior = Despesa::whereBetween('created_at', [$inicioAnterior, $fimAnterior])->sum('valor');

        $this->taxasAnteriores =  $this->buscaTaxas($inicioAnterior, $fimAnterior);

        $this->lucroAnterior = $this->receitaAnterior - $this->despesaAnterior - $this->taxasAnteriores;

        $this->variacaoReceita = $this->receitaAnterior > 0
            ? (($this->receitaTotal - $this->receitaAnterior) / $this->receitaAnterior) * 100
            : 0;

        $this->variacaoLucro = $this->lucroAnterior > 0
            ? (($this->lucro - $this->lucroAnterior) / $this->lucroAnterior) * 100
            : 0;
    }

    public function render()
    {
        return view('livewire.dashboard-dre');
    }
}
