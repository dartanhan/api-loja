@extends('layouts.layout')

@section('menu')

    @include('admin.menu')

@endsection

@section('content')

    <div class="container-fluid px-4">
        <h1 class="mt-4">Dashboard</h1>
        <input type="hidden" name="store_id" id="store_id" value="{{$user_data->loja_id}}">
        <div class="row">
            <div class="contador">
                Atualizando em:
                <span id="contador"></span>
            </div>
            <div class="col-xl-2 col-md-6">
                <div class="card bg-primary text-white mb-2">
                    <div class="d-flex align-items-center" id="totalDinner" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="#" >Detalhes</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-6">
                <div class="card bg-warning text-white mb-2">
                    <div class="d-flex align-items-center" id="totalCartao" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link detailCart" href="#" data-toggle="modal" data-target="#divModalCart" data-content="2">Detalhes</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-6">
                <div class="card bg-danger text-white mb-4">
                    <div class="d-flex align-items-center" id="totalDesconto" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="#">Detalhes</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-6">
                <div class="card bg-info text-white mb-4">
                    <div class="d-flex align-items-center" id="totalDia" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="#">Detalhes</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-6">
                <div class="card bg-secondary text-white mb-4">
                    <div class="d-flex align-items-center" id="totalSemana" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="#">Detalhes</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-6">
                <div class="card bg-dark text-white mb-4">
                    <div class="d-flex align-items-center" id="totalMes" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="#">Detalhes</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="row date" id="data">
                            <div class="col-auto input-group-sm">
                                <i class="fas fa-chart-area me-1"></i>
                                Filtro:
                            </div>
                            <div class="col-auto input-group-sm" id="dataini" data-date="{{date("d/m/Y")}}" data-date-format="dd/mm/yyyy">
                                <label for="dataini" class="visually-hidden">Data Inicial</label>
                                <input type="text" class="form-control input-group-sm" placeholder="Data Inicial" aria-label="Data Inicial" name="dataIni">
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>
                            <div class="col-auto input-group-sm" id="datafim" data-date="{{date("d/m/Y")}}" data-date-format="dd/mm/yyyy">
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
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header">
                            <div class="row date" id="data_ano">
                                <div class="col-auto input-group-sm">
                                    <i class="fas fa-chart-area me-1"></i>
                                    Filtro:
                                </div>
                                <div class="col-auto input-group-sm" id="ano" data-date="{{date("Y")}}" data-date-format="yyyy">
                                <label for="ano" class="visually-hidden">Ano</label>
                                <input type="text" class="form-control input-group-sm" placeholder="Ano" aria-label="Ano" name="ano">
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>
                            <div class="col-auto input-group-sm">
                                <button class="btn bgBtn btn-filtro-ano" type="button">Filtrar</button>
                                <button class="btn bgBtn btn-filtro-limpar-ano" type="button">Limpar</button>
                                <span id="loadChartLine"></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body"><canvas id="myAreaChart"  width="100%" height="39"></canvas></div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>Mês
                    </div>
                    <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-bar me-1"></i>Anos
                    </div>
                    <div class="card-body"><canvas id="myLineMultiChart" width="100%" height="40"></canvas></div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-bar me-1"></i>Total Vendas por Funcionário - ANO [ <span name="anoPesquisa" id="anoPesquisa"></span> ]
                    </div>
                    <div class="card-body"><canvas id="myBarChartFunc" width="100%" height="40"></canvas></div>
                </div>
            </div>
{{--            <div class="col-xl-6">--}}
{{--                <div class="card mb-4">--}}
{{--                    <div class="card-header">--}}
{{--                        <i class="fas fa-chart-bar me-1"></i>Resumo produtos mais vendidos mês--}}
{{--                    </div>--}}
{{--                    <div class="card-body">--}}
{{--                        <div style="max-height: 300px; overflow-y: auto;">--}}
{{--                            <table class="table table-bordered" id="produtosMaisVendidosTable">--}}
{{--                                <thead>--}}
{{--                                <tr>--}}
{{--                                    <th>Produto</th>--}}
{{--                                    <th>Total Vendido</th>--}}
{{--                                    <th>Estoque Atual</th>--}}
{{--                                </tr>--}}
{{--                                </thead>--}}
{{--                                <tbody>--}}
{{--                                <!-- Dados serão preenchidos dinamicamente -->--}}
{{--                                </tbody>--}}
{{--                            </table>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>Vendas no dia
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table compact table-striped table-bordered table-hover">
                        <thead class="text-center">
                        <tr>
                            <th data-sortable="true">Venda</th>
                            <th data-sortable="false" style="width: 100px">Vendedor</th>
                            <th data-sortable="true">Tipo Venda</th>
                            <th data-sortable="true">Forma Pagamento</th>
                            <th data-sortable="true">Sub Total</th>
                            <th data-sortable="true">Desconto</th>
                            <th data-sortable="true">Cashback</th>
                            <th data-sortable="true">Total</th>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="tableView" class="table table-striped table-condensed table-hover table-sm">
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
                            <tfoot><tr class="foot"></tr></tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="divModalCart" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="exampleModalLongTitle">Detalhes do Cartão</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                            <tfoot><tr class="foot"></tr></tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
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
                        <h5 class="modal-title" id="exampleModalLongTitle">Alterar Venda</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8 form-group">
                                <div class="col-md-12">
                                    <select name="payments_sale" id="payments_sale" class="form-select format-font"></select>
                                </div>
                            </div>
                            <div class="col-md-8 form-group">
                                <div class="col-md-12">
                                    <select name="payments" id="payments" class="form-select format-font"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="salvar">Salvar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection


@push("scripts")
    <script src="{{URL::asset('assets/dashboard/js/Chart.min.js')}}"></script>
    <script src="{{URL::asset('assets/jquery/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/jquery/jquery.validate.min.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/bootstrap-datepicker.pt-BR.min.js')}}" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{URL::asset('assets/bootstrap/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{URL::asset('./js/moment.js')}}"></script>
    <script type="module" src="{{URL::asset('assets/dashboard/js/dashboard.js')}}"></script>

@endpush
@push("styles")
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/bootstrap/css/bootstrap-datepicker.min.css')}}">
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/datatables/dataTableRender.css')}}">
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.css')}}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('css/custom-input-float.css')}}"/>
@endpush


