/* globals Chart:false, feather:false */
$(document).ready(function() {
    let metodo = '',titulo = '', id ='',url,json,fila,valor,taxa,token;
    const urlApi = fncUrl();

    $('#taxa').maskMoney();
    $('#valor').maskMoney();

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
            "url": urlApi + "/cashback/create",
            "data":'',
            "dataSrc":""
        },
        "columns":[
            {"data": "id"},
            {
                "data": "taxa",
                "render": $.fn.dataTable.render.number('.', ',', 2,null , '%').display
            },
            {
                "data": "valor",
                "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
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
                    "</div>"
            }
        ] ,
        /*"columnDefs": [
            { "visible": false, "targets": 5 }
        ],*/
        language: {
            "url": Helpers.asset("Portuguese-Brasil.json")
        },
        "order": [[ 0, "asc" ]]
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
        $('#title-cashback').html("<i class=\"fas fa-money-bill me-1\"></i>Novo CashBack");
    });

    /**
     * Editar
     * **/
    $(document).on("click", ".btnEditar", function(event){
        event.preventDefault();
        $("#modal-title").addClass("alert alert-secondary");

        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(0)').text()); //capturo o ID
        taxa = fila.find('td:eq(1)').text();
        valor  = fila.find('td:eq(2)').text();
       // valor_final  = fila.find('td:eq(3)').text();

        //let currentRow = $(this).closest("tr");
        //let data = $('#table').DataTable().row(currentRow).data();

        $("#metodo").val('PUT');
        $("#id").val(id);
        $("#taxa").val(taxa);
        $("#valor").val(valor);
       // $("#valor_final").val(valor_final);

        $('#title-cashback').html('<i class="fas fa-money-bill me-1">' +
            '</i> Editando CashBack ID: [<span style="color: red"> '+ id  +' </span>]');

    });

    /****
     *
     * SALVA NOVO CASHBACK
     *
     */
    $('form[name="form"]').validate({
        errorClass: "my-error-class",
        validClass: "my-valid-class",
        rules: {
            taxa: {
                required: true
            },
            valor: {
                required: true
            }
        },
        messages: {
            taxa: {
                required: "Informe o Valor da Taxa?"
            },
            valor: {
                required: "Informe o valor?"
            }
        }, submitHandler: function(form,e) {
         //   console.log('Form submitted');
           e.preventDefault();
            $("#modal-title").addClass( "alert alert-secondary" );
            metodo = $("#metodo").val();
            id = $("#id").val();

            if(metodo === 'POST'){
                url = urlApi + "/cashback";
                titulo = "NOVO CASHBACK";

            }else if(metodo === 'PUT'){
                url = urlApi + "/cashback/"+id;
                titulo = "EDITANDO CASHBACK";
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
                   // console.log(data.responseText);
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
                    url: urlApi + "/cashback/" + id ,
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
