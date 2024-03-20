@extends('layouts.layout')

@section('menu')

@include('admin.menu')

@endsection

@section('content')

    <div class="container-fluid"  style=" padding-top: 10px;padding-right: 10px">
        <div class="container-fluid px-4">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col-auto input-group-sm">
                            <i class="fas fa-cube"></i>
                            Auditoria - Alteração de quantidade do produto
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table id="table" class="table table-striped table-condensed table-hover">
                        <thead class="text-center">
                            <tr>
                                <th data-sortable="true">USUÁRIO</th>
                                <th data-sortable="true">EVENTO</th>
                                <th data-sortable="true">PRODUTO</th>
                                <th data-sortable="true">QTD ANTERIOR</th>
                                <th data-sortable="true">QTD ATUAL</th>
                                <th data-sortable="true">ATUALIZADO</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <!-- @foreach($auditsUpdate as $audit)
                                <tr>
                                    <td>{{ $audit->name }}</td>
                                    <td>{{ $audit->event }}</td>
                                    <td>{{ $audit->variacao }}</td>
                                    <td>
                                        @if(isset($audit->old_values['quantidade']))
                                            {{ $audit->old_values['quantidade'] }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($audit->new_values['quantidade']))
                                            {{ $audit->new_values['quantidade'] }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $audit->updated_at }}
                                    </td>
                                </tr>
                            @endforeach -->
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
    <div class="container-fluid px-4">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col-auto input-group-sm">
                            <i class="fas fa-cube"></i>
                            Auditoria - Criação de Produtos
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table id="tableCreate" class="table table-striped table-condensed table-hover">
                        <thead class="text-center">
                            <tr>
                                <th data-sortable="true">USUÁRIO</th>
                                <th data-sortable="true">EVENTO</th>
                                <th data-sortable="true">PRODUTO</th>
                                <th data-sortable="true">QTD ANTERIOR</th>
                                <th data-sortable="true">QTD ATUAL</th>
                                <th data-sortable="true">ATUALIZADO</th>
                            </tr>
                        </thead>
                        <tbody class="text-center"></tbody>
                        <!-- <tbody class="text-center">
                            @foreach($auditsCreate as $audit)
                                <tr>
                                    <td>{{ $audit->name }}</td>
                                    <td>{{ $audit->event }}</td>
                                    <td> 
                                        @if(isset($audit->new_values['descricao']))
                                            {{ $audit->new_values['descricao'] }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($audit->old_values['quantidade']))
                                            {{ $audit->old_values['quantidade'] }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($audit->new_values['quantidade']))
                                            {{ $audit->new_values['quantidade'] }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $audit->updated_at }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> -->
                    </table>
            </div>
        </div>

@endsection
@push("scripts")
    <script src="{{URL::asset('assets/jquery/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{URL::asset('js/audit.js')}}"></script>
@endpush
@push("styles")
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/datatables/dataTableRender.css')}}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
@endpush