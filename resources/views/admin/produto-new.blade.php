@extends('layouts.layout', ['page' => __('Produtos'), 'pageSlug' => 'produto'])

@section('menu')

    @include('admin.menu')

@endsection

@section('content')

    <div class="container-fluid mt-3" >
        <div class="col-md-12">
            <div class="card" >
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
                                    <div class="form-group border-lable-flt col-md-2 format-font">
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
                    </div>

                </form>
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
    </div>
    <!-- Modal -->
    <div class="modal fade right" id="slideInModal" tabindex="-1" aria-labelledby="slideInModalLabel" aria-hidden="true">
        <form method="post" autocomplete="off" id="form" name="form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="id">

                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="slideInModalLabel">Atualizando produto : xtz</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group border-lable-flt format-font">
                                <input type="text" name="subcodigo" id="subcodigo" class="form-control format-font" placeholder="Subcodigo" value="" readonly />
                                <label for="subcodigo">SUB CÓDIGO</label>
                            </div>
                            <div class="card">
                                <div class="card-header text-center">
                                   <h6 class="font-weight-bold">Quantidades</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group border-lable-flt format-font ">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <input type="number" name="quantidade" id="quantidade" class="form-control format-font" placeholder="Quantidade" value="" />
                                                <label for="quantidade">{{ __('QUANTIDADE') }}</label>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <input type="number" name="estoque" id="estoque" class="form-control format-font" placeholder="Estoque" value="" />
                                                <label for="estoque">{{ __('ESTOQUE') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header text-center">
                                    <h6 class="font-weight-bold"> Valores</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group border-lable-flt format-font ">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <input type="number" name="valor_varejo" id="valor_varejo" class="form-control format-font" placeholder="Valor Varejo" value="" />
                                                <label for="valor_varejo">{{ __('VALOR DO VAREJO') }}</label>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <input type="number" name="valor_atacado" id="valor_atacado" class="form-control format-font" placeholder="Valor Atacado" value="" />
                                                <label for="valor_atacado">{{ __('VALOR DO ATACADO') }}</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <input type="number" name="valor_produto" id="valor_produto" class="form-control format-font" placeholder="Valor Produto" value="" />
                                                <label for="valor_produto">{{ __('VALOR DO PRODUTO') }}</label>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <input type="number" name="desconto" id="desconto" class="form-control format-font" placeholder="Desconto" value="" />
                                                <label for="desconto">{{ __('VALOR DO DESCONTO') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-header text-center">
                                    <h6 class="font-weight-bold"> Situação</h6>
                                        <div class="form-check form-switch text-center">
                                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
                                            <label class="form-check-label" for="flexSwitchCheckDefault">Ativo ou Inativo</label>
                                        </div>
                                </div>
                            </div>

                            <div>
                                <form id="formImageProduct" name="formImageProduct" class="needs-validation form-floating" novalidate method="post" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="productId" id="productId">
                                    <input type="hidden" name="variacaoId" id="variacaoId">
                                    <input type="hidden" name="imageId" id="imageId">
                                    <input type="hidden" name="imagemName" id="imagemName">
                                    <input type="hidden" name="flagImage" id="flagImage">
                                    <input type="hidden" name="metodo" id="metodo" value="put">

                                    <div class="modal-body text-center">
                                        <input type="file"  id="image" name="image" title="Imagem" placeholder="Imagem" data-type="local">

                                        <!-- Pré-visualização da imagem atual -->
                                        <img id="modal-imagem" alt="Imagem Atual" class="img-thumbnail">
                                    </div>

                                </form>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="row w-100">
                                <div class="col-md-6 d-flex justify-content-start">
                                    <button type="submit" class="btn bgBtn" id="btnSalvar" name="onSubmit">
                                        <svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false"
                                             data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 448 512" data-fa-i2svg="">
                                            <path fill="currentColor" d="M438.6 105.4C451.1 117.9 451.1 138.1 438.6 150.6L182.6 406.6C170.1 419.1 149.9 419.1 137.4 406.6L9.372 278.6C-3.124 266.1-3.124 245.9 9.372 233.4C21.87 220.9 42.13 220.9 54.63 233.4L159.1 338.7L393.4 105.4C405.9 92.88 426.1 92.88 438.6 105.4H438.6z"></path>
                                        </svg>
                                        Salvar
                                    </button>
                                </div>
                                <div class="col-md-6 d-flex justify-content-end">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>

@endsection

@push("scripts")
    <script src="{{URL::asset('assets/jquery/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{URL::asset('assets/jquery/jquery.validate.min.js')}}"></script>
    <script src="{{URL::asset('assets/jquery/jquery.maskMoney.min.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/bootstrap-datepicker.pt-BR.min.js')}}" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="model" src="{{URL::asset('js/produtos.js')}}"></script>
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
