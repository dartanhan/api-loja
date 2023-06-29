/* globals Chart:false, feather:false */
$(document).ready(function() {
    let metodo = '',titulo = '', token = '', produto_id = '', fila='', json = '', id='', nome='',imgUrl = '';
    let resposta = '',url;
    let urlApi = window.location.protocol +"//"+ window.location.hostname + "/api-loja/admin";
    let img = '../public/img';

    //$('.file-upload').file_upload();
    $('#valorproduto').maskMoney();
	$('#valordinheiro').maskMoney();
    $('#valorcartao').maskMoney();
	//$('#valorpercentual').maskMoney();

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    });

    let table = $('#table').DataTable({
          //"lengthMenu": [ 10, 25, 50, 100 ],
          "createdRow": function(row, data) {
              //console.log(data.qtdMinFeira);
              //if (data.block === 1) {
              //    $(row).addClass('red');
             // }

              //barão
              if(data.qtdBarao <= data.qtdMinBarao && data.qtdBarao > 0){
                  $(row).find('td').eq(7).addClass("orange").attr("title", "Quantidade Mínima do Estoque foi atingida! ");
              }else if(data.qtdBarao === 0){
                  $(row).find('td').eq(7).addClass("red").attr("title", "Produto sem estoque! ");
              }
          },
          "ajax":{
              "method": 'get',
              "processing": true,
              "serverSide": true,
              "url": urlApi + "/product/create",
              "data":'',
              cache: false,
          },
          scrollX:true,
		"columns": [
            {
                "className":      'details-control',
                "orderable":      false,
                "defaultContent": ''
            },
                { "data" : "id", "defaultContent": ""},
                { "data": "codigo_produto", "defaultContent": "" },
                { "data": "descricao" , "defaultContent": ""},
                { "data": "status" , "defaultContent": ""},
                {
                    "data": "valor_produto" ,
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                },
                {
                    "data": "valor_dinheiro",
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                },
                {
                    "data": "valor_cartao",
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                },
                { "data": "qtdFeira" , "defaultContent": ""},
                { "data": "qtdBarao" , "defaultContent": ""},
                { "data": "percentual" , "defaultContent": ""},
                { "data": "dataCriacao", "defaultContent": ""},
                { "data": "dataAtualizacao" , "defaultContent": ""},
                { "data": "nome_fornecedor" , "defaultContent": ""},
                { "data": "nome_categoria" , "defaultContent": ""},
                { "data": "nome_cor" , "defaultContent": ""},
                { "data": "qtdMinFeira" , "defaultContent": ""},
                { "data": "qtdMinBarao" , "defaultContent": ""},
                { "data": "block" ,
                    "render": function ( data, type, row, meta ) {
                        return data == 0 ? 'ATIVO' : 'INATIVO';
                    }
                },{
                    "defaultContent":
                        "<div class='text-center'>" +
                            "<div class='btn-group'>" +
                                "<button class='btn btn-primary btn-xs btnEditar' title='Editar Produto' data-bs-toggle=\"modal\" data-bs-target=\"#divModal\"><i class='material-icons  btnEditar'>edit</i></button>&nbsp;" +
                                "<button class='btn btn-danger btn-xs btnBorrar' title='Excluir Produto'><i class='material-icons'>delete</i></button>&nbsp;" +
                                "<button class='btn btn-dark btn-xs btnImage' title='Imagem do Produto'  data-toggle=\"modal\" data-target=\"#gallery\"><i class='material-icons'>photo_camera</i></button>&nbsp;" +
                                "<button class='btn btn-warning btn-xs btnBlock' title='Bloquear Produto'><i class='material-icons'>lock_outline</i></button>" +
                            "</div>" +
                        "</div>"
                }

		],"columnDefs": [
              {
                  "targets": [1],
                  "visible": true,
                  "defaultContent": ""
			  },{
                  "targets": [4,8,10,11,12,13,15,16,17],
                  "visible": false,
                  "defaultContent": ""
              }
        ],
		language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
        },
        "order": [[ 0, "desc" ]]
        //"order": [[ 0, 'desc' ], [ 2, 'asc' ]]
	});

	// Add event listener for opening and closing details
    $('#table tbody').on('click', 'td.details-control', function () {
        let tr = $(this).closest('tr');
        let row = table.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );

    /**
     * CHAMA METODO DE GERAR CODIGO
     * */
    $('div[name="geraCodigo"]').click(function(event){
        event.preventDefault(); //previne reload da pagina, não permite
        geraCodigo();

    });

    /***
     * Chama o controller da API de cadasatro de produtos
     */
    $("#form").submit(function(evt){
        evt.preventDefault();
    }).validate({
        errorClass: "my-error-class",
        validClass: "my-valid-class",
        rules: {
            codigo_produto: {
                required: true
            },
            cor: {
                required: true
            },
            nome: {
                required: true
            },
            qtdfeira: {
                required: true
            },
            qtdbarao: {
                required: true
            },
            valorproduto: {
                required: true
            },
            valordinheiro: {
                required: true
            },
            valorcartao: {
                required: true
            },
            fornecedor: {
                required: true
            },
            categoria: {
                required: true
            }

        },
        messages: {
            codigo_produto: {
                required: "Informe o código do Produto?"
            },
            cor: {
                required: "Informe a Cor?"
            },
            nome: {
                required: "Informe a Descrição do Produto?"
            },
            qtdfeira: {
                required: "Informe a quantidade?"
            },
            qtdbarao: {
                required: "Informe a quantidade?"
            },
            valorproduto: {
                required: "Informe o valor do Produto?"
            },
            valorcartao: {
                required: "Informe o valor do Cartão?"
            },
            valordinheiro: {
                required: "Informe o valor do Dinheiro?"
            },
            fornecedor: {
                required: "Informe o Fornecedor?"
            },
            categoria: {
                required: "Informe a Categoria?"
            },
        }, submitHandler: function(form,event) {
            event.preventDefault();
            let formData = new FormData($(form)[0]);

            $("#modal-title").addClass( "alert alert-secondary" );

            metodo = $("#metodo").val();
            if(metodo === 'PUT'){
                //url = urlApi + "/product/update";
                titulo = "EDITANDO PRODUTO";
                imgUrl = img + '/iconfinder_edit_15511.png';
            }else{
                // url = urlApi + "/product";
                titulo = "NOVO PRODUTO";
                imgUrl = img + '/iconfinder_price-tag_1291777.png';
            }

            $.ajax({
                url: urlApi + "/product",
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
                        swalWithBootstrapButtons.fire({
                            title: titulo,
                            text: response.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        table.ajax.reload(null, false);
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
                complete:function(response){
                    //console.log(metodo  + "ssssss");
                    json = $.parseJSON(response.responseText);
                    if(json.success) {
                        $('#nome').val('');//POG não submit form com pistola
                        window.setTimeout(function () {
                            $('#divModal').modal('hide');
                            //geraCodigo();
                        }, 1500);
                    }
                }
            });
        }
    });


    /**
     * BLOQUEAR PRODUTO
     * */
    $(document).on("click", ".btnBlock", function(){
        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(1)').text()) ;
        token = $('form').find('input[name="_token"]').val();
        nome = fila.find('td:eq(3)').text();

        Swal.fire({
            title: 'Tem certeza?',
            text: "Está seguro de bloquear produto: [ " + nome + " ] ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, bloquear!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: urlApi + "/product/" + id ,
                    data: {_token: token},
                    cache: false,
                    type: "PUT",
                    datatype:"json",
                    beforeSend: function () {
                        swalWithBootstrapButtons.fire(
                            'Aguarde..',
                            '<div class=\"spinner-border spinner-border-sm ms-auto\" role=\"status\" aria-hidden=\"true\"></div>',
                            'info'
                        )
                    },
                    success: function(data) {
                        if(data.success) {
                            swalWithBootstrapButtons.fire({
                                title: 'Bloqueado!',
                                text: data.message,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function(data){
                        json = $.parseJSON(data.responseText);
                        Swal.fire(
                            'error!',
                            json.message,
                            'error'
                        )
                    }
                });
            }
        });

      /*  resposta = confirm("Está seguro de bloquear este produto : [ "+ nome +" ] ?");
        if (resposta) {
            $.ajax({
                url: urlApi + "/product/" + id ,
                data: {_token: token},
                cache: false,
                type: "PUT",
                datatype:"json",
                success: function(data) {
                    if(data.success) {
                        swalWithBootstrapButtons.fire({
                            title: titulo,
                            text: data.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        table.ajax.reload(null, false);
                    }
                },
                error: function(data){
                    //console.log(data.responseText);
                    let json = $.parseJSON(data.responseText);
                    $("#alert-title-delete").addClass("alert alert-danger").text(json.message);
                },
                complete:function(data){
                    console.log(data.responseText);
                    json = $.parseJSON(data.responseText);
                    if(json.success) {
                        $("#alert-title-delete").text(json.message).removeClass("alert alert-danger").addClass("alert alert-success");
                        $('#divDelete').fadeIn('slow').delay(5000).fadeOut(400);

                    }
                }
            });
            return false;
        }*/
    });
	/***
	 * FAZ O CALCULO DO PERCENTUAL DE VENDA DO PRODUTO
	 *
	*/
    //let valorproduto = 0, percentual =0, valVarejo = 0, calculoFinal =0;

	/**$("input").on("keyup", function(event) {
	   //console.log(event.target.id);

		if(event.target.id == "valorproduto" || event.target.id == "valorpercentual" ){
            valorproduto = $('#valorproduto').maskMoney('unmasked')[0];
            percentual = $('#valorpercentual').maskMoney('unmasked')[0];

            valVarejo  = ((percentual / 100 ) * valorproduto)+valorproduto;

            calculoFinal = valVarejo + (valVarejo * 10/100);

            //calculoFinal = valorMoeda(calculoFinal);
            //calculoFinal = calculoFinal.toLocaleString('pt-br',{style:'currency', currency:'BRL'});

            $('#valorvenda').val('R$ ' + calculoFinal.toFixed(2).replace('.', ',').split('').reverse().map((v, i) => i > 5 && (i + 6) % 3 === 0 ? `${v}.` : v).reverse().join(''));
		}
	});*/

	/**
	 * EDITAR PRODUTO UPDATE
	 *
	 */
    $(document).on("click", ".btnEditar", function(event){
	//$('img[name="acao"]').on('click', function(event) {
		event.preventDefault();
		this.blur(); // Manually remove focus from clicked link.
        $('form[id="form"]')[0].reset();
		//var id = $(this).attr("data-id");
        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(1)').text()); //capturo o ID
        $("#modal-title").addClass( "alert alert-secondary" );
        $('#modal-title').html('<p><img alt="" src="'+img+'/iconfinder_edit_15511.png"/>&nbsp;<strong>EDITANDO REGISTRO ID: [ ' + id +' ]</strong></p>');
		//$('#divCodProduto').hide();
        $('#metodo').val('PUT');

		$.ajax({
			//	url: url,
				url: urlApi + "/product/"+id+"/edit",
				type:'get',
                cache: false,
				data:'',
				dataType:'json',
				success: function(response){
					//console.log(response);

					if(response.success === true){
						//var myJSON = JSON.stringify(response.dados);
						 //var obj = JSON.parse(myJSON);
						//console.log(response.dados[0].produtos);
						//console.log("desc >> " + response.dados[0].descricao);

						$('#id').val(response.dados[0].id);
						$('#codigo_produto').val(response.dados[0].codigo_produto);
						$('#nome').val(response.dados[0].descricao);
						$('#valorproduto').val(response.dados[0].valor_produto);
						$('#valorcartao').val(response.dados[0].valor_cartao);
						$('#valordinheiro').val(response.dados[0].valor_dinheiro);
                        $('#status').val(response.dados[0].status);

						formatarMoeda('valorproduto', 'moeda');
						formatarMoeda('valordinheiro', 'moeda');
						formatarMoeda('valorcartao', 'moeda');


						$('#fornecedor').val(response.dados[0].fornecedor_id);
						$('#categoria').val(response.dados[0].categoria_id);
                        $('#cor').val(response.dados[0].cor_id);

						for (let index = 0; index < response.dados[0].produtos.length; index++) {

							if(response.dados[0].produtos[index].loja_id === 1 ){
								$('#qtdfeira').val(response.dados[0].produtos[index].quantidade);
								$('#qtdmin').val( response.dados[0].produtos[index].quantidade_minima);
							}else{
								$('#qtdbarao').val(response.dados[0].produtos[index].quantidade);
								$('#qtdmin').val( response.dados[0].produtos[index].quantidade_minima);
							}
						}

					}else{
						alert("Error" + response.message);
					}
				}
		});
	});

    //Remover
    $(document).on("click", ".btnBorrar", function(){
        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(1)').text()) ;
        nome = fila.find('td:eq(3)').text();
        token = $('form').find('input[name="_token"]').val();

        Swal.fire({
            title: 'Tem certeza?',
            text: "Está seguro de remover este registro: [ " + nome + " ] ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, deletar!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: urlApi + "/product/" + id ,
                    data: {_token: token},
                    type: "DELETE",
                    datatype:"json",
                    beforeSend: function () {
                        swalWithBootstrapButtons.fire(
                            'Aguarde..',
                            '<div class=\"spinner-border spinner-border-sm ms-auto\" role=\"status\" aria-hidden=\"true\"></div>',
                            'info'
                        )
                    },
                    success: function(data) {
                        if(data.success) {
                            swalWithBootstrapButtons.fire({
                                title: 'Deletado!',
                                text: data.message,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function(data){
                        json = $.parseJSON(data.responseText);
                        Swal.fire(
                            'error!',
                            json.message,
                            'error'
                        )
                    }
                });
            }
        });
    });

    /***
     * NOVO PRODUTO
     * */
	$('button[id="btnNuevo"]').on('click', function(event) {
		event.preventDefault();
        $('form[name="form"]')[0].reset();

		this.blur(); // Manually remove focus from clicked link.
        $("#id").val('');
        $("#metodo").val('POST');
		//$('#divCodProduto').show();
		$('#modal-title').html('<p><img alt="" src="'+img+'/iconfinder_price-tag_1291777.png"/>&nbsp;<strong>NOVO PRODUTO</strong></p>');
		geraCodigo();

	});


    /**
     * GRID DE IMAGENS
     * **/
    $(document).on("click", ".btnImage", function(event){
        event.preventDefault();

        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(1)').text()); //capturo o ID
        //console.log('id ' + id);
        $.ajax({
            //	url: url,
            url: urlApi + "/product/"+id, //product.show
            cache: false,
            type:'get',
            data:'',
            dataType:'json',
            success: function(response){
                //console.log(response.dados.length);
               // let grid;
                $('#modalBody').html('');

                if(response.dados.length > 0) {
                    $.each(response.dados, function (i, v) {
                        i++;
                        $('#modalBody').append("<figure class=\"gallery__item gallery__item--" + i + "\">" +
                            "<img src='../public/storage/" + v.path + "' alt='' class=\"gallery__img\"></figure>");
                    });
                }else{
                    //$('#divModalGrid').html("<p><h2>Sem imagens para esse produto.</h2></p>");
                    $('#modalBody').append("<figure class=\"gallery__item gallery__item\">" +
                    "<img src='../public/storage/produtos/not-image.png' alt='' class=\"gallery__img\"></figure>");
                }

            }
        });
    });


	/**
     * BUSCA CATEGORIAS PARA O SELECT
     * **/
   /* $('button[name="categoria"]').change('click', function(event) {
        event.preventDefault();
    });*/

    function geraCodigo(){

        $.ajax({

            url: urlApi + '/product/code/',
            type:'get',
            cache: false,
            dataType:'json',
            success: function(response){

                if(response.success === true){
                    console.log(response);
                    $('#codigo_produto').val(response.message);
                    $('#codigo_produto').focus();
                }else{
                    alert("Error" + response.message);
                }
            }
        });
    }
});

function formatarMoeda(elemento, flag) {
    let el = document.getElementById(elemento);
    let valor = el.value;

    valor = valor + '';
    valor = parseInt(valor.replace(/[\D]+/g,''));
    valor = valor + '';
    valor = valor.replace(/([0-9]{2})$/g, ",$1");

    if (valor.length > 6) {
        valor = valor.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
    }

    if(flag === 'moeda'){
        el.value = 'R$ ' + valor;
    }else{
        el.value = valor + '%';
    }
}


/* Formatting function for row details - modify as you need */

function format ( d ) {
	// `d` is the original data object for the row
	//console.log(d);
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
                '<tr>'+
                    '<td><strong>Percentual:</strong></td>'+
                    '<td>'+d.percentual+'</td>'+
                    '<td><strong>Fornecedor:</strong></td>'+
                    '<td>'+d.nome_fornecedor+'</td>'+
                '</tr>'+
               '<tr>'+
                    '<td><strong>Data Criação:</strong></td>'+
                    '<td>'+d.dataCriacao+'</td>'+
                    '<td><strong>Data Atualização:</strong></td>'+
                    '<td>'+d.dataAtualizacao+'</td>'+
                '</tr>'+
                '<tr>'+
                    '<td><strong>Qtd Minima Feira:</strong></td>'+
                    '<td>'+d.qtdMinFeira+'</td>'+
                    '<td><strong>Qtd Minima Barão:</strong></td>'+
                    '<td>'+d.qtdMinBarao+'</td>'+
                '</tr>'+
    '</table>';
}
