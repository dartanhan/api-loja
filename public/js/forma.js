import {sweetAlert,createSlug} from './comum.js';

$(document).ready(function() {
    const urlApi = fncUrl();

    let metodo = '', titulo = '', url,fila,id,nome,token,json,status;

    const table = $('#table').DataTable({

        ajax: {
            method: 'get',
            processing: true,
            serverSide: true,
            url: urlApi + "/forma/create",
        },
        "columns": [
            {"data": "id", "defaultContent": ""},
            {"data": "nome", "defaultContent": ""},
            {"data": "slug", "defaultContent": ""},
            {
                "data": "status",
                render: function (data, type, row) {
                    if(row.status === 1)
                        return "<span class=\"badge bg-success\">Ativo</span>";

                    return "<span class=\"badge bg-danger\">Inativo</span>";
                }
            },
            {"data": "created_at","defaultContent": ""
            },
            {"data": "updated_at","defaultContent": ""
            },
            {
                "data": "defaultContent", render: function (data, type, row) {
                    return "<div class='text-center'>" +
                                "<div class='btn-group'>" +
                                    "<button class='btn btn-primary btn-sm btnEditar' data-status="+row.status+" " +
                                    " data-bs-toggle=\"modal\" data-bs-target=\"#divModal\">" +
                                    " <i class='material-icons'>edit</i></button>&nbsp;&nbsp;" +
                                    " <button class='btn btn-danger btn-sm btnBorrar'><i class='material-icons'>delete</i></button>" +
                                "</div>" +
                            "</div>"
                }
            }
        ],
        "order": [[0, "asc"]]
        , language: {
            "url": "../public/Portuguese-Brasil.json"
        },
    });

    /**
     * AÇÃO DE ABRIR O MODAL
     * Novo
     * */
    $('button[id="btnNuevo"]').on('click', function(event) {
		event.preventDefault();
        $("#form")[0].reset();

        $("#id").val('');
        $("#metodo").val('POST');
		this.blur(); // Manually remove focus from clicked link.
		$('#modal-title').html('<strong>NOVA FORMA DE ENTREGA</strong>');
    });

    /**
     * Editar
     * **/
    $(document).on("click", ".btnEditar", function(event){
        event.preventDefault();
        $('form[name="form"]')[0].reset();

        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(0)').text()); //capturo o ID
        nome = fila.find('td:eq(1)').text();
        status =  $(this).data('status');
        let slug =  fila.find('td:eq(2)').text();

        $("#metodo").val('PUT');
        $("#id").val(id);
        $("#nome").val(nome);
        $("#status").val(status);
        $("#slug").val(slug);

        $('#modal-title').html('<strong>EDITANDO FORMA DE ENTREGA</strong>');
    });

    /**
     * Cria o slug
     * */
    $(document).on("blur", "#nome", function(event){
        event.preventDefault();

        // Pegue o valor do campo nome
        let nomeValue = $("#nome").val();
        //console.log(nomeValue);

        // Crie o slug
        let slugValue = createSlug(nomeValue);
        //console.log(slugValue);

        // Defina o valor do campo slug
        $("#slug").val(slugValue);
    });

    /****
	 *
	 * SALVA FORMA
	 *
	 */

	 $('form[name="form"]').validate({
         errorClass: "my-error-class",
         validClass: "my-valid-class",
        rules: {
            nome: {
                required: true
			},
            slug: {
                required: true
            }
        },
        messages: {
            nome: {
                required: "Informe a Forma de Entrega?"
            },
            slug: {
                required: "Slug é obrigatório, informe o nome da forma de entrega?"
            }
        }, submitHandler: function(form,e) {
            e.preventDefault();
             $("#modal-title").addClass( "alert alert-secondary" );
            metodo = $("#metodo").val();

             if(metodo === 'POST'){
                 url = urlApi + "/forma";
                 titulo = "NOVA FORMA DE ENTREGA";

             }else if(metodo === 'PUT'){
                 url = urlApi + "/forma/update";
                 titulo = "EDITANDO FORMA DE ENTREGA";
             }

    		$.ajax({
					url: url,
					type:metodo,
					data:$('form[name="form"]').serialize(),
					dataType:'json',
                beforeSend: function () {
                    $("#modal-title").removeClass( "alert alert-danger" );
                    $('#modal-title').html('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>');
                    $("#modal-title").addClass( "alert alert-info" );
                },
                success: function(data) {
                    // console.log(data);
                    if(data.success) {
                        sweetAlert({
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
                    json = $.parseJSON(data.responseText);
                    $("#modal-title").addClass( "alert alert-danger" );
                    $('#modal-title').html('<p><i class="fas fa-exclamation-circle"></i>&nbsp;<strong>'+json.message+'</strong></p>');
                    sweetAlert(
                        'error!',
                        json.message,
                        'error'
                    )
                },
                complete:function(data){
                    //console.log(data.responseText);
                    json = $.parseJSON(data.responseText);
                    if(json.success) {
                        window.setTimeout(function () {
                            $('#divModal').modal('hide');
                        }, 1500);
                    }
                }
            });
         }
     });

    /**
     * Remover
     * **/
    $(document).on("click", ".btnBorrar", function(){
        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(0)').text()) ;
        nome = fila.find('td:eq(1)').text();
        token = $('form').find('input[name="_token"]').val();

        sweetAlert({
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
                    url: urlApi + "/forma/" + id ,
                    data: {_token: token},
                    type: "DELETE",
                    datatype:"json",
                    beforeSend: function () {
                        sweetAlert(
                            'Aguarde..',
                            '<div class=\"spinner-border spinner-border-sm ms-auto\" role=\"status\" aria-hidden=\"true\"></div>',
                            'info'
                        )
                    },
                    success: function(data) {
                        if(data.success) {
                            sweetAlert({
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
                        sweetAlert(
                            'error!',
                            json.message,
                            'error'
                        )
                    }
                });
            }
        });
    });

});
