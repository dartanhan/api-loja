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
            "url": urlApi + "/ncm/create",
            "data":'',
            "dataSrc":""
        },
        "columns":[
            {"data": "id"},
            {
                "data": "numero",
            },
            {
                "data": "nome",
            },
            {"data": "created_at"},
            {"data": "updated_at"},
            {"defaultContent": "<div class='text-center'>" +
                                    "<div class='btn-group'>" +
                                        "<button class='btn btn-primary btn-sm btnEditar' " +
                                            " data-bs-toggle=\"modal\" data-bs-target=\"#divModal\" title=\"Editar NCM\">" +
                                            " <i class=\"bi bi-pencil-square\"></i></button>&nbsp;&nbsp;" +
                                        "<button class='btn btn-danger btn-sm btnBorrar' title='Deletar NCM'><i class=\"bi bi-trash\"></i></button>" +
                                    "</div>" +
                                "</div>"
            }
        ] ,
        language: {
            "url": "../public/Portuguese-Brasil.json"
        },
        "order": [[ 0, "asc" ]]
    });

    $('button[id="btnNuevo"]').on('click', function(event) {
        event.preventDefault();
        $('form[name="formNcm"]')[0].reset();

        $("#id").val('');
        $("#metodo").val('POST');
        $('#title-ncm').html("<i class=\"bi bi-file-earmark-plus\"></i>&nbsp;Novo NCM");

        $('#divModal').on('shown.bs.modal', function () {
            $('#numero').trigger('focus')
        });
    });

    $(document).on("click", ".btnEditar", function(event){
        event.preventDefault();

        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(0)').text());
        numero = fila.find('td:eq(1)').text();
        nome  = fila.find('td:eq(2)').text();

        $("#metodo").val('PUT');
        $("#id").val(id);
        $("#numero").val(numero);
        $("#nome").val(nome);

        $('#title-ncm').html('<i class=\"bi bi-pencil-square\"></i> Editando NCM - ID [ '+id+' ]');
    });

    $('form[name="formNcm"]').validate({
        errorClass: "my-error-class",
        validClass: "my-valid-class",
        rules: {
            numero: {
                required: true
            },
            nome: {
                required: true
            }
        },
        messages: {
            numero: {
                required: "Informe o Número do NCM?"
            },
            nome: {
                required: "Informe o Nome/Descrição do NCM?"
            }
        }, submitHandler: function(form,e) {
           e.preventDefault();
            $("#modal-title").addClass( "alert alert-secondary" );
            metodo = $("#metodo").val();
            id = $("#id").val();

            if(metodo === 'POST'){
                url = urlApi + "/ncm";
            }else if(metodo === 'PUT'){
                url = urlApi + "/ncm/"+id;
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

    $(document).on("click", ".btnBorrar", function(event){
        event.preventDefault();
        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(0)').text()) ;
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
                    url: urlApi + "/ncm/" + id ,
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

});
