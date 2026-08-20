@extends('layouts.layout')

@section('menu')
    @include('admin.menu')
@endsection

@push('styles')
<style>
    .kni-page { background: #f0f2f8; min-height: 100vh; padding: 1.5rem 1.75rem; }
    .kni-header { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
    .kni-header h1 { font-size: 1.6rem; font-weight: 700; color: #1a1d2e; margin: 0; }
    .kni-header p  { font-size: .85rem; color: #6b7280; margin: .2rem 0 0; }
    
    .kni-card-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .kni-card { background: #fff; border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,.1); display: flex; align-items: center; justify-content: space-between;}
    .kni-card-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; color: #6b7280; }
    .kni-card-value { font-size: 1.5rem; font-weight: 800; color: #111827; margin-top: .25rem; }
    
    .kni-panel { background: #fff; border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,.1); margin-bottom: 1.5rem; }
    
    .kni-table { width: 100%; font-size: .82rem; border-collapse: collapse; }
    .kni-table th { background: #f9fafb; color: #4b5563; font-weight: 600; text-transform: uppercase; font-size: .7rem; padding: .6rem; border-bottom: 1px solid #e5e7eb; text-align: left; }
    .kni-table td { padding: .6rem; border-bottom: 1px solid #e5e7eb; color: #374151; vertical-align: middle; }
    
    .badge-ok { background: #d1fae5; color: #059669; padding: .2rem .5rem; border-radius: 50px; font-weight: 700; font-size:.7rem; }
    .badge-div { background: #fee2e2; color: #dc2626; padding: .2rem .5rem; border-radius: 50px; font-weight: 700; font-size:.7rem; }
    
    .btn-action { padding: .4rem 1rem; border-radius: 8px; font-size: .85rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: .5rem; }
    .btn-primary { background: #6366f1; color: #fff; border: none; }
    .btn-secondary { background: #fff; color: #374151; border: 1px solid #d1d5db; }
</style>
@endpush

@section('content')
<div class="kni-page">
    
    <div class="kni-header">
        <div>
            <h1>🛠️ Diagnóstico do KN Intelligence</h1>
            <p>Validação da engine matemática cruzando (loja_produtos_variacao + loja_vendas_produtos)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.kn_intelligence.diagnostico.estoque', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn-action btn-secondary">
                <i class="fas fa-file-csv"></i> Exportar CSV
            </a>
        </div>
    </div>

    <!-- Filtros de Período -->
    <div class="kni-panel">
        <form method="GET" action="{{ route('admin.kn_intelligence.diagnostico.estoque') }}" class="d-flex align-items-end gap-3 flex-wrap">
            <div>
                <label style="font-size: .8rem; font-weight: 600; color: #4b5563;">Data Inicial (Vendas)</label>
                <input type="date" name="data_inicial" class="form-control" style="border-radius:8px;" value="{{ $resumo['data_inicial'] }}">
            </div>
            <div>
                <label style="font-size: .8rem; font-weight: 600; color: #4b5563;">Data Final (Vendas)</label>
                <input type="date" name="data_final" class="form-control" style="border-radius:8px;" value="{{ $resumo['data_final'] }}">
            </div>
            <div>
                <label style="font-size: .8rem; font-weight: 600; color: #4b5563;">Visualizar</label>
                <select name="filtro" class="form-control" style="border-radius:8px;">
                    <option value="todos" {{ $filtro == 'todos' ? 'selected' : '' }}>Todos as variações</option>
                    <option value="com_vendas" {{ $filtro == 'com_vendas' ? 'selected' : '' }}>Somente Com Vendas no período</option>
                    <option value="sem_vendas" {{ $filtro == 'sem_vendas' ? 'selected' : '' }}>Sem vendas no período</option>
                    <option value="sem_estoque" {{ $filtro == 'sem_estoque' ? 'selected' : '' }}>Estoque Zerado</option>
                    <option value="estoque_negativo" {{ $filtro == 'estoque_negativo' ? 'selected' : '' }}>Estoque Negativo</option>
                    <option value="sem_custo" {{ $filtro == 'sem_custo' ? 'selected' : '' }}>Sem Custo Lançado</option>
                </select>
            </div>
            <button type="submit" class="btn-action btn-primary border-0">Filtrar</button>
        </form>
    </div>

    <!-- Resumo -->
    <div class="kni-card-grid">
        <div class="kni-card">
            <div>
                <div class="kni-card-label">Variações Mapeadas</div>
                <div class="kni-card-value">{{ $resumo['total_variacoes_analisadas'] }}</div>
            </div>
            <i class="fas fa-boxes fa-2x" style="color: #6366f1; opacity: 0.2;"></i>
        </div>
        <div class="kni-card">
            <div>
                <div class="kni-card-label">C/ Vendas | S/ Vendas</div>
                <div class="kni-card-value">{{ $resumo['com_vendas'] }} | {{ $resumo['sem_vendas'] }}</div>
            </div>
            <i class="fas fa-shopping-cart fa-2x" style="color: #10b981; opacity: 0.2;"></i>
        </div>
        <div class="kni-card">
            <div>
                <div class="kni-card-label">Ruptura (Sem Estoque)</div>
                <div class="kni-card-value">{{ $resumo['variacoes_sem_estoque'] }}</div>
            </div>
            <i class="fas fa-exclamation-triangle fa-2x" style="color: #ef4444; opacity: 0.2;"></i>
        </div>
        <div class="kni-card">
            <div>
                <div class="kni-card-label">Divergências Encontradas</div>
                <div class="kni-card-value" style="color: {{ $resumo['divergencias'] > 0 ? '#dc2626' : '#059669' }}">
                    {{ $resumo['divergencias'] }}
                </div>
            </div>
            <i class="fas fa-bug fa-2x" style="color: {{ $resumo['divergencias'] > 0 ? '#dc2626' : '#059669' }}; opacity: 0.2;"></i>
        </div>
    </div>

    <!-- Tabela -->
    <div class="kni-panel" style="overflow-x: auto;">
        <table class="kni-table">
            <thead>
                <tr>
                    <th>Var ID</th>
                    <th>Produto</th>
                    <th>Estoque<br><small>(loja_produtos_variacao)</small></th>
                    <th>Custo Unit. (Aquisição)<br><small>(loja_produtos_variacao.valor_produto)</small></th>
                    <th style="background: #fdf2f8;">Qtd Vendida<br><small>(Service)</small></th>
                    <th style="background: #f0fdf4;">Qtd Vendida<br><small>(DB Bruto)</small></th>
                    <th>Comparação</th>
                    <th>Velocidade/Dia<br><small>(Service)</small></th>
                    <th>Cobertura<br><small>(Service)</small></th>
                </tr>
            </thead>
            <tbody>
                @forelse($dadosPaginados as $row)
                <tr style="{{ $row['resultado'] === 'DIVERGÊNCIA' ? 'background: #fef2f2;' : '' }}">
                    <td><code>{{ $row['variacao_id'] }}</code></td>
                    <td>
                        <strong style="color:#111827;">{{ $row['codigo_produto'] }} - {{ $row['descricao'] }}</strong><br>
                        @if($row['variacao'] !== 'Produto sem variação')
                        <small style="color:#6b7280;">{{ $row['subcodigo'] }} - {{ $row['variacao'] }}</small>
                        @endif
                    </td>
                    <td>
                        {{ $row['estoque_atual'] }} 
                        @if($row['sem_estoque'] === 'SIM') <br><span class="badge-div">RUPTURA</span> @endif
                        @if($row['estoque_negativo'] === 'SIM') <br><span class="badge-div">NEGATIVO</span> @endif
                    </td>
                    <td>
                        R$ {{ number_format($row['custo_unitario'], 2, ',', '.') }}
                        @if($row['custo_unitario'] == 0) <br><span class="badge-div">SEM CUSTO</span> @endif
                    </td>
                    <td style="font-weight:700; text-align:center;">{{ $row['qtd_service'] }}</td>
                    <td style="font-weight:700; text-align:center;">{{ $row['qtd_banco'] }}</td>
                    <td>
                        @if($row['resultado'] === 'OK')
                            <span class="badge-ok"><i class="fas fa-check"></i> OK</span>
                        @else
                            <span class="badge-div"><i class="fas fa-times"></i> ERRO</span>
                        @endif
                    </td>
                    <td>{{ number_format($row['velocidade'], 2, ',', '') }} un/d</td>
                    <td>
                        @if($row['cobertura'] === 99999)
                            <span style="color:#6b7280;font-size:.7rem;">ILIMITADA</span>
                        @else
                            {{ $row['cobertura'] }} dias
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4" style="color:#6b7280;">Nenhuma variação encontrada com os filtros atuais.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="mt-3 px-3 pb-3 d-flex justify-content-end">
            {{ $dadosPaginados->links('pagination::bootstrap-4') }}
        </div>
    </div>

</div>
@endsection
