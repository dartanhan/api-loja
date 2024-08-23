
@extends('layouts.layout', ['page' => __('Produtos'), 'pageSlug' => 'product'])

@section('menu')

    @include('admin.menu')

@endsection

@section('content')

    <div class="container-fluid"  style="padding-top: 10px;">
         <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col-auto input-group-sm">
                        <h6><i class="fas fa-table me-1"></i>Vendas</h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="table" class="table table-striped table-condensed table-hover  responsive" style="width:100%">
                    <thead class="text-center">
                    <tr>
                        <th>#</th>
                        <th data-sortable="true">Atendente</th>
                        <th data-sortable="true">Cliente</th>
                        <th data-sortable="true">Status</th>
                        <th data-sortable="true">Data</th>
                        <th data-sortable="true">Ação</th>
                    </tr>
                    </thead>
                    <tbody class="text-center"></tbody>
                </table>
            </div>
        </div>
    </div>


@endsection
@push("scripts")
    <script src="{{URL::asset('assets/jquery/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/dataTables.bootstrap4.min.js')}}"></script>
    <script type="module" src="{{URL::asset('js/sale.js')}}"></script>

@endpush
@push("styles")
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/datatables/dataTableRender.css')}}">
@endpush
