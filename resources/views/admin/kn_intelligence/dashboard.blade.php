@extends('layouts.layout')

@section('menu')
    @include('admin.menu')
@endsection

@push('styles')
<style>
    /* ── Painel Geral ── */
    .kni-page { background: #f0f2f8; min-height: 100vh; padding: 1.5rem 1.75rem; }

    /* ── Header ── */
    .kni-header { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem; }
    .kni-header h1 { font-size: 1.6rem; font-weight: 700; color: #1a1d2e; margin: 0; }
    .kni-header p  { font-size: .85rem; color: #6b7280; margin: .2rem 0 0; }

    /* ── Filtros ── */
    .kni-filters { display: flex; align-items: center; gap: .4rem; flex-wrap: wrap; }
    .kni-filters .btn-periodo {
        padding: .35rem .85rem; border-radius: 50px; font-size: .8rem; font-weight: 600;
        border: 1.5px solid #d1d5db; background: #fff; color: #6b7280;
        cursor: pointer; transition: all .2s; text-decoration: none;
    }
    .kni-filters .btn-periodo:hover  { border-color: #6366f1; color: #6366f1; }
    .kni-filters .btn-periodo.active { background: #6366f1; border-color: #6366f1; color: #fff; }
    .kni-filters .btn-personalizado  { border-style: dashed; }

    /* ── Cards de métricas ── */
    .kni-metrics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    @media (max-width: 768px) { .kni-metrics { grid-template-columns: 1fr; } }

    .kni-card {
        background: #fff; border-radius: 14px; padding: 1.4rem 1.5rem;
        box-shadow: 0 1px 4px rgba(0,0,0,.07); position: relative; overflow: hidden;
        display: flex; align-items: center; gap: 1.1rem;
    }
    .kni-card .kni-icon {
        width: 52px; height: 52px; border-radius: 14px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
    }
    .kni-card .kni-icon.green  { background: #d1fae5; color: #059669; }
    .kni-card .kni-icon.blue   { background: #dbeafe; color: #2563eb; }
    .kni-card .kni-icon.purple { background: #ede9fe; color: #7c3aed; }
    .kni-card .kni-icon.orange { background: #fef3c7; color: #d97706; }

    .kni-card .kni-ghost {
        position: absolute; right: -10px; bottom: -10px; font-size: 5rem;
        opacity: .06; color: #6366f1; pointer-events: none;
    }
    .kni-card-body { flex: 1; }
    .kni-card-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #9ca3af; margin-bottom: .3rem; }
    .kni-card-value { font-size: 1.75rem; font-weight: 800; color: #111827; line-height: 1.1; }
    .kni-card-badge { display: inline-flex; align-items: center; gap: .25rem; font-size: .75rem; font-weight: 600; margin-top: .35rem; }
    .kni-card-badge.up   { color: #059669; }
    .kni-card-badge.down { color: #dc2626; }
    .kni-card-badge.flat { color: #6b7280; }

    /* ── Painéis inferiores ── */
    .kni-panels { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    @media (max-width: 900px) { .kni-panels { grid-template-columns: 1fr; } }

    .kni-panel {
        background: #fff; border-radius: 14px; box-shadow: 0 1px 4px rgba(0,0,0,.07); overflow: hidden;
    }
    .kni-panel-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: .9rem 1.25rem; border-bottom: 1px solid #f3f4f6;
    }
    .kni-panel-header .title { display: flex; align-items: center; gap: .5rem; font-size: .9rem; font-weight: 700; color: #1a1d2e; }
    .kni-panel-header .dot-alert  { width: 9px; height: 9px; border-radius: 50%; background: #ef4444; flex-shrink: 0; }
    .kni-panel-header .dot-ai     { color: #7c3aed; font-size: 1rem; }
    .kni-panel-header a { font-size: .8rem; font-weight: 600; color: #6366f1; text-decoration: none; }
    .kni-panel-header a:hover { text-decoration: underline; }
    .kni-panel-body { padding: 1rem 1.25rem; min-height: 180px; }

    /* ── Filtro personalizado ── */
    .kni-date-form { background: #fff; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1rem; box-shadow: 0 1px 4px rgba(0,0,0,.07); display: none; align-items: center; gap: 1rem; flex-wrap: wrap; }
    .kni-date-form.show { display: flex; }
    .kni-date-form label { font-size: .8rem; font-weight: 600; color: #6b7280; margin-right: .3rem; }
    .kni-date-form input[type=date] { border: 1.5px solid #d1d5db; border-radius: 8px; padding: .3rem .7rem; font-size: .85rem; color: #374151; }
    .kni-date-form .btn-aplicar { padding: .35rem 1rem; background: #6366f1; color: #fff; border: none; border-radius: 8px; font-size: .82rem; font-weight: 600; cursor: pointer; }

    /* ── Tabela de alertas ── */
    .kni-alert-table { width: 100%; font-size: .82rem; border-collapse: collapse; }
    .kni-alert-table th { color: #9ca3af; font-weight: 600; text-transform: uppercase; font-size: .7rem; letter-spacing: .05em; padding: .5rem .4rem; border-bottom: 1px solid #f3f4f6; text-align: left; }
    .kni-alert-table td { padding: .6rem .4rem; border-bottom: 1px solid #f9fafb; color: #374151; vertical-align: middle; }
    .kni-alert-table tr:last-child td { border-bottom: none; }
    .badge-estoque { display: inline-block; padding: .2rem .55rem; border-radius: 50px; font-size: .7rem; font-weight: 700; background: #fee2e2; color: #dc2626; }

    /* ── Insights ── */
    .kni-insight-item { padding: .65rem 0; border-bottom: 1px solid #f3f4f6; }
    .kni-insight-item:last-child { border-bottom: none; }
    .kni-insight-item .insight-tag { display: inline-block; font-size: .68rem; font-weight: 700; text-transform: uppercase; padding: .15rem .5rem; border-radius: 50px; background: #ede9fe; color: #7c3aed; margin-bottom: .25rem; }
    .kni-insight-item .insight-text { font-size: .82rem; color: #374151; line-height: 1.5; }
    .kni-insight-item .insight-date { font-size: .72rem; color: #9ca3af; margin-top: .15rem; }

    .kni-empty { text-align: center; padding: 2.5rem 1rem; color: #9ca3af; font-size: .85rem; }
    .kni-empty i { display: block; font-size: 2rem; margin-bottom: .6rem; opacity: .4; }
</style>
@endpush

@section('content')
@php
    // Variação percentual entre atual e anterior
    function varPct($atual, $anterior) {
        if ($anterior == 0) return ['pct' => 0, 'dir' => 'flat'];
        $pct = (($atual - $anterior) / abs($anterior)) * 100;
        return ['pct' => round($pct, 1), 'dir' => $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'flat')];
    }
    $vFat  = varPct($vendas['faturamento'],  $vendasAnt['faturamento']);
    $vPed  = varPct($vendas['pedidos'],      $vendasAnt['pedidos']);
    $vTick = varPct($vendas['ticket_medio'], $vendasAnt['ticket_medio']);
@endphp

<div class="kni-page">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:10px; font-size:.85rem; margin-bottom:1rem;">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:10px; font-size:.85rem; margin-bottom:1rem;">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
        </div>
    @endif

    {{-- ── Header ── --}}
    <div class="kni-header">
        <div>
            <h1>🤖 Painel de IA</h1>
            <p>Métricas analíticas consolidadas de vendas com insights gerados automaticamente.</p>
        </div>
        <div class="kni-filters">
            <a href="{{ route('admin.kn_intelligence.dashboard', ['periodo' => 'hoje']) }}"
               class="btn-periodo {{ $periodo === 'hoje' ? 'active' : '' }}">Hoje</a>
            <a href="{{ route('admin.kn_intelligence.dashboard', ['periodo' => '7dias']) }}"
               class="btn-periodo {{ $periodo === '7dias' ? 'active' : '' }}">7 Dias</a>
            <a href="{{ route('admin.kn_intelligence.dashboard', ['periodo' => '30dias']) }}"
               class="btn-periodo {{ $periodo === '30dias' ? 'active' : '' }}">30 Dias</a>
            <button class="btn-periodo btn-personalizado {{ $periodo === 'personalizado' ? 'active' : '' }}"
                    onclick="togglePersonalizado()">
                <i class="fas fa-calendar-alt me-1"></i>Personalizado
            </button>
        </div>
    </div>

    {{-- ── Filtro personalizado ── --}}
    <div class="kni-date-form {{ $periodo === 'personalizado' ? 'show' : '' }}" id="kni-date-form">
        <form method="GET" action="{{ route('admin.kn_intelligence.dashboard') }}" class="d-flex align-items-center gap-3 flex-wrap">
            <input type="hidden" name="periodo" value="personalizado">
            <div>
                <label>De</label>
                <input type="date" name="inicio" value="{{ $periodo === 'personalizado' ? \Carbon\Carbon::parse($inicio)->format('Y-m-d') : now()->startOfMonth()->format('Y-m-d') }}">
            </div>
            <div>
                <label>Até</label>
                <input type="date" name="fim" value="{{ $periodo === 'personalizado' ? \Carbon\Carbon::parse($fim)->format('Y-m-d') : now()->format('Y-m-d') }}">
            </div>
            <button type="submit" class="btn-aplicar">Aplicar</button>
        </form>
    </div>

    {{-- ── Cards de métricas ── --}}
    <div class="kni-metrics">

        {{-- Receita Líquida --}}
        <div class="kni-card">
            <div class="kni-icon green"><i class="fas fa-dollar-sign"></i></div>
            <div class="kni-card-body">
                <div class="kni-card-label">Receita Líquida</div>
                <div class="kni-card-value">R$ {{ number_format($vendas['faturamento'], 2, ',', '.') }}</div>
                <div class="kni-card-badge {{ $vFat['dir'] }}">
                    @if($vFat['dir'] === 'up') <i class="fas fa-arrow-up"></i>
                    @elseif($vFat['dir'] === 'down') <i class="fas fa-arrow-down"></i>
                    @else <i class="fas fa-minus"></i> @endif
                    {{ $vFat['pct'] }}% &nbsp;<span style="font-weight:400;color:#9ca3af;">vs per. anterior</span>
                </div>
            </div>
            <i class="fas fa-dollar-sign kni-ghost"></i>
        </div>

        {{-- Total Pedidos --}}
        <div class="kni-card">
            <div class="kni-icon blue"><i class="fas fa-shopping-bag"></i></div>
            <div class="kni-card-body">
                <div class="kni-card-label">Total Pedidos</div>
                <div class="kni-card-value">{{ $vendas['pedidos'] }}</div>
                <div class="kni-card-badge {{ $vPed['dir'] }}">
                    @if($vPed['dir'] === 'up') <i class="fas fa-arrow-up"></i>
                    @elseif($vPed['dir'] === 'down') <i class="fas fa-arrow-down"></i>
                    @else <i class="fas fa-minus"></i> @endif
                    {{ $vPed['pct'] }}% &nbsp;<span style="font-weight:400;color:#9ca3af;">vs per. anterior</span>
                </div>
            </div>
            <i class="fas fa-shopping-bag kni-ghost"></i>
        </div>

        {{-- Ticket Médio --}}
        <div class="kni-card">
            <div class="kni-icon purple"><i class="fas fa-chart-bar"></i></div>
            <div class="kni-card-body">
                <div class="kni-card-label">Ticket Médio</div>
                <div class="kni-card-value">R$ {{ number_format($vendas['ticket_medio'], 2, ',', '.') }}</div>
                <div class="kni-card-badge {{ $vTick['dir'] }}">
                    @if($vTick['dir'] === 'up') <i class="fas fa-arrow-up"></i>
                    @elseif($vTick['dir'] === 'down') <i class="fas fa-arrow-down"></i>
                    @else <i class="fas fa-minus"></i> @endif
                    {{ $vTick['pct'] }}% &nbsp;<span style="font-weight:400;color:#9ca3af;">vs per. anterior</span>
                </div>
            </div>
            <i class="fas fa-chart-bar kni-ghost"></i>
        </div>

    </div>

    {{-- ── Painéis inferiores ── --}}
    <div class="kni-panels">

        {{-- Alertas Críticos --}}
        <div class="kni-panel">
            <div class="kni-panel-header">
                <div class="title">
                    <span class="dot-alert"></span>
                    Alertas Críticos (Estoque e Margem)
                </div>
                <a href="{{ route('admin.kn_intelligence.dashboard') }}">Ver todos</a>
            </div>
            <div class="kni-panel-body">
                @if(count($estoqueRisco['produtos']) > 0)
                    <table class="kni-alert-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Produto</th>
                                <th>Estoque</th>
                                <th>Mínimo</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estoqueRisco['produtos'] as $prod)
                            <tr>
                                <td><code style="font-size:.75rem;">{{ $prod->codigo_produto }}</code></td>
                                <td>{{ Str::limit($prod->descricao, 28) }}</td>
                                <td><span class="badge-estoque">{{ $prod->quantidade }}</span></td>
                                <td style="color:#6b7280;">{{ $prod->quantidade_minima }}</td>
                                <td style="color:#374151;font-weight:600;">R$ {{ number_format($prod->valor_produto, 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="kni-empty">
                        <i class="fas fa-check-circle" style="color:#059669;opacity:.7;"></i>
                        Excelente! Nenhum alerta crítico ativo no momento.
                    </div>
                @endif
            </div>
        </div>

        {{-- Insights de IA --}}
        <div class="kni-panel">
            <div class="kni-panel-header">
                <div class="title">
                    <span class="dot-ai">✦</span>
                    Direcionamentos & Insights de IA
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form method="POST" action="{{ route('admin.kn_intelligence.gerar_insights') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary" style="border-radius:20px; font-size:.75rem; font-weight:600;" onclick="this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin me-1\'></i> Gerando...'; this.form.submit();">
                            <i class="fas fa-sync-alt me-1"></i> Atualizar Insights
                        </button>
                    </form>
                    <a href="{{ route('admin.kn_intelligence.assistant') }}" style="font-size:.8rem; font-weight:600; color:#6366f1; text-decoration:none;">Ver todos</a>
                </div>
            </div>
            <div class="kni-panel-body">
                @if($insights->count() > 0)
                    @foreach($insights as $insight)
                    <div class="kni-insight-item">
                        <span class="insight-tag">{{ $insight->tipo ?? 'insight' }}</span>
                        @if($insight->titulo)
                            <div style="font-weight:700; font-size:.85rem; color:#1a1d2e; margin-top:.2rem;">{{ $insight->titulo }}</div>
                        @endif
                        <div class="insight-text">{{ $insight->descricao ?? $insight->conteudo ?? $insight->content ?? '' }}</div>
                        <div class="insight-date">
                            <i class="fas fa-clock me-1"></i>{{ $insight->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="kni-empty">
                        <i class="fas fa-robot"></i>
                        Nenhum insight gerado recentemente.<br>
                        <form method="POST" action="{{ route('admin.kn_intelligence.gerar_insights') }}" class="d-inline-block mt-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary" style="border-radius:20px; font-size:.8rem;">
                                <i class="fas fa-bolt me-1"></i> Gerar Insights com IA Agora
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
function togglePersonalizado() {
    const form = document.getElementById('kni-date-form');
    form.classList.toggle('show');
}
// Abre automaticamente se já está no modo personalizado
@if($periodo === 'personalizado')
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('kni-date-form').classList.add('show');
});
@endif
</script>
@endpush
@endsection
