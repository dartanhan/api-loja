<div class="container-fluid mt-4 mb-4" xmlns:wire="http://www.w3.org/1999/xhtml" xmlns:livewire="">
    {{-- Header do Produto --}}
    <div class="card shadow border-0 mb-3">
        <div class="card-body">
            <h5 class="card-title fw-bold mb-0"><i class="fas fa-add"></i> Criar Novo Produto</h5>
            <hr>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}">
                            <i class="fas fa-home"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('produto.produto_create') }}">Produtos</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Criar Produto</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card shadow-sm mb-3 p-2 rounded">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card-header">
            <h6>
                <i class="fas fa-box"></i><b> Produto</b>
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-auto" style="max-width: 250px;">
                    <div class="floating-label-group border-lable-flt">
                        <input type="text" placeholder="{{ __('CÓDIGO PRODUTO (SKU)') }}"
                               wire:model.defer="produto.codigo_produto" id="codigo_produto"
                               class="form-control form-control-sm format-font"
                               data-toggle="tooltip" data-placement="top" title="Código do Produto (SKU)"
                               onkeyup="SomenteNumeros(this);" readonly >
                        <label for="label-codigo">{{ __('CÓDIGO PRODUTO(SKU)') }}</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="floating-label-group border-lable-flt">
                        <input type="text" placeholder="{{ __('NOME PRODUTO') }}"
                               wire:model.defer="produto.descricao" id="descricao"
                               class="form-control form-control-sm format-font
                                @error('produto.descricao') is-invalid @enderror"
                               data-toggle="tooltip" data-placement="top" title="Nome do Produto">
                        <label for="label-descricao">{{ __('NOME PRODUTO') }}</label>
                    </div>
                </div>
                <div class="col-auto" style="max-width: 150px;">
                    <div class="floating-label-group border-lable-flt">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">R$</span>
                            <input type="text" placeholder="{{ __('VALOR') }}"
                                   data-toggle="tooltip" data-placement="top" title="Valor do Produto"
                                   wire:model.defer="produto.valor_produto" id="valor_prdouto"
                                   class="form-control form-control-sm format-font moeda
                                   @error('produto.valor_produto') is-invalid @enderror">
                            <label for="label-valor-produto">{{ __('VALOR') }}</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="floating-label-group border-lable-flt col-xs-2 format-font">
                        <select wire:model.defer="produto.categoria_id" key="{{ now() }}" id="categoria_id" name="categoria_id"
                                class="form-select format-font form-control-sm @error('produto.categoria_id') is-invalid @enderror"
                                data-toggle="tooltip" data-placement="top" title="Categoria do Produto" required>
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
                        <select wire:model.defer="produto.origem_id" key="{{ now() }}" id="origem_id" name="origem_id_id"
                                class="form-select format-font form-control-sm @error('produto.origem_id') is-invalid @enderror"
                                data-toggle="tooltip" data-placement="top" title="Origem do Produto" required>
                            <option value="" class="select-custom">Selecione?</option>
                            @foreach($origens->sortBy('codigo') as $origem)
                                <option value="{{$origem->id}}">
                                    {{$origem->codigo}} - {{ \Illuminate\Support\Str::limit($origem->descricao, 30, '...')  }}
                                </option>
                            @endforeach
                        </select>
                        <label for="label-qtd">ORIGEM</label>
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
                                           wire:model="produto.status"
                                           wire:ignore.self
                                           onchange="toggleStatusDescription(this)">
                                </div>
                                <span class="small" id="statusLabel">
                                     {{ $produto['status'] ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" >
                <div class="card-body mb-3 p-2" id="filepond-wrapper" wire:key="filepond-produto">
{{--                    @livewire('produto-filepond', ['modelId' => $produto['id'] ?? null], key('produto-filepond'))--}}
                        <livewire:filepond-upload
                            context="produto"
                            :multiple="false"
                            wire:key="pond-produto"
                        />

                    {{-- Lista de arquivos já enviados --}}
                    {{--<ul class="list-group mt-2">
                        @foreach($temporaryFiles as $f)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $f->file }}
                                <button class="btn btn-sm btn-outline-danger"
                                        wire:click="deleteTemporaryFile('{{ $f->folder }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </li>
                        @endforeach
                    </ul>--}}
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-5 mb-3  rounded">
            <div class="card-header">
                <h6>
                    <i class="fas fa-cube"></i><b> Variações</b>
                </h6>
            </div>
            <div class="card-body">

                <livewire:produto-variacoes-form :produto="$produto" :variacoes="$variacoes"
                                                 :fornecedores="$fornecedores" :produtoId="$produtoId ?? null"
                                                 :codigoPai="$codigoProduto" />

            </div>
        </div>

        <div class="card shadow-sm mt-3 rounded text-end">
            <div class="card-body">
                <button class="btn btn-sm btn-outline-success"
                        wire:click="emitirSalvar"
                        wire:loading.attr="disabled"
                        wire:target="emitirSalvar">

                        <span wire:loading.remove wire:target="emitirSalvar">
                            <i class="fas fa-save me-1"></i> Salvar
                        </span>

                                    <span wire:loading wire:target="emitirSalvar">
                            <i class="fas fa-spinner fa-spin me-1"></i> Salvando...
                        </span>
                </button>
            </div>

        </div>
    </div>


    @push('styles')
        <link rel="stylesheet" type="text/css" href="{{URL::asset('css/custom-input-float.css')}}"/>
    @endpush

    @push('scripts')
        <script src="{{ asset('js/util.js') }}"></script>
        <script>
            document.addEventListener('livewire:load', () => {
                //inicializaFilePondVariacoes();
               // loadFilePondProduto();
                aplicarMascaraMoeda();
                aplicarMascaraQuantidade();
                aplicarMascaraDataDDMMYYYY();
                initTooltips();

                Livewire.hook('message.processed', () => {
                   // inicializaFilePondVariacoes()
                   // loadFilePondProduto();
                    aplicarMascaraMoeda();
                    aplicarMascaraQuantidade();
                    aplicarMascaraDataDDMMYYYY();
                    initTooltips();
                });
            });

            {{--document.addEventListener('livewire:load', () => {--}}
            {{--    const inputElement = document.querySelector('.filepond-produto');--}}
            {{--    const pond = FilePond.create(inputElement, {--}}
            {{--        server: {--}}
            {{--            process: {--}}
            {{--                url: '/admin/upload/tmp-upload',--}}
            {{--                method: 'POST',--}}
            {{--                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },--}}
            {{--                onload: (response) => {--}}
            {{--                    // Atualiza lista de arquivos no Livewire--}}
            {{--                    Livewire.emit('refreshTemporaryFiles');--}}
            {{--                    foldersEnviados.push(response);--}}
            {{--                    return response;--}}
            {{--                }--}}
            {{--            },--}}
            {{--            revert: (folder, load, error) => {--}}
            {{--                fetch('/admin/upload/tmp-delete', {--}}
            {{--                    method: 'DELETE',--}}
            {{--                    headers: {--}}
            {{--                        'Content-Type': 'application/json',--}}
            {{--                        'X-CSRF-TOKEN': '{{ csrf_token() }}'--}}
            {{--                    },--}}
            {{--                    body: JSON.stringify({ folder: folder })--}}
            {{--                }).then(res => {--}}
            {{--                    if(res.ok) {--}}
            {{--                        Livewire.emit('refreshTemporaryFiles');--}}
            {{--                        load();--}}
            {{--                    } else {--}}
            {{--                        error('Erro ao excluir');--}}
            {{--                    }--}}
            {{--                }).catch(() => error('Erro na comunicação'));--}}
            {{--            }--}}
            {{--        },--}}
            {{--        allowMultiple: true,--}}
            {{--        maxFiles: 10,--}}
            {{--        labelIdle: 'Arraste ou clique para enviar imagens'--}}
            {{--    });--}}
            // });
        </script>
@endpush
