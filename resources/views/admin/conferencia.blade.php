@extends('layouts.layout')

@section('menu')

@include('admin.menu')

@endsection

@section('content')

    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css" rel="stylesheet">

        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js"></script>

        <script src="{{URL::asset('public/js/bootstrap-datepicker.pt-BR.min.js')}}"></script>

        <script	src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

        <script src="{{URL::asset('public/js/moment.min.js')}}"></script>

    </head>
        <div class="form-row">
            <div class="col-md-12 mb-2">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashbord')}}">Dashbord</a></li>
                    <li class="breadcrumb-item {{ Route::current()->getName() === 'conferenciames.index' ? 'active' : '' }}" aria-current="page">Conferência Mês</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container caja">
        <div class="container">
            <form name="form" method="post" id="formSend" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="col-md-1 mb-2">
                        <div class="input-group">
                            <select class="form-control" id="lojas">

                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="input-group">
                            <input class="datames form-control" id="datames" name="datames" type="text" placeholder="Data" required>
                        </div>
                    </div>
                    <div class="col-md-1 mb-1">
                        <div class="input-group">
                            <button type="submit" class="btn btn-info" id="btnSend">Enviar</button>
                        </div>
                    </div>
                    <div class="col-md-1 mb-1">
                        <div class="input-group">
                            <button id="btnNuevo" type="button" class="btn btn-warning" data-toggle="modal" title="Capital" style="height: 40px">
                                <i class="material-icons">attach_money</i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12 mb-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page"><h1 class="h5">Conferência Mês : Feira</h1></li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-sm-2 sm-2">
                <div class="card card-chart">
                    <div class="card-header">
                        <h6 class="card-category">Capital</h6>
                        <h5 class="card-title">
                            <i class="tim-icons icon-bell-55 text-success" id="capital"></i>
                        </h5>
                    </div>
                    <!--div class="card-body">
                        <div class="chart-area">
                            <canvas id="chartLinePurple"></canvas>
                        </div>
                    </div-->
                </div>
            </div>
            <div class="col-sm-2 mb-2">
                <div class="card card-chart">
                    <div class="card-header">
                        <h6 class="card-category">Total Bruto</h6>
                        <h5 class="card-title">
                            <i class="tim-icons icon-bell-55 text-success" id="totalbruto"></i>
                        </h5>
                    </div>
                    <!--div class="card-body">
                        <div class="chart-area">
                            <canvas id="chartLinePurple"></canvas>
                        </div>
                    </div-->
                </div>
            </div>
            <div class="col-sm-2 mb-2">
                <div class="card card-chart">
                    <div class="card-header">
                        <h6 class="card-category">Lucro</h6>
                        <h5 class="card-title">
                            <i class="tim-icons icon-bell-55 text-danger" id="totallucro"></i>
                        </h5>
                    </div>
                    <!--div class="card-body">
                        <div class="chart-area">
                            <canvas id="chartLinePurple"></canvas>
                        </div>
                    </div-->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table id="tableCategorias" class="table table-striped table-condensed table-hover">
                        <thead class="text-center">
                        <tr>
                            <th>Id</th>
                            <th>Categoria</th>
                            <th>Quantidade</th>
                            <th>Status</th>
                            <th>Data Criação</th>
                            <th>Data Atualização</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody class="text-center">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--Modal para CRUD-->
    <div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display:none;height: auto">
        <div class="modal-dialog" role="document" style="padding-top: 10px">
            <div class="modal-content">
                <div class="alert alert-secondary" role="alert" id="modal-title"></div>
                <form id="formCategorias" name="formCategorias" class="needs-validation" novalidate method="post" >
                    @csrf
                    <input type="hidden" name="categoria_id" id="categoria_id">
                    <input type="hidden" name="metodo" id="metodo">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md8 mb-2">
                                   <input type="text" class="form-control" id="nome" name="nome" title="Nome da Categoria" placeholder="Nome da Categoria" >
                            </div>
                            <div class="col-md-4 mb-2">
                                <select class="form-control" id="status" name="status" required>
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                            <div class="col-md12 mb-2">
                                <input type="number" class="form-control" id="quantidade" name="quantidade" title="Quantidade" placeholder="Quantidade" >
                            </div>
                            <div class="col-md4 mb-2">
                               &nbsp; <input class="btn btn-primary" type="submit" name="salvar"  id="btnGuardar" value="Salvar"/>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <script src="{{URL::asset('js/conferencia.js')}}"></script>

    </body>


@endsection
