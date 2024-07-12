@extends('layouts.layout')

@section('menu')

    @include('admin.menu')

@endsection

@section('content')

    <div class="container-fluid"  style="padding-top: 10px;">
        <div class="col-md-12" style="padding:unset">
            <div class="card" style="padding:unset">
                <form method="post" autocomplete="off" id="form" name="form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                   
                    <div class="card-body">
                       
                       <div class="card mb-4">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-auto input-group-sm">
                                        <h6><i class="fas fa-table me-1"></i>Lista de Compras</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="table" class="table table-striped table-condensed table-hover  responsive" style="width:100%">
                                    <thead class="text-center">
                                    <tr>
                                        <th>#</th>
                                        <th data-sortable="true">ID</th>
                                        <th data-sortable="true">Código</th>
                                        <th data-sortable="true">Imagem</th>
                                        <th data-sortable="true">Produto</th>
                                        <th data-sortable="true">Categoria</th>
                                        <th data-sortable="true">Status</th>
                                        <th data-sortable="true">Criado</th>
                                        <th data-sortable="true">Atualizado</th>
                                    </tr>
                                    </thead>
                                    <tbody class="text-center"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    
@endsection
@push("scripts")
    <script src="{{URL::asset('assets/jquery/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/dataTables.bootstrap4.min.js')}}"></script>    
    <script src="{{URL::asset('assets/jquery/jquery.validate.min.js')}}"></script>
    <script type="module" src="{{URL::asset('js/listadecompras.js')}}"></script>

@endpush
@push("styles")
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/datatables/dataTableRender.css')}}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.2/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/sweetalert2/animate.min.css')}}"/>
@endpush
