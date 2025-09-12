<div xmlns:wire="http://www.w3.org/1999/xhtml" xmlns:livewire="">
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
            <thead class="table-light text-center">
            <tr>
                <th style="width: 50px;">#</th>
                <th wire:click="sortBy('id')" style="cursor:pointer;">
                    Id
                    @if($sortField === 'id')
                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                    @else
                        <i class="fas fa-sort text-muted"></i>
                    @endif
                </th>
                <th>Código</th>
                <th class="w-10">Imagem</th>
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
                <th>Ações</th>
            </tr>
            </thead>

            <tbody>

            @foreach($produtos as $produto)
                <tr wire:key="produto-{{ $produto->id }}" >
                    <td class="text-center align-middle">
                        <button wire:click="toggleExpand({{ $produto->id }})"
                                wire:loading.attr="disabled"
                                class="btn btn-sm p-0 m-0 align-middle">

                            {{-- Ícone de expandir/recolher --}}
                            <span wire:loading.remove wire:target="toggleExpand({{ $produto->id }})">
                                <img src="{{ asset($this->isExpanded($produto->id) ? 'img/minus.png' : 'img/plus.png') }}"
                                      class="icon-expand" data-toggle="tooltip" data-placement="top" title="Expandir">
                            </span>

                            {{-- Spinner de carregamento --}}
                            <span wire:loading wire:target="toggleExpand({{ $produto->id }})"
                                  class="spinner-border spinner-border-sm text-secondary"
                                  role="status" aria-hidden="true">
                            </span>
                        </button>
                    </td>
                    <td class="text-center align-middle">{{ $produto->id }}</td>
                    <td class="text-center align-middle">{{$produto->codigo_produto}}</td>
                    <td class="text-center align-middle">
                        @php
                            /** @var TYPE_NAME $produto */
                            $primeiraImagem = $produto->images->first();
                            $path = $primeiraImagem ? 'product/' . $produto->images[0]->produto_id  . '/' . $primeiraImagem->path : null;

                            $imagemPath = ($path && Storage::disk('public')->exists($path))
                                ? $path
                                : 'produtos/not-image.png';
                        @endphp

                        <img
                            src="{{ asset('storage/' . $imagemPath) }}"
                            class="img-thumbnail"
                            style="cursor: pointer;"
                            onclick="previewImagem('{{ asset('storage/' . $imagemPath) }}')"
                            data-toggle="tooltip" data-placement="top" title="Click para ampliar Imagem"
                        />
                    </td>
                    <td class="text-center align-middle">{{ $produto->descricao }}</td>
                    <td class="text-center align-middle">R$ {{ number_format($produto->valor_produto, 2, ',', '.') }}</td>
                    <td class="text-center align-middle" width="100px">
                        <span class="badge {{ $produto->status ? 'bg-success' : 'bg-danger' }}">
                            {{ $produto->status ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>
                    <td class="text-center align-middle">
                        {{-- Ações --}}
                        <button class="btn btn-outline-primary btn-sm"
                                wire:click="editarProduto({{ $produto->id }})"
                                wire:loading.attr="disabled"
                                data-toggle="tooltip" data-placement="top" title="Editar">
                                    <span wire:loading.remove wire:target="editarProduto({{ $produto->id }})">
                                        <i class="fas fa-edit"></i>
                                    </span>
                            <span wire:loading wire:target="editarProduto({{ $produto->id }})">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </td>
                </tr>

                @if($this->isExpanded($produto->id))

                    @foreach($variacoesCarregadas[$produto->id] as $index => $variacao)

                        @if($variacao && is_object($variacao))
                            <tr class="bg-light variation-row" wire:key="variacao-{{ $variacao->id }}">

                                <td colspan="12">
                                    <div class="row g-2 align-items-start px-3 py-2">
                                        {{-- Coluna das Imagens --}}
                                        <div class="col-md-3" style="max-width: 270px;">
                                            @forelse ($variacao->images as $image)
                                                <img
                                                    src="{{ asset('storage/'.$image->path) }}"
                                                    class="img-thumbnail"
                                                    style="cursor: pointer;"
                                                    data-toggle="tooltip" data-placement="top"
                                                    onclick="previewImagem('{{ asset('storage/'.$image->path) }}')"
                                                    title="Click para ampliar"
                                                />
                                            @empty
                                                <img
                                                    src="{{ asset('storage/produtos/not-image.png') }}"
                                                    class="img-thumbnail"
                                                    style="cursor: pointer; max-width: 100%; height: auto;"
                                                    onclick="previewImagem('{{ asset('storage/produtos/not-image.png') }}')"
                                                    alt="Imagem não disponível"
                                                />
                                            @endforelse
                                        </div>

                                        {{-- Coluna dos Campos --}}
                                        <div class="col-md-9">
                                            <div class="row g-2">
                                                <div class="col-md-2">
                                                    <label class="form-label form-label-sm mb-1">Subcód.</label>
                                                    <input type="text" class="form-control form-control-sm" value="{{ $variacao->subcodigo }}" disabled />
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label form-label-sm mb-1">Variação</label>
                                                    <input type="text"
                                                           class="form-control form-control-sm"
                                                           value="{{ $variacao->variacao }}"
                                                           wire:blur="atualizarCampo({{ $variacao->id }}, 'variacao', $event.target.value)">
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="form-label form-label-sm mb-1">Quantidade</label>
                                                    <div class="input-group input-group-sm">
                                                        <button wire:click="decrementar({{ $variacao->id }}, 'quantidade')"
                                                                wire:loading.attr="disabled"
                                                                wire:target="decrementar({{ $variacao->id }}, 'quantidade')"
                                                                class="btn btn-outline-danger btn-sm">
                                                        <span wire:loading wire:target="decrementar({{ $variacao->id }}, 'quantidade')"
                                                              class="spinner-border spinner-border-sm">
                                                        </span>
                                                            <span wire:loading.remove wire:target="decrementar({{ $variacao->id }}, 'quantidade')">−</span>
                                                        </button>

                                                        <input type="text"
                                                               class="form-control text-center form-control-sm p-0"
                                                               wire:change="atualizarCampo({{ $variacao->id }}, 'quantidade', $event.target.value)"
                                                               value="{{ $variacao->quantidade }}">

                                                        <button wire:click="incrementar({{ $variacao->id }}, 'quantidade')"
                                                                wire:loading.attr="disabled"
                                                                wire:target="incrementar({{ $variacao->id }}, 'quantidade')"
                                                                class="btn btn-outline-success btn-sm">
                                                        <span wire:loading wire:target="incrementar({{ $variacao->id }}, 'quantidade')"
                                                              class="spinner-border spinner-border-sm">
                                                        </span>
                                                            <span wire:loading.remove wire:target="incrementar({{ $variacao->id }}, 'quantidade')">+</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label form-label-sm mb-1">Estoque.</label>
                                                    <div class="input-group input-group-sm">
                                                        <button wire:click="decrementar({{ $variacao->id }}, 'estoque')"
                                                                wire:loading.attr="disabled"
                                                                wire:target="decrementar({{ $variacao->id }}, 'estoque')"
                                                                class="btn btn-outline-danger btn-sm">
                                                        <span wire:loading wire:target="decrementar({{ $variacao->id }}, 'estoque')"
                                                              class="spinner-border spinner-border-sm"></span>
                                                            <span wire:loading.remove wire:target="decrementar({{ $variacao->id }}, 'estoque')">−</span>
                                                        </button>

                                                        <input type="text"
                                                               class="form-control text-center form-control-sm p-0"
                                                               wire:change="atualizarCampo({{ $variacao->id }}, 'estoque', $event.target.value)"
                                                               value="{{ $variacao->estoque }}">

                                                        <button wire:click="incrementar({{ $variacao->id }}, 'estoque')"
                                                                wire:loading.attr="disabled"
                                                                wire:target="incrementar({{ $variacao->id }}, 'estoque')"
                                                                class="btn btn-outline-success btn-sm">
                                                        <span wire:loading wire:target="incrementar({{ $variacao->id }}, 'estoque')"
                                                              class="spinner-border spinner-border-sm"></span>
                                                            <span wire:loading.remove wire:target="incrementar({{ $variacao->id }}, 'estoque')">+</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                {{-- Ações --}}
                                                <div class="col-md-1 text-end">
                                                    <label class="form-label form-label-sm mb-1 d-block">Ações</label>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-outline-primary btn-sm"
                                                                wire:click="editarVariacao({{ $variacao->id }})"
                                                                wire:loading.attr="disabled"
                                                                data-toggle="tooltip" data-placement="top" title="Editar">
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
                                            <div class="row g-2 mt-2">
                                                <div class="col-md-2">
                                                    <label class="form-label form-label-sm mb-1">Valor Varejo</label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">R$</span>
                                                        <input type="text"
                                                               class="form-control moeda form-control-sm"
                                                               wire:blur="atualizarCampo({{ $variacao->id }}, 'valor_varejo', $event.target.value)"
                                                               value="{{ number_format($variacao->valor_varejo, 2, ',', '.') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label form-label-sm mb-1">Valor Produto</label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">R$</span>
                                                        <input type="text"
                                                               class="form-control moeda form-control-sm"
                                                               wire:blur="atualizarCampo({{ $variacao->id }}, 'valor_produto', $event.target.value)"
                                                               value="{{ number_format($variacao->valor_produto, 2, ',', '.') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-auto">
                                                    <label class="form-label form-label-sm mb-1">Fornecedor</label>
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
                                                <div class="col-md-2">
                                                    <label class="form-label form-label-sm mb-1">Situação</label>
                                                    <div class="form-check form-switch mt-1">
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
        <!-- Modal Preview de Imagem -->
        <livewire:produto-preview-image/>

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
        .icon-expand {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            padding: 2px;
            box-shadow: 0 0 3px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease;
            cursor: pointer;
        }

        .icon-expand:hover {
            transform: scale(1.1);
        }
        .img-thumbnail{
            border-radius: 5%;
            width: 110px;
            height: 100px;
            cursor: pointer;
        }
        .td-center {
            text-align: center;        /* Centraliza horizontal */
            vertical-align: middle;    /* Centraliza vertical */
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/util.js') }}"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            loadSetAbas();
            aplicarMascaraMoeda?.();
            aplicarMascaraQuantidade?.();
            aplicarMascaraDataDDMMYYYY?.();
            initTooltips?.();

            Livewire.hook('message.processed', () => {
                loadSetAbas();
                aplicarMascaraMoeda?.();
                aplicarMascaraQuantidade?.();
                aplicarMascaraDataDDMMYYYY?.();
                initTooltips?.();
            });

        });

    </script>
@endpush


