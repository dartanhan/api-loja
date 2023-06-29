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
                    <li class="breadcrumb-item {{ Route::current()->getName() === 'cliente.index' ? 'active' : '' }}" aria-current="page">Clientes</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container">
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
    </div>
    <div class="container caja">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table id="tableClientes" class="table table-striped table-condensed table-hover">
                        <thead class="text-center">
                        <tr>
                            <th>Id</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Cep</th>
                            <th>Logradouro</th>
                            <th>Número</th>
                            <th>Complemento</th>
                            <th>Bairro</th>
                            <th>Localidade</th>
                            <th>Uf</th>
                            <th>Data Cadastro</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody class="text-center">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--Modal para CRUD-->
    <div class="modal fade" id="divModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display:none;height: auto">
        <div class="modal-dialog" role="document" style="padding-top: 10px">
            <div class="modal-content">
                <div class="alert alert-secondary" role="alert" id="modal-title"></div>
                <form id="formCliente" name="formCliente" class="needs-validation" novalidate method="post" >
                    @csrf
                    <input type="hidden" name="cliente_id" id="cliente_id">
                    <input type="hidden" name="metodo" id="metodo">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md8 mb-2">
                                   <input type="text" class="form-control" id="nome" name="nome" title="Nome da Categoria" placeholder="Nome da Categoria" >
                            </div>

                            <div class="col-md12 mb-2">
                                <input type="number" class="form-control" id="quantidade" name="quantidade" title="Quantidade" placeholder="Quantidade" >
                            </div>
                            <div class="col-md4 mb-2">
                               &nbsp; <input class="btn btn-primary" type="submit" name="salvar"  id="btnGuardar" value="Salvar"/>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <script src="{{URL::asset('js/clientes.js')}}"></script>

    </body>


@endsection
