<div xmlns:wire="http://www.w3.org/1999/xhtml">
    <div class="container mt-4">
        <h4>Gerenciar Produtos e Variações</h4>

        <!-- Campo de busca -->
        <div class="mb-3">
            <input type="text" class="form-control" placeholder="Buscar por produto ou variação..."
                   wire:model.debounce.500ms="search">
            <div wire:loading.delay class="text-center mt-3">
                <span class="spinner-border text-primary" role="status"></span>
                <span>Carregando dados...</span>
            </div>
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

                    @foreach($variacoesCarregadas[$produto->id] as $index => $variacao)

                        @if($variacao && is_object($variacao))
                            <tr class="bg-light variation-row" wire:key="variacao-{{ $variacao->id }}">
                                <td colspan="12">
                                    <div class="row align-items-start g-1 px-3 py-2">

                                        {{-- Subcódigo --}}
                                        <div class="col-md-1">
                                            @if ($loop->first)
                                                <label class="form-label form-label-sm mb-1">Subcód.</label>
                                            @endif
                                            <input type="text" class="form-control form-control-sm" value="{{ $variacao->subcodigo }}" disabled />
                                        </div>

                                        {{-- Variação --}}
                                        <div class="col-md-3">
                                            @if ($loop->first)
                                                <label class="form-label form-label-sm mb-1">Variação</label>
                                            @endif
                                            <input type="text"
                                                   class="form-control form-control-sm"
                                                   value="{{ $variacao->variacao }}"
                                                   wire:blur="atualizarCampo({{ $variacao->id }}, 'variacao', $event.target.value)">
                                        </div>

                                        {{-- Quantidade com botões --}}
                                        <div class="col-md-2">
                                            @if ($loop->first)
                                                <label class="form-label form-label-sm mb-1">Qtd.</label>
                                            @endif
                                            <div class="input-group input-group-sm">
                                                <button wire:click="decrementar({{ $variacao->id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="decrementar({{ $variacao->id }})"
                                                        class="btn btn-outline-danger btn-sm">
                                                    <span wire:loading wire:target="decrementar({{ $variacao->id }})" class="spinner-border spinner-border-sm"></span>
                                                    <span wire:loading.remove wire:target="decrementar({{ $variacao->id }})">−</span>
                                                </button>

                                                <input type="text"
                                                       class="form-control text-center form-control-sm"
                                                       wire:blur="atualizarCampo({{ $variacao->id }}, 'quantidade', $event.target.value)"
                                                       value="{{ $variacao->quantidade }}">

                                                <button wire:click="incrementar({{ $variacao->id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="incrementar({{ $variacao->id }})"
                                                        class="btn btn-outline-success btn-sm">
                                                    <span wire:loading wire:target="incrementar({{ $variacao->id }})" class="spinner-border spinner-border-sm"></span>
                                                    <span wire:loading.remove wire:target="incrementar({{ $variacao->id }})">+</span>
                                                </button>
                                            </div>
                                        </div>


                                        {{-- Valor Unitário --}}
                                        <div class="col-md-2">
                                            @if ($loop->first)
                                                <label class="form-label form-label-sm mb-1">Valor Varejo</label>
                                            @endif
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">R$</span>
                                                <input type="text"
                                                       class="form-control moeda form-control-sm"
                                                       wire:blur="atualizarCampo({{ $variacao->id }}, 'valor_varejo', $event.target.value)"
                                                       value="{{ number_format($variacao->valor_varejo, 2, ',', '.') }}">
                                            </div>
                                        </div>

                                        {{-- Valor Produto --}}
                                        <div class="col-md-2">
                                            @if ($loop->first)
                                                <label class="form-label form-label-sm mb-1">Valor Produto</label>
                                            @endif
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">R$</span>
                                                <input type="text"
                                                       class="form-control moeda form-control-sm"
                                                       wire:blur="atualizarCampo({{ $variacao->id }}, 'valor_produto', $event.target.value)"
                                                       value="{{ number_format($variacao->valor_produto, 2, ',', '.') }}">
                                            </div>
                                        </div>

                                        {{-- Fornecedor --}}
                                        <div class="col-md-2">
                                            @if ($loop->first)
                                                <label class="form-label form-label-sm mb-1">Fornecedor</label>
                                            @endif
                                                <select class="form-select form-select-sm"
                                                        wire:change="atualizarCampo({{ $variacao->id }}, 'fornecedor_id', $event.target.value)">
                                                    <option value="">Selecione</option>
                                                    @foreach($fornecedores as $fornecedor)
                                                        <option value="{{ $fornecedor->id }}"
                                                            {{ $fornecedor->id == $variacao->fornecedor ? 'selected' : '' }}>
                                                            {{ strtoupper($fornecedor->nome) }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                        </div>

                                        {{-- Categoria --}}
                                        <div class="col-md-2">
                                            @if ($loop->first)
                                                <label class="form-label form-label-sm mb-1">Categoria</label>
                                            @endif
                                            <select class="form-select form-select-sm"
                                                    wire:change="atualizarCampo({{ $variacao->id }}, 'categoria_id', $event.target.value)">
                                                <option value="">Selecione</option>
                                                @foreach($categorias as $categoria)
                                                    <option value="{{ $categoria['id']}}">
                                                        {{ $categoria['nome'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Ações --}}
                                        <div class="col-md-1 text-center">
                                            @if ($loop->first)
                                                <label class="form-label form-label-sm mb-1 d-block">Ações</label>
                                            @endif
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-outline-danger btn-sm" wire:click="excluirVariacao({{ $variacao->id }})" title="Excluir">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                                <button class="btn btn-outline-primary btn-sm" wire:click="abrirFotos({{ $variacao->id }})" title="Fotos">
                                                    <i class="fas fa-image"></i>
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </td>
                            </tr>

                        @endif
                    @endforeach
                @endif
            @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {!! $produtos->links('vendor.pagination.bootstrap-4') !!}
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
@push('scripts')
    <script>
        $('.quantidade').mask('000000', { reverse: false });

        function aplicarMascaraMoeda() {
            $('.moeda').mask('R$ 000.000.000,00', {
                reverse: true,
                placeholder: 'R$ 0,00'
            });
        }

        document.addEventListener("livewire:load", function () {
            // Aplica na primeira renderização
            aplicarMascaraMoeda();

            // Reaplica após DOM ser atualizado por Livewire
            Livewire.hook('message.processed', () => {
                aplicarMascaraMoeda();
            });
        });
    </script>
@endpush


