<div xmlns:wire="http://www.w3.org/1999/xhtml">
    <div class="container-fluid mt-4">
        <!-- Campo de busca -->
        <div class="card shadow border-0 mb-3">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-0">Gerenciar Produtos e Variações</h5>
                <hr>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb small">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.home') }}">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('produtos.produtos_ativos') }}">Produtos</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Lista de Produtos</li>
                    </ol>
                </nav>
                <div class="floating-label-group border-lable-flt">
                    <input class="form-control" placeholder="Buscar por produto ou variação..." type="text"
                           wire:model.debounce.500ms="search">
                    <label for="label-pesqusia">{{ __('Buscar por produto ou variação...') }}</label>
                </div>
            </div>
        </div>

        <div class="card shadow border-0 mb-3">
            <div wire:loading.delay class="text-center mt-3">
                <span class="spinner-border text-primary" role="status"></span>
                <span>Carregando dados...</span>
            </div>
        </div>

        <div class="card shadow border-0 mb-3 p-2">
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
                    <td class="text-align ">
                        <span class="badge {{ $produto->status ? 'bg-success' : 'bg-danger' }}">
                            {{ $produto->status ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>
                </tr>

                @if($this->isExpanded($produto->id))

                    @foreach($variacoesCarregadas[$produto->id] as $index => $variacao)

                        @if($variacao && is_object($variacao))
                            <tr class="bg-light variation-row" wire:key="variacao-{{ $variacao->id }}">
                                <td colspan="12">
                                    <div class="row align-items-start g-1 px-3 py-2">

                                        {{-- Subcódigo --}}
                                        <div class="col-md-1" style="max-width: 80px;">
                                            @if ($loop->first)
                                                <label class="form-label form-label-sm mb-1">Subcód.</label>
                                            @endif
                                            <input type="text" class="form-control form-control-sm" value="{{ $variacao->subcodigo }}" disabled />
                                        </div>

                                        {{-- Variação --}}
                                        <div class="col-md-2" style="max-width: 220px;">
                                            @if ($loop->first)
                                                <label class="form-label form-label-sm mb-1">Variação</label>
                                            @endif
                                            <input type="text"
                                                   class="form-control form-control-sm"
                                                   value="{{ $variacao->variacao }}"
                                                   wire:blur="atualizarCampo({{ $variacao->id }}, 'variacao', $event.target.value)">
                                        </div>

                                        {{-- Quantidade com botões --}}
                                        <div class="col-md-2" style="max-width: 100px;">
                                            @if ($loop->first)
                                                <label class="form-label form-label-sm mb-1">Qtd.</label>
                                            @endif
                                            <div class="input-group input-group-sm">
                                                <button wire:click="incrementar({{ $variacao->id }}, 'quantidade')"
                                                        wire:loading.attr="disabled"
                                                        wire:target="incrementar({{ $variacao->id }}, 'quantidade')"
                                                        class="btn btn-outline-success btn-sm">
                                                    <span wire:loading wire:target="incrementar({{ $variacao->id }}, 'quantidade')"
                                                          class="spinner-border spinner-border-sm">
                                                    </span>
                                                    <span wire:loading.remove wire:target="incrementar({{ $variacao->id }}, 'quantidade')">+</span>
                                                </button>

                                                <input type="text"
                                                       class="form-control text-center form-control-sm p-0"
                                                       wire:change="atualizarCampo({{ $variacao->id }}, 'quantidade', $event.target.value)"
                                                       value="{{ $variacao->quantidade }}">

                                                <button wire:click="decrementar({{ $variacao->id }}, 'quantidade')"
                                                        wire:loading.attr="disabled"
                                                        wire:target="decrementar({{ $variacao->id }}, 'quantidade')"
                                                        class="btn btn-outline-danger btn-sm">
                                                    <span wire:loading wire:target="decrementar({{ $variacao->id }}, 'quantidade')"
                                                          class="spinner-border spinner-border-sm">
                                                    </span>
                                                    <span wire:loading.remove wire:target="decrementar({{ $variacao->id }}, 'quantidade')">−</span>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-md-2" style="max-width: 100px;">
                                            @if ($loop->first)
                                                <label class="form-label form-label-sm mb-1">Estoque.</label>
                                            @endif
                                            <div class="input-group input-group-sm">
                                                <button wire:click="incrementar({{ $variacao->id }}, 'estoque')"
                                                        wire:loading.attr="disabled"
                                                        wire:target="incrementar({{ $variacao->id }}, 'estoque')"
                                                        class="btn btn-outline-success btn-sm">
                                                    <span wire:loading wire:target="incrementar({{ $variacao->id }}, 'estoque')"
                                                          class="spinner-border spinner-border-sm"></span>
                                                    <span wire:loading.remove wire:target="incrementar({{ $variacao->id }}, 'estoque')">+</span>
                                                </button>

                                                <input type="text"
                                                       class="form-control text-center form-control-sm p-0"
                                                       wire:change="atualizarCampo({{ $variacao->id }}, 'estoque', $event.target.value)"
                                                       value="{{ $variacao->estoque }}">

                                                <button wire:click="decrementar({{ $variacao->id }}, 'estoque')"
                                                        wire:loading.attr="disabled"
                                                        wire:target="decrementar({{ $variacao->id }}, 'estoque')"
                                                        class="btn btn-outline-danger btn-sm">
                                                    <span wire:loading wire:target="decrementar({{ $variacao->id }}, 'estoque')"
                                                          class="spinner-border spinner-border-sm"></span>
                                                    <span wire:loading.remove wire:target="decrementar({{ $variacao->id }}, 'estoque')">−</span>
                                                </button>
                                            </div>
                                        </div>
                                        {{-- Valor Unitário --}}
                                        <div class="col-md-2" style="max-width: 120px;">
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
                                        <div class="col-md-2" style="max-width: 120px;">
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
                                                <select class="form-select form-select-sm {{ $variacao->status == 0 ? 'bg-warning' : '' }}"
                                                        wire:change="atualizarCampo({{ $variacao->id }}, 'fornecedor', $event.target.value)">
                                                    <option value="">Selecione</option>
                                                    @foreach($fornecedores as $fornecedor)
                                                        <option value="{{ $fornecedor->id }}"
                                                            {{ $fornecedor->id == $variacao->fornecedor ? 'selected' : '' }}>
                                                            {{ strtoupper($fornecedor->nome) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            @if ($loop->first)
                                                <label class="form-label form-label-sm mb-1">Status</label>
                                            @endif
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           id="switchStatus{{ $variacao->id }}"
                                                           wire:change="atualizarCampo({{ $variacao->id }}, 'status', $event.target.checked ? 1 : 0)"
                                                            {{ $variacao->status ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="switchStatus{{ $variacao->id }}">
                                                        {{ $variacao->status ? 'Ativo' : 'Inativo' }}
                                                    </label>
                                                </div>
                                        </div>

                                        {{-- Ações --}}
                                        <div class="col-md-1 text-end">
                                            @if ($loop->first)
                                                <label class="form-label form-label-sm mb-1 d-block">Ações</label>
                                            @endif
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-outline-primary btn-sm"
                                                        wire:click="editarVariacao({{ $variacao->id }})"
                                                        wire:loading.attr="disabled"
                                                        title="Editar">
                                                        <span wire:loading.remove wire:target="editarVariacao({{ $variacao->id }})">
                                                            <i class="fas fa-edit"></i>
                                                        </span>
                                                            <span wire:loading wire:target="editarVariacao({{ $variacao->id }})">
                                                            <i class="fas fa-spinner fa-spin"></i>
                                                        </span>
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
        </div>

        <div class="d-flex justify-content-center">
            {!! $produtos->links('vendor.pagination.bootstrap-4') !!}
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('css/custom-input-float.css')}}"/>
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
@endpush

@push('scripts')
    <script src="{{ asset('js/util.js') }}"></script>
@endpush


