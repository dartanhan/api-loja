@extends('layouts.layout')

@section('menu')

    @include('admin.menu')

@endsection

@section('content')
    <input type="hidden" name="store_id" id="store_id" value="{{$user_data->loja_id}}">

    <div class="container-fluid px-4">
        <h1 class="mt-4">Dashboard</h1>
        <div id="estoque-alerta" class="d-flex mb-3 mt-2">
            <!-- Botão de Alerta transformado em um link -->
            <a id="alertaBaixoEstoque" href="{{ route('produtos.baixo_estoque') }}" class="btn btn-warning alerta-piscante" style="display: none;">
                ⚠️ Produtos com estoque baixo
            </a>
        </div>

        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>

        <div class="row">
            <div class="col-xl-2 col-md-6">
                <div class="card bg-primary text-white mb-2">
                    <div class="d-flex  align-items-center" id="totalDinner" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer">
                        <a class="small text-white stretched-link detailDinner" href="#"
                        data-toggle="modal" data-target="#divModalDinner" data-content="2">Detalhes</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-6">
                <div class="card bg-warning text-white mb-2">
                    <div class="d-flex align-items-center" id="totalCartao" name="load">
                            <div class="card-body" name="card-body"></div>
                            <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer">
                        <a class="small text-white stretched-link detailCart" href="#"
                        data-toggle="modal" data-target="#divModalCart" data-content="2">Detalhes</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-6">
                <div class="card bg-danger text-white mb-2">
                    <div class="d-flex align-items-center" id="totalDesconto" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer">
                        <a class="small text-white stretched-link" href="#">Detalhes</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-6">
                <div class="card bg-info text-white mb-2">
                    <div class="d-flex align-items-center" id="totalDia" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="#">Detalhes</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-6">
                <div class="card bg-secondary text-white mb-2">
                    <div class="d-flex align-items-center" id="totalSemana" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="#">Detalhes</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-6">
                <div class="card bg-dark text-white mb-2">
                    <div class="d-flex align-items-center" id="totalMes" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="#">Detalhes</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-6">
                <div class="card bg-danger opacity-75 text-white mb-2">
                    <div class="d-flex align-items-center" id="totalImposto" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="#">Detalhes</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-6">
                <div class="card bg-success text-white mb-2 p-0">
                    <div class="d-flex align-items-center p-0" id="totalMc" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="#">Detalhes</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-6">
                <div class="card bg-success opacity-75 text-white mb-2 p-0">
                    <div class="d-flex align-items-center p-0" id="totalPmc" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="#">Detalhes</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap floating-card mt-3 p-2">
            <div class="row date" id="data">
                <div class="col-auto input-group-sm">
                    <i class="fas fa-chart-area me-1"></i>
                    Filtro:
                </div>
                <div class="col-auto input-group-sm mb-2" id="dataIni" data-date="{{date("d/m/Y")}}" data-date-format="dd/mm/yyyy">
                    <label for="dataini" class="visually-hidden">Data Inicial</label>
                    <input type="text" class="form-control input-group-sm" placeholder="Data Inicial" aria-label="Data Inicial" name="dataIni">
                    <span class="add-on"><i class="icon-th"></i></span>
                </div>
                <div class="col-auto input-group-sm mb-2" id="dataFim" data-date="{{date("d/m/Y")}}" data-date-format="dd/mm/yyyy">
                    <label for="datafim" class="visually-hidden">Data Final</label>
                    <input type="text" class="form-control input-group-sm" placeholder="Data Final" aria-label="Data Final" name="dataFim">
                    <span class="add-on"><i class="icon-th"></i></span>
                </div>
                <div class="col-auto input-group-sm">
                    <button class="btn bgBtn btn-enviar" type="button">Filtrar</button>
                    <button class="btn bgBtn btn-limpar" type="button">Limpar</button>
                    <span id="loadChartBar"></span>
                </div>
            </div>
        </div>
        <div class="card floating-card mt-3">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>Vendas no dia
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatablesDiario" class="table compact table-striped table-hover">
                        <thead class="text-center">
                            <tr>
                                <th data-sortable="true">#</th>
                                <th data-sortable="true">Venda</th>
                                <th data-sortable="true">Cliente</th>
                                <th data-sortable="false">Vendedor</th>
                                <th data-sortable="true">Tipo Venda</th>
                                <th data-sortable="true">Forma Pagamento</th>
                                <th data-sortable="true">Total Venda</th>
                                <th data-sortable="true">Valor Recebido</th>

