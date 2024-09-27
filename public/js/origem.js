/* globals Chart:false, feather:false */
$(function() {
    let metodo = '',titulo = '', id ='',url,json,fila,valor,taxa,token;
    const urlApi = fncUrl();

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    });

    let table  = $('#table').DataTable({
        "ajax":{
            "method": 'get',
            "url": urlApi + "/origem/create",
            "data":'',
            "dataSrc":""
        },
        "columns":[
            {"data": "id"},
            {
                "data": "codigo",
                //"render": $.fn.dataTable.render.number('.', ',', 2,null , '%').display
            },
            {
                "data": "descricao",
                //"render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
            },
            {"data": "created_at"},
            {"data": "updated_at"},
            {"defaultContent": "<div class='text-center'>" +
                                    "<div class='btn-group'>" +
                                        "<button class='btn btn-primary btn-sm btnEditar' " +
                                            " data-bs-toggle=\"modal\" data-bs-target=\"#divModal\" title=\"Editar Origem\">" +
                                            " <i class=\"bi bi-pencil-square\"></i></button>&nbsp;&nbsp;" +
                                        "<button class='btn btn-danger btn-sm btnBorrar' title='Deletar Origem'><i class=\"bi bi-trash\"></i></button>" +
                                    "</div>" +
                                "</div>"
            }
        ] ,
        /*"columnDefs": [
            { "visible": false, "targets": 5 }
        ],*/
        language: {
            "url": "../public/Portuguese-Brasil.json"
        },
        "order": [[ 0, "asc" ]]
    });

    /**
     * AÇÃO DE ABRIR O MODAL
     * Novo
     * */
    $('button[id="btnNuevo"]').on('click', function(event) {
        event.preventDefault();
        $('form[name="formOrigem"]')[0].reset();

        $("#id").val('');
        $("#metodo").val('POST');
        $('#title-origem').html("<i class=\"bi bi-clouds\"></i>&nbsp;Nova Origem");

        $('#divModal').on('shown.bs.modal', function () {
            $('#codigo').trigger('focus')
        });
    });

    /**
     * Editar
     * **/
    $(document).on("click", ".btnEditar", function(event){
        event.preventDefault();

        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(0)').text()); //capturo o ID
        codigo = fila.find('td:eq(1)').text();
        descricao  = fila.find('td:eq(2)').text();
       // valor_final  = fila.find('td:eq(3)').text();

        //let currentRow = $(this).closest("tr");
        //let data = $('#table').DataTable().row(currentRow).data();

        $("#metodo").val('PUT');
        $("#id").val(id);
        $("#codigo").val(codigo);
        $("#descricao").val(descricao);
       // $("#valor_final").val(valor_final);

        $('#title-origem').html('<i class=\"bi bi-clouds\"></i> Editando Origem - ID [ '+id+' ]');

    });

    /****
     *
     * SALVA NOVA ORIGEM
     *
     */
    $('form[name="formOrigem"]').validate({
        errorClass: "my-error-class",
        validClass: "my-valid-class",
        rules: {
            codigo: {
                required: true
            },
            descricao: {
                required: true
            }
        },
        messages: {
            codigo: {
                required: "Informe o Código da Origem?"
            },
            descricao: {
                required: "Informe a Descrição da Origem?"
            }
        }, submitHandler: function(form,e) {
         //   console.log('Form submitted');
           e.preventDefault();
            $("#modal-title").addClass( "alert alert-secondary" );
            metodo = $("#metodo").val();
            id = $("#id").val();

            if(metodo === 'POST'){
                url = urlApi + "/origem";
            }else if(metodo === 'PUT'){
                url = urlApi + "/origem/"+id;
            }

            $.ajax({
                type: metodo,
                url: url,
                data:$(form).serialize(),
                dataType:"json",
                beforeSend: function () {
                    $("#modal-title").removeClass( "alert alert-danger" );
                    $('#modal-title').html('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>');
                    $("#modal-title").addClass( "alert alert-info" );
                },
                success: function(data) {
                   // console.log(data);
                    if(data.success) {
                        swalWithBootstrapButtons.fire({
                            title: "SUCESSO!",
                            text: data.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        table.ajax.reload(null, false);
                    }else{
                        $("#modal-title").addClass( "alert alert-danger" );
                        $('#modal-title').html('<p><i class="fas fa-exclamation-circle"></i>&nbsp;<strong>'+json.message+'</strong></p>');
                        Swal.fire(
                            'error!',
                            json.message,
                            'error'
                        );
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
                   // console.log(data);
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
    $(document).on("click", ".btnBorrar", function(event){
        event.preventDefault();
        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(0)').text()) ;
        //taxa = fila.find('td:eq(1)').text();
        token = $('form').find('input[name="_token"]').val();

        Swal.fire({
            title: 'Tem certeza?',
            text: "Está seguro de remover este registro: ID [ " + id + " ] ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, deletar!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: urlApi + "/origem/" + id ,
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
                        json = $jQuery.parseJSON(data.responseText);
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
         * Salva Tributos do Produto
         */
    $( "#formOrigem" ).on( "submit", function( event ) {
        event.preventDefault();
        }).validate({
            errorClass: "my-error-class",
            validClass: "my-valid-class",
            rules: {
                origem: {
                    required: true
                },
                ncm: {
                    required: true
                },
                cest: {
                    required: true
                }
            },
            messages: {
                origem: {
                    required: "Informe a Origem do Produto?"
                },
                ncm: {
                    required: "Informe o NCM do Produto?"
                },
                cest: {
                    required: "Informe o CEST do Produto?"
                }
            }, submitHandler: function(form,event) {
                event.preventDefault();
                let formData = new FormData($(form)[0]);

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
                            swalWithBootstrapButtons.fire({
                                title: "Sucesso!",
                                text: response.message,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            });
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
                    /*complete:function(response){
                        //console.log(metodo  + "ssssss");
                        json = $.parseJSON(response.responseText);
                        if(json.success) {
                            $('#nome').val('');//POG não submit form com pistola
                            window.setTimeout(function () {
                                $('#divModal').modal('hide');
                                //geraCodigo();
                            }, 1500);
                        }
                    }*/
                });
            }
    });
});
