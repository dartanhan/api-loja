@extends('layouts.layout')

@section('menu')

@include('admin.menu')

@endsection

@section('content')

    <div class="form-row">
        <div class="col-md-12 mb-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item {{ Route::current()->getName() === 'admin.dashbord' ? 'active' : '' }}" aria-current="page"><h1 class="h4">Dashbord</h1></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container caja">
      <!--div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group mr-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
          </div>
          <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
            <span data-feather="calendar"></span>
            This week
          </button>
        </div>
      </div-->
        <!--div class="col-md-12 mb-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page"><h1 class="h5">Movimento Dia : Feira</h1></li>
                </ol>
            </nav>
        </div>
        <div class="container" align="center">
            <div class="row">
                    <div class="col-sm-2 sm-2">
                        <div class="card card-chart">
                            <div class="card-header">
                                <h6 class="card-category">Total Dinheiro</h6>
                                <h5 class="card-title">
                                    <i class="tim-icons icon-bell-55 text-success" id="totalDinheiroFeira"></i>
                                </h5>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <div class="card card-chart">
                            <div class="card-header">
                                <h6 class="card-category">Total Cartão
                                    <span data-feather="eye" class="detailCart" data-tooltip="Click para detalhes" data-content="1" id="detailCart"></span></h6>
                                <h5 class="card-title">
                                    <i class="tim-icons icon-bell-55 text-success" id="totalCartFeira"></i>
                                </h5>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <div class="card card-chart">
                            <div class="card-header">
                                <h6 class="card-category">Total Desconto</h6>
                                <h5 class="card-title">
                                    <i class="tim-icons icon-bell-55 text-danger" id="totalDescFeira"></i>
                                </h5>
                            </div>
                           
                        </div>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <div class="card card-chart">
                            <div class="card-header">
                                <h6 class="card-category">Total Dia</h6>
                                <h5 class="card-title">
                                    <i class="tim-icons icon-bell-55 text-success" id="totalDiaFeira"></i>
                                </h5>
                            </div>
                           
                        </div>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <div class="card card-chart">
                            <div class="card-header">
                                <h6 class="card-category">Total Semana</h6>
                                <h5 class="card-title">
                                    <i class="tim-icons icon-bell-55 text-primary" id="totalSemanaFeira"></i>
                                </h5>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <div class="card card-chart">
                            <div class="card-header">
                                <h6 class="card-category">Total Mês</h6>
                                <h5 class="card-title">
                                    <i class="tim-icons icon-bell-55 text-primary" id="totalMesFeira"></i>
                                </h5>
                            </div>
                            
                        </div>
                    </div>
            </div>
        </div>
        <div class="chart-container">
            <canvas class="my-4 w-100" id="myChartFeira" width="900" height="180"></canvas>
        </div>
        <div class="col-md-12 mb-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page"><h1 class="h5">Vendas em {{ \Carbon\Carbon::today()->format("d/m/Y")}}</h1></li>
                </ol>
            </nav>
        </div>
        <div class="table-responsive">
            <table id="tableVendaDia1" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead class="text-center">
                <tr>
                    <th data-sortable="true">Codigo Venda</th>
                    <th data-sortable="true">Forma Pgto</th>
                    <th data-sortable="true">Sub Total</th>
                    <th data-sortable="true">Desconto</th>
                    <th data-sortable="true">Total</th>
                    <th data-sortable="true">Data</th>
                    <th data-sortable="false" style="width: 50px">Ações</th>
                </tr>
                </thead>
                <tbody class="text-center"></tbody>
            </table>
        </div>
        <br/-->
        <div class="col-md-12 mb-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page"><h1 class="h5">Movimento Dia : Barão</h1></li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-sm-2 mb-2">
                <div class="card card-chart">
                    <div class="card-header">
                        <h6 class="card-category">Total Dinheiro</h6>
                        <h5 class="card-title">
                            <i class="tim-icons icon-bell-55 text-success" id="totalDinheiroBarao"></i>
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
                        <h6 class="card-category">Total Cartão <span data-feather="eye" class="detailCart" data-tooltip="Click para detalhes" data-content="2" id="detailCart"></span></h6>
                        <h5 class="card-title">
                            <i class="tim-icons icon-bell-55 text-success" id="totalCartBarao"></i>
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
                    <div class="card-header ">
                        <h6 class="card-category">Total Desconto</h6>
                        <h5 class="card-title">
                                <i id="totalDescBarao" class="tim-icons icon-bell-55 text-danger"></i>
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
                        <h6 class="card-category">Total Dia</h6>
                        <h5 class="card-title">
                            <i class="tim-icons icon-bell-55 text-primary" id="totalDiaBarao"></i>
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
                        <h6 class="card-category">Total Semana</h6>
                        <h5 class="card-title">
                            <i class="tim-icons icon-bell-55 text-primary" id="totalSemanaBarao"></i>
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
                        <h6 class="card-category">Total Mês</h6>
                        <h5 class="card-title">
                            <i class="tim-icons icon-bell-55 text-primary" id="totalMesBarao"></i>
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
        <canvas class="my-4 w-100" id="myChartBarao" width="900" height="180"></canvas>
        <div class="col-md-12 mb-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page"><h1 class="h5">Vendas em {{ \Carbon\Carbon::today()->format("d/m/Y")}}</h1></li>
                </ol>
            </nav>
        </div>
        <div class="table-responsive">
            <table id="tableVendaDia2" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead class="text-center">
                <tr>
                    <th data-sortable="true">Codigo Venda</th>
                    <th data-sortable="true">Forma Pgto</th>
                    <th data-sortable="true">Sub Total</th>
                    <th data-sortable="true">Desconto</th>
                    <th data-sortable="true">Total</th>
                    <th data-sortable="true">Data</th>
                    <th data-sortable="false" style="width: 50px">Ações</th>
                </tr>
                </thead>
                <tbody class="text-center"></tbody>
            </table>
        </div>

        <div id="divModal" class="modal fade"  style="height: auto;" data-dismiss="modal">
                <div class="table-responsive">
                    <table id="tableView" class="table table-striped table-condensed table-hover table-sm" style="width:100%">
                        <thead class="text-center">
                        <tr>
                            <th data-sortable="false">CÓDIGO</th>
                            <th data-sortable="false">PRODUTO</th>
                            <th data-sortable="false">VALOR</th>
                            <th data-sortable="false">QTD</th>
                            <th data-sortable="false">TOTAL</th>
                        </tr>
                        </thead>
                        <tbody class="text-center">
                            <tfoot><tr id="foot"></tr><tfoot>
                        </tbody>
                    </table>
                </div>

            <div id="divModalCart" class="modal fade"  style="height: auto;" data-dismiss="modal">
                <div class="table-responsive">
                    <table id="tableViewCart" class="table table-striped table-condensed table-hover table-sm" style="width:100%">
                        <thead class="text-center">
                        <tr>
                            <th data-sortable="false">FORMA PAGAMENTO</th>
                            <th data-sortable="false">TOTAL</th>
                            <!--th data-sortable="false">TAXA</th-->
                            <th data-sortable="false">TOTAL FINAL</th>
                        </tr>
                        </thead>
                        <tbody class="text-center"></tbody>
                    </table>
                </div>
        </div>
		</div>
		<hr>
        <div class="table-responsive">
            <div class="row">
                <div class="col-sm-4 mb-2">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item" aria-current="page"><h1 class="h5">TOTAL POR MÊS</h1></li>
                        </ol>
                    </nav>
                    <table class="table table-striped table-condensed table-hover table-sm" id="tableViewTotalMeses">
                        <thead>
                        <tr>
                            <th>Mês</th>
                            <th>Valor</th>
                        </tr>
                        </thead>
                        <tbody class="text-center"></tbody>
                    </table>
                </div>
                <div class="col-sm-4 mb-2">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item" aria-current="page"><h1 class="h5">FECHAMENTO MÊS(CARTÕES)</h1></li>
                        </ol>
                    </nav>
                    <table class="table table-striped table-condensed table-hover table-sm" id="tableViewFechamentoMeses">
                        <thead>
                        <tr>
                            <th>Mês</th>
                            <th>Valor</th>
                        </tr>
                        </thead>
                        <tbody class="text-center"></tbody>
                    </table>
                </div>
            </div>
        </div>
    
    </div>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
     <script src="{{URL::asset('js/dashboard.js')}}"></script>


    </body>
@endsection
