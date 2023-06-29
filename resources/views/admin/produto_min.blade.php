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
                <li class="breadcrumb-item {{ Route::current()->getName() === 'productMin.index' ? 'active' : '' }}" aria-current="page">Produtos Quantidade Minima</li>
              </ol>
            </nav>
         </div>
    </div>

    <div class="container caja">
        <div id="divAlert" >
            <div class="" role="alert" id="alert-title"></div>
        </div>
        @csrf
        <table id="table" class="table table-striped table-condensed table-hover" style="width:100%">
            <thead class="text-center">
                 <tr>
                    <th>#</th>
                    <th data-sortable="true">ID</th>
                    <th data-sortable="true">Código</th>
                    <th data-sortable="true">Nome</th>
                     <th data-sortable="true">Qtd</th>
                     <th data-sortable="true">Qtd Min</th>
                    <th data-sortable="true">Val.Prd</th>
                    <th data-sortable="true">Val.Din</th>
                    <th data-sortable="true">Val.Cart</th>
                </tr>
            </thead>
            <tbody class="text-center"></tbody>
        </table>
    </div>

<script src="{{URL::asset('js/jquery.maskMoney.min.js')}}"></script>
<script src="{{URL::asset('js/produto_min.js')}}"></script>
<link rel="stylesheet" href="{{URL::asset('css/dataTableRender.css')}}">

@endsection