{{--                                <th data-sortable="true">Desconto</th>--}}
{{--                                <th data-sortable="true">Cashback</th>--}}
{{--                                <th data-sortable="true">Motoboy</th>--}}
{{--                                <th data-sortable="true" style="width:120px">Total</th>--}}
{{--                                <th data-sortable="true">Taxa</th>--}}
{{--                                <th data-sortable="true">Imposto</th>--}}
{{--                                <th data-sortable="true">Total Final</th>--}}
{{--                                <th data-sortable="true">Valor Produto</th>--}}
{{--                                <th data-sortable="true" style="width:80px">MC</th>--}}
{{--                                <th data-sortable="true" style="width:80px">% MC</th>--}}
                                <th data-sortable="true">Data</th>
                                <th data-sortable="false" style="width: 50px">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-center"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
        <!-- Modal -->
        <div class="modal fade" id="divModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="exampleModalLongTitle">Detalhes da Venda - <span name="codigo_venda"></span></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table id="tableView" class="table table-striped table-condensed table-hover table-sm w-100">
                                <thead class="text-center">
                                    <tr>
                                        <th data-sortable="false">CÓDIGO</th>
                                        <th data-sortable="false">PRODUTO</th>
                                        <th data-sortable="false">VALOR PRODUTO</th>
                                        <th data-sortable="false">VALOR VENDA</th>
                                        <th data-sortable="false">QTD</th>
                                        <th data-sortable="false">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center" id="dataTableModal">
                                <tfoot><tr id="foot"></tr></tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="divModalUpdate" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <form id="form" name="form">
                    @csrf
                    <input type="hidden" name="new_taxa" id="new_taxa">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="exampleModalLongTitle">Alterar Forma de Pagamento da Venda</h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="card text-center">
                                    <div class="card-header bg-primary text-white justify-content-between p-1">
                                        Código da Venda : <b><span name="codigo_venda"></span></b>
                                    </div>
                                    <div class="text-center mt-2">
                                        <div class="container">
                                            <div class="row justify-content-center">
                                                <div class="col-md-8 form-group">
                                                    <div class="form-floating">
                                                        <select name="payments_sale" id="payments_sale" class="form-select format-font"></select>
                                                        <label class="form-label" for="floatingSelect">Pagamento a ser Alterado</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row justify-content-center mt-3">
                                                <div class="col-md-8 form-group">
                                                    <div class="form-floating">
                                                        <select name="payments" id="payments" class="form-select format-font"></select>
                                                        <label class="form-label" for="floatingSelect">Forma de Pagamento Nova</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" id="salvar">Salvar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                            </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="divModalCart" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="exampleModalLongTitle">Detalhes do Cartão<br> <span id="periodo" name="periodo"></span></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table id="tableViewCart" class="table table-striped table-condensed table-hover table-sm" style="width:100%">
                                <thead class="text-center">
                                <tr>
                                    <th data-sortable="false">FORMA PAGAMENTO</th>
                                    <th data-sortable="false">TOTAL</th>
                                    <th data-sortable="false">TAXA</th>
                                    <th data-sortable="false">TOTAL FINAL</th>
                                </tr>
                                </thead>
                                <tbody class="text-center" id="dataTableModal"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="divModalDinner" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="exampleModalLongTitle">Detalhes Vendas por Fucionário<br> <span id="periodo" name="periodo"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table id="dataTableModalDinner" class="table table-striped table-condensed table-hover table-sm" style="width:100%">
                                <thead class="text-center">
                                <tr>
                                    <th data-sortable="false">NOME</th>
                                    <th data-sortable="false">TOTAL</th>
                                    <!--th data-sortable="false">TAXA</th-->
                                    <th data-sortable="false">TOTAL FINAL</th>
                                </tr>
                                </thead>
                                <tbody class="text-center" id="dataTableModalDinner"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>


@endsection


@push("scripts")
    <script src="{{URL::asset('assets/dashboard/js/Chart.min.js')}}"></script>
    <script src="{{URL::asset('assets/jquery/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/jquery/jquery.validate.min.js')}}"></script>
    <script type="module" src="{{URL::asset('js/dashboard-diario.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/bootstrap-datepicker.pt-BR.min.js')}}" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{URL::asset('assets/bootstrap/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{URL::asset('js/moment.min.js')}}"></script>
    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <!-- Biblioteca para gerar o arquivo Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
@endpush
@push("styles")
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/bootstrap/css/bootstrap-datepicker.min.css')}}">
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/datatables/dataTableRender.css')}}">
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.css')}}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('css/custom-input-float.css')}}"/>
@endpush
