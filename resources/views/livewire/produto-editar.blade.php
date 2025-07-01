<div xmlns:wire="http://www.w3.org/1999/xhtml">
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-3">
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

    <div class="card">
        <div class="card-header">Variações</div>
        <div class="card-body">
            @foreach($variacoes as $index => $variacao)
                <div class="row mb-3">
                    <div class="col-md-2">
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
                    <div class="col-md-2">
                        <label class="form-label-sm">Valor Varejo</label>
                        <input type="text" class="form-control form-control-sm" wire:model.defer="variacoes.{{ $index }}.valor_varejo">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-sm">Fornecedor</label>
                        <select class="form-select form-select-sm" wire:model.defer="variacoes.{{ $index }}.fornecedor_id">
                            <option value="">Selecione</option>
                            @foreach($fornecedores as $f)
                                <option value="{{ $f['id'] }}">{{ $f['nome'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-3 text-end">
        <button class="btn btn-success" wire:click="salvar">
            <i class="fas fa-save me-1"></i> Salvar
        </button>
        <a href="" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>
</div>
