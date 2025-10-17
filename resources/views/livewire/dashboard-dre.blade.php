<div>
    <div class="container-fluid mt-4 mb-4" xmlns:wire="http://www.w3.org/1999/xhtml" xmlns:livewire="">
        {{-- Header do Produto --}}
        <div class="card shadow border-0 mb-3">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-0"><i class="fas fa-bar-chart"></i> DRE Simplificado</h5>
                <hr>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb small">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.home') }}">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('produto.produto_create') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">DRE</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card shadow border-0 mb-4 p-4">
            <div class="row mb-4">
                <div class="col-md-3">
                    <livewire:pesquisa/>
                </div>
                <div wire:loading class="text-center mt-2">
                    <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                    <span class="ms-2">Atualizando dados...</span>
                </div>
            </div>

            <div class="row text-center mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-arrow-up fa-2x mb-2"></i>
                            <h5 class="card-title">Receita</h5>
                            <p class="card-text fs-4">R$ {{ number_format($receitaTotal, 2, ',', '.') }}</p>
                        </div>
                    </div>
                    @if(!is_null($variacaoReceita))
                        <div class="mb-2">
                            <span class="badge {{ $variacaoReceita >= 0 ? 'bg-success' : 'bg-danger' }}">
                                Receita {{ $variacaoReceita >= 0 ? '↑' : '↓' }} {{ number_format(abs($variacaoReceita), 2, ',', '.') }}% em relação ao mês anterior
                            </span>
                        </div>
                    @endif
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-danger text-white shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-arrow-down fa-2x mb-2"></i>
                            <h5 class="card-title">Despesa</h5>
                            <p class="card-text fs-4">R$ {{ number_format($despesaTotal, 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-coins fa-2x mb-2"></i>
                            <h5 class="card-title">Lucro</h5>
                            <p class="card-text fs-4">R$ {{ number_format($lucro, 2, ',', '.') }}</p>
                        </div>
                    </div>
                    @if(!is_null($variacaoLucro))
                        <div class="mb-2">
                            <span class="badge {{ $variacaoLucro >= 0 ? 'bg-primary' : 'bg-warning text-dark' }}">
                                Lucro {{ $variacaoLucro >= 0 ? '↑' : '↓' }} {{ number_format(abs($variacaoLucro), 2, ',', '.') }}% em relação ao mês anterior
                            </span>
                        </div>
                    @endif
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-dark shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-credit-card fa-2x mb-2"></i>
                            <h5 class="card-title">Taxas</h5>
                            <p class="card-text fs-4">R$ {{ number_format($taxasAplicadas, 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <canvas id="graficoDre" style="max-width: 600px; max-height: 300px;"></canvas>
            </div>

            <h4 class="mb-3">📌 Despesas no período</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                    <tr>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th>Data</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($despesas as $despesa)
                        <tr>
                            <td>{{ $despesa->descricao }}</td>
                            <td>R$ {{ number_format($despesa->valor, 2, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($despesa->data)->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let chartInstance;

        function renderChart(receita, despesa, lucro) {
            const ctx = document.getElementById('graficoDre')?.getContext('2d');
            if (!ctx) return;

            if (chartInstance) {
                chartInstance.destroy();
            }

            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Receita', 'Despesa', 'Lucro'],
                    datasets: [{
                        label: 'R$',
                        data: [receita, despesa, lucro],
                        backgroundColor: ['#4CAF50', '#F44336', '#2196F3']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Render inicial
        document.addEventListener('DOMContentLoaded', function () {
            renderChart(@js($receitaTotal), @js($despesaTotal), @js($lucro));
        });

        // Atualização dinâmica
        window.addEventListener('refreshChart', (event) => {
            const { receita, despesa, lucro } = event.detail;
            renderChart(receita, despesa, lucro);
        });
    </script>

@endpush


