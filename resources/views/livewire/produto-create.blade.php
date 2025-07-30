<div class="container-fluid mt-4" xmlns:wire="http://www.w3.org/1999/xhtml">
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

    <div class="card shadow-sm mb-3 rounded">
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

            <div class="row"  wire:ignore>
                <div class="card-body mb-3 p-2" id="filepond-wrapper"  wire:ignore>
                    <input type="file"
                           id="image"
                           name="image[]"
                           data-max-files="1"
                           data-allow-reorder="true"
                           data-max-file-size="3MB"
                           data-allow-multiple="true"
                           class="filepond" />
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3 rounded">
        <div class="card-header">
            <h6>
                <i class="fas fa-cube"></i><b> Variações</b>
            </h6>
        </div>
        <div class="card-body">
            <button wire:click="adicionarVariacao" wire:loading.attr="disabled" class="btn btn-sm btn-outline-primary">
                <span wire:loading.remove wire:target="adicionarVariacao">
                    <i class="fas fa-plus-circle me-1"></i> Adicionar Variação
                </span>
                <span wire:loading wire:target="adicionarVariacao">
                    <i class="fas fa-spinner fa-spin me-1"></i> Adicionando...
                </span>
            </button>
            @foreach ($variacoes as $index => $variacao)
                <div class="card shadow-sm mb-2 mt-3 rounded">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-auto mb-2" style="max-width:120px;">
                                <div class="floating-label-group border-lable-flt">
                                    <input type="text" placeholder="{{ __('SUB CÓDIGO') }}"
                                           wire:model="variacoes.{{ $index }}.subcodigo"
                                           class="form-control form-control-sm format-font"
                                           data-toggle="tooltip" data-placement="top" title="Sub Código" readonly>
                                    <label for="label-subcodigo">{{ __('SUB CÓDIGO') }}</label>
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <div class="floating-label-group border-lable-flt">
                                    <input type="text" placeholder="{{ __('GTIN') }}"
                                           wire:model="variacoes.{{ $index }}.gtin"
                                           class="form-control form-control-sm format-font"
                                           data-toggle="tooltip" data-placement="top" title="GTIN">
                                    <label for="label-gtin">{{ __('GTIN') }}</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="floating-label-group border-lable-flt">
                                    <input type="text" placeholder="{{ __('VARIACÃO') }}"
                                           wire:model="variacoes.{{ $index }}.variacao"
                                           class="form-control form-control-sm format-font"
                                           data-toggle="tooltip" data-placement="top" title="Nome da Variação">
                                    <label for="label-variacao">{{ __('VARIACÃO') }}</label>
                                </div>
                            </div>
                            <div class="col-md-1 mb-2">
                                <div class="floating-label-group border-lable-flt">
                                    <input type="text" placeholder="{{ __('QTD') }}"
                                           wire:model="variacoes.{{ $index }}.quantidade"
                                           class="form-control form-control-sm format-font"
                                           data-toggle="tooltip" data-placement="top" title="Quantidade">
                                    <label for="label-quantidade">{{ __('QTD') }}</label>
                                </div>
                            </div>
                            <div class="col-md-1 mb-2">
                                <div class="floating-label-group border-lable-flt">
                                    <input type="text" placeholder="{{ __('QTD MIN') }}"
                                           wire:model="variacoes.{{ $index }}.quantidade_minima"
                                           class="form-control form-control-sm format-font"
                                           data-toggle="tooltip" data-placement="top" title="Quantidade Minima">
                                    <label for="label-quantidade_minima">{{ __('QTD MIN') }}</label>
                                </div>
                            </div>
                            <div class="col-md-1 mb-2">
                                <div class="floating-label-group border-lable-flt">
                                    <input type="text" placeholder="{{ __('ESTOQUE') }}"
                                           wire:model="variacoes.{{ $index }}.estoque"
                                           class="form-control form-control-sm format-font"
                                           data-toggle="tooltip" data-placement="top" title="Estoque">
                                    <label for="label-estoque">{{ __('ESTOQUE') }}</label>
                                </div>
                            </div>
                            <div class="col-auto mb-2" style="max-width: 150px;">
                                <div class="floating-label-group border-lable-flt">
                                    <input type="text" placeholder="{{ __('VALOR VAREJO') }}"
                                           wire:model.defer="variacoes.{{ $index }}.valor_varejo"
                                           class="form-control form-control-sm format-font moeda"
                                           data-toggle="tooltip" data-placement="top" title="Valor Varejo">
                                    <label for="label-valor_varejo">{{ __('VALOR VAREJO') }}</label>
                                </div>
                            </div>
                            <div class="col-auto mb-2" style="max-width: 150px;">
                                <div class="floating-label-group border-lable-flt">
                                    <input type="text" placeholder="{{ __('VALOR ATACADO') }}"
                                           wire:model.defer="variacoes.{{ $index }}.valor_atacado"
                                           class="form-control form-control-sm format-font moeda"
                                           data-toggle="tooltip" data-placement="top" title="Valor Atacado">
                                    <label for="label-valor_atacado">{{ __('VALOR ATACADO') }}</label>
                                </div>
                            </div>
                            <div class="col-auto mb-2" style="max-width: 150px;">
                                <div class="floating-label-group border-lable-flt">
                                    <input type="text" placeholder="{{ __('VALOR PAGO') }}"
                                           wire:model.defer="variacoes.{{ $index }}.valor_pago"
                                           class="form-control form-control-sm format-font moeda"
                                           data-toggle="tooltip" data-placement="top" title="Valor Pago">
                                    <label for="label-valor_pago">{{ __('VALOR PAGO') }}</label>
                                </div>
                            </div>
                            <div class="col-auto mb-2" style="max-width:120px;">
                                <div class="floating-label-group border-lable-flt">
                                    <input type="text" placeholder="{{ __('DESCONTO') }}"
                                           wire:model="variacoes.{{ $index }}.percentage"
                                           class="form-control form-control-sm format-font"
                                           data-toggle="tooltip" data-placement="top" title="Desconto em %">
                                    <label for="label-percentage">{{ __('DESCONTO EM %') }}</label>
                                </div>
                            </div>
                            <div class="col-auto mb-2" style="max-width:150px;">
                                <div class="floating-label-group border-lable-flt">
                                    <input type="text" placeholder="{{ __('VALIDADE') }}"
                                           wire:model.defer="variacoes.{{ $index }}.validade"
                                           class="form-control form-control-sm format-font"
                                           data-toggle="tooltip" data-placement="top" title="Validade"
                                           onkeyup="formatDate(this)" maxlength="10">
                                    <label for="label-validade">{{ __('VALIDADE') }}</label>
                                </div>
                            </div>
                            <div class="col-auto mb-2" style="max-width:250px;">
                                <div class="floating-label-group border-lable-flt col-xs-2 format-font">
                                    <select  wire:model.defer="variacoes.{{ $index }}.fornecedor_id"
                                             class="form-select format-font form-control-sm" required>
                                        <option value="">Selecione</option>
                                        @foreach($fornecedores as $f)
                                            <option value="{{ $f['id'] }}" title="{{ $f['nome'] }}">
                                                {{ \Illuminate\Support\Str::limit(ucfirst(strtolower($f['nome'])), 30, '...') }} </option>
                                        @endforeach
                                    </select>
                                    <label for="status">FORNECEDOR</label>
                                </div>
                            </div>
                            <div class="col-auto" style="max-width: 150px;">
                                <div class="floating-label-group border-lable-flt col-xs-2 format-font">
                                    <div class="form-control d-flex align-items-center justify-content-between px-2" style="height: 38px;">
                                        <label class="form-label m-0">STATUS</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="form-check form-switch m-0">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       id="switchStatus"
        {{--                                               wire:click="$emit('confirmarAlteracaoStatus','variacao', {{ $variacao['id'] }},{{$produto['id']}}, event.target)"--}}
                                                    {{ $produto['status'] ? 'checked' : '' }}>
                                            </div>
                                            <span class="small">
                                                {{ $produto['status'] ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto" style="max-width: 150px;">
                                <button class="btn btn-sm btn-outline-danger"
                                        data-toggle="tooltip" data-placement="top" title="Remover Variação"
                                        wire:click="removerVariacao({{ $index }})" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="removerVariacao({{ $index }})">
                                            <i class="fas fa-times"></i>
                                        </span>
                                        <span wire:loading wire:target="removerVariacao({{ $index }})">
                                            <i class="fas fa-spinner fa-spin me-1"></i> Removendo...
                                        </span>
                                </button>
                            </div>
                            <div class="row" id="filepond-wrapper-variacao-{{ $index }}">
{{--                                <input type="file" multiple class="filepond-variacao"--}}
{{--                                       data-max-files="10" data-allow-reorder="true" data-max-file-size="3MB" data-allow-multiple="true" />--}}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card shadow-sm mt-3 rounded text-end">
        <div class="card-body">
            <button class="btn btn-sm btn-outline-success" id="btn-salvar-produto" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="salvar">
                    <i class="fas fa-save me-1"></i> Salvar
                </span>
            <span wire:loading wire:target="salvar">
                    <i class="fas fa-spinner fa-spin me-1"></i> Salvando...
                </span>
            </button>
            <button type="button" id="btn-livewire-salvar" wire:click="salvar" style="display: none;"></button>
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
            loadFilePondProduto();
            inicializaFilePondVariacoes();
            aplicarMascaraMoeda();
            aplicarMascaraQuantidade();
            aplicarMascaraDataDDMMYYYY();
            initTooltips();

            Livewire.hook('message.processed', () => {
                loadFilePondProduto();
                inicializaFilePondVariacoes()
                aplicarMascaraMoeda();
                aplicarMascaraQuantidade();
                aplicarMascaraDataDDMMYYYY();
                initTooltips();
            });
        });

    </script>
@endpush
