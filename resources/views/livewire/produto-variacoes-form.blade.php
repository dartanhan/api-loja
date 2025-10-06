<div xmlns:wire="http://www.w3.org/1999/xhtml" xmlns:livewire="">
        <div class="row g-2">
            <div class="mb-2">
                <button wire:click="adicionarVariacao" wire:loading.attr="disabled" class="btn btn-sm btn-outline-primary">
                    <span wire:loading.remove wire:target="adicionarVariacao">
                        <i class="fas fa-plus-circle me-1"></i> Adicionar Variação
                    </span>
                    <span wire:loading wire:target="adicionarVariacao">
                        <i class="fas fa-spinner fa-spin me-1"></i> Adicionando...
                    </span>
                </button>
            </div>
            <div class="card-body">
            @foreach($variacoes as $index => $variacao)
                <div class="row card mb-2 p-3" style="border: 1px solid #cecece" >
                    <div class="card mb-2" style="background-color: #f8f9fa" wire:key="variacao-{{ $variacao['id'] }}">
                        <div class="row card-body">
                            <div class="col-auto mb-3" style="max-width: 100px;min-width: 150px">
                                <div class="floating-label-group border-lable-flt">
                                    <input type="text" placeholder="{{ __('SUB CÓDIGO (SKU)') }}"
                                           wire:model.defer="variacoes.{{ $index }}.subcodigo"  class="form-control form-control-sm format-font" readonly >
                                    <label for="label-codigo-{{ $variacao['subcodigo'] }}">{{ __('SUB CÓDIGO(SKU)') }}</label>
                                </div>
                            </div>
                            <div class="col-auto mb-3" style="max-width: 100px;min-width: 150px">
                                <div class="floating-label-group border-lable-flt">
                                    <input type="text" placeholder="{{ __('GTIN') }}"
                                           wire:model.defer="variacoes.{{ $index }}.gtin"  class="form-control form-control-sm format-font" >
                                    <label for="label-gtin-{{ $index }}">{{ __('GTIN') }}</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3" style="min-width: 350px">
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
                                               wire:model="variacoes.{{ $index }}.valor_varejo"
                                               class="form-control form-control-sm format-font moeda" >
                                        <label for="label-valor-varejo-{{ $index }}">{{ __('VALOR VAREJO') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto" style="max-width: 150px;">
                                <div class="floating-label-group border-lable-flt">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" placeholder="{{ __('VALOR ATACADO') }}"
                                               wire:model.defer="variacoes.{{ $index }}.valor_atacado"
                                               class="form-control form-control-sm format-font moeda" >
                                        <label for="label-valor-atacado-{{ $index }}">{{ __('VALOR ATACADO') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto mb-3" style="max-width: 150px;">
                                <div class="floating-label-group border-lable-flt">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" placeholder="{{ __('VALOR PRODUTO') }}"
                                               wire:model.defer="variacoes.{{ $index }}.valor_produto"
                                               class="form-control form-control-sm format-font moeda" >
                                        <label for="label-valor-produto-{{ $index }}">{{ __('VALOR PRODUTO') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto mb-3" style="max-width: 100px">
                                <div class="floating-label-group border-lable-flt">
                                    <div class="input-group input-group-sm">
                                        <input type="text" placeholder="{{ __('DESC.EM %') }}"
                                               wire:model.defer="variacoes.{{ $index }}.percentage" class="form-control form-control-sm format-font moeda" >
                                        <label for="label-valor-percentage-{{ $index }}">{{ __('DESC.EM %') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto mb-3" style="max-width: 150px;">
                                <div class="floating-label-group border-lable-flt">
                                    <div class="input-group input-group-sm">
                                        <input type="text" placeholder="{{ __('VALIDADE') }}"
                                               id="validade-{{ $index }}"
                                               wire:model.defer="variacoes.{{ $index }}.validade"
                                               class="form-control form-control-sm format-font data-mask" maxlength="10">
                                        <label for="label-valor-validade-{{ $index }}">{{ __('VALIDADE') }}</label>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3" style="max-width: 100px;min-width: 250px">
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
                                @error("variacoes.$index.fornecedor_id")
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3" style="max-width: 100px;min-width: 150px ">
                                <div class="floating-label-group border-lable-flt col-xs-2 format-font">
                                    <div class="form-control d-flex align-items-center justify-content-between px-2" style="height: 38px;">
                                        <label class="form-label m-0">STATUS</label>

                                        <div class="d-flex align-items-center gap-2">
                                            <div class="form-check form-switch m-0">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       id="switchStatus-{{ $index }}"
                                                       {{ $produto['status'] ? 'checked' : '' }}
                                                       onchange="toggleStatusDescription(this)">
                                            </div>
                                            <span class="small" id="statusLabel">
                                                 {{ $produto['status'] ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(!is_numeric($variacao['id']))
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
                            @endif
                        </div>
                    </div>

                    <div id="filepond-wrapper" wire:key="variacoes.{{ $index }}">
                        <!-- Upload de imagens da variação -->
                        <livewire:filepond-upload
                                context="variacao"
                                :multiple="true"
                                :variacao-key="$variacao['id']"
                                wire:key="filepond-variacao-{{ $variacao['id'] }}"
                        />
                    </div>
{{--                    @dump($variacao['images'])--}}
                    @if(isset($variacao['images']) && count($variacao['images']) > 0)
                        <div class="card mb-3 p-2">
                            <form method="post" autocomplete="off" id="formImage" name="formImage" enctype="multipart/form-data" class="form-inline">
                                @csrf
                                <input type="hidden" id="products_variation_id" name="products_variation_id" value="{{$variacao['id']}}">
                                <input type="hidden" name="tipoImage" id="tipoImage" value="variation">

                                <div class="card-body mb-3 p-2" id="filepond-wrapper">
                                    <div class="row">

                                            @foreach($variacao['images'] as $index => $imagem)
                                                <div class="col-md-2 mb-3 imagem-item" id="imagem-{{ $imagem['id'] }}" wire:key="imagem-{{ $imagem['id'] }}">
                                                    <div class="border rounded p-2 text-center position-relative">
                                                        <img src="{{ asset('storage/' . $imagem['path']) }}"
                                                             alt="Imagem"
                                                             class="img-fluid mb-1 rounded"
                                                             style="cursor: pointer;"
                                                             onclick="previewImagem('{{ asset('storage/' . $imagem['path']) }}')"
                                                             style="max-height: 150px;min-height: 120px; object-fit: cover;">

                                                        <div class="d-flex justify-content-center">
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    onclick="confirmarExclusao({{ $imagem['id'] }}, 'produtos', {{$produtoId}})"
                                                                    data-toggle="tooltip" data-placement="right"  title="Excluir imagem"
                                                                    id="btn-excluir-{{ $imagem['id'] }}">
                                                                <i class="fas fa-trash-alt" id="icon-trash-{{ $imagem['id'] }}"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif

                </div>

                @endforeach
        </div>
    </div>

</div>
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')

    <script>
        function initCalendars() {
            if (window.Helpers && typeof window.Helpers.calendar === "function") {
                window.Helpers.calendar();
            }
        }

        document.addEventListener("DOMContentLoaded", initCalendars);
        document.addEventListener("livewire:load", initCalendars);
        Livewire.hook('message.processed', (message, component) => {
            initCalendars();
        });

    </script>
@endpush
