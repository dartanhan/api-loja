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
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-imagens" data-toggle="tab" data-target="#aba-imagens" type="button" role="tab" aria-controls="imagens" aria-selected="false">
                            <i class="fas fa-image me-1"></i> <strong>Imagens</strong>
                        </button>
                    </li>
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
                                                   wire:model="produto.codigo_produto" id="codigo_produto"
                                                   class="form-control form-control-sm format-font"

                                                   onkeyup="SomenteNumeros(this);" readonly >
                                            <label for="label-codigo">{{ __('CÓDIGO DO PRODUTO (SKU)') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="floating-label-group border-lable-flt">
                                            <input type="text" placeholder="{{ __('DECRIÇÃO') }}"
                                                   wire:model="produto.descricao" id="descricao"
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
                                                       wire:model="produto.valor_produto" class="form-control form-control-sm format-font valor-mask" >
                                                <label for="label-valor-produto">{{ __('VALOR') }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="floating-label-group border-lable-flt col-xs-2 format-font">
                                            <select wire:model="produto.categoria_id" key="{{ now() }}" id="categoria_id" name="categoria_id"
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
                                                               wire:click="$emit('confirmarAlteracaoStatus','produto', '' ,{{ $produto['id'] }}, event.target)"
                                                            {{ $produto['status'] ? 'checked' : '' }}>
                                                    </div>
                                                    <span class="small">
                                                        {{ $produto['status'] ? 'Ativo' : 'Inativo' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                    </div>

                    <div class="tab-pane fade" id="aba-imagens" role="tabpanel" aria-labelledby="tab-imagens">
                        <div class="card mb-3 p-2">
                            <form method="post" autocomplete="off" id="formImage" name="formImage" enctype="multipart/form-data" class="form-inline">
                                @csrf
                                <input type="hidden" id="products_variation_id" name="products_variation_id" value="{{$variacoes[0]['id']}}">
                                <input type="hidden" name="tipoImage" id="tipoImage" value="variation">

                                    <div class="card-body mb-3 p-2" id="filepond-wrapper">
                                        <input type="file"
                                               multiple
                                               id="image"
                                               name="image[]"
                                               data-max-files="10"
                                               data-allow-reorder="true"
                                               data-max-file-size="3MB"
                                               data-allow-multiple="true"
                                               class="filepond" />
                                    </div>

                            </form>
                            <div class="row">
                                @foreach($imagens as $imagem)
                                    <div class="col-md-2 mb-3 imagem-item" id="imagem-{{ $imagem->id }}" wire:key="imagem-{{ $imagem->id }}">
                                        <div class="border rounded p-2 text-center position-relative">
                                            <img src="{{ asset('storage/' . $imagem->path) }}"
                                                 alt="Imagem"
                                                 class="img-fluid mb-2 rounded"
                                                 style="max-height: 150px;min-height: 120px; object-fit: cover;">

                                            <div class="d-flex justify-content-center">
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="confirmarExclusaoImagem({{ $imagem->id }})"
                                                        data-toggle="tooltip" data-placement="right"  title="Excluir imagem"
                                                        id="btn-excluir-{{ $imagem->id }}">
                                                    <i class="fas fa-trash-alt" id="icon-trash-{{ $imagem->id }}"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="aba-variacoes" role="tabpanel" aria-labelledby="tab-variacoes">
                        <div class="card-body mb-3">
                            <div class="row g-2">
{{--                                <div class="text-end mb-3">--}}
{{--                                    <button class="btn btn-sm btn-outline-primary" wire:click="adicionarVariacao" wire:loading.attr="disabled">--}}
{{--                                        <span wire:loading.remove wire:target="adicionarVariacao">--}}
{{--                                            <i class="fas fa-plus me-1"></i> Nova Variação--}}
{{--                                        </span>--}}
{{--                                        <span wire:loading wire:target="adicionarVariacao">--}}
{{--                                            <i class="fas fa-spinner fa-spin me-1"></i> Adicionando...--}}
{{--                                        </span>--}}
{{--                                    </button>--}}
{{--                                </div>--}}

                                @foreach($variacoes as $index => $variacao)
                                    <div class="row mb-3 g-2 p-2 align-items-end">
                                        <div class="row card p-1 mb-2">
                                            <div class="row card-body ">
                                                <div class="col-md-2 mb-3">
                                                    <div class="floating-label-group border-lable-flt">
                                                        <input type="text" placeholder="{{ __('SUB CÓDIGO (SKU)') }}"
                                                               value="{{ $variacao['subcodigo'] }}"  class="form-control form-control-sm format-font" disabled >
                                                        <label for="label-codigo-{{ $variacao['subcodigo'] }}">{{ __('SUB CÓDIGO(SKU)') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="floating-label-group border-lable-flt">
                                                        <input type="text" placeholder="{{ __('GTIN') }}"
                                                               wire:model.defer="variacoes.{{ $index }}.gtin"  class="form-control form-control-sm format-font" >
                                                        <label for="label-gtin-{{ $index }}">{{ __('GTIN') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
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

                                                <div class="col-auto" style="max-width: 150px;">
                                                    <div class="floating-label-group border-lable-flt">
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text">R$</span>
                                                            <input type="text" placeholder="{{ __('VALOR VAREJO') }}"
                                                                   wire:model.defer="variacoes.{{ $index }}.valor_varejo" class="form-control form-control-sm format-font valor-mask" >
                                                            <label for="label-valor-varejo-{{ $index }}">{{ __('VALOR VAREJO') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-auto" style="max-width: 150px;">
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
                                                <div class="col-auto" style="max-width: 150px;">
                                                    <div class="floating-label-group border-lable-flt">
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" placeholder="{{ __('VALIDADE') }}"
                                                                   wire:model.defer="variacoes.{{ $index }}.validade"
                                                                   class="form-control form-control-sm format-font data-mask" maxlength="10">
                                                            <label for="label-valor-validade-{{ $index }}">{{ __('VALIDADE') }}</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="floating-label-group border-lable-flt col-xs-2 format-font">
                                                        <select wire:model.defer="variacoes.{{ $index }}.fornecedor_id" class="form-select format-font form-control-sm" required>
                                                            <option value="">Selecione</option>
                                                            @foreach($fornecedores as $f)
                                                                <option value="{{ $f['id'] }}" title="{{ $f['nome'] }}">
                                                                    {{ \Illuminate\Support\Str::limit(ucfirst(strtolower($f['nome'])), 30, '...') }} </option>
                                                            @endforeach
                                                        </select>
                                                        <label for="status">FORNECEDOR</label>
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
                                                                           wire:click="$emit('confirmarAlteracaoStatus','variacao', {{ $variacao['id'] }},{{$produto['id']}}, event.target)"
                                                                        {{ $produto['status'] ? 'checked' : '' }}>
                                                                </div>
                                                                <span class="small">
                                                        {{ $produto['status'] ? 'Ativo' : 'Inativo' }}
                                                    </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

{{--                                                <div class="col-auto" style="max-width: 100px;">--}}
{{--                                                    <button class="btn btn-sm btn-outline-danger" wire:click="removerVariacao({{ $index }})" wire:loading.attr="disabled">--}}
{{--                                                        <span wire:loading.remove wire:target="removerVariacao({{ $index }})">--}}
{{--                                                            <i class="fas fa-times"></i>--}}
{{--                                                        </span>--}}
{{--                                                        <span wire:loading wire:target="removerVariacao({{ $index }})">--}}
{{--                                                            <i class="fas fa-spinner fa-spin me-1"></i> Removendo...--}}
{{--                                                        </span>--}}
{{--                                                    </button>--}}
{{--                                                </div>--}}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="aba-fiscal" role="tabpanel" aria-labelledby="fiscal-tab">
                        <div class="card-body mb-3">
                            <div class="row g-2">
                                <div class="col-md-2">
                                    <div class="floating-label-group border-lable-flt">
                                        <input type="number" placeholder="{{ __('NCM') }}"
                                               wire:model="produto.ncm" id="ncm" class="form-control form-control-sm format-font"
                                               data-toggle="tooltip" data-placement="top" title="NCM">
                                        <label for="label-ncm">{{ __('NCM') }}</label>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="floating-label-group border-lable-flt">
                                        <input type="number" placeholder="{{ __('CEST') }}"
                                               wire:model="produto.cest" id="cest" class="form-control form-control-sm format-font"
                                               data-toggle="tooltip" data-placement="top" title="CEST">
                                        <label for="label-ncm">{{ __('CEST') }}</label>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="floating-label-group border-lable-flt">
                                        <input type="number" placeholder="{{ __('CFOP Interno (Venda para o mesmo estado)') }}"
                                               wire:model="produto.cfop_interno" id="cfop_interno" class="form-control form-control-sm format-font"
                                               data-toggle="tooltip" data-placement="top" title="CFOP Interno (Venda para o mesmo estado)">
                                        <label for="label-cfpt-interno">{{ __('CFOP Interno') }}</label>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="floating-label-group border-lable-flt">
                                        <input type="number" placeholder="{{ __('CFOP Interestadual (Venda para outro estado)') }}"
                                               wire:model="produto.cfop_inter" id="cfop_inter" class="form-control form-control-sm format-font"
                                               data-toggle="tooltip" data-placement="top" title="CFOP Interestadual (Venda para outro estado)">
                                        <label for="label-cfop-inter">{{ __('CFOP Interestadual') }}</label>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="floating-label-group border-lable-flt col-xs-2 format-font">
                                        <select wire:model="produto.origem_id" id="origem_id" name="origem_id"
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
                    <button type="button" id="btn-livewire-salvar" wire:click="salvar" style="display: none;"></button>

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
    <script src="{{ asset('js/util.js') }}"></script>
    <script src="{{URL::asset('js/filePond.js')}}"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            $('.valor-mask').mask('#.##0,00', {reverse: true});
            $('.data-mask').mask('##/##/####', {reverse: true});

            const activeTab = sessionStorage.getItem('activeTab');

            if (activeTab) {
                $('#myTab a[href="' + activeTab + '"]').tab('show');
            }
        });

        Livewire.hook('message.processed', () => {
            $('.valor-mask').mask('#.##0,00', {reverse: true});
            $('.data-mask').mask('##/##/####', {reverse: true});
            $('[data-toggle="tooltip"]').tooltip();

            //reativa as abas
            const activeTab = sessionStorage.getItem('activeTab');
            if (activeTab) {
                $('#myTab a[href="' + activeTab + '"], #myTab button[data-target="' + activeTab + '"]').tab('show');
            }
            loadFilePond();
        });

        //Controla as tabs
        $('#myTab button').on('click', function (event) {
            event.preventDefault()
            $(this).tab('show')
        })

        //Ao clicar em salvar aciona o Livewire
        document.getElementById('btn-salvar-produto').addEventListener('click', function () {
            //sessionStorage.removeItem('activeTab'); // <-- volta pra aba 1 após salvar
            // Chama o botão invisível com wire:click="salvar"
            document.getElementById('btn-livewire-salvar').click();
            if (typeof Livewire !== 'undefined') {
                Livewire.emit('setPastasImagens', foldersEnviados);
                Livewire.emit('salvar');
                loadFilePond();
            }
        });


        //Função SweetAlert de confirmação deleção
        function confirmarExclusaoImagem(id) {
            Swal.fire({
                title: 'Excluir imagem?',
                text: "Essa ação não poderá ser desfeita!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Muda ícone para spinner
                    const icon = document.getElementById(`icon-trash-${id}`);
                    icon.classList.remove('fa-trash-alt');
                    icon.classList.add('fa-spinner', 'fa-spin');

                    // Dispara o evento Livewire
                    Livewire.emit('deletarImagem', id);
                }
            });
        }

        // Salva a aba ativa no sessionStorage ao trocar
        $('#myTab button[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            const tabId = $(e.target).data('target');
            //console.log('tabId', tabId);
            sessionStorage.setItem('activeTab', tabId);
        });

        //para centralizar re-render o Filepond quando for rendreizado pleo livewire
        function loadFilePond() {
            const container = document.getElementById('filepond-wrapper');
            if (!container) return;

            // Remove conteúdo anterior
            container.innerHTML = `
                <input type="file"
                       multiple
                       id="image"
                       name="image[]"
                       data-max-files="10"
                       data-allow-reorder="true"
                       data-max-file-size="3MB"
                       data-allow-multiple="true"
                       class="filepond" />
            `;

            const inputElement = container.querySelector('input');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            FilePond.create(inputElement, {
                server: {
                    process: {
                        url: '/admin/upload/tmp-upload',
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        onload: (res) => {
                            foldersEnviados.push(res);
                            return res;
                        }
                    },
                    revert: '/admin/upload/tmp-delete',
                },
                labelIdle: 'Arraste imagens ou <span class="filepond--label-action">clique para escolher</span>',
                allowMultiple: true
            });
        }


    </script>
@endpush
