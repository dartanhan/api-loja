@extends('layouts.layout')

@section('menu')

@include('admin.menu')

@endsection

@section('content')
    <div class="container-fluid px-4">
        <h2 class="mt-4">Mais Vendidos por Categorias <span id="filterLabel"></span></h2>
            <div class="card mb-4">
                <div class="card-header">
                    <form id="form" name="form">
                        @csrf
                        <div class="row date" id="data_mes_ano">
                            <div class="col-auto input-group-sm">
                                <i class="fas fa-table me-1"></i>
                                Vendas no Mês
                            </div>
                            <div class="col-auto input-group-sm" id="mes_ano" data-date="{{date("m/Y")}}" data-date-format="mm/yyyy">
                                <label for="data" class="visually-hidden">Filtro por Data</label>
                                <input type="text" class="form-control input-group-sm" placeholder="Filtro por Mês/Ano" aria-label="Filtro por Mês/Ano" name="mes_ano" id="mes_ano">
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>
                            <div class="col-auto input-group-sm">
                                <button class="btn bgBtn btn-enviar" type="submit" id="btn-enviar">Filtrar</button>
                                <button class="btn bgBtn btn-limpar" type="button">Limpar</button>
                                <span id="load"></span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="row"  id="cards">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Vendas
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table compact table-striped table-bordered table-hover">
                            <thead class="text-center">
                            <tr>
                                <th data-sortable="true">#</th>
                                <th data-sortable="true">id</th>
                                <th data-sortable="true">Código</th>
                                <th data-sortable="true">Produto</th>
                                <th data-sortable="true">Qtd</th>
                                <th data-sortable="true">Data</th>
                            </tr>
                            </thead>
                            <tbody class="text-center"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="divModalDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-100 w-50" role="document">
            <div class="modal-content container">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Detalhes Produtos mais Vendidos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="tableDetail" class="table table-striped table-condensed table-hover table-sm" >
                            <thead class="text-center">
                            <tr>
                                <th data-sortable="true">Descrição</th>
                                <th data-sortable="true">Qtd</th>
                                <th data-sortable="true">Produto</th>
                                <th data-sortable="true">Tot.Produto</th>
                                <th data-sortable="true">Tot.Mensal</th>
                                <th data-sortable="true">lucro</th>
                                <th data-sortable="false">Data</th>
                            </tr>
                            </thead>
                            <tbody class="text-center"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="divModalDetailCost" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-100 w-50" role="document">
            <div class="modal-content container">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Detalhes Custo Produtos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="tableDetailCost" class="table table-striped table-condensed table-hover table-sm" >
                            <thead class="text-center">
                            <tr>
                                <th data-sortable="true">Descrição</th>
                                <th data-sortable="true">Qtd</th>
                                <th data-sortable="true">Produto</th>
                                <th data-sortable="true">Tot.Produto</th>
                                <th data-sortable="false">Data</th>
                            </tr>
                            </thead>
                            <tbody class="text-center"></tbody>
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

    <script src="{{URL::asset('assets/jquery/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/jquery/jquery.validate.min.js')}}"></script>
    <script src="{{URL::asset('js/moment.js')}}"></script>
    <script src="{{URL::asset('js/produto_mais_vendidos.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/bootstrap-datepicker.pt-BR.min.js')}}" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{URL::asset('assets/bootstrap/js/dataTables.bootstrap4.min.js')}}"></script>


@endpush
@push("styles")
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/bootstrap/css/bootstrap-datepicker.min.css')}}">
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/datatables/dataTableRender.css')}}">
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.css')}}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

@endpush
