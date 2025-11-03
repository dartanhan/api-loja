/* globals Chart:false, feather:false */
$(document).ready(function() {

    let metodo = '', titulo = '';
    let fila,url;
    let status,nome,token,json,quantidade,categoria,id;

    const urlApi = fncUrl();

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    });

    let table  = $('#tableCategorias').DataTable({
        /*"createdRow": function(row, data) {
            if (data.status === `INATIVO`) {
                $(row).addClass('red');
            }
        },*/
        "ajax":{
            "method": 'get',
            "url": urlApi + "/categoria/create",
            "data":'',
            "dataSrc":""
        },
        "columns":[
            {"data": "id"},
            {"data": "nome"},
            {"data": "slug"},
            {"data": "defaultContent",
                render: function (data, type, row) {
                    return '<img src="'+Helpers.asset("storage/categorias/"+row.id+"/"+ row.imagem)+'" class="img-datatable">';
                }
            },
            {"data": "quantidade"},
            {"data": "defaultContent",
                render: function (data, type, row) {
                    let status = "<span class=\"badge badge-pill badge-success\">"+row.status+"</span>";
                    if(row.status === "INATIVO"){
                        status = "<span class=\"badge badge-pill badge-danger\">"+row.status+"</span>";
                    }
                    return status;
                }
            },
            {"data": "created_at"},
            {"data": "updated_at"},
            {"defaultContent": "<div class='text-center'>" +
                                    "<div class='btn-group'>" +
                                        "<button class='btn btn-primary btn-sm btnEditar' " +
                                             "data-toggle=\"modal\" data-target=\"#divModal\">" +
                                                "<i class='material-icons'>edit</i></button>&nbsp;&nbsp;" +
                                        "<button class='btn btn-danger btn-sm btnBorrar'><i class='material-icons'>delete</i></button>" +
                                    "</div>" +
                                "</div>"}
        ] ,
        language: {
            "url": Helpers.asset("Portuguese-Brasil.json")
        },
        "order": [[ 1, "asc" ]]
    });

    /**
     * AÇÃO DE ABRIR O MODAL
     * Novo
     * */
    $("#btnNuevo").click(function(event){
        event.preventDefault();
        $('form[name="form"]')[0].reset();

        $("#id").val('');
        $("#metodo").val('POST');
        $('#modal-title').html('<p><strong>NOVA CATEGORIA</strong></p>');
        $("#modal-title").addClass( "alert alert-secondary" );
    });

    /**
     * Editar
     * */
    $(document).on("click", ".btnEditar", function(event){
        event.preventDefault();

        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(0)').text()); //capturo o ID
        categoria = fila.find('td:eq(1)').text();
        let slug = fila.find('td:eq(2)').text();
        quantidade = fila.find('td:eq(4)').text()
        status = fila.find('td:eq(5)').text() === 'ATIVO'? 1 : 0;

        $("#metodo").val('PUT');
        $("#id").val(id);
        $("#nome").val(categoria);
        $("#slug").val(slug);
        //$("#image").val(image);
        $("#status").val(status);

        $('#modal-title').html('<p><strong>EDITANDO CATEGORIA</strong></p>');
    });


    /**
     * AÇÃO DE GRAVAR NOVA CATEGORIA COM VALIDAÇÃO DE CAMPOS
     * */
    $('form[name="form"]').validate({
        errorClass: "my-error-class",
        validClass: "my-valid-class",
        rules: {
            nome: {
                required: true
            },
            image: {
                required: false
            }
        },
        messages: {
            nome: {
                required: "Informe o nome da categoria!!"
            },
            image: {
                required: "Informe a imagem!"
            }
        }, submitHandler: function(form,e) {
         //   console.log('Form submitted');
           e.preventDefault();
            $("#modal-title").addClass( "alert alert-secondary" );
            metodo = $("#metodo").val();

            if(metodo === 'POST'){
                url = urlApi + "/categoria";
                titulo = "NOVA CATEGORIA";

            }else if(metodo === 'PUT'){
                url = urlApi + "/categoria/update";
                titulo = "EDITANDO CATEGORIA";
            }

            $.ajax({
                type: metodo,
                url: url,
                data:$('form[name="form"]').serialize(),
                dataType:"json",
                beforeSend: function () {
                    $("#modal-title").removeClass( "alert alert-danger" );
                    $('#modal-title').html('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>');
                    $("#modal-title").addClass( "alert alert-info" );
                },
                success: function(data) {
                    //console.log(data.success);
                    if(data.success) {
                        swalWithBootstrapButtons.fire({
                            title: titulo,
                            text: data.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        //table.ajax.reload(null, false);
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
                                $('#divModal').modal('hide');
                                window.location.reload();
                            }, 1500);
                        }
                }
            });
         //return false;
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
                    url: urlApi + "/categoria/" + id ,
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
                        }else{
                            swalWithBootstrapButtons.fire({
                                title: 'Error!',
                                text: data.message,
                                icon: 'danger',
                                showConfirmButton: false,
                                timer: 1500
                            });
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
