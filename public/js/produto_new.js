import {sweetAlert,formatMoney,getFormattedDate,SomenteNumeros,formatMoneyPress,formatDate,removeCampo} from './comum.js';


$(function() {
    let json,id,table;
    const errorMessages = document.getElementById('errorMessages');
    const fileInput = document.getElementById('file');

    const url = fncUrl();

    $('#codigo_produto').trigger("focus");
    $('#adicionarCampo').prop('disabled', true);
    $('#GerarCodigo').prop('disabled', false);
    $('#btnLote').prop('disabled', false);
    /**
     * DATATABLES
     * */

   // initialiseTable();
   // function initialiseTable() {
        table = $('#table').DataTable({
            ajax: {
                method: 'get',
                processing: true,
                serverSide: true,
                url: url + "/produto/create",
                cache: false,
                data: function (d) {
                    d._ = $.now(); // isso adiciona um timestamp e impede o cache
                }
            },
            "columns": [
                {
                    "className": 'details-control',
                    "orderable": false,
                    "data": null,
                    "defaultContent": ''
                },
                {"data": "id", "defaultContent": ""},
                {"data": "codigo_produto", "defaultContent": ""},
                {"data": "imagem",
                    render: function (data, type, row) {
                        if(row.produto_imagens.length > 0){
                            let path = row.produto_imagens[0].path; // Pegar o caminho da primeira imagem
                            return '<img src="../public/storage/product/' + row.id + '/' + path + '" class="image img-datatable" title="Clique para Visualizar" data-toggle="tooltip" data-placement="right" />';
                        }else{
                            return '<img src="../public/storage/produtos/not-image.png" class="image img-datatable"/>';
                        }
                    }
                },
                {"data": "descricao", "defaultContent": ""},
                {"data": "categoria", "defaultContent": ""},
                {
                    "data": "status",
                    render: function (data, type, row) {
                        return "<span class='badge bg-success'>ATIVO</span>";
                    }
                },
                {"data": "created", "defaultContent": ""},
                {"data": "updated", "defaultContent": ""},
                {
                    "data": "defaultContent",
                    render: function (data, type, row) {
                        let image = "../public/storage/produtos/not-image.png";
                        let image_id = null;
                        let path = null;
                        //if(row.imagem !== null){
                        if(row.produto_imagens.length > 0){
                                path = row.produto_imagens[0].path; // Pegar o caminho da primeira imagem
                                image = '../public/storage/product/'+row.id+'/'+ path;
                                image_id = row.produto_imagens[0].id;
                            }

                        return "<div class='text-center'>" +
                            "<span data-toggle=\"tooltip\" data-placement=\"right\"  title='Alterar Imagem do Produto'> "+
                            " <i class=\"bi-image\" " +
                            "   style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" " +
                            "   data-bs-toggle=\"modal\" " +
                            "   data-bs-target=\"#divModalImageProduct\" data-id='"+row.id+"' " +
                            "   data-image-preview='"+image+"'  data-path='"+path+"' data-flag-image='0'  " +
                            "   data-image-id='"+image_id+"'></i></span>"+
                            "<i class=\"bi-pencil-square btnUpdateProduct\" " +
                            "               style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" " +
                            "               title=\"Atualizar Produto\" data-id='"+row.id+"' "+
                            "               data-toggle=\"tooltip\" data-placement=\"right\">" +
                            "</i>" +
                            "</div>";
                    }
                }

            ],
            initComplete: function(settings, json) {
                $('[data-toggle="tooltip"]').tooltip();
            },
            scrollX: true,
            select: false,
            "columnDefs": [
                {
                    "targets": [],
                    "visible": false,
                    "searchable": false
                }
            ],
            language: {
                "url": "../public/Portuguese-Brasil.json"
            },
            "order": [[0, "desc"]]
        });
    //}

    /**
     * Add event listener for opening and closing details
     */
    $('#table tbody').on('click', 'td.details-control', function (event) {
        event.preventDefault();

        let tr = $(this).closest('tr');
        let row = table.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
           // row.child( format(row.data()) ).show();
            tr.addClass('shown');

            let tmpRow  ="<table class='table table-striped table-condensed'>" +
                            "<thead class=\"text-center\">" +
                                "<tr class='bg-secondary '>" +
                                    "<th>IMAGEM</th>" +
                                    "<th>SUB CÓDIGO</th>" +
                                    "<th>VARIAÇÃO</th>" +
                                    "<th>QTD</th>" +
                                    "<th>ESTOQUE</th>" +
                                    "<th>VAREJO</th>" +
                                    "<th>ATACADO</th>" +
                                    "<th>PRODUTO</th>" +
                                    "<th>DESCONTO EM %</th>" +
                                    "<th>STATUS</th>" +
                                    "<th>AÇÃO</th>" +
                                "</tr>" +
                            "</thead>";

                    $.ajax({
                        url: url + "/produto/getProducts/"+row.data().id,
                        type: 'GET',
                        data: '',
                        dataType: 'json',
                        beforeSend: function () {
                            row.child('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>').show();
                        },
                        success: function (response) {
                           //  console.log(response.data.products);
                            if (response.success) {
                                let arrayProducts = JSON.stringify(response.data.products);

                                JSON.parse(arrayProducts).forEach(async function (arrayItem, index, fullArray) {
                                    // console.log(arrayItem.subcodigo);
                                    let image = arrayItem.path !== null ?
                                        "<img src='../public/storage/" + arrayItem.path + "' class=\"image img-datatable\" alt=\"\" title='" + arrayItem.variacao + "'></img>" :
                                        "<img src='../public/storage/produtos/not-image.png' class=\"image img-datatable\" alt=\"\" title='" + arrayItem.variacao + "'></img>"

                                    let image_filho = "../public/storage/produtos/not-image.png";
                                    if (arrayItem.path !== null) {
                                        image_filho = '../public/storage/' + arrayItem.path;
                                    }
                                    if (arrayItem.status !== 'INATIVO'){
                                        tmpRow += "<tr>" +
                                            "<td>" + image + "</td>" +
                                            "<td>" + arrayItem.subcodigo + "</td>" +
                                            "<td>" + arrayItem.variacao + "</td>" +
                                            "<td>" + arrayItem.quantidade + "</td>" +
                                            "<td>" + arrayItem.estoque + "</td>" +
                                            "<td>" + formatMoney(arrayItem.valor_varejo) + "</td>" +
                                            "<td>" + formatMoney(arrayItem.valor_atacado_10un) + "</td>" +
                                            "<td>" + formatMoney(arrayItem.valor_produto) + "</td>" +
                                            "<td>" + arrayItem.percentage + "% </td>" +
                                            "<td>" + "<span class='badge bg-success'>" + arrayItem.status + "</span>" + "</td>" +
                                            "<td><i class=\"bi-image\" " +
                                            "   style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" " +
                                            "   title='Imagem da Variação do Produto' data-bs-toggle=\"modal\" " +
                                            "   data-bs-target=\"#divModalImageProduct\" data-variacao-id='" + arrayItem.id + "' " +
                                            "   data-subcodigo='" + arrayItem.subcodigo + "' data-image-id='" + arrayItem.id_image + "'" +
                                            "   data-image-preview='" + image_filho + "'  data-path='" + arrayItem.path + "' data-flag-image='1'>" +
                                            "</td>" +
                                            "</tr>"
                                    }
                                });

                                tmpRow  +=      "</table>";
                                row.child(tmpRow).show();
                            }
                        },
                        error: function (response) {
                            json = $.parseJSON(response.responseText);
                            $("#modal-title").addClass("alert alert-danger");
                            $('#modal-title').html('<p><i class="fas fa-exclamation-circle"></i>&nbsp;<strong>' + json.message + '</strong></p>');
                            Swal.fire({
                                title: 'error!',
                                text: json.message,
                                icon: 'error'
                            }).then((result) => {
                                /* Read more about isConfirmed, isDenied below */
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        }
                    });

        }
    } );

    /**
     * GERAR CÓDIGO PRODUTO
     * */
    //$('button[name="GerarCodigo"]').on('click',function(event) {
    $(".gerarCodigo").on('click',function (event) {
        event.preventDefault();
        code();
    });

    function code(){
        $.ajax({
            url: url + '/produto/show',
            type:'get',
            cache: false,
            dataType:'json',
            success: function(response){
                if(response.success === true){
                    $('#codigo_produto').val(response.data);
                    $('#codigo_produto').trigger("focus");

                    $('#subcodigo0').val(response.id);
                    $('#adicionarCampo').prop('disabled', false);
                }else{
                    alert("Error" + response.message);
                }
            }
        });
    }
    /**  Fim GerarCodigo */

    /***
     * Salva imagem variação ou produto
     * */
/*
    $("#formImage").on('submit',function (event) {
        event.preventDefault();

    }).validate({
        errorClass: "my-error-class",
        validClass: "my-valid-class",
        rules: {
            arquivo: {
                required: true
            }
        },
        messages: {
            arquivo: {
                required: "Informe a imagem do Produto?"
            }
        }, submitHandler:  function(form,event) {
            event.preventDefault();

                let formData = new FormData($(form)[0]);

                $.ajax({
                    url: url + "/image",
                    type: 'POST',
                    data: formData,
                    async: false,
                    cache: false,
                    contentType: false,
                    enctype: 'multipart/form-data',
                    processData: false,
                    dataType: 'json',
                    beforeSend:  function () {
                        //document.getElementById("load").style.display = "block";
                        //$("#load").addClass("alert alert-info");
                        //$('#load').html('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>');
                    },
                    success:  function (response) {

                        if (response.success) {
                            Swal.fire({
                                title: "Sucesso!",
                                text: response.message,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            //table.ajax.reload(null, false);
                            //table.destroy();
                           // getdata();
                        }
                    },
                    error: function (response) {
                        json = $.parseJSON(response.responseText);
                        $("#modal-title").addClass("alert alert-danger");
                        $('#modal-title').html('<p><i class="fas fa-exclamation-circle"></i>&nbsp;<strong>' + json.message + '</strong></p>');
                        Swal.fire(
                            'error!',
                            json.message,
                            'error'
                        )
                    },
                    complete:function(response){
                        json = $.parseJSON(response.responseText);
                        if(json.success) {
                            window.setTimeout(function () {
                                $('#divModalImage').modal('hide');
                            }, 1500);
                        }
                    }
                });
            }
    });
*/
    /***
     * Salva o produto
     * **/

    $( "#form" ).on( "submit", function( event ) {
        event.preventDefault();
    }).validate({
        errorClass: "my-error-class",
        validClass: "my-valid-class",
        rules: {
            codigo_produto: {
                required: true
            },
            descricao: {
                required: true
            },
            fornecedor: {
                required: false
            },
            categoria: {
                required: true
            },
            subcodigo: {
                required: true
            },
            variacao0: {
                required: true
            },
            valor_varejo0: {
                required: true
            },
            valor_atacado0: {
                required: true
            },
            valor_produto0: {
                required: true
            },
            quantidade: {
                required: true
            },
            validade: {
                required: true
            },
            origem: {
                required: true
            },
            ncm: {
                required: true
            },
            cest: {
                required: true
            },
            gtin0: {
                required: true
            },
            percentage: {
                required: true
            }
        },
        messages: {
            codigo_produto: {
                required: "Informe o código do Produto?"
            },
            descricao: {
                required: "Informe a Descrição do Produto?"
            },
            fornecedor: {
                required: "Informe o Fornecedor?"
            },
            categoria: {
                required: "Informe a Categoria?"
            },
            subcodigo: {
                required: "Informe o subCodigo?"
            },
            'variacao[]': {
                required: "Informe a variação?"
            },
            valor_varejo0: {
                required: "Informe o valor do Varejo?"
            },
            valor_atacado0: {
                required: "Informe o valor do Atacado?"
            },
            valor_produto0: {
                required: "Informe o valor Pago?"
            },
            quantidade: {
                required: "Informe a quantidade do produto?"
            },
            validade: {
                required: "Informe a data de validade?"
            },
            ncm: {
                required: "Informe o número NCM do produto?"
            },
            cest: {
                required: "Informe o número CEST do produto?"
            },
            'gtin[]': {
                required: "Informe o Gtin?"
            },
            percentage: {
                required: "Informe o Valor Percentual de Desconto?"
            }
        }, submitHandler: function(form,event) {
            event.preventDefault();
                let formData = "";
                let update = ($("#produto_id").val() === "") ? false : true;

                if(update){
                    let selectElements  = document.querySelectorAll('select[name="status_variacao[]"]');

                    let valorSelecionado ="";

                    selectElements.forEach(function(select) {
                         valorSelecionado = select.value;
                    });
                   // console.log(selectElements.length + " - " + valorSelecionado);

                    if(selectElements.length === 1 && valorSelecionado === "0"){
                        Swal.fire({
                            title: 'Atenção!',
                            icon: 'question',
                            text: 'Ao desativar essa última variação, o produto também será desativado!',
                            showDenyButton: false,
                            showCancelButton: true,
                            confirmButtonText: 'Salvar!',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                /***
                                 * seta o status do produto para INATIVO
                                 * */
                                $("#status").val(0);

                                formData = new FormData($(form)[0]);
                                fnc_enviaForm(formData);
                            }
                        })
                    }else{
                        formData = new FormData($(form)[0]);
                        fnc_enviaForm(formData);
                    }
                }else{
                    formData = new FormData($(form)[0]);
                    fnc_enviaForm(formData);
                }
        }
    });


    /***
     * Editar o produto
     * **/
    $("#table").on("click",".btnUpdateProduct" , async function(event){
        event.preventDefault();
        //Linha da datatable clicada + coluna escondida
        //let line = $('#table').DataTable().row($(this).closest("tr")).data();
        //id =  parseInt(line['id']);
        id = $(this).data('id');

        await fetch(url + "/produto/getProducts/"+id).then( function (response) {
            return response.json()
        }).then( function (response) {
            //console.log(JSON.stringify(response.data));

            $('#produto_id').val(response.data.id);
            $('#codigo_produto').val(response.data.codigo_produto);
            $('#descricao').val(response.data.descricao);
            $('#status').val(response.data.status);
            $('#categoria_id').val(response.data.categoria_id);
            $('#ncm').val(response.data.ncm);
            $('#cest').val(response.data.cest);
            $('#origem').val(response.data.origem_id);
            $('#percentage').val(response.data.percentage);

            $('#codigo_produto').prop('readonly', true);
            $('#GerarCodigo').prop('disabled', true);
            $('#btnLote').prop('disabled', true);

            $('#btnSalvar').html("<i class=\"fas fa-refresh\"></i> Atualizar");
            let arrayProducts = JSON.stringify(response.data.products);
            //console.log(arrayProducts);

            //$("#tblVariacao").html("");
            $("#tbl").html("");
            JSON.parse(arrayProducts).forEach(function (arrayItem, index, fullArray) {
                //console.log(index);

                //$("#tblVariacao").append(camposVariacao(arrayProducts));
                let selected = arrayItem.status === 'ATIVO' ? '' : 'selected';

                //$("#tblVariacao").append(camposformVariacao(index,arrayItem,selected));
                $("#tbl").append(fnc_variacao(index,'',index,arrayItem,selected));

            });
        });
    });

    /***
     *
     * */
/*
    $(document).on("click",".btnProductImage" ,function(event){
        event.preventDefault();
        id = $(this).data('id') != null ? $(this).data('id') : 0; //capturo o ID
        $("#product_id").val(id);
    });*/
        /**
     * Exibe as imagens das variações dos produtos
     * **/
    /*$(document).on("click",".btnImageProduct" ,function(event){
        event.preventDefault();

        //console.log($(this).data('variacao-id'));
        id = $(this).data('variacao-id') != null ? $(this).data('variacao-id') : 0; //capturo o ID
       // $("#products_variation_id").val($(this).data('subcodigo'));
        $("#products_variation_id").val(id);

        $.ajax({
            url: url + "/produto/pictures/"+id,
            type:'get',
            cache: false,
            dataType:'json',
            beforeSend: function () {
                $("#modal-title").removeClass( "alert alert-danger" );
                $('#modal-title').html('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>');
                $("#modal-title").addClass( "alert alert-info" );
            },
            success: function(response) {
               // console.log(response);
                grid = "";
                if(response.data.length > 0){
                    $.each(response.data, function (idx, value) {
                        grid += "<div class=\"col\">";
                        grid += "<img src='../public/storage/" + value.path + "' width='180px' height='180px' alt=\"\"></img>";
                        grid += "<i class=\"bi-trash btnRemoveImage\"  data-id='"+value.id+"' style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" title='Remover Imagem'></i>";
                        grid += "</div>";
                    });
                }else{
                    grid = "<img src='../public/storage/produtos/not-image.png' width='180px' height='180px' alt=\"\"></img>";
                }
                $("#pictures").html(grid);
            },
            error:function(response){
                json = $.parseJSON(response.responseText);
                $("#modal-title").addClass( "alert alert-danger" );
                $('#modal-title').html('<p><i class="fas fa-exclamation-circle"></i>&nbsp;<strong>'+json.message+'</strong></p>');
                Swal.fire(
                    'error!',
                    json.message,
                    'error'
                )

            },complete: function(response){

            }
        });
    });/

    /**
     * Deleta a imagem do produto
     * */
   // $('i[name="btnRemoveImage"]').on('click',function(event) {
   /* $(document).on("click",".btnRemoveImage" , function(event){
        event.preventDefault();

        Swal.fire({
            title: 'Tem certeza?',
            text: "Está seguro de remover esta imagem ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, deletar!'
        }).then((result) => {
            if (result.isConfirmed) {

            id = $(this).data('id');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
                $.ajax({
                    url: url + '/image/destroy',
                    type: 'POST',
                    data: {
                        id: id,
                        _method: 'DELETE'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function (response) {
                        //console.log(response.message);
                        //table.ajax.reload();
                        //$("#alert-success").html(response.message).fadeIn('slow').fadeOut(3000);
                        Swal.fire({
                            title: "Sucesso!",
                            text: response.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        //table.ajax.reload(null, false);
                        table.destroy();
                        getdata();
                    },
                    error: function (response) {
                        json = $.parseJSON(response.responseText);
                        Swal.fire(
                            'error!',
                            json.message,
                            'error'
                        )
                    },
                    complete:function(response){
                        json = $.parseJSON(response.responseText);
                        if(json.success) {
                            window.setTimeout(function () {
                                $('#divModalImage').modal('hide');
                            }, 1500);
                        }
                    }
                });
            }
        });
    });*/

    /*** Fim */

    /**
     * Upload em Lote
     * */
    fileInput.onchange = () => {
        const selectedFile = fileInput.files[0];

        document.getElementById('arquivo').value = selectedFile.name;
    }

    /**
     * Ao clicar em Adiconar Variação, faz append dos campos na div
     * */
    $(".adicionar").on('click',function (event) {
        event.preventDefault();

        let i = $("#tbl .row").length; //qtd de divs,linhas

        // Seleciona todos os inputs com o nome 'subcodigo[]'
        let inputs = document.querySelectorAll('input[name="subcodigo[]"]');
        if (inputs.length > 0) {
            // Pega o último input
            let lastInput = inputs[inputs.length - 1];

            // Pega o valor do último input
            let lastInputValue = lastInput.value;

            // Mostra o valor no console (ou faça o que você precisar com ele)
            console.log('Valor do último input:', lastInputValue);


            let val = Number(typeof $('#subcodigo0').val() !== "undefined" ? lastInputValue : 0) + 1;

            val = val >= 10 ? val : "0" + val;
            fnc_variacao(i, val, null, null, '');
        }
    });

    /**
     * Busca os Fornecedores no localStorage
     * */
    let fnc_fornecedor = async function(name,value){
       // console.log("aquii >>>> " + localStorage.getItem("data-suppliers"));
        //pega do local, após qualquer mudança no fornecedor em fornecedor.js
        if(localStorage.getItem("data-suppliers") !== null){
            let myArray = JSON.parse(localStorage.getItem("data-suppliers"));
           // console.log("localStorage");
            await Promise.all(myArray).then(valores=> {
                $(name).append('<option value="">SELECIONE?</option>');
               // console.log(valores); // [3, 1337, "foo"]
               // console.log("aqui1 >> " + value);
                valores.forEach(function (ret) {
                    let sel = ret.id === value ? 'selected' : '';
                 //   console.log(ret.id + " - " + value);
                    $(name).append("<option value="+ret.id+" "+sel+">"+ret.nome+"</option>");
                });
            });

        }
        else {
              fetch(url + "/fornecedor/1")
                .then(function (response) {
                    return response.json()
                })
                .then(function (response) {
                 //    console.log("aqui2 >> " + value);
                   // console.log("fetch api");
                    /**
                     * set local os dados do fornecedor para não ficar indo na api
                     */
                    localStorage.setItem("data-suppliers", JSON.stringify(response));
                    $(name).append('<option value="">SELECIONE?</option>');
                    response.forEach(function (ret) {
                        //       console.log(ret.id +" - "+ value);
                        let sel = ret.id === value ? 'selected' : '';

                        $(name).append("<option value=" + ret.id + " " + sel + ">" + ret.nome + "</option>");
                    });
                });
        }
    }
    /**
     * Retorna os campos de variações do produto
     * */

    let fnc_variacao = function (i,val,index,arrayItem,selected) {
        let display = '';
        let icon_remove = "";
        let id = arrayItem != null ? arrayItem.id : '';
        let subcodigo = arrayItem != null ? arrayItem.subcodigo.substring(arrayItem.subcodigo.length-2,arrayItem.subcodigo.length) : val;
        let variacao = arrayItem != null ? arrayItem.variacao : '';
        let valor_varejo = arrayItem != null ? formatMoney(arrayItem.valor_varejo) : typeof $("#valor_varejo0").val() !== "undefined" ? $("#valor_varejo0").val() : '';
        let valor_atacado = arrayItem != null ? formatMoney(arrayItem.valor_atacado_10un) : typeof $("#valor_atacado_10un0").val() !== "undefined" ? $("#valor_atacado_10un0").val() : '';
        let valor_atacado_10un = arrayItem != null ? formatMoney(arrayItem.valor_atacado_10un) : typeof $("#valor_atacado_10un0").val() !== "undefined" ? $("#valor_atacado_10un0").val() : '';
        let valor_produto = arrayItem != null ? formatMoney(arrayItem.valor_produto) : typeof $("#valor_produto0").val() !== "undefined" ? $("#valor_produto0").val() : '';
        let quantidade = arrayItem != null ? arrayItem.quantidade : '';
        let estoque = arrayItem != null ? arrayItem.estoque : 0;
        let quantidade_minima = arrayItem != null ? arrayItem.quantidade_minima : 2;
        let validade = arrayItem != null ? getFormattedDate(arrayItem.validade) : '00/00/0000';
        let fornecedor_id = arrayItem != null ? arrayItem.fornecedor : 0;
        let percentage = arrayItem != null ? formatMoney(arrayItem.percentage,'') : typeof $("#percetage0").val() !== "undefined" ? $("#percetage0").val() : '0,00';
        let gtin = arrayItem && arrayItem.gtin !== null ? arrayItem.gtin : 0;

        /**
         * Adiciona o icone de remover do segundo em diante
         * */
        if(i > 0){
            icon_remove =  "<div class=\"col-md-1\" style='padding:unset;left: -6px;width: 10px' >"+
                "<a href=\"javascript:void(0)\" onclick=\"removeCampo('div_pai" + i + "')\" " +
                "title=\"Remover linha\"><img src=\"../public/img/minus.png\" border=\"0\"></img>" +
                "</a>"+
                "</div>" ;
        }

        if(arrayItem !== null ){
            display = arrayItem.status === 'INATIVO' ? 'padding:0px;display:none' : 'padding: 3px;';
        }

        $("#tbl").append("<div class=\"row mt-1\" style=\" "+display+"\" id=\"div_pai"+i+"\">" +
                                "<input type=\"hidden\" name=\"variacao_id[]\" id=\"variacao_id"+i+"\"" +
                                " class=\"form-control\" value=\'"+id+"\'/>"+
                                "<div class=\"px-80\">" +
                                    "<span class=\"border-lable-flt\">"+
                                        "<input type=\"text\" name=\"subcodigo[]\" id=\"subcodigo"+i+"\" " +
                                            "class=\"form-control format-font\" placeholder=\"Subcodigo\" " +
                                            "style='width: 63px' value=\'"+subcodigo+"\' readonly/>" +
                                        "<label for=\"label-subcodigo\">SUBCOD</label>"+
                                    "</span>"+
                                "</div>"+
                                "<div class=\"col-md-2\" style='left: -2px;width: 200px'>" +
                                    "<span class=\"border-lable-flt\">"+
                                        "<input type=\"number\" name=\"gtin[]\"  maxlength='15' id=\"gtin"+i+"\" " +
                                        "class=\"form-control format-font\" placeholder=\"GTIN\" maxlength='15' " +
                                        "value=\'" + gtin + "\' required/>" +
                                        "<label for=\"label-gtin\">GTIN</label>"+
                                    "</span>"+
                                "</div>"+
                                "<div class=\"col-md-2\" style='left: -12px;width: 250px'>" +
                                    "<span class=\"border-lable-flt\">"+
                                        "<input type=\"text\" name=\"variacao[]\"  maxlength='150' id=\"variacao"+i+"\" " +
                                            "class=\"form-control format-font\" placeholder=\"VARIAÇÃO\" maxlength='9' " +
                                            "value=\'" + variacao + "\' required/>" +
                                        "<label for=\"label-variacao\">VARIAÇÃO</label>"+
                                    "</span>"+
                                "</div>"+
                                "<div class=\"col-md-2\" style='left: -32px;width: 140px'>"+
                                    "<span class=\"border-lable-flt\" >"+
                                        "<input type=\"text\" name=\"valor_varejo[]\"  id=\"valor_varejo"+i+"\" "+
                                            "class=\"form-control format-font\" placeholder=\"VAREJO\" maxlength='9' "+
                                            "onkeyup=\"formatMoneyPress(this)\" value=\'" + valor_varejo + "\' required/>"+
                                            "<label for=\"label-varejo\">VAREJO</label>"+
                                    "</span>"+
                                 "</div>"+

                                "<div class=\"col-md-2\" style='padding:unset;left: -32px;width: 120px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                    "<input type=\"text\" name=\"valor_atacado_10un[]\"  id=\"valor_atacado_10un"+i+"\""+
                                    "class=\"form-control format-font\" placeholder=\"ATACADO\"  maxlength='9'"+
                                    "onkeyup=\"formatMoneyPress(this)\" value=\'" + valor_atacado_10un + "\'  required/>"+
                                    "   <label for=\"label-atacado10un\">ATACADO</label>"+
                                    "</span>"+
                                "</div>" +

                                "<div  class=\"col-md-2\" style='padding:unset;left: -24px;width: 120px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                        " <input type=\"text\" name=\"valor_produto[]\"  id=\"valor_produto"+i+"\" "+
                                        " class=\"form-control\" placeholder=\"VALOR PAGO\"  maxlength='9'"+
                                        " onkeyup=\"formatMoneyPress(this)\" value=\'" + valor_produto + "\'  required/>"+
                                        " <label for=\"label-produto\">VALOR PAGO</label>"+
                                    "</span>"+
                                "</div>" +
                                "<div class=\"col-md-2\" style='padding:unset;left: -20px;width: 70px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                        "<input type=\"text\" name=\"quantidade[]\"  maxlength='3' id=\"quantidade"+i+"\""+
                                        "class=\"form-control\" placeholder=\"QTD\" onkeyup=\"SomenteNumeros(this)\" " +
                                        "value=\'" + quantidade + "\' required/>"+
                                        "<label for=\"label-qtd\">QTD</label>"+
                                    "</span>"+
                                "</div>" +
                                "<div class=\"col-md-2\" style='padding:unset;left: -16px;width: 70px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                        "<input type=\"text\" name=\"quantidade_minima[]\"   maxlength='3' id=\"quantidade_minima"+i+"\""+
                                        "class=\"form-control\" placeholder=\"QTD.MIN\" onkeyup=\"SomenteNumeros(this)\" " +
                                        "value=\'" + quantidade_minima + "\'  required/>"+
                                        "<label for=\"label-qtd\">QTD.MIN</label>"+
                                    "</span>"+
                                "</div>" +
                                "<div class=\"col-md-2\" style='padding:unset;left: -12px;width: 70px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                        "<input type=\"text\" name=\"estoque[]\"  maxlength='3' id=\"estoque"+i+"\""+
                                        "class=\"form-control\" placeholder=\"ESTOQUE\" onkeyup=\"SomenteNumeros(this)\" " +
                                        "value=\'" + estoque + "\' required/>"+
                                        "<label for=\"label-estoque\">ESTOQUE</label>"+
                                    "</span>"+
                                "</div>" +
                                "<div class=\"col-md-2\" style='padding:unset;left: -8px;width: 80px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                        " <input type=\"text\" name=\"percentage[]\"  maxlength='5' id=\"percentage"+i+"\""+
                                        " class=\"form-control\" placeholder=\"DESC.EM %\" data-tooltip=\"toggle\" title=\"Desconto em %\"" +
                                        " onkeyup=\"formatMoneyPress(this);\" value=\'" + percentage + "\' required/>"+
                                        " <label for=\"label-estoque\">DESC.EM %</label>"+
                                    "</span>"+
                                "</div>" +
                                "<div class=\"col-md-2 date\" style='padding:unset;left: -6px;width: 122px' id=\"data_validade"+i+"\">"+
                                    "<span class=\"border-lable-flt\">"+
                                        "<input type=\"text\" name=\"validade[]\"  id=\"validade"+i+"\""+
                                        "class=\"form-control\" placeholder=\"QTD.MIN\"  " +
                                        "onKeyUp=\"formatDate(this)\" maxlength=\"10\" value=\'" + validade + "\' />"+
                                        "<label for=\"label-qtd\">VALIDADE</label>"+
                                    "</span>"+
                                "</div>" +
                                "<div class=\"col-md-2\" style='padding:unset;left: -2px;width: 78px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                        "<SELECT type=\"text\" name=\"status_variacao[]\"  id=\"status_variacao"+i+"\""+
                                            "class=\"form-control status_variacao\" placeholder=\"STATUS\" required/>"+
                                            "<option value=\"1\" "+selected+">ATIVO</option>"+
                                            "<option value=\"0\" "+selected+">INATIVO</option>"+
                                            "</select>"+
                                        "<label for=\"label-qtd\">STATUS</label>"+
                                    "</span>"+
                                "</div>" +
                                "<div class=\"col-md-2\" style='padding:unset;left: 2px;width: 150px' >"+
                                    "<span class=\"border-lable-flt\">"+
                                        "<SELECT type=\"text\" name=\"fornecedor[]\"  id=\"fornecedor"+i+"\""+
                                            "class=\"form-control\" placeholder=\"FORNECEDOR\"  required/>"+
                                            ""+fnc_fornecedor('#fornecedor'+i,fornecedor_id)+""+
                                        "</select>"+
                                        "<label for=\"label-qtd\">FORNECEDOR</label>"+
                                    "</span>"+
                                "</div>" +
                               ""+icon_remove+""+
                        "</div>");

    }



    /**
     * Salva as informações do produdo
     * */
    let fnc_enviaForm = function (formData) {
       const errors = [];
       const inputs_variacao = document.querySelectorAll('input[name="variacao[]"]');
       const inputs_qtd = document.querySelectorAll('input[name="quantidade[]"]');
       const selects = document.querySelectorAll('select[name="fornecedor[]"]');
        const inputs_gtin = document.querySelectorAll('input[name="gtin[]"]');
       let error = false;

        inputs_gtin.forEach(input => {
            if (input.value.trim() === '') {
                //errors.push(`Campo ${input.name} deve ser preencido!`);
                error = true;
                input.classList.add('invalid-input');
            } else {
                input.classList.remove('invalid-input');
            }
        });

        inputs_variacao.forEach(input => {
            if (input.value.trim() === '') {
                //errors.push(`Campo ${input.name} deve ser preencido!`);
                error = true;
                input.classList.add('invalid-input');
            } else {
                input.classList.remove('invalid-input');
            }
        });

        inputs_qtd.forEach(input => {
            if (input.value.trim() === '') {
                //errors.push(`Campo ${input.name} deve ser preencido! `);
                error = true;
                input.classList.add('invalid-input');
            } else {
                input.classList.remove('invalid-input');
            }
        });

        selects.forEach(select  => {
            if (select.value === '') {
                //errors.push(`Campo ${input.name} deve ser preencido! `);
                error = true;
                select.classList.add('invalid-input');
            } else {
                select.classList.remove('invalid-input');
            }
        });

        //if (errors.length > 0) {
        if(error){
            errors.push(`Campos em destaque devem ser preencido(s)! `);
            displayErrors(errors);
            return;
        }

        $.ajax({
            url: url + "/produto",
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            enctype: 'multipart/form-data',
            processData: false,
            dataType:'json',
           /*beforeSend: function () {
                $("#modal-title").removeClass( "alert alert-danger" );
                $('#modal-title').html('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>');
                $("#modal-title").addClass( "alert alert-info" );
            },*/
            success: function (response) {
                 console.log(response);
                if(response.success) {
                    sweetAlert({
                        title: "Sucesso!",
                        text: response.message,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    table.ajax.reload(null, false);
                    //table.destroy();
                    //initialiseTable();

                    $("#produto_id").val('');
                    $("#tbl").html('');
                    $("#tbl").append(fnc_variacao(0,null,1,null,1));
                    $("#descricao").val('');
                    $("#fornecedor_id").val('');
                    $("#categoria_id").val('');
                    $("#origem").val('');
                    $("#ncm").val('');
                    $("#cest").val('');
                    $('#GerarCodigo').prop('disabled', false);
                    $('#btnLote').prop('disabled', false);
                    $('#btnSalvar').html("<i class=\"fas fa-check\"></i> Salvar");
                    code();
                }else{
                    sweetAlert({
                        title: "Error!",
                        text: response.message,
                        icon: 'danger',
                        showCloseButton: true,
                    });
                }
            },
            error: function(xhr){
                let json = {};
                try {
                    json = xhr.responseJSON ?? JSON.parse(xhr.responseText);
                } catch (e) {
                    console.error("Erro ao processar JSON de erro:", e);
                    return;
                }

                console.error("Erro na requisição:", json);

                $('#modal-title').html('<p><i class="fas fa-exclamation-circle"></i>&nbsp;<strong>' + (json.message || 'Erro desconhecido') + '</strong></p>');

                Swal.fire({
                    title: 'Erro!',
                    text: json.message || 'Ocorreu um erro inesperado.',
                    icon: 'error'
                });
            },
        });
    }
    /**************************************
     ******* FUNÇÕES ONLAOD SISTEMA *******
     **************************************
     * */
     //getdata();
     code();
     //fnc_preview();
     $('#data_validade input').datepicker({
         'language' : 'pt-BR',
         'todayBtn': true,
         'todayHighlight':true,
         'weekStart':0,
         'orientation':'bottom',
         'autoclose':true
     });

    /**
     * Exibe os primeiros campos dos inputs da variação ao carregar a página.
     * */
     fnc_variacao(0,1,null,null, '');

});

/**
 *  Formatting function for row details - modify as you need
 */
    function format ( d ) {

        return '<table class="table table-striped table-condensed">'+
            '<tr>'+
            '<td><strong>Categoria:</strong></td>'+
            '<td>'+d.categoria+'</td>'+
            '<td><strong>Fornecedor:</strong></td>'+
            '<td>'+d.fornecedor+'</td>'+
            '</tr>'+
            '<tr>'+
            '<td><strong>Status:</strong></td>'+
            '<td>'+d.status_produto+'</td>'+
            '<td><strong>Quantidade Minima:</strong></td>'+
            '<td>'+d.quantidade_minima+'</td>'+
            '</tr>'+
            '<tr>'+
            '<td><strong>Data Criação:</strong></td>'+
            '<td>'+d.created+'</td>'+
            '<td><strong>Data Atualização:</strong></td>'+
            '<td>'+d.updated+'</td>'+
            '</tr>'+
            '<tr>'+
            '<td><strong>Estoque:</strong></td>'+
            '<td>'+d.estoque+'</td>'+
            '</tr>'+
            '</table>';
    }

    function displayErrors(errors) {
        sweetAlert({
            title: "Error!",
            text: errors,
            icon: 'error',
            showConfirmButton: true
        });
    }

    /***
     * Ao mudar o status do produto PAI para inativo, informar que os filhos serão desabilitados.
     * */
    document.addEventListener("DOMContentLoaded", function () {
        const statusSelect = document.getElementById("status");
        const produtoIdField = document.getElementById("produto_id");

        statusSelect.addEventListener("change", function () {
            const selectedValue = this.value;
            const isProdutoExistente = produtoIdField.value.trim() !== "";

            if (selectedValue === "0" && isProdutoExistente) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção!',
                    text: 'Ao inativar o produto PAI, todos os produtos filhos também serão inativados automaticamente.',
                    confirmButtonText: 'Entendi',
                    confirmButtonColor: '#d33'
                });
            }
        });
    });

