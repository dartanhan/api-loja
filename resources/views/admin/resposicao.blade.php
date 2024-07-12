@extends('layouts.layout')

@section('menu')

    @include('admin.menu')

@endsection

@section('content')

    <div class="container-fluid mt-4">
        <div class="card" style="padding:unset">
            <div class="d-flex flex-wrap d-none">
                <div class="card bg-primary text-white card-custom-width h-25">
                    <div class="d-flex  align-items-center" id="totalDinner" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer">
                        <a class="small text-white stretched-link detailDinner" href="#" 
                        data-toggle="modal" data-target="#divModalDinner" data-content="2">Detalhes</a>
                    </div>
                </div>
                <div class="card bg-warning text-white card-custom-width h-25">
                    <div class="d-flex align-items-center" id="totalCartao" name="load">
                            <div class="card-body" name="card-body"></div>
                            <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer">
                        <a class="small text-white stretched-link detailCart" href="#" 
                        data-toggle="modal" data-target="#divModalCart" data-content="2">Detalhes</a>
                    </div>
                </div>
                <div class="card bg-danger text-white card-custom-width h-25">
                    <div class="d-flex align-items-center" id="totalDesconto" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer">
                        <a class="small text-white stretched-link" href="#">Detalhes</a>
                    </div>
                </div>
                <div class="card bg-info text-white card-custom-width h-25">
                    <div class="d-flex align-items-center" id="totalDia" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="#">Detalhes</a>
                    </div>
                </div>
                <div class="card bg-secondary text-white card-custom-width h-25">
                    <div class="d-flex align-items-center" id="totalSemana" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="#">Detalhes</a>
                    </div>
                </div>
                <div class="card bg-dark text-white card-custom-width h-25">
                    <div class="d-flex align-items-center" id="totalMes" name="load">
                        <div class="card-body" name="card-body"></div>
                        <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="#">Detalhes</a>
                    </div>
                </div>
            </div>
            <div>
                
                    
                    <div class="card-body">

                        <div class="card mb-4">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-auto input-group-sm">
                                        <h6><i class="fas fa-table me-1"></i>Resposição de Produtos&nbsp; <span id="data-periodo" class="text-primary"></span></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mt-03">
                                    <div class="card-header ">
                                        <form method="post" autocomplete="off" id="formFiltro" name="formFiltro" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="id" id="id">
                                            <div class="row date" id="data">
                                                <div class="col-auto input-group-sm">
                                                    <i class="fas fa-chart-area me-1"></i>
                                                    Filtro:
                                                </div>
                                                <div class="col-auto input-group-sm" id="dataini" data-date="{{date("d/m/Y")}}" data-date-format="dd/mm/yyyy">
                                                    <label for="dataini" class="visually-hidden">Data Inicial</label>
                                                    <input type="text" class="form-control input-group-sm" placeholder="Data Inicial" aria-label="Data Inicial" name="dataini" id="dataini">
                                                    <span class="add-on"><i class="icon-th"></i></span>
                                                </div>
                                                <div class="col-auto input-group-sm" id="datafim" data-date="{{date("d/m/Y")}}" data-date-format="dd/mm/yyyy">
                                                    <label for="datafim" class="visually-hidden">Data Final</label>
                                                    <input type="text" class="form-control input-group-sm" placeholder="Data Final" aria-label="Data Final" name="datafim" id="datafim">
                                                    <span class="add-on"><i class="icon-th"></i></span>
                                                </div>

                                                <div class="col-auto input-group-sm">
                                                    <button class="btn bgBtn btn-enviar" type="button">Filtrar</button>
                                                    <button class="btn bgBtn btn-limpar" type="button">Limpar</button>
                                                    <span id="loadChartBar"></span>
                                                </div>
                                            </div>
                                        </form>
                                   </div>
                                </div>
                                <div class="mt-03"><br/>
                                  
                                    <table id="table" class="table table-striped table-condensed table-hover  responsive" style="width:100%">
                                        <thead class="text-center">
                                            <tr>
                                                <th data-sortable="false">Imagem</th>
                                                <th data-sortable="false">Codigo</th>
                                                <th data-sortable="false">Descrição</th>
                                                <th data-sortable="false">Valor Produto</th>
                                                <th data-sortable="false">Quantidade</th>
                                                <th data-sortable="false">Valor Total</th>
                                                <th data-sortable="false">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
              
            </div>
        </div>
    </div>
    
    <!-- Modal Image -->
       <div class="modal fade" id="divModalImageProduct" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content ">
                <form id="formImageProduct" name="formImageProduct" class="needs-validation form-floating" novalidate method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="productId" id="productId">
                    <input type="hidden" name="variacaoId" id="variacaoId">
                    <input type="hidden" name="metodo" id="metodo" value="put">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="exampleModalLongTitle">Imagem do Produto </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <input type="file"  id="image" name="image" title="Imagem" placeholder="Imagem" >

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
    
@endsection
@push("scripts")
    <script src="{{URL::asset('assets/jquery/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/dataTables.bootstrap4.min.js')}}"></script>    
    <script src="{{URL::asset('assets/jquery/jquery.validate.min.js')}}"></script>
    <script type="module" src="{{URL::asset('js/resposicao.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/bootstrap-datepicker.pt-BR.min.js')}}" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{URL::asset('js/filePond.js')}}"></script>
    <script src="{{URL::asset('js/moment.min.js')}}"></script>
 
@endpush
@push("styles")
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/bootstrap/css/bootstrap-datepicker.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/datatables/dataTableRender.css')}}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.2/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/sweetalert2/animate.min.css')}}"/>
@endpush
