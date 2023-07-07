/* globals Chart:false, feather:false */
$(document).ready(function() {
    const urlApi = fncUrl();

    let metodo = '',titulo = '', id ='',url,json,fila,nome,taxa,token;
    
    $('#valor_taxa').maskMoney();

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
            "url": urlApi + "/tarifa/create",
            "data":'',
            "dataSrc":""
        },
        "columns":[
            {"data": "id"},
            {"data": "forma_nome"},
            {
                "data": "valor_taxa",
                "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
            },
            {"data": "created_at"},
            {"data": "updated_at"},
            {"data": "forma_id"},
            {"defaultContent": "<div class='text-center'>" +
                    "<div class='btn-group'>" +
                    "<button class='btn btn-primary btn-sm btnEditar' " +
                    "data-bs-toggle=\"modal\" data-bs-target=\"#divModal\">" +
                    "<i class='material-icons'>edit</i></button>&nbsp;&nbsp;" +
                    "<button class='btn btn-danger btn-sm btnBorrar'><i class='material-icons'>delete</i></button>" +
                    "</div>" +
                    "</div>"
            }
        ] ,
        //esconde coluna forma_id
        "columnDefs": [
            { "visible": false, "targets": 5 }
        ],
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
        },
        "order": [[ 2, "asc" ]]
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
        $('#modal-title').html('<p><img src="../public/img/iconfinder_price-tag_1291777.png"/> <strong>NOVA TARIFA</strong></p>');
        $("#modal-title").addClass("alert alert-secondary");
    });

    /**
     * Editar
     * **/
    $(document).on("click", ".btnEditar", function(event){
        event.preventDefault();
        $("#modal-title").addClass("alert alert-secondary");

        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(0)').text()); //capturo o ID
        nome  = fila.find('td:eq(1)').text();
        taxa = fila.find('td:eq(2)').text();

        let currentRow = $(this).closest("tr");
        let data = $('#table').DataTable().row(currentRow).data();

        $("#metodo").val('PUT');
        $("#id").val(id);
        $("#valor_taxa").val(taxa);
        $("#forma_id").val(data['forma_id']);

        $('#modal-title').html('<p><strong>EDITANDO REGISTRO: ID [<span style="color: red"> '+ id  +' </span>]<br/> NOME [<span style="color: red"> '+ nome  +' </span>]</strong></p>');
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
            valor_taxa: {
                required: false
            },
            taxas: {
                required: true
            }
        },
        messages: {
            valor_taxa: {
                required: "Informe o Valor da Taxa?"
            },
            taxas: {
                required: "Informe a Tipo de Pagamento?"
            }
        }, submitHandler: function(form,e) {
         //   console.log('Form submitted');
           e.preventDefault();
            $("#modal-title").addClass( "alert alert-secondary" );
            metodo = $("#metodo").val();
            id = $("#id").val();


            if(metodo === 'POST'){
                url = urlApi + "/tarifa";
                titulo = "NOVA TAXA";

            }else if(metodo === 'PUT'){
                url = urlApi + "/tarifa/"+id;
                titulo = "EDITANDO TAXA";
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
        taxa = fila.find('td:eq(2)').text();
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
                    url: urlApi + "/tarifa/" + id ,
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

    /**
     * pega as formas de pagamentos para vincular com a tarifa
     * */
    fetch(urlApi+ "/payment/asc")
        .then(function (response) {
            return response.json()
        })
        .then(function (response) {
           //console.log(response);
            $("#forma_id").html("");
            $("#forma_id").append('<option value="">Selecione</option>');
            response.forEach(function (ret) {
                //    console.log(ret.id + " - " + ret.nome);
                $("#forma_id").append('<option value="'+ret.id+'">'+ret.nome+'</option>');
            });
    });
});
