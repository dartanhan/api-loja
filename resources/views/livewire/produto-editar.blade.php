<div class="container-fluid mt-4" xmlns:wire="http://www.w3.org/1999/xhtml" xmlns:livewire=""
     xmlns:livewire="http://www.w3.org/1999/html">
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->has('variacoes'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first('variacoes') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

        {{-- Header do Produto --}}
        <div class="card shadow border-0 mb-3">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-0">Editar Produto</h5>
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
                        <li class="breadcrumb-item active" aria-current="page">Editar Produto</li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- Tabs do Produto --}}

        <div class="card shadow-sm rounded">
            {{-- Tabs dentro do Card --}}
            <div class="mt-2 p-2">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-geral" data-toggle="tab" data-target="#aba-geral" type="button" role="tab" aria-controls="geral" aria-selected="true">
                            <i class="fas fa-info-circle me-1"></i><strong>Inf. Gerais</strong>
                        </button>
                    </li>
{{--                    <li class="nav-item" role="presentation">--}}
{{--                        <button class="nav-link" id="tab-imagens" data-toggle="tab" data-target="#aba-imagens" type="button" role="tab" aria-controls="imagens" aria-selected="false">--}}
{{--                            <i class="fas fa-image me-1"></i> <strong>Imagens</strong>--}}
{{--                        </button>--}}
{{--                    </li>--}}
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-variacoes" data-toggle="tab" data-target="#aba-variacoes" type="button" role="tab" aria-controls="variacoes" aria-selected="false">
                            <i class="fas fa-tags me-1"></i> <strong>Variação</strong>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-fiscal" data-toggle="tab" data-target="#aba-fiscal" type="button" role="tab" aria-controls="fiscal" aria-selected="false">
                            <i class="fas fa-file-invoice-dollar me-1"></i> <strong>Inf. Fiscais</strong>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="aba-geral" role="tabpanel" aria-labelledby="tab-geral">
                            <div class="card-body mb-3">
                                <div class="row g-2">
                                    <div class="col-md-2">
                                        <div class="floating-label-group border-lable-flt">
                                            <input type="text" placeholder="{{ __('CÓDIGO DO PRODUTO (SKU)') }}"
                                                   wire:model.defer="produtos.codigo_produto" id="codigo_produto"
                                                   class="form-control form-control-sm format-font"

                                                   onkeyup="SomenteNumeros(this);" readonly >
                                            <label for="label-codigo">{{ __('CÓDIGO DO PRODUTO (SKU)') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="floating-label-group border-lable-flt">
                                            <input type="text" placeholder="{{ __('DECRIÇÃO') }}"
                                                   wire:model.defer="produtos.descricao" id="descricao"
                                                   class="form-control form-control-sm format-font"
                                                   data-toggle="tooltip" data-placement="top" title="Descrição/Nome do produto" required autofocus>
                                            <label for="label-descricao">{{ __('DESCRIÇÃO') }}</label>
                                        </div>
                                    </div>
                                    {{-- Valor  --}}
                                    <div class="col-auto" style="max-width: 150px;">
                                        <div class="floating-label-group border-lable-flt">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">R$</span>
                                                <input type="text" placeholder="{{ __('VALOR') }}" data-toggle="tooltip" data-placement="right" title="Valor do Produto"
                                                       wire:model.defer="produtos.valor_produto" class="form-control form-control-sm format-font moeda" >
                                                <label for="label-valor-produto">{{ __('VALOR') }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="floating-label-group border-lable-flt col-xs-2 format-font">
                                            <select wire:model.defer="produtos.categoria_id" key="{{ now() }}" id="categoria_id" name="categoria_id"
                                                    class="form-select format-font form-control-sm" title="Categoria do Produto" required>
                                                <option value="" class="select-custom">Selecione?</option>
                                                @foreach($categorias->sortBy('nome') as $categoria)
                                                    <option value="{{$categoria->id}}"> {{ ucfirst(strtolower($categoria->nome)) }}</option>
                                                @endforeach
                                            </select>
                                            <label for="label-qtd">CATEGORIAS</label>
                                        </div>
                                    </div>


                                    <div class="col-md-2">
                                        <div class="floating-label-group border-lable-flt col-xs-2 format-font">
                                            <div class="form-control d-flex align-items-center justify-content-between px-2" style="height: 38px;">
                                                <label class="form-label m-0">STATUS</label>

                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="form-check form-switch m-0">
                                                        <input type="checkbox"
                                                               class="form-check-input"
                                                               id="switchStatus"
                                                               wire:click="$emit('confirmarAlteracaoStatus','produto', '' ,{{ $produtos['id'] }}, event.target)"
                                                            {{ $produtos['status'] ? 'checked' : '' }}>
                                                    </div>
                                                    <span class="small">
                                                        {{ $produtos['status'] ? 'Ativo' : 'Inativo' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="card-body mb-3 p-2">
                                            {{-- Se existir imagem do produto pai --}}
                                            @if(isset($produto['images']) && count($produto['images']) > 0)
                                                <div class="text-center">
                                                    {{-- Miniatura da imagem --}}
                                                    <img src="{{ asset('storage/product/'.$produto['id'].'/'.$produto['images'][0]->path) }}"
                                                         data-toggle="tooltip" data-placement="top"  title="Clique, para ampliar"
                                                         class="img-thumbnail mb-2"
                                                         style="max-width: 150px; cursor: pointer;"
                                                         onclick="previewImagem('{{ asset('storage/product/'.$produto['id'].'/'.$produto['images'][0]->path) }}')">

                                                    {{-- Botão de excluir --}}
                                                    <div class="d-flex justify-content-center">
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="confirmarExclusao({{ $produto['images'][0]->id }})"
                                                                data-toggle="tooltip" data-placement="right"  title="Excluir imagem"
                                                                id="btn-excluir-{{ $produto['images'][0]->id }}">
                                                            <i class="fas fa-trash-alt" id="icon-trash-{{ $produto['images'][0]->id}}"></i>
                                                        </button>
                                                    </div>
                                                </div>

{{--                                                --}}{{-- Modal de preview --}}
{{--                                                <div class="modal fade" id="previewImagemProduto" tabindex="-1" aria-hidden="true">--}}
{{--                                                    <div class="modal-dialog modal-dialog-centered modal-lg">--}}
{{--                                                        <div class="modal-content bg-transparent border-0 shadow-none">--}}
{{--                                                            <div class="modal-body text-center p-0">--}}
{{--                                                                <img src="{{ asset('storage/product/'.$produto['id'].'/'.$produto['images'][0]->path) }}"--}}
{{--                                                                     class="img-fluid rounded shadow"--}}
{{--                                                                     alt="Preview Imagem Produto">--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
                                            @else
                                                {{-- Filepond só aparece se não existir imagem --}}
                                                <div id="filepond-wrapper" wire:ignore wire:key="filepond-produto">
                                                    <livewire:filepond-upload
                                                        context="produto"
                                                        :multiple="false"
                                                        wire:key="pond-produto"
                                                    />
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>

                   {{-- <div class="tab-pane fade p-2" id="aba-imagens" role="tabpanel" aria-labelledby="tab-imagens">

                            @if(isset($produto['products']) && count($produto['products']) > 0)
                                <div class="card mb-3 p-2">
                                    <form method="post" autocomplete="off" id="formImage" name="formImage" enctype="multipart/form-data" class="form-inline">
                                        @csrf
                                        <input type="hidden" id="products_variation_id" name="products_variation_id" value="{{$produto['products'][0]['id']}}">
                                        <input type="hidden" name="tipoImage" id="tipoImage" value="variation">

                                        <div class="card-body mb-3 p-2" id="filepond-wrapper">
--}}{{--                                            <input type="file"--}}{{--
--}}{{--                                                   multiple--}}{{--
--}}{{--                                                   id="image"--}}{{--
--}}{{--                                                   name="image[]"--}}{{--
--}}{{--                                                   data-max-files="10"--}}{{--
--}}{{--                                                   data-allow-reorder="true"--}}{{--
--}}{{--                                                   data-max-file-size="3MB"--}}{{--
--}}{{--                                                   data-allow-multiple="true"--}}{{--
--}}{{--                                                   class="filepond" />--}}{{--
                                        </div>

                                    </form>
                                    <div class="row">
                                        @if(isset($produto['images']) && count($produto['images']) > 0)
                                            @foreach($produto['images'] as $index => $imagem)
                                                <livewire:filepond-upload
                                                    context="variacao"
                                                    :multiple="true"
                                                    wire:key="filepond-variacao-{{ $imagem->id }}"
                                                />

    --}}{{--                                            <livewire:filepond-upload--}}{{--
    --}}{{--                                                :multiple="true"--}}{{--
    --}}{{--                                                :imagens-existentes="$imagem['imagens'] ?? []"--}}{{--
    --}}{{--                                                wire:key="filepond-variacao-{{ $imagem->id }}"--}}{{--
    --}}{{--                                                data-is-variacao="true"--}}{{--
    --}}{{--                                                data-variacao-id="{{ $imagem->id }}"--}}{{--
    --}}{{--                                            />--}}{{--

                                                <div class="col-md-2 mb-3 imagem-item" id="imagem-{{ $imagem->id }}" wire:key="imagem-{{ $imagem->id }}">
                                                    <div class="border rounded p-2 text-center position-relative">
                                                        <img src="{{ asset('storage/' . $imagem->path) }}"
                                                             alt="Imagem"
                                                             class="img-fluid mb-2 rounded"
                                                             style="max-height: 150px;min-height: 120px; object-fit: cover;">

                                                        <div class="d-flex justify-content-center">
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    onclick="confirmarExclusaoImagem({{ $imagem->id }}, true, {{$produto['products'][$index]['id']}})"
                                                                    data-toggle="tooltip" data-placement="right"  title="Excluir imagem"
                                                                    id="btn-excluir-{{ $imagem->id }}">
                                                                <i class="fas fa-trash-alt" id="icon-trash-{{ $imagem->id }}"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div>
                                    <p>Sem imagens para essa variação</p>
                                </div>
                            @endif
                    </div>--}}
                    <div class="tab-pane fade p-2" id="aba-variacoes" role="tabpanel" aria-labelledby="tab-variacoes">
                        <livewire:produto-variacoes-form :produto="$produto" :variacoes="$variacoes"
                                                         :fornecedores="$fornecedores" :produtoId="$produtoId ?? null"
                                                            :codigoPai="$codigoProduto" />
                    </div>


                    <div class="tab-pane fade p-2" id="aba-fiscal" role="tabpanel" aria-labelledby="fiscal-tab">
                        <div class="card-body mb-3">
                            <div class="row g-2">
                                <div class="col-md-2">
                                    <div class="floating-label-group border-lable-flt">
                                        <input type="number" placeholder="{{ __('NCM') }}"
                                               wire:model.defer="produtos.ncm" id="ncm" class="form-control form-control-sm format-font"
                                               data-toggle="tooltip" data-placement="top" title="NCM">
                                        <label for="label-ncm">{{ __('NCM') }}</label>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="floating-label-group border-lable-flt">
                                        <input type="number" placeholder="{{ __('CEST') }}"
                                               wire:model.defer="produtos.cest" id="cest" class="form-control form-control-sm format-font"
                                               data-toggle="tooltip" data-placement="top" title="CEST">
                                        <label for="label-ncm">{{ __('CEST') }}</label>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="floating-label-group border-lable-flt">
                                        <input type="number" placeholder="{{ __('CFOP Interno (Venda para o mesmo estado)') }}"
                                               wire:model="produtos.cfop_interno" id="cfop_interno" class="form-control form-control-sm format-font"
                                               data-toggle="tooltip" data-placement="top" title="CFOP Interno (Venda para o mesmo estado)">
                                        <label for="label-cfpt-interno">{{ __('CFOP Interno') }}</label>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="floating-label-group border-lable-flt">
                                        <input type="number" placeholder="{{ __('CFOP Interestadual (Venda para outro estado)') }}"
                                               wire:model.defer="produtos.cfop_inter" id="cfop_inter" class="form-control form-control-sm format-font"
                                               data-toggle="tooltip" data-placement="top" title="CFOP Interestadual (Venda para outro estado)">
                                        <label for="label-cfop-inter">{{ __('CFOP Interestadual') }}</label>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="floating-label-group border-lable-flt col-xs-2 format-font">
                                        <select wire:model.defer="produtos.origem_id" id="origem_id" name="origem_id"
                                                class="form-select format-font form-control-sm"
                                                data-toggle="tooltip" data-placement="top" title="ICMS - Origem" required>
                                            <option value="" class="select-cstom">Selecione</option>
                                            @foreach($origem_nfces as $origem_nfce)
                                                <option value="{{$origem_nfce->id}}"
                                                        title="{{ $origem_nfce['descricao'] }}">
                                                    {{ strtoupper($origem_nfce->codigo)  }} - {{ \Illuminate\Support\Str::limit($origem_nfce->descricao, 30, '...')  }}</option>
                                            @endforeach
                                        </select>
                                        <label for="label-qtd">ORIGEM DO PRODUTO</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <div class="card shadow border-0 mb-3 mt-2">
            <div class="card-body">
                <div class="mt-3 text-end">
                    <button class="btn btn-sm btn-outline-success" id="btn-salvar-produto" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="salvar">
                            <i class="fas fa-save me-1"></i> Salvar
                        </span>
                                <span wire:loading wire:target="salvar">
                            <i class="fas fa-spinner fa-spin me-1"></i> Salvando...
                        </span>
                    </button>
                    <button type="button" id="btn-livewire-salvar"
                            wire:click="$emitTo('produto-variacoes-form', 'syncAndSave')"
                            style="display: none;"></button>

                    <button wire:click="voltar"
                            wire:loading.attr="disabled"
                            class="btn btn-sm btn-outline-secondary">
                                <span wire:loading wire:target="voltar"
                                      class="spinner-border spinner-border-sm me-1"
                                      role="status" aria-hidden="true">
                                </span>
                                <span wire:loading.remove wire:target="voltar">
                            <i class="fas fa-arrow-left me-1"></i> Voltar
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- modal de preview de imagem--}}
        <livewire:produto-preview-image></livewire:produto-preview-image>
</div>

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('css/custom-input-float.css')}}"/>
    <style>
        .input-group-text {
            padding-left: 0.4rem;
            padding-right: 0.4rem;
        }
        .variacao-row {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            gap: 12px; /* espaço entre campos */
            margin-bottom: 10px;
            overflow-x: auto; /* scroll horizontal se necessário */
        }

        .variacao-row > * {
            flex-shrink: 0; /* impede que os itens encolham */
        }

        .variacao-subcodigo {
            width: 100px;
        }

        .variacao-descricao {
            flex: 1 1 250px; /* flex grow para ocupar espaço restante */
        }

        .variacao-qtd {
            width: 80px;
        }

        .variacao-valor-varejo,
        .variacao-valor-produto {
            width: 120px;
        }

        .variacao-remove-btn {
            width: 40px;
            text-align: center;
        }
        .format-font{
            font-size: medium;
        }


    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/util.js') }}"></script>
    <script type="module" src="{{URL::asset('js/comum.js')}}"></script>
{{--    <script src="{{ asset('js/filePond.js') }}"></script>--}}
    <script>
        document.addEventListener('livewire:load', function () {
           // loadFilePondProduto();
            loadSetAbas();
            aplicarMascaraMoeda();
            aplicarMascaraDataDDMMYYYY();

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                sessionStorage.setItem('activeTab', $(e.target).attr('href'));
            });

            Livewire.hook('message.processed', () => {
               // loadFilePondProduto();
                loadSetAbas();
                aplicarMascaraMoeda();
                aplicarMascaraDataDDMMYYYY();
            });
        });


        //Controla as tabs
        $('#myTab button').on('click', function (event) {
            event.preventDefault()
            $(this).tab('show')
        });

        // Salva a aba ativa no sessionStorage ao trocar
        $('#myTab button[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            const tabId = $(e.target).data('target');
            //console.log('tabId', tabId);
            sessionStorage.setItem('activeTab', tabId);
        });


        function confirmarExclusao(id) {
            Swal.fire({
                title: 'Tem certeza?',
                text: "A imagem será excluída permanentemente!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('removerImagem', { id: id });
                }
            })
        }

        </script>
    @endpush
