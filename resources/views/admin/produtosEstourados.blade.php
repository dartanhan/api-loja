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
                            <h6><i class="fas fa-table me-1"></i>Produtos com Vendas Acima do Estoque -</h6>
                        </div>
                        <div>
                            <h6>
                                <span class="badge bg-info">
                                    <strong>Período filtrado:</strong> <span id="periodoFiltrado"></span>
                                </span>
                            </h6>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row mt-2 p-2">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Data Início:</label>
                        <input type="date" id="data_inicio" class="form-control" value="{{ now()->startOfMonth()->toDateString() }}">
                    </div>
                    <div class="col-md-3">
                        <label>Data Fim:</label>
                        <input type="date" id="data_fim" class="form-control" value="{{ now()->endOfMonth()->toDateString() }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary" id="btnFiltrar">Filtrar</button>
                    </div>
                </div>
                <table id="tableProdutos" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Fornecedor</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="{{URL::asset('assets/jquery/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/dataTables.bootstrap4.min.js')}}"></script>
    <script>
        let table;

        function formatDetalhes(row) {
            const assetStorageUrl = "{{ asset('storage') }}";
            let html = '<div class="p-2 zebra-detalhes">';
            row.produtos.forEach((produto,index) => {

                let imagem = produto.imagem
                    ? `${assetStorageUrl}/${produto.imagem}`
                    : `${assetStorageUrl}/produtos/not-image.png`;

                html += `
                        <div class="item-produto d-flex align-items-center border-bottom pb-2 w-100 rounded p-2">
                            <img src="${imagem}" style="width: 60px; height: 60px; object-fit: cover; margin-right: 10px;" class="rounded border" />

                            <div>
                                <strong>${produto.descricao}</strong><br>
                                Código: ${produto.codigo_produto}<br>
                                Vendido: <span class="badge bg-success">${produto.quantidade_vendida}</span> /
                                Estoque Atual: <span class="badge bg-warning text-dark">${produto.quantidade}</span>
                            </div>
                        </div>
                    `;
                });
            html += '</div>';
            return html;
        }

        function carregarTabela(data_inicio, data_fim) {
            if (table) table.destroy(); // destrói a tabela anterior se já existe

            table = $('#tableProdutos').DataTable({
                ajax: {
                    url: "{{ url('/admin/produto/produtos-estourados') }}",
                    data: function(d) {
                        d.data_inicio = data_inicio;
                        d.data_fim = data_fim;
                    }
                },
                columns: [
                    {
                        className: 'details-control',
                        orderable: false,
                        data: null,
                        defaultContent: ''
                    },
                    { data: 'fornecedor' }
                ],
                drawCallback: function () {
                    $('#tableProdutos tbody').off('click', 'td.details-control').on('click', 'td.details-control', function () {
                        let tr = $(this).closest('tr');
                        let row = table.row(tr);

                        if (row.child.isShown()) {
                            row.child.hide();
                            tr.removeClass('shown');
                        } else {
                            row.child(formatDetalhes(row.data())).show();
                            tr.addClass('shown');
                        }
                    });
                },
                order: [[1, 'asc']]
            });

            // $('#tableProdutos tbody').on('click', 'td.details-control', function () {
            //     let tr = $(this).closest('tr');
            //     let row = table.row(tr);
            //
            //     if (row.child.isShown()) {
            //         row.child.hide();
            //         tr.removeClass('shown');
            //     } else {
            //         row.child(formatDetalhes(row.data())).show();
            //         tr.addClass('shown');
            //     }
            // });
        }

        $('#btnFiltrar').on('click', function () {
            let data_inicio = $('#data_inicio').val();
            let data_fim = $('#data_fim').val();

            if (!data_inicio || !data_fim) {
                alert('Selecione um período válido!');
                return;
            }

            $('#periodoFiltrado').text(
                formatarDataLocal(data_inicio) + ' a ' + formatarDataLocal(data_fim)
            );

            carregarTabela(data_inicio, data_fim);
        });

        function formatarDataLocal(dateStr) {
            const [ano, mes, dia] = dateStr.split("-");
            return `${dia}/${mes}/${ano}`;
        }

        // carregar ao abrir página
        $(document).ready(function () {
            $('#btnFiltrar').click();
        });
    </script>
@endpush
@push("styles")
{{--    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/bootstrap/css/bootstrap-datepicker.min.css')}}">--}}
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/datatables/dataTableRender.css')}}">
{{--    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.2/font/bootstrap-icons.css">--}}
@endpush
