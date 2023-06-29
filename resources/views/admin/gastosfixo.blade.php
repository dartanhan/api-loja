@extends('layouts.layout')

@section('menu')

    @include('admin.menu')

@endsection

@section('content')

    <div class="container">
        <form name="formFiltro" id="formFiltro" class="row" method="post">
            @csrf
                <div class="col-sm-2 sm-2">
                    <button id="btnNovo" name="btnNovo" type="button" class="btn btn-info" data-toggle="modal">
                        <i class="material-icons">library_add</i>
                    </button>
                </div>

                <div class="col-sm-2 sm-2">
                       <input class="dateini form-control" id="dateini" name="dateini" type="text" placeholder="Data Inicial" required>
                </div>
                <div class="col-sm-2 mb-2">
                       <input class="datefim form-control" id="datefim" name="datefim" type="text" placeholder="Data Final">
                </div>
                <div class="col-sm-2 mb-2">
                       <button type="submit" class="btn btn-info"  name="enviarForm" id="enviarForm">Enviar</button>
                </div>
            </form>
    </div>
    <br>
    <div id="divDelete" >
        <div class="" role="alert" id="alert-title-delete"></div>
    </div>
        <div class="container caja">
            <div class="container" align="center">
                <div class="row">
                    <div class="col-sm-2 sm-2">
                        <div class="card card-chart">
                            <div class="card-header">
                                <h6 class="card-category">Total Mês</h6>
                                <h5 class="card-title">
                                    <i class="tim-icons icon-bell-55 text-primary" id="totalMes"></i>
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <div class="card card-chart">
                            <div class="card-header">
                                <h6 class="card-category">Gastos Fixos</h6>
                                <h5 class="card-title">
                                    <i class="tim-icons icon-bell-55 text-danger" id="totalFixo"></i>
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <div class="card card-chart">
                            <div class="card-header">
                                <h6 class="card-category">Gastos Variáveis</h6>
                                    <h5 class="card-title">
                                        <i class="tim-icons icon-bell-55 text-danger" id="totalVariado"></i>
                                    </h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <div class="card card-chart">
                            <div class="card-header">
                                <h6 class="card-category">Lucro</h6>
                                    <h5 class="card-title">
                                        <i class="tim-icons icon-bell-55 text-success" id="totalLucro"></i>
                                    </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-2">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item" aria-current="page"><h1 class="h5">Gastos Fixos</h1></li>
                    </ol>
                </nav>
            </div>

        <table id="table1" class="table table-striped table-condensed table-hover display" style="width:100%">
            <thead class="text-center">
                 <tr>
                    <th>#</th>
                    <th data-sortable="true">Descrição</th>
                     <th data-sortable="true">Valor</th>
                     <th data-sortable="true">Data</th>
                     <th data-sortable="true">Atualizado</th>
                    <th data-sortable="true">Ação</th>
                </tr>
            </thead>
            <tbody class="text-center"></tbody>
        </table>

            <div class="col-md-12 mb-2">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item" aria-current="page"><h1 class="h5">Gastos Variados</h1></li>
                    </ol>
                </nav>
            </div>
        <table id="table2" class="table table-striped table-condensed table-hover display" style="width:100%">
            <thead class="text-center">
            <tr>
                <th>#</th>
                <th data-sortable="true">Descrição</th>
                <th data-sortable="true">Valor</th>
                <th data-sortable="true">Data</th>
                <th data-sortable="true">Atualizado</th>
                <th data-sortable="true">Ação</th>
            </tr>
            </thead>
            <tbody class="text-center"></tbody>
        </table>
    </div>

    <div id="divModal" class="modal fade"  style="height: auto;" tabindex="-1" role="dialog">
        <form class="needs-validation md-form" novalidate id="formModal" name="formModal" method="post"  enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="metodo" id="metodo">

            <div class="alert alert-secondary" role="alert" id="modal-title"></div>

            <div class="form-row">
                <div class="col-md-8 mb-2">
                    <select class="form-control center" id="tipo_gasto" name="tipo_gasto" title="Tipo do Gasto" placeholder="Tipo Gasto Fixo" data-affixes-stay="true" required>
                        <option value="">Selecione o Tipo de Gasto</option>
                        <option value="1">Gasto Fixo</option>
                        <option value="2">Gasto Variável</option>
                    </select>
                    <div class="invalid-feedback">Obrigatório.</div>
                </div>
                <div class="col-md-8 mb-2">
                    <input type="text" class="form-control center" id="descricao" name="descricao"  title="Descrição do Gasto" placeholder="Descrição Gasto Fixo" data-affixes-stay="true" required>
                    <div class="invalid-feedback">Obrigatório.</div>
                </div>
                <div class="col-md-4 mb-2">
                    <input type="text" class="form-control center" id="valor" name="valor"  title="Valor do Gasto" placeholder="Valor Gasto Fixo" data-affixes-stay="true" data-prefix="R$ " data-thousands="." data-decimal="," somenteNumeros(this); required>
                    <div class="invalid-feedback">Obrigatório.</div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-2">
                    <button type="submit" class="btn btn-info">Enviar</button>
                </div>
            </div>
        </form>

    </div>

    <script src="{{URL::asset('js/jquery.maskMoney.min.js')}}"></script>
    <script src="{{URL::asset('js/gastosfixo.js')}}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js"></script>

    <script src="{{URL::asset('js/bootstrap-datepicker.pt-BR.min.js')}}"></script>



@endsection
