@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">🤖 KN Intelligence</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Inteligência de Dados</li>
    </ol>

    <div class="row">
        <!-- Card Faturamento -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Faturamento (Mês)</div>
                            <div class="h4 mb-0 font-weight-bold">R$ {{ number_format($vendas['faturamento'], 2, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Pedidos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 bg-gradient-success text-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Pedidos</div>
                            <div class="h4 mb-0 font-weight-bold">{{ $vendas['pedidos'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Ticket Médio -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 bg-gradient-info text-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Ticket Médio</div>
                            <div class="h4 mb-0 font-weight-bold">R$ {{ number_format($vendas['ticket_medio'], 2, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Risco Estoque -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Itens em Alerta de Estoque</div>
                            <div class="h4 mb-0 font-weight-bold">{{ $estoqueRisco['total_itens_criticos'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Ações de IA -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-brain text-info"></i> Assistente Estratégico IA</h5>
                    <a href="{{ route('admin.kn_intelligence.assistant') }}" class="btn btn-sm btn-info font-weight-bold">
                        <i class="fas fa-comments"></i> Abrir Chatbot de IA
                    </a>
                </div>
                <div class="card-body bg-light">
                    <p class="text-muted mb-3">Converse com a inteligência artificial treinada sobre os dados reais da sua loja para obter sugestões de vendas, giro de estoque e otimização de margens.</p>
                    <a href="{{ route('admin.kn_intelligence.assistant') }}" class="btn btn-primary">Fazer Pergunta à IA</a>
                    <a href="{{ route('admin.kn_intelligence.configuracoes') }}" class="btn btn-outline-secondary">Configurar IA (Gemini API)</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Produtos em Risco -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-boxes"></i> Produtos com Risco de Ruptura (Estoque ≤ Mínimo)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Descrição</th>
                            <th>Estoque Atual</th>
                            <th>Estoque Mínimo</th>
                            <th>Valor Unitário</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estoqueRisco['produtos'] as $prod)
                        <tr>
                            <td><code>{{ $prod->codigo_produto }}</code></td>
                            <td>{{ $prod->descricao }}</td>
                            <td><span class="badge bg-danger text-white">{{ $prod->quantidade }}</span></td>
                            <td>{{ $prod->quantidade_minima }}</td>
                            <td>R$ {{ number_format($prod->valor_produto, 2, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Nenhum produto em risco crítico de estoque no momento.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
