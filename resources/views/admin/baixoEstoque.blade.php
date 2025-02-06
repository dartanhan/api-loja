@extends('layouts.layout')

@section('menu')

    @include('admin.menu')

@endsection

@section('content')

    <div class="container-fluid"  style="padding-top: 10px;">
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="d-flex align-items-center gap-1">
                        <div >
                            <h6><i class="fas fa-table me-1"></i>Produtos com Estoque Baixo -</h6>
                        </div>
                        <div >
                            <h6>Período de vendas consultado:
                                <span class="badge bg-info">
                                    {{ $inicioPeriodo->format('d/m/Y') }} a {{ $fimPeriodo->format('d/m/Y') }}
                                </span>
                            </h6>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <div class="accordion" id="accordionFornecedores">
                    @forelse ($fornecedores as $index => $fornecedor)
                        @if ($fornecedor->variacoes->isNotEmpty())
                            <div class="accordion-item accordion-custom {{ $index % 2 === 0 ? 'even' : 'odd' }}">
                                <h2 class="accordion-header" id="heading{{ $fornecedor->id }}">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $fornecedor->id }}" aria-expanded="true" aria-controls="collapse{{ $fornecedor->id }}">
                                        {{ $fornecedor->nome }} ({{ $fornecedor->variacoes->count() }} itens)
                                    </button>
                                </h2>
                                <div id="collapse{{ $fornecedor->id }}" class="accordion-collapse collapse hide" aria-labelledby="heading{{ $fornecedor->id }}" data-bs-parent="#accordionFornecedores">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <div style="max-height: 450px; overflow-y: auto;">
                                            @php
                                                // Ordenar os produtos pela quantidade vendida no mês, em ordem decrescente
                                                /** @var TYPE_NAME $fornecedor */
                                                $variacoesOrdenadas = $fornecedor->variacoes->map(function ($produto) {
                                                    $produto->totalVendidoMes = $produto->vendas->sum('quantidade'); // Calcula o total vendido
                                                    return $produto;
                                                })->sortByDesc('totalVendidoMes');
                                            @endphp

                                            @foreach ($variacoesOrdenadas as $produto)
                                                @php
                                                    /** @var TYPE_NAME $produto */
                                                    $totalVendidoMes = $produto->totalVendidoMes;
                                                    $estoque = $produto->quantidade; // Estoque atual do produto
                                                @endphp

{{--                                                @if ($totalVendidoMes > $estoque) <!-- Exibe apenas se a quantidade vendida for maior que o estoque -->--}}
                                                    <li class="list-group-item d-flex align-items-center">
                                                        @if ($produto->images->isNotEmpty())
                                                            <div class="ms-3 mr-2">
                                                                @foreach ($produto->images as $image)
                                                                    <img src="{{ asset('storage/' . $image->path) }}"
                                                                         alt="Imagem do Produto" class="img-thumbnail"
                                                                         style="width: 60px; height: 60px; cursor:pointer" data-bs-toggle="modal"
                                                                         data-bs-target="#modalImage" data-image="{{ asset('storage/' . $image->path) }}">
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="ms-3 mr-2">
                                                                <img src="{{ asset('storage/produtos/not-image.png') }}" alt="Imagem do Produto" class="img-thumbnail" style="width: 60px; height: 60px;">
                                                            </div>
                                                        @endif

                                                        <div>
                                                            <strong>{{ $produto->produtoPai->descricao ?? 'Produto Pai Não Encontrado' }}</strong><br>
                                                            <strong>{{ $produto->subcodigo }}</strong> - {{ $produto->variacao }}
                                                            <div class="mt-0">
                                                                <small>
                                                                    Vendidos este mês: <span class="badge bg-success text-white">Qtd: {{ $totalVendidoMes }}</span> /
                                                                    Estoque: <span class="badge bg-warning text-dark">Qtd: {{ $estoque }}</span>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </li>
{{--                                                    @endif--}}
                                                @endforeach
                                            </div>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="alert alert-info">Nenhum produto com estoque baixo.</div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
    <!-- Modal de Exibição da Imagem -->
    <div class="modal fade" id="modalImage" tabindex="-1" aria-labelledby="modalImageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalImageLabel">Imagem do Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img id="modalImageContent" src="" alt="Imagem maior" class="img-fluid" />
                </div>
            </div>
        </div>
    </div>
@endsection
@push("scripts")
<script>
    // Quando a imagem for clicada, atualize o conteúdo do modal com a imagem maior
    let modalImage = document.getElementById('modalImage');
    modalImage.addEventListener('show.bs.modal', function (event) {
        // Obtém a imagem clicada
        let button = event.relatedTarget; // Botão que foi clicado
        let imagePath = button.getAttribute('data-image'); // Caminho da imagem

        // Atualiza o conteúdo do modal com a imagem maior
        let modalImageContent = modalImage.querySelector('#modalImageContent');
        modalImageContent.src = imagePath;
    });
</script>
@endpush
@push("styles")
    <link rel="stylesheet" type="text/css" href="{{URL::asset('css/dashboard.css')}}"/>
@endpush
