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
                    <li class="breadcrumb-item {{ Route::current()->getName() === 'fluxo.index' ? 'active' : '' }}" aria-current="page">Fluxo</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container caja">
		<div id="retorno_fluxo"></div>
        @csrf
        <!--table id="table" class="table table-striped table-condensed table-hover" style="width:100%"-->
        <table id="table" class="table table-striped table-condensed table-hover" style="width:100%">
            <thead class="text-center">
            <tr>
                <th>id</th>
                <th>Valor Caixa</th>
                <th>Valor Sangria</th>
                <th>Descrição</th>
                <th>Loja</th>
				<th>Total Dia</th>
                <th data-sortable="true">Data</th>
                <th>Atualização</th>
				 <th width="10px">Ação</th>
            </tr>
            </thead>
            <tbody class="text-center">
            </tbody>
        </table>

    </div>

    <script src="{{URL::asset('js/fluxo.js')}}"></script>

    </body>
@endsection
