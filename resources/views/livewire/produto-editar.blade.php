<div class="container-fluid mt-4" xmlns:wire="http://www.w3.org/1999/xhtml">
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

    <div class="card mb-3 w-100">
        <div class="card-header">Dados do Produto</div>
        <div class="card-body mb-3">
            <div class="row g-2">
                <div class="col-md-2">
                    <div class="floating-label-group border-lable-flt">
                        <input type="text" placeholder="{{ __('CÓDIGO DO PRODUTO') }}"
                               wire:model="produto.codigo_produto" id="codigo_produto" class="form-control form-control-sm format-font"
                               onkeyup="SomenteNumeros(this);" readonly >
                        <label for="label-codigo">{{ __('CÓDIGO DO PRODUTO') }}</label>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="floating-label-group border-lable-flt">
                        <input type="text" placeholder="{{ __('DECRIÇÃO') }}"
                               wire:model="produto.descricao" id="descricao" class="form-control form-control-sm format-font" required autofocus>
                        <label for="label-descricao">{{ __('DESCRIÇÃO') }}</label>
                    </div>
                </div>

                <div class="col-md-1">
                    <div class="floating-label-group border-lable-flt">
                        <input type="number" placeholder="{{ __('NCM') }}"
                               wire:model="produto.ncm" id="ncm" class="form-control form-control-sm format-font" required>
                        <label for="label-ncm">{{ __('NCM') }}</label>
                    </div>
                </div>

                <div class="col-md-1">
                    <div class="floating-label-group border-lable-flt">
                        <input type="number" placeholder="{{ __('CEST') }}"
                               wire:model="produto.cest" id="cest" class="form-control form-control-sm format-font" required>
                        <label for="label-ncm">{{ __('CEST') }}</label>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="floating-label-group border-lable-flt col-xs-2 format-font">
                        <select wire:model="produto.origem_id" id="origem_id" name="origem_id" class="form-select format-font form-control-sm" title="Origem do Produto" required>
                            <option value="" class="select-cstom">Selecione?</option>
                            @foreach($origem_nfces as $origem_nfce)
                                <option value="{{$origem_nfce->id}}"
                                        title="{{ $origem_nfce['descricao'] }}">
                                    {{ strtoupper($origem_nfce->codigo)  }} - {{ \Illuminate\Support\Str::limit($origem_nfce->descricao, 30, '...')  }}</option>
                            @endforeach
                        </select>
                        <label for="label-qtd">ORIGEM DO PRODUTO</label>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="floating-label-group border-lable-flt col-xs-2 format-font">
                        <select wire:model="produto.categoria_id" id="categoria_id" name="categoria_id" class="form-select format-font form-control-sm"
                                title="Categoria do Produto" required>
                            <option value="" class="select-custom">Selecione?</option>
                            @foreach($categorias as $categoria)
                                <option value="{{$categoria->id}}"> {{ ucfirst(strtolower($categoria->nome)) }}</option>
                            @endforeach
                        </select>
                        <label for="label-qtd">CATEGORIA</label>
                    </div>
                </div>

                <div class="col-md-1">
                    <div class="floating-label-group border-lable-flt col-xs-2 format-font">
                        <select wire:model="produto.status" id="status" class="form-select format-font form-control-sm" required>
                            <option value="">Selecione</option>
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                        <label for="status">STATUS</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card w-100">
        <div class="card-header">Variações</div>
        <div class="card-body">
            <div class="text-end mb-3">
                <button class="btn btn-sm btn-outline-primary" wire:click="adicionarVariacao" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="adicionarVariacao">
                        <i class="fas fa-plus me-1"></i> Nova Variação
                    </span>
                    <span wire:loading wire:target="adicionarVariacao">
                        <i class="fas fa-spinner fa-spin me-1"></i> Adicionando...
                    </span>
                </button>
            </div>

            @foreach($variacoes as $index => $variacao)
                <div class="row mb-3 g-2 align-items-end">
                    <div class="col-auto" style="max-width: 100px">
                        <div class="floating-label-group border-lable-flt">
                            <input type="text" placeholder="{{ __('SUB CÓDIGO') }}"
                                   value="{{ $variacao['subcodigo'] }}"  class="form-control form-control-sm format-font" disabled >
                            <label for="label-codigo-{{ $variacao['subcodigo'] }}">{{ __('SUB CÓDIGO') }}</label>
                        </div>
                    </div>
                    <div class="col-auto" style="max-width: 100px">
                        <div class="floating-label-group border-lable-flt">
                            <input type="text" placeholder="{{ __('GTIN') }}"
                                   wire:model.defer="variacoes.{{ $index }}.gtin"  class="form-control form-control-sm format-font" >
                            <label for="label-gtin-{{ $index }}">{{ __('GTIN') }}</label>
                        </div>
                    </div>
                    <div class="col-auto" style="max-width: 300px">
                        <div class="floating-label-group border-lable-flt">
                            <input type="text" placeholder="{{ __('VARIAÇÃO') }}"
                                   wire:model.defer="variacoes.{{ $index }}.variacao" class="form-control form-control-sm format-font" >
                            <label for="label-variacao-{{ $index }}">{{ __('VARIAÇÃO') }}</label>
                        </div>
                    </div>
                    {{-- QTD --}}
                    <div class="col-auto" style="max-width: 100px;">
                        <div class="floating-label-group border-lable-flt">
                            <input type="number"
                                   placeholder="{{ __('QTD') }}"
                                   wire:model.defer="variacoes.{{ $index }}.quantidade"
                                   class="form-control form-control-sm format-font variacao-qtd">
                            <label>{{ __('QTD') }}</label>
                        </div>
                    </div>

                    {{-- ESTOQUE --}}
                    <div class="col-auto" style="max-width: 100px;">
                        <div class="floating-label-group border-lable-flt">
                            <input type="number"
                                   placeholder="{{ __('ESTOQUE') }}"
                                   wire:model.defer="variacoes.{{ $index }}.estoque"
                                   class="form-control form-control-sm format-font variacao-estoque">
                            <label>{{ __('ESTOQUE') }}</label>
                        </div>
                    </div>

                    {{-- QTD MÍN --}}
                    <div class="col-auto" style="max-width: 100px;">
                        <div class="floating-label-group border-lable-flt">
                            <input type="number"
                                   placeholder="{{ __('QTD MIN') }}"
                                   wire:model.defer="variacoes.{{ $index }}.quantidade_minima"
                                   class="form-control form-control-sm format-font variacao-qtd-min">
                            <label>{{ __('QTD MIN') }}</label>
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="floating-label-group border-lable-flt">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">R$</span>
                                <input type="text" placeholder="{{ __('VALOR VAREJO') }}"
                                       wire:model.defer="variacoes.{{ $index }}.valor_varejo" class="form-control form-control-sm format-font valor-mask" >
                                <label for="label-valor-varejo-{{ $index }}">{{ __('VALOR VAREJO') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="floating-label-group border-lable-flt">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">R$</span>
                                <input type="text" placeholder="{{ __('VALOR PRODUTO') }}"
                                       wire:model.defer="variacoes.{{ $index }}.valor_produto" class="form-control form-control-sm format-font valor-mask" >
                                <label for="label-valor-produto-{{ $index }}">{{ __('VALOR PRODUTO') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto" style="max-width: 100px">
                        <div class="floating-label-group border-lable-flt">
                            <div class="input-group input-group-sm">
                                <input type="text" placeholder="{{ __('DESC.EM %') }}"
                                       wire:model.defer="variacoes.{{ $index }}.percentage" class="form-control form-control-sm format-font valor-mask" >
                                <label for="label-valor-percentage-{{ $index }}">{{ __('DESC.EM %') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto" style="max-width: 110px">
                        <div class="floating-label-group border-lable-flt">
                            <div class="input-group input-group-sm">
                                <input type="text" placeholder="{{ __('VALIDADE') }}"
                                       wire:model.defer="variacoes.{{ $index }}.validade"
                                       class="form-control form-control-sm format-font data-mask" maxlength="10">
                                <label for="label-valor-validade-{{ $index }}">{{ __('VALIDADE') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto" style="max-width: 220px">
                        <div class="floating-label-group border-lable-flt col-xs-2 format-font">
                            <select wire:model="variacoes.{{ $index }}.fornecedor_id" class="form-select format-font form-control-sm" required>
                                <option value="">Selecione</option>
                                @foreach($fornecedores as $f)
                                    <option value="{{ $f['id'] }}" title="{{ $f['nome'] }}">
                                        {{ \Illuminate\Support\Str::limit(ucfirst(strtolower($f['nome'])), 30, '...') }} </option>
                                @endforeach
                            </select>
                            <label for="status">FORNECEDOR</label>
                        </div>
                    </div>
                    <div class="col-auto" style="max-width: 100px;">
                        <button class="btn btn-sm btn-outline-danger" wire:click="removerVariacao({{ $index }})" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="removerVariacao({{ $index }})">
                                <i class="fas fa-times"></i>
                            </span>
                            <span wire:loading wire:target="removerVariacao({{ $index }})">
                                <i class="fas fa-spinner fa-spin me-1"></i> Removendo...
                            </span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-3 text-end">
        <button class="btn btn-sm btn-outline-success" wire:click="salvar" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="salvar">
                <i class="fas fa-save me-1"></i> Salvar
            </span>
            <span wire:loading wire:target="salvar">
                <i class="fas fa-spinner fa-spin me-1"></i> Salvando...
            </span>
        </button>

        <a href="{{ route('produtos.produtos_livewire') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>
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
    <script src="{{URL::asset('assets/jquery/jquery.validate.min.js')}}"></script>
    <script type="module" src="{{URL::asset('js/comum.js')}}"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            $('.valor-mask').mask('#.##0,00', {reverse: true});
            $('.data-mask').mask('##/##/####', {reverse: true});
        });

        Livewire.hook('message.processed', () => {
            $('.valor-mask').mask('#.##0,00', {reverse: true});
            $('.data-mask').mask('##/##/####', {reverse: true});
        });
    </script>
@endpush
