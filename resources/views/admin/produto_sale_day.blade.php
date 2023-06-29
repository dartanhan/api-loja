@extends('layouts.layout.blade.php')

@section('menu')

@include('admin.menu')

@endsection

@section('content')
        <div class="form-row">
            <div class="col-md-12 mb-2">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashbord')}}">Dashbord</a></li>
                <li class="breadcrumb-item {{ Route::current()->getName() === 'productSaleDay.index' ? 'active' : '' }}" aria-current="page">Produtos Vendidos no Dia</li>
              </ol>
            </nav>
         </div>
    </div>

    <div class="container caja">
        <div >
            <form name="formFiltro" id="formFiltro" class="row" method="post">
                @csrf
                <div class="col-sm-2 sm-2">
                    <input class="dateini form-control" id="dating" name="dating" type="text" placeholder="Data" required>
                </div>
                <!--div class="col-sm-2 mb-2">
                    <input class="datefim form-control" id="datefim" name="datefim" type="text" placeholder="Data Final">
                </div-->
                <div class="col-sm-2 mb-2">
                    <button type="submit" class="btn btn-info"  name="enviarForm" id="enviarForm">Enviar</button>
                </div>
            </form>
        </div>
        <div id="divAlert" >
            <div class="" role="alert" id="alert-title"></div>
        </div>
        @csrf
        <table id="table" class="table table-striped table-condensed table-hover" style="width:100%">
            <thead class="text-center">
                 <tr>
                     <th>#</th>
                    <th data-sortable="true">Código</th>
                    <th data-sortable="true">Nome</th>
                    <th data-sortable="true">Valor</th>
                     <th data-sortable="true">Quantidade</th>
                     <th data-sortable="true">Total</th>
                    <th data-sortable="true">Data</th>
                </tr>
            </thead>
            <tbody class="text-center"></tbody>
        </table>
    </div>

    <script src="{{URL::asset('js/jquery.maskMoney.min.js')}}"></script>
    <script src="{{URL::asset('js/produto_sale_day.js')}}"></script>
    <link rel="stylesheet" href="{{URL::asset('css/dataTableRender.css')}}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js"></script>
    <script src="{{URL::asset('js/bootstrap-datepicker.pt-BR.min.js')}}"></script>

@endsection
