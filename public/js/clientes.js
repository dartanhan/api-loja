/* globals Chart:false, feather:false */
$(document).ready(function() {

    feather.replace();

    var metodo = '',titulo = '';
    let urlApi = window.location.protocol +"//"+ window.location.hostname + "/api-loja/admin";

    var table  = $('#tableClientes').DataTable({
        "ajax":{
            "method": 'get',
            "url": urlApi + "/cliente/create",
            "data":'',
            "dataSrc":""
        },
        "columns":[
            {"data": "id"},
            {"data": "nome"},
            {"data": "telefone"},
            {"data": "cep"},
            {"data": "logradouro"},
            {"data": "numero"},
            {"data": "complemento"},
            {"data": "bairro"},
            {"data": "localidade"},
            {"data": "uf"},
            {"data": "created_at"},
            {"defaultContent": "<div class='text-center'>" +
                                    "<div class='btn-group'>" +
                                        "<button class='btn btn-primary btn-sm btnEditar'><i class='material-icons'>edit</i></button>&nbsp;&nbsp;" +
                                        "<button class='btn btn-danger btn-sm btnBorrar'><i class='material-icons'>delete</i></button>" +
                                    "</div>" +
                                "</div>"}
        ] ,
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
        },
        "order": [[ 1, "asc" ]]
    });

    //para limpiar los campos antes de dar de Alta una Persona
    $("#btnNuevo").click(function(){
        $("#formClientes").trigger("reset");
        //$('form[name="formCategorias"]')[0].reset();
        $("#metodo").val('POST');
        //$(".modal-header").css( "background-color", "#17a2b8");
        //$(".modal-header").css( "color", "white" );
        //$(".modal-title").text("Cadastrar Categorias");
        $('#modal-title').html('<p><img src="../img/iconfinder_interface-99_4634467.png"></img>&nbsp;<strong>NOVA </strong></p>');

        modal('#divModal');

    });

    //submit para el Alta y Actualización
    $('form[name="formClientes"]').validate({
        rules: {
            nome: {
                required: true
            },
            quantidade: {
                required: true
            }
        },
        messages: {
            nome: {
                required: "Informe o nome!!"
            },
            quantidade: {
                required: "Informe a quantidade!"
            }
        }, submitHandler: function(form,e) {
         //   console.log('Form submitted');
           e.preventDefault();

            metodo = $("#metodo").val();

            if(metodo == 'POST'){
                url = urlApi + "/cliente";
                titulo = "NOVA CATEGORIA";

            }else if(metodo == 'PUT'){
                url = urlApi + "/cliente/update";
                titulo = "EDITANDO CLIENTE";
            }

            $.ajax({
                type: metodo,
                url: url,
                data:$('form[name="formCliente"]').serialize(),
                dataType:"json",
            success: function(data) {
               // console.log(data);
                table.ajax.reload(null, false);
            },
            error: function(data){
                //console.log(data.responseText);
                var json = $.parseJSON(data.responseText);
                $("#modal-title").removeClass( "alert alert-secondary" ).addClass("alert alert-danger");
                $("#modal-title").text(json.message);
            },
            complete:function(data){
                console.log(data.responseText);
                var json = $.parseJSON(data.responseText);
                if(json.success) {
                    $("#modal-title").removeClass( "alert alert-danger" );
                    $("#modal-title").fadeIn(700, function () {
                        $("#modal-title").text(json.message).removeClass("alert alert-secondary").addClass("alert alert-success");
                        window.setTimeout(function () {
                            $('#modal-title').fadeIn('slow').removeClass("alert alert-success")
                                .addClass("alert alert-secondary")
                                .html('<p><img src="../img/iconfinder_interface-99_4634467.png"></img>' +
                                    '&nbsp;<strong>'+titulo+'</strong></p>');
                        }, 3000);
                    });
                }
            }
        });
          return false;
        }
    });

    //Editar
    $(document).on("click", ".btnEditar", function(){
        fila = $(this).closest("tr");
        categoria_id = parseInt(fila.find('td:eq(0)').text()); //capturo o ID
        categoria = fila.find('td:eq(1)').text();
        quantidade = fila.find('td:eq(2)').text();
        status = fila.find('td:eq(3)').text() == 'ATIVO'? 1 : 0;

        $("#metodo").val('PUT');
        $("#cliente_id").val(categoria_id);
        $("#nome").val(categoria);
        $("#telefone").val(quantidade);
        $("#cep").val(status);

        $('#modal-title').html('<p><img src="../img/iconfinder_interface-99_4634467.png"></img>&nbsp;<strong>EDITANDO</strong></p>');
        modal('#divModal');
    });

    //Remover
    $(document).on("click", ".btnBorrar", function(){
        fila = $(this).closest("tr");
        categoria_id = parseInt(fila.find('td:eq(0)').text()) ;
        nome = fila.find('td:eq(1)').text();
        let token = $('form').find('input[name="_token"]').val();

        var resposta = confirm("Está seguro de remover este registro nome: [ "+ nome +" ] ?");
        if (resposta) {
            $.ajax({
                url: urlApi + "/cliente/" + cliente_id ,
                data: {_token: token},
                type: "DELETE",
                datatype:"json",
                success: function(data) {
                    // console.log(data);
                    table.ajax.reload(null, false);
                },
                error: function(data){
                    //console.log(data.responseText);
                    var json = $.parseJSON(data.responseText);
                    $("#alert-title-delete").addClass("alert alert-danger");
                    $("#alert-title-delete").text(json.message);
                },
                complete:function(data){
                 //   console.log(data.responseText);
                    var json = $.parseJSON(data.responseText);
                    if(json.success) {
                        $("#alert-title-delete").text(json.message).removeClass("alert alert-danger").addClass("alert alert-success");
                        $('#divDelete').fadeIn('slow');
                        $('#divDelete').delay(5000).fadeOut(400);

                        /*$("#divDelete").fadeIn(700, function () {
                            $("#alert-title-delete").text(json.message).addClass("alert alert-success");
                            window.setTimeout(function () {
                                $('#divlDelete').fadeOut('slow');
                            }, 3000);
                        });*/
                    }
                }
            });
            return false;
        }
    });
});
