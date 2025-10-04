<div>
    <div class="container-fluid mt-4 mb-4" xmlns:wire="http://www.w3.org/1999/xhtml" xmlns:livewire="">
        {{-- Header do Produto --}}
        <div class="card shadow border-0 mb-3">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-0"><i class="fas fa-money-bill"></i> Despesas</h5>
                <hr>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb small">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.home') }}">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('produto.produto_create') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Despesas</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card shadow border-0 mb-4 p-4">
            {{-- form --}}
            <div class="">
                <form wire:submit.prevent="{{ $editandoId ? 'atualizar' : 'salvar' }}">
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <input type="text" id="descricao" class="form-control" wire:model.defer="descricao">
                        @error('descricao') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="valor" class="form-label">Valor (R$)</label>
                        <input type="text" id="valor" class="form-control moeda" wire:model.defer="valor">
                        @error('valor') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="data" class="form-label">Data</label>
                        <input type="date" id="data" class="form-control" wire:model.defer="data">
                        @error('data') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4 d-flex gap-2 align-items-start">
                        <button type="submit"
                                class="btn btn-{{ $editandoId ? 'primary' : 'success' }} btn-sm"
                                wire:loading.attr="disabled"
                                wire:target="{{ $editandoId ? 'atualizar' : 'salvar' }}">
                                <span wire:loading.remove wire:target="{{ $editandoId ? 'atualizar' : 'salvar' }}">
                                    <i class="fas fa-{{ $editandoId ? 'edit' : 'save' }}"></i> {{ $editandoId ? 'Atualizar' : 'Salvar' }}
                                </span>
                                <span wire:loading wire:target="{{ $editandoId ? 'atualizar' : 'salvar' }}">
                                    <i class="fas fa-spinner fa-spin"></i> Processando...
                                </span>
                        </button>

                        @if($editandoId)
                            <button type="button"
                                wire:click="cancelarEdicao"
                                class="btn btn-outline-secondary btn-sm"
                                wire:loading.attr="disabled"
                                wire:target="cancelarEdicao">
                                <span wire:loading.remove wire:target="cancelarEdicao">
                                    <i class="fas fa-times-circle"></i> Cancelar edição
                                </span>
                                <span wire:loading wire:target="cancelarEdicao">
                                    <i class="fas fa-spinner fa-spin"></i> Cancelando...
                                </span>
                            </button>
                        @endif
                    </div>

                </form>
            </div>

            {{-- Tabela --}}
            <div class="mt-3">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                    <tr>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($despesas as $despesa)
                        <tr>
                            <td>{{ $despesa->descricao }}</td>
                            <td>R$ {{ number_format($despesa->valor, 2, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($despesa->data)->format('d/m/Y') }}</td>
                            <td>
                                <button wire:click="editar({{ $despesa->id }})"
                                        class="btn btn-sm btn-primary"
                                        wire:loading.attr="disabled"
                                        wire:target="editar">
                                        <span wire:loading.remove wire:target="editar">
                                            <i class="fas fa-edit"></i>
                                        </span>
                                                                        <span wire:loading wire:target="editar">
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </span>
                                </button>

                                <button type="button"
                                        class="btn btn-sm btn-danger"
                                        onclick="confirmarExclusao({{ $despesa->id }})"
                                        id="btn-excluir-{{ $despesa->id }}">
                                    <span id="icon-trash-{{ $despesa->id }}">
                                        <i class="fas fa-trash"></i>
                                    </span>
                                                                    <span id="spinner-trash-{{ $despesa->id }}" style="display:none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">Nenhuma despesa cadastrada.</td></tr>
                    @endforelse
                    </tbody>
                </table>

                {{-- Paginação --}}
                <div class="d-flex justify-content-center">
                    {{ $despesas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script src="{{ asset('js/util.js') }}"></script>
    <script>
        document.addEventListener('livewire:load', () => {
            aplicarMascaraMoeda();

            Livewire.hook('message.processed', () => {
                aplicarMascaraMoeda();
            });
        });

    </script>
@endpush
