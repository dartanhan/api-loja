@extends('layouts.layout')

@section('menu')

@include('admin.menu')

@endsection

@section('content')
    <div class="container-fluid"  style="padding-top: 10px;">
        <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-auto input-group-sm ">
                            <h4 class="title"><i class="fas fa-eye"></i><strong> {{ __('MONITORAMENTO DE ESTOQUE') }}</strong></h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" id="formFiltro" action="{{ route('monitoramento.index') }}" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="data" class="form-label">Período</label>
                            <input type="text" name="data_range" id="data_range" class="form-control"
                                   value="{{ request('data_range') }}" autocomplete="off">
                        </div>
                        <div class="col-md-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select name="tipo" id="tipo" class="form-control">
                                <option value="">Todos</option>
                                <option value="entrada" {{ ($filtros['tipo'] ?? '') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                                <option value="saida" {{ ($filtros['tipo'] ?? '') == 'saida' ? 'selected' : '' }}>Saída</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="codigo" class="form-label">Subcódigo da Variação</label>
                            <input type="text" name="codigo" class="form-control" value="{{ $filtros['codigo'] ?? '' }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="mr-2">
                                <button id="btnFiltrar" class="btn btn-primary d-flex align-items-center justify-content-center" type="submit">
                                    <span id="spinnerFiltrar" class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                                    <span>Filtrar</span>
                                </button>
                            </div>
                            <div>
                                <!-- Botão Limpar -->
                                <a href="{{ route('monitoramento.index') }}" id="btnLimpar" class="btn btn-secondary d-flex align-items-center justify-content-center">
                                    <span id="spinnerLimpar" class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                                    <span>Limpar</span>
                                </a>
                            </div>
                        </div>
                    </form>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark text-uppercase text-monospace text-center">
                            <tr>
                                <th>#</th>
                                <th>DATA</th>
                                <th>TIPO</th>
                                <th>VENDA</th>
                                <th>PRODUTO</th>
                                <th>VARIAÇÃO</th>
                                <th>SUBCÓDIGO</th>
                                <th>QTD SAIDA</th>
                                <th>MOTIVO</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-uppercase text-monospace text-center">
{{--                        @dd($movimentacoes)--}}
                            @forelse ($movimentacoes as $mov)
                                <tr>
                                    <td>{{ $mov->id }}</td>
                                    <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $mov->tipo == 'entrada' ? 'success' : 'danger' }}">
                                            {{ ucfirst($mov->tipo) }}
                                        </span>
                                    </td>
                                    <td>{{ $mov->venda->codigo_venda ?? '-' }}</td>
                                    <td>{{ optional($mov->variacao->produtoPai)->descricao ?? '-' }}</td>
                                    <td>{{ $mov->variacao->variacao ?? '-' }}</td>
                                    <td>{{ $mov->variacao->subcodigo ?? '-' }}</td>
                                    <td>{{ $mov->quantidade }}</td>
                                    <td>{{ $mov->motivo ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary"
                                                data-subcodigo="{{ $mov->variacao->subcodigo }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#historicoModal"
                                                onclick="carregarHistorico('{{ $mov->variacao->subcodigo }}','{{$mov->variacao->produtoPai->descricao}} - {{$mov->variacao->variacao}}')">
                                            <i class="fas fa-history"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">Nenhuma movimentação encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                {{ $movimentacoes->withQueryString()->links() }}
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="historicoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Histórico do Produto: <span id="subcodigoTitulo"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div id="conteudoHistorico" class="table-responsive">
                        <p>Carregando...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push("scripts")
    <!-- Moment.js + Daterangepicker -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(function () {
            $('#data_range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'DD/MM/YYYY',
                    applyLabel: 'Aplicar',
                    cancelLabel: 'Cancelar',
                    fromLabel: 'De',
                    toLabel: 'Até',
                    customRangeLabel: 'Personalizado',
                    daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
                    monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                    firstDay: 0
                }
            });

            $('#data_range').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            });

            $('#data_range').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });
        });

        // JavaScript para ativar o loading
        $(document).ready(function () {
            $('#formFiltro').on('submit', function () {
                $('#spinnerFiltrar').removeClass('d-none');
                $('#btnFiltrar').attr('disabled', true);
            });

            $('#btnLimpar').on('click', function () {
                $('#spinnerLimpar').removeClass('d-none');
                $(this).addClass('disabled');
            });

        });

        function carregarHistorico(subcodigo, produto) {
            $('#subcodigoTitulo').text(subcodigo + " - " + produto);
            $('#conteudoHistorico').html('<p>Carregando...</p>');

            const tipo = document.getElementById('tipo').value;
            const dataRange = document.getElementById('data_range').value;

            $.get('{{ route('monitoramento.historico') }}', {
                subcodigo: subcodigo,
                tipo: tipo,
                data_range: dataRange
            }, function (data) {
                if (data.length === 0) {
                    $('#conteudoHistorico').html('<p>Nenhuma movimentação encontrada.</p>');
                } else {
                    let tabela = `<table class="table table-hover table-striped table-sm">
                <thead class="table-dark text-uppercase text-monospace text-center" style="font-size: small"><tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Qtd Atual</th>
                    <th>Qtd Saida/Entrada</th>
                    <th>Motivo</th>
                    <th>Venda</th>
                </tr></thead><tbody class="text-uppercase text-monospace text-center" style="font-size: small">`;

                    data.forEach(item => {
                        tabela += `<tr>
                    <td>${formatarData(item.created_at)}</td>
                    <td><span class="badge bg-${item.tipo === 'entrada' ? 'success' : 'danger'}">${item.tipo}</span></td>
                    <td>${item.variacao.quantidade}</td>
                    <td>${item.quantidade}</td>
                    <td>${item.motivo ?? '-'}</td>
                    <td>${item.venda?.codigo_venda ?? '-'}</td>
                </tr>`;
                    });

                    tabela += '</tbody></table>';
                    $('#conteudoHistorico').html(tabela);
                }
            });
        }


        function formatarData(dataStr) {
            const data = new Date(dataStr);
            return data.toLocaleString('pt-BR');
        }
    </script>

@endpush
@push("styles")
    <!-- Daterangepicker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
