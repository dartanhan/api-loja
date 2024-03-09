
@extends('layouts.layout', ['page' => __('Produtos'), 'pageSlug' => 'product'])

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
                    <div class="card-header">
                        <div class="row">
                            <div class="col-auto input-group-sm ">
                                <h4 class="title"><i class="fas fa-weight-hanging"></i><strong> {{ __('PRODUTOS') }}</strong></h4>
                            </div>
                            <div class="text-right" style="position: absolute;margin-top: 0; text-align: right">
                                <button type="button" class="btn bgBtn gerarCodigo" id="GerarCodigo" name="GerarCodigo">
                                    <i class="fa fa-gear"></i> {{ __('Gerar Código') }}</button>
                                <button type="button" class="btn bgBtn" id="btnLote" data-bs-toggle="modal" data-bs-target="#modalUpdateLote">
                                    <i class="fas fa-cogs"></i> {{ __('Atualização em Lote') }}</button>
                                <button type="submit" class="btn bgBtn" id="btnSalvar" name='onSubmit'>
                                    <i class="fas fa-check"></i> {{ __('Salvar') }}</button>
                                <button type="button" class="btn bgBtn adicionar" id="adicionar">
                                    <i class="far fa-plus-square"></i> {{ __('Adicionar Variação') }}</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card mb-4">
                            <div class="card-header">
                                <div class="form-row ">
                                    <div class="form-group border-lable-flt col-md-2">
                                        <input type="text" name="codigo_produto" id="codigo_produto" class="form-control format-font"
                                               placeholder="{{ __('CÓDIGO DO PRODUTO') }}"
                                               onkeyup="SomenteNumeros(this);" required autofocus>
                                        <label for="label-password">{{ __('CÓDIGO DO PRODUTO') }}</label>
                                    </div>
                                    <div class="form-group border-lable-flt col-md-3 format-font">
                                        <input type="text" name="descricao" id="descricao" class="form-control format-font"
                                                placeholder="{{ __('DESCRIÇÃO') }}"  required autofocus>
                                        <label for="label-descricao">{{ __('DESCRIÇÃO') }}</label>
                                    </div>
                                    <div class="form-group col-md-1.2 border-lable-flt format-font">
                                        <select name="status" id="status" class="form-select format-font" required>
                                            <option value="1" selected>ATIVO</option>
                                            <option value="0">INATIVO</option>
                                        </select>
                                        <label for="label-qtd">STATUS</label>
                                    </div>
                                    <div class="form-group border-lable-flt col-xs-2 format-font">
                                            <select id="categoria_id" name="categoria_id" class="form-select format-font"
                                                    title="Categoria do Produto" required>
                                                <option value="" class="select-custom">CATEGORIA?</option>
                                                @foreach($categories as $category)
                                                    <option value="{{$category->id}}" > {{ $category->nome  }}</option>
                                                @endforeach
                                            </select>
                                            <label for="label-qtd">CATEGORIA</label>
                                    </div>
                                    <div class="form-group border-lable-flt col-md-2 format-font">
                                        <input type="text" name="ncm" id="ncm" class="form-control format-font" placeholder="{{ __('NCM') }}"  required>
                                        <label for="label-ncm">{{ __('NCM') }}</label>
                                    </div>
                                    <div class="form-group border-lable-flt col-md-2 format-font">
                                        <input type="text" name="cest" id="cest" class="form-control format-font" placeholder="{{ __('CEST') }}"  required>
                                        <label for="label-cest">{{ __('CEST') }}</label>
                                    </div>
                                    <div class="form-group border-lable-flt col-md-6 format-font">
                                        <select name="origem" id="origem"
                                                class="form-select format-font" title="Origem do Produto" required>
                                            <option value="" class="select-custom">ORIGEM?</option>
                                            @foreach($origem_nfces as $origem_nfce)
                                                <option value="{{$origem_nfce->id}}" > {{ strtoupper($origem_nfce->codigo)  }} - {{ strtoupper($origem_nfce->descricao)  }}</option>
                                            @endforeach
                                        </select>
                                        <label for="label-qtd">ORIGEM DO PRODUTO</label>
                                    </div>

                                    <div id="tbl" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                       <hr>
                       <div class="card mb-4">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-auto input-group-sm">
                                        <h6><i class="fas fa-table me-1"></i>Produtos</h6>
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
                                        <th data-sortable="true">Ação</th>
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
    <!-- Modal Image-->
    <div class="modal fade" id="divModalImage" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Incluir/Alterar/Remover - Fotos do Produto </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="load" style="display: none"></div>
                    <form method="post" autocomplete="off" id="formImage" name="formImage" enctype="multipart/form-data" class="form-inline">
                        @csrf
                        <input type="hidden" id="products_variation_id" name="products_variation_id">
                        <input type="hidden" name="tipoImage" id="tipoImage" value="variation">
                        <div class="form-row">
                                <div class="form-group col-md-6">
                                    <input type="Text" name="arquivo" id="arquivo" class="form-control" placeholder="Arquivo" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="btn btn-primary">Selecionar Arquivo
                                        <input type="file" name="images" id="file"  multiple="multiple" accept=".gif,.jpg,.jpeg,.png,.svg"/>
                                    </label>
                                </div>
                            </div>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-center flex-nowrap">
                    <div class="row" id="pictures"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Image Pooduto PAI-->
       <div class="modal fade" id="divModalImageProduct" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content ">
                <form id="formImageProduct" name="formImageProduct" class="needs-validation form-floating" novalidate method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="productId" id="productId">
                    <input type="hidden" name="variacaoId" id="variacaoId">
                    <input type="hidden" name="variacaoImageId" id="variacaoImageId">
                    <input type="hidden" name="imagemName" id="imagemName">
                    <input type="hidden" name="flagImage" id="flagImage">
                    <input type="hidden" name="metodo" id="metodo" value="put">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="exampleModalLongTitle">Imagem do Produto </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <input type="file"  id="image" name="image" title="Imagem" placeholder="Imagem" data-type="local">

                        <!-- Pré-visualização da imagem atual -->
                        <img id="modal-imagem" alt="Imagem Atual" class="img-thumbnail">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardar">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal Update Lote-->
    <div class="modal fade" id="modalUpdateLote" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" autocomplete="off" id="formUpload" name="formUpload" enctype="multipart/form-data" action="{{ route('import-product') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Atualização de Produtos em Lote</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <input type="Text" name="arquivo" id="arquivo" class="form-control" placeholder="Arquivo" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="btn btn-primary">Selecionar Arquivo
                                        <input type="file" name="fileUpload" id="file" />
                                    </label>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{URL::asset('/storage/atualizar_produto.xlsx')}}" class="btn btn-primary">Template</a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Enviar</button>
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
    <script src="{{URL::asset('assets/bootstrap/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/bootstrap-datepicker.pt-BR.min.js')}}" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{URL::asset('js/comum.js')}}"></script>
    <script src="{{URL::asset('js/produto_new.js')}}"></script>
    <script src="{{URL::asset('js/filePond.js')}}"></script>

@endpush
@push("styles")
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/bootstrap/css/bootstrap-datepicker.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/datatables/dataTableRender.css')}}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.2/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('css/custom-input-float.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/sweetalert2/animate.min.css')}}"/>

@endpush
