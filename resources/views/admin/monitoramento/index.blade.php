@extends('layouts.layout')

@section('menu')

@include('admin.menu')

@endsection

@section('content')
<div class="container">
    <h2>Monitoramento de Estoque</h2>

    <form method="GET" action="{{ route('monitoramento.index') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select name="tipo" id="tipo" class="form-control">
                <option value="">Todos</option>
                <option value="entrada" {{ ($filtros['tipo'] ?? '') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                <option value="saida" {{ ($filtros['tipo'] ?? '') == 'saida' ? 'selected' : '' }}>Saída</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="codigo" class="form-label">Subcódigo da Variação</label>
            <input type="text" name="codigo" class="form-control" value="{{ $filtros['codigo'] ?? '' }}">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Tipo</th>
                <th>Produto</th>
                <th>Variação</th>
                <th>Subcódigo</th>
                <th>Quantidade</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movimentacoes as $mov)
                <tr>
                    <td>{{ $mov->id }}</td>
                    <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge bg-{{ $mov->tipo == 'entrada' ? 'success' : 'danger' }}">
                            {{ ucfirst($mov->tipo) }}
                        </span>
                    </td>
                    <td>{{ optional($mov->variacao->produto)->descricao ?? '-' }}</td>
                    <td>{{ $mov->variacao->variacao ?? '-' }}</td>
                    <td>{{ $mov->variacao->subcodigo ?? '-' }}</td>
                    <td>{{ $mov->quantidade }}</td>
                    <td>{{ $mov->motivo ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Nenhuma movimentação encontrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $movimentacoes->withQueryString()->links() }}
</div>
@endsection
