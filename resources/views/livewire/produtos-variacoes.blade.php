<div xmlns:wire="http://www.w3.org/1999/xhtml">
    <div class="container mt-4">
        <h4>Gerenciar Produtos e Variações</h4>

        <!-- Campo de busca -->
        <div class="mb-3">
            <input type="text" class="form-control" placeholder="Buscar por produto ou variação..."
                   wire:model.debounce.500ms="search">
        </div>

        <table class="table table-bordered table-striped table-hover">
            <thead class="table-light">
            <tr>
                <th style="width: 50px;"></th>

                <th wire:click="sortBy('id')" style="cursor:pointer;">
                    Id
                    @if($sortField === 'id')
                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                    @else
                        <i class="fas fa-sort text-muted"></i>
                    @endif
                </th>

                <th wire:click="sortBy('descricao')" style="cursor:pointer;">
                    Produto
                    @if($sortField === 'descricao')
                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                    @else
                        <i class="fas fa-sort text-muted"></i>
                    @endif
                </th>

                <th>Preço</th>
                <th>Status</th>
            </tr>
            </thead>

            <tbody>
            @foreach($produtos as $produto)
                <tr wire:key="produto-{{ $produto->id }}">
                    <td>
                        <button wire:click="toggleExpand({{ $produto->id }})"
                                wire:loading.attr="disabled"
                                class="btn btn-sm btn-link p-0 m-0 align-middle">
                            <span wire:loading.remove wire:target="toggleExpand({{ $produto->id }})">
                                {{ $this->isExpanded($produto->id) ? '▼' : '▶' }}
                            </span>
                            <span wire:loading wire:target="toggleExpand({{ $produto->id }})"
                                  class="spinner-border spinner-border-sm text-secondary" role="status" aria-hidden="true"></span>
                        </button>
                    </td>
                    <td>{{ $produto->id }}</td>
                    <td>{{ $produto->descricao }}</td>
                    <td>R$ {{ number_format($produto->valor_produto, 2, ',', '.') }}</td>
                    <td>{{ $produto->status ? 'Ativo' : 'Inativo' }}</td>
                </tr>

                @if($this->isExpanded($produto->id))
                    @foreach($variacoesCarregadas[$produto->id] ?? [] as $variacao)
                        <tr class="bg-light variation-row" wire:key="variacao-{{ $variacao->id }}">
                            <td colspan="5">
                                <div class="row align-items-center px-3 py-2">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control"
                                               wire:blur="atualizarCampo({{ $variacao->id }}, 'variacao', $event.target.value)"
                                               value="{{ $variacao->variacao }}">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <button wire:click="decrementar({{ $variacao->id }})"
                                                    wire:loading.attr="disabled"
                                                    class="btn btn-outline-danger btn-sm">−</button>
                                            <input type="text" class="form-control text-center" value="{{ $variacao->quantidade }}" readonly>
                                            <button wire:click="incrementar({{ $variacao->id }})"
                                                    wire:loading.attr="disabled"
                                                    class="btn btn-outline-success btn-sm">+</button>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control"
                                               wire:blur="atualizarCampo({{ $variacao->id }}, 'valor_varejo', $event.target.value)"
                                               value="{{ $variacao->valor_varejo }}">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {!! $produtos->links('vendor.pagination.bootstrap-4') !!}
        </div>

        <div wire:loading.delay class="text-center mt-3">
            <span class="spinner-border text-primary" role="status"></span>
            <span>Carregando dados...</span>
        </div>
    </div>

    <style>
        .variation-row {
            animation: fadeSlideDown 0.3s ease-in-out;
        }

        @keyframes fadeSlideDown {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        th {
            user-select: none;
        }
    </style>
</div>
