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
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <input type="text" class="form-control" wire:model="produto.descricao">
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" wire:model="produto.status">
                    <option value="1">Ativo</option>
                    <option value="0">Inativo</option>
                </select>
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
                <div class="row mb-3 align-items-end ">
                    <div class="col-md-1">
                        <label class="form-label-sm">Subcódigo</label>
                        <input type="text" class="form-control form-control-sm" value="{{ $variacao['subcodigo'] }}" disabled>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-sm">Variação</label>
                        <input type="text" class="form-control form-control-sm" wire:model.defer="variacoes.{{ $index }}.variacao">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label-sm">Qtd</label>
                        <input type="number" class="form-control form-control-sm" wire:model.defer="variacoes.{{ $index }}.quantidade">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label-sm">Valor Varejo</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">R$</span>
                            <input type="text" class="form-control valor-mask"
                                   wire:model.defer="variacoes.{{ $index }}.valor_varejo">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label-sm">Valor Produto</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">R$</span>
                            <input type="text" class="form-control valor-mask"
                                   wire:model.defer="variacoes.{{ $index }}.valor_produto">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">Fornecedor</label>
                        <select class="form-select form-select-sm" wire:model.defer="variacoes.{{ $index }}.fornecedor_id">
                            <option value="">Selecione</option>
                            @foreach($fornecedores as $f)
                                <option value="{{ $f['id'] }}">{{ $f['nome'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 d-flex justify-content-center mt-4">
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
            width: 60px;
        }

        .variacao-valor-varejo,
        .variacao-valor-produto {
            width: 120px;
        }

        .variacao-fornecedor {
            width: 150px;
        }

        .variacao-remove-btn {
            width: 40px;
            text-align: center;
        }

    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('livewire:load', function () {
            $('.valor-mask').mask('#.##0,00', {reverse: true});
        });

        Livewire.hook('message.processed', () => {
            $('.valor-mask').mask('#.##0,00', {reverse: true});
        });
    </script>
@endpush
