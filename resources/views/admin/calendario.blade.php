@extends('layouts.layout')

@section('menu')

@include('admin.menu')

@endsection

@section('content')

        <div class="form-row">
            <div class="col-md-12 mb-2">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashbord')}}">Dashbord</a></li>
                    <li class="breadcrumb-item {{ Route::current()->getName() === 'calendario.index' ? 'active' : '' }}" aria-current="page">Calendario</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--div class="container">
        <div class="row">
            <div class="col-lg-12">
                <button id="btnNuevo" type="button" class="btn btn-info" data-toggle="modal">
                    <i class="material-icons">library_add</i>
                </button>
            </div>
        </div>
    </div>
    <br>
    <div id="divDelete" >
        <div class="" role="alert" id="alert-title-delete"></div>
    </div-->
    <div class="container caja">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">

                    <div
                        id='calendar'
                        data-calendarioshow="{{route('calendario.show','id')}}">
                    </div>

                </div>
            </div>
        </div>
    </div>

    <link href="{{URL::asset('calendar/main.css')}}" rel='stylesheet' />
    <script src="{{URL::asset('calendar/main.js')}}"></script>
    <script src="{{URL::asset('calendar/locales-all.js')}}"></script>
    <script src="{{URL::asset('calendar/scripts.js')}}"></script>
    <script src="{{URL::asset('calendar/calendar.js')}}"></script>

    </body>


@endsection
