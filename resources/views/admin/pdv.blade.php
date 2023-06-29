@extends('layouts.layout', ['page' => __('Pdv'), 'pageSlug' => 'pdv'])

@section('menu')

    @include('admin.menu')

@endsection

@section('content')

    <div class="container-fluid"  style="padding-top: 10px;padding-right: 10px">
        <div class="col-md-12">
            <div class="card">
                <form method="post" autocomplete="off" id="form" name="form" enctype="multipart/form-data">
                    @csrf
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="">
                                <div class="col-auto input-group-sm">
                                    <div class="form-row ">
                                        <div class="form-group border-lable-flt col-md-4 input-group-lg">
                                            <input type="text" name="codigo_produto" id="codigo_produto" class="form-control format-font "
                                                   placeholder="{{ __('CÓDIGO/NOME DO PRODUTO') }}" required autofocus/>
                                            <label for="label-password">{{ __('CÓDIGO/NOME DO PRODUTO') }}</label>
                                        </div>
                                        <div class="form-group border-lable-flt col-md-8 format-font input-group-lg">
                                            <input type="text" name="descricao" id="descricao" class="form-control format-font"
                                                   placeholder="{{ __('DESCRIÇÃO') }}"  required autofocus disabled/>
                                            <label for="label-descricao">{{ __('DESCRIÇÃO') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-striped table-condensed table-hover display nowrap" style="width:100%">
                                <thead class="text-center">
                                <tr>
                                    <th data-sortable="true"></th>
                                    <th data-sortable="true">Código</th>
                                    <th data-sortable="true">Produto</th>
                                    <th data-sortable="true">Quantidade</th>
                                    <th data-sortable="true">Valor</th>
                                    <th data-sortable="true" width="50px">Ações</th>
                                </tr>
                                </thead>
                                <tbody class="text-center"></tbody>
                            </table>
                        </div>
                    </div>
                    <input type="hidden" name="codigo_venda" id="codigo_venda"/>
                </form>
            </div>
        </div>
    </div>
@endsection
@push("scripts")
    <script src="{{URL::asset('assets/jquery/jquery-3.3.1.min.js')}}"></script>
    <script src="{{URL::asset('assets/jquery/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{URL::asset('assets/jquery/jquery.validate.min.js')}}"></script>
    <script src="{{URL::asset('assets/jquery/jquery.maskMoney.min.js')}}"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="{{URL::asset('js/scripts_pdv_web.js')}}"></script>
@endpush
@push("styles")

    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/datatables/dataTableRender.css')}}">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('css/custom-input-float.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/sweetalert2/animate.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{URL::asset('css/jquery-ui.min.css')}}"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
@endpush
