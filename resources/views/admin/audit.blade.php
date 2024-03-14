@extends('layouts.layout')

@section('menu')

@include('admin.menu')

@endsection

@section('content')
<div class="container-fluid"  style=" padding-top: 10px;padding-right: 10px">
        <div class="form-row">
            <div class="col-md-12 mb-2">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashbord</a></li>
                <li class="breadcrumb-item {{ Route::current()->getName() === 'audit.index' ? 'active' : '' }}" aria-current="page">Produtos Alterados</li>
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
                    <th data-sortable="true">USUÁRIO</th>
                    <th data-sortable="true">EVENTO</th>
                    <th data-sortable="true">PRODUTO</th>
                    <th data-sortable="true">QTD ANTERIOR</th>
                    <th data-sortable="true">QTD ATUAL</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach($audits as $audit)
                    <tr>
                        <td>{{ $audit->id }}</td>
                        <td>{{ $audit->user->name }}</td>
                        <td>{{ $audit->event }}</td>
                        <td>{{ $audit->auditable_id }}</td>
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
                    </tr>
                @endforeach
            </tbody>
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