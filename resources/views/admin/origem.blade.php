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
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col-auto input-group-sm">
                            <h5 class="title"><i class="bi bi-clouds"></i><strong> {{ __('Origem dos Produtos') }}</strong></h5>                          
                        </div>
                        <div class="text-right" style="position: absolute;margin-top: 0px; text-align: right">
                            <button id="btnNuevo" type="button" class="btn bgBtn" data-bs-toggle="modal" data-bs-target="#divModal">
                                <i class="bi bi-clouds"></i>
                                Nova Origem
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table id="table" class="table compact table-striped table-bordered table-hover">
                        <thead class="text-center">
                        <tr>
                            <th>id</th>
                            <th data-sortable="true">Codigo</th>
                            <th data-sortable="true">Descricão</th>
                            <th data-sortable="true">Criado</th>
                            <th data-sortable="true">Atualizado</th>
                            <th data-sortable="true">Ação</th>
                        </tr>
                        </thead>
                        <tbody class="text-center"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="divModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form method="post" autocomplete="off" id="formOrigem" name="formOrigem" enctype="multipart/form-data" action="{{route('origem.store')}}" novalidate="validate">
                    @csrf
                        <input type="hidden" name="id" id="id">
                        <input type="hidden" name="metodo" id="metodo">
                    <div class="modal-header bg-pink text-white">
                        <h5 class="modal-title" id="title-origem"> </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-xs-1">
                                <span class="border-lable-flt">
                                    <input type="text" class="form-control" placeholder="CODIGO DA ORIGEM" name="codigo" id="codigo" required>
                                    <label for="label-codigo">CÓDIGO</label>
                                </span>
                            </div>
                            <div class="form-group col-md-8">
                                <span class="border-lable-flt">
                                    <input type="text" class="form-control" placeholder="DESCRIÇÃO" name="descricao" id="descricao" required>
                                    <label for="label-descricao">DESCRIÇÃO</label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bgBtn" data-toggle="modal" data-target=".bd-example-modal-sm">
                            <i class="bi bi-cursor-fill"></i> Enviar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Fechar
                        </button>
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
    <script src="{{URL::asset('assets/jquery/jquery.maskMoney.min.js')}}"></script>
    <script src="{{URL::asset('js/origem.js')}}"></script>
@endpush
@push("styles")
 
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/bootstrap/css/bootstrap-datepicker.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/datatables/dataTableRender.css')}}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.2/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('css/custom-input-float.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/sweetalert2/animate.min.css')}}"/>
@endpush
