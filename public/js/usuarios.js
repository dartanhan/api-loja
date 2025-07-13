/* globals Chart:false, feather:false */
$(document).ready(function() {
    const urlApi = fncUrl();

    let metodo = '',titulo = '',url,json,fila,id,nome,login,status,token;

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    });

    let table = $('#table').DataTable({
          "createdRow": function(row, data, dataIndex) {
              if (data.status === 'INATIVO') {
                  $(row).addClass('red');
              }
          },
          "ajax":{
              "method": 'get',
              "url": urlApi + "/usuario/create",
              "data":'',
              "dataSrc":""
          },
          "columns":[
              {"data": "id"},
              {"data": "nome"},
              {"data": "login"},
              {"data": "senha"},
              {"data": "status"},
              {"data": "created_at"},
              {"data": "updated_at"},
              {"defaultContent": "<div class='text-center'>" +
                      "<div class='btn-group'>" +
                      "<button class='btn btn-primary btn-sm btnEditar' " +
                      "data-toggle=\"modal\" data-target=\"#divModal\">" +
                      "<i class='material-icons'>edit</i></button>&nbsp;&nbsp;" +
                      "<button class='btn btn-danger btn-sm btnBorrar'><i class='material-icons'>delete</i></button>" +
                      "</div>" +
                      "</div>"
              }
          ] ,
          "order": [[ 1, "asc" ]]
          ,	language: {
            "url": Helpers.asset("Portuguese-Brasil.json")
        },
	});

    /**
     * AÇÃO DE ABRIR O MODAL
     * Novo
     * */
	$('button[id="btnNuevo"]').on('click', function(event) {
		event.preventDefault();
        $('form[name="form"]')[0].reset();

        $("#id").val('');
        $("#metodo").val('POST');
		this.blur(); // Manually remove focus from clicked link.
        $("#modal-title").addClass("alert alert-success");
		$('#modal-title').html('<p><img src="'+Helpers.asset("img/iconfinder_user-alt_285645.png")+'"/>&nbsp;<strong>NOVO USUÁRIO</strong></p>');
    });

    /**
     * Editar
     * **/
    $(document).on("click", ".btnEditar", function(){
        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(0)').text()); //capturo o ID
        nome = fila.find('td:eq(1)').text();
        login = fila.find('td:eq(2)').text();
        status = fila.find('td:eq(4)').text() === 'ATIVO'? 1 : 0;

        $("#metodo").val('PUT');
        $("#id").val(id);
        $("#login").val(login);
        $("#nome").val(nome);
        $("#status").val(status);

        $("#modal-title").addClass("alert alert-secondary");
        $('#modal-title').html('<p><img src="'+Helpers.asset("img/iconfinder_user-alt_285645.png")+'"/>&nbsp;<strong>EDITANDO USUÁRIO</strong></p>');
    });

	/****
	 *
	 * SALVA FORNECEDOR
	 *
	 */

	 $('form[name="form"]').validate({
         errorClass: "my-error-class",
         validClass: "my-valid-class",
        rules: {
            nome: {
                required: true
			},
            login: {
                required: true
            },
            senha: {
                minlength: 5
            },
            senha_confirm: {
                minlength: 5,
                equalTo: "#senha"
            }
        },
        messages: {
            nome: {
                required: "Informe o nome do Usuário?"
            },
            login: {
                required: "Informe o login do Usuário?"
            },
            senha: {
                required: "Informe a senha do Usuário?",
            },
            senha_confirm: {
                required: "Senha de confirmação deve ser igual a senha do Usuário!",
                senha_confirm: "Senha de confirmação deve ser igual a senha!",
            },
        }, submitHandler: function(form,e) {
            e.preventDefault();
             $("#modal-title").addClass( "alert alert-secondary" );

            metodo = $("#metodo").val();

             if(metodo === 'POST'){
                 url = urlApi + "/usuario";
                 titulo = "NOVO USUÁRIO";

             }else if(metodo === 'PUT'){
                 url = urlApi + "/usuario/update";
                 titulo = "EDITANDO USUÁRIO";
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
                    json = $.parseJSON(data.responseText);
                    $("#modal-title").addClass( "alert alert-danger" );
                    $('#modal-title').html('<p><i class="fas fa-exclamation-circle"></i>&nbsp;<strong>'+json.message+'</strong></p>');
                    Swal.fire(
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
                    url: urlApi + "/usuario/" + id ,
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
});
