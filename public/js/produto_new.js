$(function() {
    let json,id,grid,table,asyncData;

    const fileInput = document.getElementById('file');

    const url = fncUrl();

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    });

    $('#codigo_produto').trigger("focus");
    $('#adicionarCampo').prop('disabled', true);
    $('#GerarCodigo').prop('disabled', false);
    $('#btnLote').prop('disabled', false);
    /**
     * DATATABLES
     * */
    function getdata(){
        const getDados = async () => {
            const data = await fetch(url + "/produto/create", {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            asyncData = await data.json();
            initialiseTable();
            return asyncData;
        };
        getDados();
    }
    function initialiseTable() {
        table = $('#table').DataTable({
            data:asyncData,
            responsive: true,
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
                        if(row.imagem !== null){
                            return '<img src="../public/storage/product/'+row.id+'/'+ row.imagem + '" class="image img-datatable">';
                        }else{
                            return '<img src="../public/storage/produtos/not-image.png" class="img-datatable">';
                        }
                    }
                },
                {"data": "descricao", "defaultContent": ""},
                {"data": "categoria", "defaultContent": ""},
                {
                    "data": "status",
                    render: function (data, type, row) {
                        return "<span class=\"badge bg-success\">" + row.status + "</span>";
                    }
                },
                {"data": "created", "defaultContent": ""},
                {"data": "updated", "defaultContent": ""},
                {
                    "data": "defaultContent",
                    render: function (data, type, row) {
                        return "<div class='text-center'>" +
                            "<i class=\"bi-image btnProductImage\" " +
                            "               style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" " +
                            "               title='Imagem do Produto' data-bs-toggle=\"modal\" " +
                            "               data-bs-target=\"#divModalImageProduct\" data-id='"+row.id+"'></i>"+
                            "<i class=\"bi-pencil-square btnUpdateProduct\" " +
                            "               style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" " +
                            "               title='Atualizar Produto' data-id=\"" + row.id + "\">" +
                            "</i>&nbsp;" +
                            "</div>" +
                            "</div>";
                    }
                }

            ],
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
            "order": [[0, "desc"]],
            //"order": [[ 0, 'desc' ], [ 2, 'asc' ]]
        });
    }

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
                                    "<th>ATA.3UN</th>" +
                                    "<th>ATA.5UN</th>" +
                                    "<th>ATA.10UN</th>" +
                                    "<th>VAL.LISTA</th>" +
                                    "<th>PRODUTO</th>" +
                                    "<th>VAL.CARTÃO/PIX</th>" +
                                    "<th>VAL.PARCELADO</th>" +
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
                                                            "<img src='../public/storage/"+ arrayItem.path + "' class=\"image img-datatable\" width='120px' height='80px' alt=\"\" title='"+arrayItem.variacao+"'/>" :
                                                            "<img src='../public/storage/produtos/not-image.png' class=\"image img-datatable\" width='80px' height='80px' alt=\"\" title='"+arrayItem.variacao+"'/>"

                                    tmpRow += "<tr>" +
                                        "<td>"+image+"</td>" +
                                        "<td>" + arrayItem.subcodigo + "</td>" +
                                        "<td>" + arrayItem.variacao + "</td>" +
                                        "<td>" + arrayItem.quantidade + "</td>" +
                                        "<td>" + arrayItem.estoque + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_varejo) + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_atacado) + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_atacado_5un) + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_atacado_10un) + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_lista) + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_produto) + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_cartao_pix) + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_parcelado) + "</td>" +
                                        "<td>" + "<span class='badge bg-success'>"+arrayItem.status+"</span>" + "</td>" +
                                        "<td><i class=\"bi-image btnImageProduct\" " +
                                        "               style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" " +
                                        "               title='Imagem da Variação do Produto' data-bs-toggle=\"modal\" " +
                                        "               data-bs-target=\"#divModalImage\" data-variacao-id='"+arrayItem.id+"' " +
                                        "               data-subcodigo='"+arrayItem.subcodigo+"'>"+
                                        "</td>"+
                                    "</tr>"
                                });

                                tmpRow  +=      "</table>";
                                row.child(tmpRow).show();
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
     * Salva a imagem no produto PAI
     * */
    $('form[name="formImageProduct"]').validate({
        errorClass: "my-error-class",
        validClass: "my-valid-class",
        rules: {
            image: {
                required: false
            }
        },
        messages: {
            image: {
                required: "Informe a imagem!"
            }
        }, submitHandler: function(form,e) {
             //  console.log('Form submitted');
            e.preventDefault();

            let metodo = $("#metodo").val();
            //console.log(metodo);

            $.ajax({
                type: metodo,
                url: url + "/image/"+$("#product_id").val(),
                data:$('form[name="formImageProduct"]').serialize(),
                dataType:"json",
                beforeSend: function () {
                    //$("#modal-title").removeClass( "alert alert-danger" );
                    $('#modal-title').html('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>');
                    //$("#modal-title").addClass( "alert alert-info" );
                },
                success: function(data) {
                    //console.log(data.success);

                    if(data.success) {
                        swalWithBootstrapButtons.fire({
                            title: "Sucesso!",
                            text: data.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        table.destroy();
                        getdata();
                    }
                },
                error: function(data){
                    //console.log(data.responseText);
                    json = $.parseJSON(data.responseText);
                    $("#modal-title").addClass( "alert alert-danger" );
                    $('#modal-title').html('<p><strong>'+json.message+'</strong></p>');
                    Swal.fire(
                        'error!',
                        json.message,
                        'error'
                    )
                },
                complete:function(data){
                    // console.log(data.responseText);
                    json = $.parseJSON(data.responseText);
                    if(json.success) {
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    }
                }
            });
        }
    });



    /***
     * Salva imagem variação ou produto
     * */

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
                            table.destroy();
                            getdata();
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
            valor_cartao_pix0: {
                required: true
            },
            valor_parcelado0: {
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
            variacao0: {
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
            valor_cartao_pix: {
                required: "Informe o Valor?"
            },
            valor_parcelado: {
                required: "Informe o Valor?"
            }
        }, submitHandler: function(form,event) {
            event.preventDefault();
                let formData = "";
                let update = ($("#id").val() === "") ? false : true;

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

            $('#id').val(response.data.id);
            $('#codigo_produto').val(response.data.codigo_produto);
            $('#descricao').val(response.data.descricao);
            $('#status').val(response.data.status);
            $('#categoria_id').val(response.data.categoria_id);
            $('#ncm').val(response.data.ncm);
            $('#cest').val(response.data.cest);
            $('#origem').val(response.data.origem_id);


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

    $(document).on("click",".btnProductImage" ,function(event){
        event.preventDefault();
        id = $(this).data('id') != null ? $(this).data('id') : 0; //capturo o ID
        $("#product_id").val(id);
    });
        /**
     * Exibe as imagens das variações dos produtos
     * **/
    $(document).on("click",".btnImageProduct" ,function(event){
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
                        grid += "<img src='../public/storage/" + value.path + "' width='180px' height='180px' alt=\"\"/>";
                        grid += "<i class=\"bi-trash btnRemoveImage\"  data-id='"+value.id+"' style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" title='Remover Imagem'></i>";
                        grid += "</div>";
                    });
                }else{
                    grid = "<img src='../public/storage/produtos/not-image.png' width='180px' height='180px' alt=\"\"/>";
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
    });

    /**
     * Deleta a imagem do produto
     * */
   // $('i[name="btnRemoveImage"]').on('click',function(event) {
    $(document).on("click",".btnRemoveImage" , function(event){
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
    });

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

        let val = Number(typeof $('#subcodigo0').val() !== "undefined" ? $('#subcodigo0').val() : 0)+i;
        val = val >=10 ? val : "0" + val;
        fnc_variacao(i,val,null,null, '');
    });

    /**
     * Busac os Fornecedores
     * */
    let fnc_fornecedor = async function(name,value){
       // localStorage.clear();
        /*if(localStorage.getItem("data-suppliers") !== null){
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

        }else {*/
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
                 //   localStorage.setItem("data-suppliers", JSON.stringify(response));
                    $(name).append('<option value="">SELECIONE?</option>');
                    response.forEach(function (ret) {
                        //       console.log(ret.id +" - "+ value);
                        let sel = ret.id === value ? 'selected' : '';

                        $(name).append("<option value=" + ret.id + " " + sel + ">" + ret.nome + "</option>");
                    });
                });
       // }
    }
    /**
     * Retorna os campos de variações do produto
     * */
    let fnc_variacao = function (i,val,index,arrayItem,selected) {
       //console.log(arrayItem);
        let icon_remove = "";
        let id = arrayItem != null ? arrayItem.id : '';
        let subcodigo = arrayItem != null ? arrayItem.subcodigo.substring(arrayItem.subcodigo.length-2,arrayItem.subcodigo.length) : val;
        let variacao = arrayItem != null ? arrayItem.variacao : '';
        let valor_varejo = arrayItem != null ? formatMoney(arrayItem.valor_varejo) : typeof $("#valor_varejo0").val() !== "undefined" ? $("#valor_varejo0").val() : '';
        let valor_atacado = arrayItem != null ? formatMoney(arrayItem.valor_atacado) : typeof $("#valor_atacado0").val() !== "undefined" ? $("#valor_atacado0").val() : '';
        let valor_atacado_5un = arrayItem != null ? formatMoney(arrayItem.valor_atacado_5un) : typeof $("#valor_atacado_5un0").val() !== "undefined" ? $("#valor_atacado_5un0").val() : '';
        let valor_atacado_10un = arrayItem != null ? formatMoney(arrayItem.valor_atacado_10un) : typeof $("#valor_atacado_10un0").val() !== "undefined" ? $("#valor_atacado_10un0").val() : '';
        let valor_lista = arrayItem != null ? formatMoney(arrayItem.valor_lista) : typeof $("#valor_lista0").val() !== "undefined" ? $("#valor_lista0").val() : '';
        let valor_produto = arrayItem != null ? formatMoney(arrayItem.valor_produto) : typeof $("#valor_produto0").val() !== "undefined" ? $("#valor_produto0").val() : '';
        let quantidade = arrayItem != null ? arrayItem.quantidade : '';
        let estoque = arrayItem != null ? arrayItem.estoque : '';
        let quantidade_minima = arrayItem != null ? arrayItem.quantidade_minima : 2;
        let validade = arrayItem != null ? getFormattedDate(arrayItem.validade) : '00/00/0000';
        let fornecedor_id = arrayItem != null ? arrayItem.fornecedor : 0;
        let valor_cartao_pix = arrayItem != null ? formatMoney(arrayItem.valor_cartao_pix) : typeof $("#valor_cartao_pix0").val() !== "undefined" ? $("#valor_cartao_pix0").val() : '';
        let valor_parcelado = arrayItem != null ? formatMoney(arrayItem.valor_parcelado) : typeof $("#valor_parcelado0").val() !== "undefined" ? $("#valor_parcelado0").val() : '';



        /**
         * Adiciona o icone de remover do segundo em diante
         * */
        if(i > 0){
            icon_remove =  "<div class=\"col-md-1\" style='padding:unset;left: -6px;width: 10px' >"+
                "<a href=\"javascript:void(0)\" onclick=\"removeCampo('div_pai" + i + "')\" " +
                "title=\"Remover linha\"><img src=\"../public/img/minus.png\" border=\"0\" />" +
                "</a>"+
                "</div>" ;
        }

        $("#tbl").append("<div class=\"row \" style='padding: 3px;' id=\"div_pai"+i+"\">" +
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
                                "<div class=\"col-md-2\" style='left: -12px;width: 200px'>" +
                                    "<span class=\"border-lable-flt\">"+
                                        "<input type=\"text\" name=\"variacao[]\" id=\"variacao"+i+"\" " +
                                            "class=\"form-control format-font\" placeholder=\"VARIAÇÃO\" " +
                                            "value=\'" + variacao + "\'/>" +
                                        "<label for=\"label-variacao\">VARIAÇÃO</label>"+
                                    "</span>"+
                                "</div>"+
                                "<div class=\"col-md-2\" style='left: -32px;width: 110px'>"+
                                    "<span class=\"border-lable-flt\" >"+
                                        "<input type=\"text\" name=\"valor_varejo[]\"  id=\"valor_varejo"+i+"\""+
                                            "class=\"form-control\" placeholder=\"VAREJO\""+
                                            "onkeyup=\"formatMoneyPress(this)\" value=\'" + valor_varejo + "\' required/>"+
                                            "<label for=\"label-varejo\">VAREJO</label>"+
                                    "</span>"+
                                 "</div>"+
                                "<div class=\"col-md-2\" style='padding:unset;left: -40px;width: 100px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                        "<input type=\"text\" name=\"valor_atacado[]\"  id=\"valor_atacado"+i+"\""+
                                        "class=\"form-control\" placeholder=\"ATA.3UN\""+
                                        "onkeyup=\"formatMoneyPress(this)\" value=\'" + valor_atacado + "\' required/>"+
                                    "   <label for=\"label-atacado3n\">ATACADO 3UN</label>"+
                                    "</span>"+
                                "</div>" +
                                "<div class=\"col-md-2\" style='padding:unset;left: -36px;width: 100px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                    "<input type=\"text\" name=\"valor_atacado_5un[]\"  id=\"valor_atacado_5un"+i+"\""+
                                    "class=\"form-control\" placeholder=\"ATA.5UN\""+
                                    "onkeyup=\"formatMoneyPress(this)\" value=\'" + valor_atacado_5un + "\' required/>"+
                                    "   <label for=\"label-atacado5un\">ATACADO 5UN</label>"+
                                    "</span>"+
                                "</div>" +
                                "<div class=\"col-md-2\" style='padding:unset;left: -32px;width: 100px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                    "<input type=\"text\" name=\"valor_atacado_10un[]\"  id=\"valor_atacado_10un"+i+"\""+
                                    "class=\"form-control\" placeholder=\"CX.FECHADA\""+
                                    "onkeyup=\"formatMoneyPress(this)\" value=\'" + valor_atacado_10un + "\' required/>"+
                                    "   <label for=\"label-atacado10un\">CX.FECHADA</label>"+
                                    "</span>"+
                                "</div>" +
                                "<div class=\"col-md-2\" style='padding:unset;left: -28px;width: 100px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                    "<input type=\"text\" name=\"valor_lista[]\"  id=\"valor_lista"+i+"\""+
                                    "class=\"form-control\" placeholder=\"DINHEIRO\""+
                                    "onkeyup=\"formatMoneyPress(this)\" value=\'" + valor_lista + "\' required/>"+
                                    "   <label for=\"label-valor-lista\">DINHEIRO</label>"+
                                    "</span>"+
                                "</div>&nbsp;" +
                                "<div class=\"col-md-2\" style='padding:unset;left: -28px;width: 100px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                    "<input type=\"text\" name=\"valor_cartao_pix[]\"  id=\"valor_cartao_pix"+i+"\""+
                                    "class=\"form-control\" placeholder=\"CARTÃO/PIX\""+
                                    "onkeyup=\"formatMoneyPress(this)\" value=\'" + valor_cartao_pix + "\' required/>"+
                                    "   <label for=\"label-valor-lista\">CARTÃO/PIX</label>"+
                                "</span>"+
                                "</div>&nbsp;" +"<div class=\"col-md-2\" style='padding:unset;left: -28px;width: 100px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                    "<input type=\"text\" name=\"valor_parcelado[]\"  id=\"valor_parcelado"+i+"\""+
                                    "class=\"form-control\" placeholder=\"PARCELADO\""+
                                    "onkeyup=\"formatMoneyPress(this)\" value=\'" + valor_parcelado + "\' required/>"+
                                "   <label for=\"label-valor-lista\">PARCELADO</label>"+
                                "</span>"+
                                "</div>" +
                                "<div  class=\"col-md-2\" style='padding:unset;left: -24px;width: 100px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                        "<input type=\"text\" name=\"valor_produto[]\"  id=\"valor_produto"+i+"\""+
                                        "class=\"form-control\" placeholder=\"VLR.PAGO\""+
                                        "onkeyup=\"formatMoneyPress(this)\" value=\'" + valor_produto + "\' required/>"+
                                        "<label for=\"label-produto\">VLR.PAGO</label>"+
                                    "</span>"+
                                "</div>" +
                                "<div class=\"col-md-2\" style='padding:unset;left: -20px;width: 50px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                        "<input type=\"text\" name=\"quantidade[]\"  id=\"quantidade"+i+"\""+
                                        "class=\"form-control\" placeholder=\"QTD\" onkeyup=\"SomenteNumeros(this)\" " +
                                        "value=\'" + quantidade + "\' required/>"+
                                        "<label for=\"label-qtd\">QTD</label>"+
                                    "</span>"+
                                "</div>" +
                                "<div class=\"col-md-2\" style='padding:unset;left: -16px;width: 55px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                        "<input type=\"text\" name=\"quantidade_minima[]\"  id=\"quantidade_minima"+i+"\""+
                                        "class=\"form-control\" placeholder=\"QTD.MIN\" onkeyup=\"SomenteNumeros(this)\" " +
                                        "value=\'" + quantidade_minima + "\' required/>"+
                                        "<label for=\"label-qtd\">QTD.MIN</label>"+
                                    "</span>"+
                                "</div>" +
                                "<div class=\"col-md-2\" style='padding:unset;left: -12px;width: 60px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                        "<input type=\"text\" name=\"estoque[]\"  id=\"estoque"+i+"\""+
                                        "class=\"form-control\" placeholder=\"EST\" onkeyup=\"SomenteNumeros(this)\" " +
                                        "value=\'" + estoque + "\' required/>"+
                                        "<label for=\"label-estoque\">ESTOQUE</label>"+
                                    "</span>"+
                                "</div>" +
                                "<div class=\"col-md-2 date\" style='padding:unset;left: -8px;width: 122px' id=\"data_validade"+i+"\">"+
                                    "<span class=\"border-lable-flt\">"+
                                        "<input type=\"text\" name=\"validade[]\"  id=\"validade"+i+"\""+
                                        "class=\"form-control\" placeholder=\"QTD.MIN\" " +
                                        "onKeyUp=\"formatDate(this)\" maxlength=\"10\" value=\'" + validade + "\'/>"+
                                        "<label for=\"label-qtd\">VALIDADE</label>"+
                                    "</span>"+
                                "</div>" +
                                "<div class=\"col-md-2\" style='padding:unset;left: -4px;width: 78px'>"+
                                    "<span class=\"border-lable-flt\">"+
                                        "<SELECT type=\"text\" name=\"status_variacao[]\"  id=\"status_variacao"+i+"\""+
                                            "class=\"form-control status_variacao\" placeholder=\"STATUS\" required/>"+
                                            "<option value=\"1\" "+selected+">ATIVO</option>"+
                                            "<option value=\"0\" "+selected+">INATIVO</option>"+
                                            "</select>"+
                                        "<label for=\"label-qtd\">STATUS</label>"+
                                    "</span>"+
                                "</div>" +
                                "<div class=\"col-md-2\" style='padding:unset;left: -1px;width: 122px' >"+
                                    "<span class=\"border-lable-flt\">"+
                                        "<SELECT type=\"text\" name=\"fornecedor[]\"  id=\"fornecedor"+i+"\""+
                                            "class=\"form-control\" placeholder=\"FORNECEDOR\" required/>"+
                                            ""+fnc_fornecedor('#fornecedor'+i,fornecedor_id)+""+
                                        "</select>"+
                                        "<label for=\"label-qtd\">FORNECEDOR</label>"+
                                    "</span>"+
                                "</div>" +
                               ""+icon_remove+""+
                        "</div>");

    }

    /**
     *  Preview da imagem ao passar o mause
     * */
    $(document).on("mouseover",".image" , function(e){
            let img = $(this);

            swalWithBootstrapButtons.fire({
                imageUrl:  img[0].currentSrc,
                imageWidth: 420,
                imageHeight: 240,
                showConfirmButton: false,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
    });

    let fnc_enviaForm = function (formData) {
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
            beforeSend: function () {
                $("#modal-title").removeClass( "alert alert-danger" );
                $('#modal-title').html('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>');
                $("#modal-title").addClass( "alert alert-info" );
            },
            success: function (response) {
                // console.log(response);
                if(response.success) {
                    Swal.fire({
                        title: "Sucesso!",
                        text: response.message,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    //.ajax.reload(null, false);
                    table.destroy();
                    getdata();

                    $("#id").val('');
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
                }
            },
            error: function(response){
                json = $.parseJSON(response.responseText);
                $("#modal-title").addClass( "alert alert-danger" );
                $('#modal-title').html('<p><i class="fas fa-exclamation-circle"></i>&nbsp;<strong>'+json.message+'</strong></p>');
                Swal.fire(
                    'error!',
                    json.message,
                    'error'
                )
            },
        });
    }
    /**************************************
     ******* FUNÇÕES ONLAOD SISTEMA *******
     **************************************
     * */
     getdata();
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

/**
 * SÓ PERMITE DIGITAR NUMEROS NO CAMPO
 * */
    function SomenteNumeros(num) {
        let er = /[^0-9.]/;
        er.lastIndex = 0;
        let campo = num;
        if (er.test(campo.value)) {
            campo.value = "";
        }
    }

    /***
     * FORMATA CAMPO COM MOEDA
     *
     * OnkeyPress
     * */
    function formatMoneyPress(parm) {
        let valor = parm.value;

        valor = valor + '';
        valor = parseInt(valor.replace(/[\D]+/g, ''));
        valor = valor + '';
        valor = valor.replace(/([0-9]{2})$/g, ",$1");

        if (valor.length > 6) {
            valor = valor.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
        }

        parm.value = valor;
        if(valor === 'NaN') parm.value = '';
    }

    /**
     * Ajusta para exibição nos inputs e etc.. valor moeda!
     * */
    function formatMoney(valor)
    {
        const v = ((valor.replace(/\D/g, '') / 100).toFixed(2) + '').split('.');

        const m = v[0].split('').reverse().join('').match(/.{1,3}/g);

        for (let i = 0; i < m.length; i++)
            m[i] = m[i].split('').reverse().join('') + '.';

        const r = m.reverse().join('');

        return r.substring(0, r.lastIndexOf('.')) + ',' + v[1];
    }

/**
 * REMOVE OS INPUTS DINAMICOS DAS VARIAÇÕES
 * */
    function removeCampo(parm) {
        document.getElementById(parm).remove();
    }

    /***
     * Formata data de yyyy/mm/dd para dd/mm/yyyy
     * */
    function getFormattedDate(parm) {
        let d = parm.split('-');
        return  d[2] + '/' + d[1] + '/' + d[0];
    }

    /***
     * Ao digitar, formata a data no campo em dd/mm/yyyy
     * */
    function formatDate(parm) {

        let tecla = this.keyCode;
        let vr = String(parm.value);
        vr = vr.replace("/", "");
        vr = vr.replace("/", "");
        vr = vr.replace("/", "");
        let tam = vr.length + 1;

        if (tecla !== 8 && tecla !== 8) {
            if (tam > 0 && tam < 2)
                parm.value = vr.substr(0, 2) ;
            if (tam > 2 && tam < 4)
                parm.value = vr.substr(0, 2) + '/' + vr.substr(2, 2);
            if (tam > 4 && tam < 7)
                parm.value = vr.substr(0, 2) + '/' + vr.substr(2, 2) + '/' + vr.substr(4, 7);
        }

    }
