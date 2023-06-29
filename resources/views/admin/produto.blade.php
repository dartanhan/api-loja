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
                            <i class="fas fa-table me-1"></i>
                            Produtos
                        </div>
                        <div class="text-right" style="position: absolute;margin-top: -7px; text-align: right">
                            <button id="btnNuevo" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#divModal">
                                <i class="fas fa-weight-hanging"></i>
                                Novo Produto
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table id="table" class="table compact table-striped table-bordered table-hover">
                        <thead class="text-center">
                            <tr>
                                <th>#</th>
                                <th data-sortable="true">ID</th>
                                <th data-sortable="true">Código</th>
                                <th data-sortable="true">Nome</th>
                                <th data-sortable="true">Status</th>
                                <th data-sortable="true">Val.Prd</th>
                                <th data-sortable="true">Val.Din</th>
                                <th data-sortable="true">Val.Cart</th>
                                <th data-sortable="true">Feira</th>
                                <th data-sortable="true">Barão</th>
                                <th data-sortable="true">Percentual</th>
                                <th data-sortable="true">Data Criação</th>
                                <th data-sortable="true">Data Update</th>
                                <th data-sortable="true">Fornecedor</th>
                                <th data-sortable="true">Categ.</th>
                                <th data-sortable="true">Cor</th>
                                <th data-sortable="true">QtdMinFeira</th>
                                <th data-sortable="true">QtdMinFeira</th>
                                <th data-sortable="true">Status</th>
                                <th data-sortable="false" class="action">Ação</th>
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
                <form id="form" name="form" class="needs-validation form-floating" novalidate method="post" data-url="{{route('admin')}}" enctype="multipart/form-data">
                    <div class="modal-header ">
                        <div class="alert col-md-11 alert-secondary" role="alert" id="modal-title"></div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        @csrf
                        <input type="hidden" name="id" id="id">
                        <input type="hidden" name="metodo" id="metodo">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-8 mb-2">
                                    <div class="input-group">
                                        <input autofocus="autofocus" class="form-control" id="codigo_produto" name="codigo_produto"
                                               onkeyup="somenteNumeros(this);"
                                               placeholder="Código" required
                                               style="font-weight: bold;text-align: center;" type="text">
                                        <div class="invalid-feedback">Obrigatório.</div>
                                        <div class="input-group-prepend"  name="geraCodigo" style="cursor: hand;  cursor:pointer;" id="divCodProduto">
                                <span class="input-group-text" id="inputGroupPrepend">
                                    <img title="Gerar Código" src="{{URL::asset('img/iconfinder_017_-_Reload_2949863.png')}}" alt="">
                                </span>
                                        </div>
                                    </div>                </div>
                                <div class="col-md-4 mb-2">
                                    <select class="form-select" id="cor" name="cor" required>
                                        <option value="">Cor?</option>
                                        @foreach($cores as $cor)
                                            <option value="{{$cor->id}}" > {{ $cor->nome  }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Obrigatório.</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-2">
                                    <!-- label for="validationCustom02">Descrição</label-->
                                    <input type="text" class="form-control center" id="nome" name="nome" placeholder="Descrição do Produto" required>
                                    <!-- div class="valid-feedback">ok!</div-->
                                    <div class="invalid-feedback">Obrigatório.</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4 mb-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control center" value="0" id="qtdfeira" name="qtdfeira" placeholder="Feira" title="Quantidade Feira"  aria-describedby="inputGroupPrepend" onkeyup="somenteNumeros(this);" required readonly>
                                        <div class="invalid-feedback">Obrigatório.</div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-1">
                                    <div class="input-group">
                                        <input type="text" class="form-control center" id="qtdbarao" name="qtdbarao" placeholder="Barão" title="Quantidade Barão" aria-describedby="inputGroupPrepend" onkeyup="somenteNumeros(this);" required>
                                        <div class="invalid-feedback">Obrigatório.</div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-1">
                                    <div class="input-group">
                                        <input type="text" class="form-control center " id="qtdmin" name="qtdmin" placeholder="Minima" title="Quantidade Minima" aria-describedby="inputGroupPrepend" value="2" onkeyup="somenteNumeros(this);">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4 mb-2">
                                    <input type="text" class="form-control center" id="valorproduto" name="valorproduto"  title="Valor Produto" placeholder="Valor Produto" data-affixes-stay="true" data-prefix="R$ " data-thousands="." data-decimal="," somenteNumeros(this); required>
                                    <div class="invalid-feedback">Obrigatório.</div>
                                </div>
                                <!--div class="col-md-4 mb-2">
                                    <input type="text" class="form-control center" id="valorpercentual" name="valorpercentual" title="Percentual" placeholder="Valor Percentual" value="99,99 %" data-suffix=" %" data-thousands="." data-decimal="," somenteNumeros(this); required>
                                    <div class="invalid-feedback">Obrigatório.</div>
                                </div-->
                                <div class="col-md-4 mb-2">
                                    <input type="text" class="form-control center" id="valordinheiro" name="valordinheiro"  title="Valor Dinheiro" placeholder="Valor Dinheiro" data-affixes-stay="true" data-prefix="R$ " data-thousands="." data-decimal="," somenteNumeros(this); required>
                                    <div class="invalid-feedback">Obrigatório.</div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <input type="text" class="form-control center" id="valorcartao" name="valorcartao" title="Valor Cartão" placeholder="Valor Cartão" data-affixes-stay="true" data-prefix="R$ " data-thousands="." data-decimal="," somenteNumeros(this); required>
                                    <div class="invalid-feedback">Obrigatório.</div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <select class="form-select" id="fornecedor" name="fornecedor" required>
                                        <option value="">Fornecedor?</option>
                                        @foreach($fornecedors as $fornecedor)
                                            <option value="{{$fornecedor->id}}" > {{ $fornecedor->nome  }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Obrigatório.</div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <select class="form-select" id="categoria" name="categoria" required>
                                        <option value="">Categoria?</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{$categoria->id}}" > {{ $categoria->nome  }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Obrigatório.</div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="1">Ativo</option>
                                        <option value="0">Inativo</option>
                                    </select>
                                </div>
                                <div class="col-md-8 mb-2">
                                    <div class="custom-file mb-3">
                                        <input type="file" class="custom-file-input" name="images[]" id="file-upload" accept=".gif,.jpg,.jpeg,.png,.svg" multiple="multiple"/>
                                        <label class="custom-file-label" for="file-upload" data-browse="Enviar Imagens"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                <button type="submit" class="btn btn-primary" id="btnGuardar">Salvar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="gallery" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Imagens do Produto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body gallery" id="modalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push("scripts")
    <script src="{{URL::asset('assets/jquery/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{URL::asset('assets/jquery/jquery.validate.min.js')}}"></script>
    <script src="{{URL::asset('assets/jquery/jquery.maskMoney.min.js')}}"></script>
    <script src="{{URL::asset('js/modal.js')}}"></script>
    <script src="{{URL::asset('js/produto.js')}}"></script>
@endpush
@push("styles")
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/datatables/dataTableRender.css')}}">
    <link rel="stylesheet" href="{{URL::asset('css/gallery.css')}}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
@endpush
