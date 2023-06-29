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

        <script src="{{URL::asset('js/bootstrap-datepicker.pt-BR.min.js')}}"></script>

        <script	src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

        <script src="{{URL::asset('js/moment.min.js')}}"></script>

    </head>

    <body>

    <div class="form-row">
        <div class="col-md-12 mb-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashbord</a></li>
                    <li class="breadcrumb-item {{ Route::current()->getName() === 'relatorio.index' ? 'active' : '' }}" aria-current="page">Relatórios</li>
                    <li class="breadcrumb-item">Lançamento Diário de Vendas</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container" align="center">

        <div class="form-row">
            <div class="col-md-4 mb-2">
                <div class="input-group">
                    <input class="dateini form-control" id="dateini" name="dateini" type="text" placeholder="Data Inicial" required>
                </div>
            </div>
            <div class="col-md-4 mb-1">
                <div class="input-group">
                    <input class="datefim form-control" id="datefim" name="datefim" type="text" placeholder="Data Final">
                </div>
            </div>
            <div class="col-md-4 mb-1">
                <div class="input-group">
                    <button type="submit" class="btn btn-info">Enviar</button>
                </div>
            </div>
        </div>

        <div class="container caja">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card card-chart">
                        <div class="card-header">
                            <h5 class="card-category">Total Mensal</h5>
                            <h3 class="card-title"><i class="tim-icons icon-bell-55 text-primary"></i><div id="totalMesFeira"></div> </h3>
                        </div>
                        <!--div class="card-body">
                            <div class="chart-area">
                                <canvas id="chartLinePurple"></canvas>
                            </div>
                        </div-->
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-chart">
                        <div class="card-header">
                            <h5 class="card-category">Total Semanal</h5>
                            <h3 class="card-title"><i class="tim-icons icon-delivery-fast text-info"></i><div id="totalSemanaFeira"></div></h3>
                        </div>
                        <!--div class="card-body">
                            <div class="chart-area">
                                <canvas id="CountryChart"></canvas>
                            </div>
                        </div-->
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-chart">
                        <div class="card-header">
                            <h5 class="card-category">Total Diário</h5>
                            <h3 class="card-title"><i class="tim-icons icon-send text-success"></i> <div id="totalDiaFeira"></div></h3>
                        </div>
                        <!--div class="card-body">
                            <div class="chart-area">
                                <canvas id="chartLineGreen"></canvas>
                            </div>
                        </div-->
                    </div>
                </div>
            </div>
            <hr>
            <table id="tableVendaDia" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead class="text-center">
                <tr>
                    <th>#</th>
                    <th data-sortable="true">Código</th>
                    <th data-sortable="true">Forma Pagamento</th>
                    <th data-sortable="true">SubTotal</th>
                    <th data-sortable="true">% Desconto</th>
                    <th data-sortable="true">Total</th>
                    <th data-sortable="true">Data</th>
                    <th data-sortable="true">Hora</th>
                    <th data-sortable="true">Ações</th>
                </tr>
                </thead>
                <tbody class="text-center"></tbody>
            </table>
        </div>
    </div>

    <script src="{{URL::asset('js/relatorio.js')}}"></script>

    </body>


@endsection
