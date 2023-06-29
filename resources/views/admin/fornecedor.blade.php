@extends('layouts.layout')

@section('menu')

@include('admin.menu')

@endsection

@section('content')

    <div class="container-fluid"  style=" padding-top: 10px;padding-right: 10px">
        <div id="divDelete" name="load">
            <div class="" role="alert" id="alert-title-delete"></div>
        </div>
        <div class="container-fluid px-4">
            <div class="mb-4">
                <button id="btnNuevo" type="button" class="btn btn-success btn-block" data-bs-toggle="modal" data-bs-target="#divModal">
                    <i class="fas fa-truck"></i>
                    Adicionar Fornecedor
                </button>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col-auto input-group-sm">
                            <i class="fas fa-table me-1"></i>
                                Fornecedores
                        </div>

                    </div>
                </div>

                <div class="card-body">
                     <table id="table" class="table table-striped table-condensed table-hover display nowrap">
                        <thead class="text-center">
                        <tr>
                            <th>ID</th>
                            <th data-sortable="true">Nome</th>
                            <th>Status</th>
                            <th>Data Criação</th>
                            <th>Data Atualização</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody class="text-center"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="divModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content ">
                <form id="form" name="form" class="needs-validation form-floating" novalidate method="post">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="mdal-title" id="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <input type="hidden" name="metodo" id="metodo">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <input type="text" class="form-control" id="nome" name="nome" title="Descrição do Fornecedor" placeholder="Descrição do Fornecedor">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <select class="form-select" id="status" name="status" onChange="fncComboFornecedor(this.value);" required>
                                        <option value="1">Ativo</option>
                                        <option value="0">Inativo</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <select class="form-select" id="fornecedor_new" name="fornecedor_new" required>
                                        <option value="" class="select-custom">Selecione o Fornecedor?</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{$supplier->id}}"> {{strtoupper($supplier->nome)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="alert alert-warning" role="alert" id="message-alert-update" hidden="hidden"></div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardar">Salvar</button>
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
    <script src="{{URL::asset('js/fornecedor.js')}}"></script>
@endpush
@push("styles")
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/datatables/dataTableRender.css')}}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
@endpush
