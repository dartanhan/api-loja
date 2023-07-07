/* globals Chart:false, feather:false */

$(document).ready(function() {
    const urlApi = fncUrl();

    let metodo = '', titulo = '', url,fila,id,nome,token,json,status,table,table2;

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    });

    table = $('#table').DataTable({
        "createdRow": function(row, data) {
            if (data.status === "INATIVO") {
                $(row).find('td:eq(2)').css('color', '#ff1a1a');
            }else{
                $(row).find('td:eq(2)').css('color', '#0a58ca');
            }
            $(row).find('td:eq(2)').css({ "font-weight": "bold" });
        },
        "ajax":{
            "method": 'get',
            "url": urlApi + "/fornecedor/3",
            "data":'',
            "dataSrc":""
        },
        "columns":[
            {"data": "id"},
            {"data": "nome"},
            {"data": "status"},
            {"data": "created_at"},
            {"data": "updated_at"},
            {"defaultContent": "<div class='text-center'>" +
                    "<div class='btn-group'>" +
                    "<button class='btn btn-primary btn-sm btnEditar' " +
                    "data-bs-toggle=\"modal\" data-bs-target=\"#divModal\">" +
                    "<i class='material-icons'>edit</i></button>&nbsp;&nbsp;" +
                    "<button class='btn btn-danger btn-sm btnBorrar'><i class='material-icons'>delete</i></button>" +
                    "</div>" +
                    "</div>"}
        ] ,
        "columnDefs": [
            {
                "targets": [  ],
                "visible": false,
                "searchable":true
            }
        ],
        "order": [[ 1, "asc" ]]
        ,	language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
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
        $('#modal-title').html('<strong>Novo Fornecedor</strong>');

    });

    /**
     * Editar
     * **/
    $(document).on("click", ".btnEditar", function(){
        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(0)').text()); //capturo o ID
        nome = fila.find('td:eq(1)').text();
        status = fila.find('td:eq(2)').text() === 'ATIVO'? 1 : 0;

        $("#metodo").val('PUT');
        $("#id").val(id);
        $("#nome").val(nome);
        $("#status").val(status);

        $('#modal-title').html('<strong>Editando Fornecedor</strong>');
        $('#fornecedor_new').attr('disabled', true);
        $('#message-alert-update').attr("hidden", "hidden");
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
            }
        },
        messages: {
            nome: {
                required: "Informe a Descrição do Fornecedor!"
            }
        }, submitHandler: function(form,e) {
            e.preventDefault();


            metodo = $("#metodo").val();

            if(metodo === 'POST'){
                url = urlApi + "/fornecedor";
                titulo = "NOVO FORNECEDOR";

            }else if(metodo === 'PUT'){
                url = urlApi + "/fornecedor/update";
                titulo = "EDITANDO FORNECEDOR";
            }

            $.ajax({
                url: url,
                type:metodo,
                data:$('form[name="form"]').serialize(),
                dataType:'json',
                beforeSend: function () {

                    $('#modal-title').html('<strong>Aguarde... <span class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></span></strong>');

                },
                success: function(data) {
                    console.log(data);

                    if(data.success) {
                        swalWithBootstrapButtons.fire({
                            title: titulo,
                            text: data.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        table.ajax.reload(null, false);

                        //usado para no combo de fornecedor não ficar indo sempre no banco buscar as informações
                        localStorage.clear();

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
        let token = $('form').find('input[name="_token"]').val();

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
                    url: urlApi + "/fornecedor/" + id ,
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

let fncComboFornecedor =  function(value){
    //console.log(value);
    let id_fornecedor = parseInt($("#id").val());
    if(parseInt(value) === 0){
        //console.log("inativo " + value);
        $('#fornecedor_new').attr('disabled', false);

        $("#fornecedor_new option[value='"+id_fornecedor+"']").remove();
        $("#message-alert-update").removeAttr('hidden');
        $('#message-alert-update').html("<i class=\"fas fa-lg  fa-triangle-exclamation\"></i> Ao desabilitar um fornecedor, você deve migrar os produtos, para outro fornecdor <strong>ATIVO</strong>");


    } else {
        //   console.log("ativo " +value);
        $('#fornecedor_new').attr('disabled', true);
        $('#message-alert-update').attr("hidden", "hidden");
    }
};
