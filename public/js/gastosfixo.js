/* globals Chart:false, feather:false */
$(document).ready(function() {

    feather.replace();
    
    const urlApi = fncUrl();

    $('#valor').maskMoney();

    let metodo = '', titulo = '';
    let fila = "";
    let descricao = "";
    let valor = "";
    let id = "";
    let json,url;
    let table1;
    let idGasto;

    $('#formFiltro').on('submit', function(e) {
    //$('form[name="formFiltro"]').submit(function (e) {
        table1.ajax.reload();
        e.preventDefault();
       // dataTable1();

       // console.log($('input[name="_token"]').val());
//$('form[name="formFiltro"]').serialize();
     /*   $.ajax({
            url: urlApi + "/gastosfixofiltro",
            type: "post",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                console.log(response);
            },
            error: function(response){
                console.log(response);
            }
        });*/
    });

        //Tabela 1 Gastos Fixos
         table1 = $('#table1').DataTable({
            "ajax": {
                processing: true,
                serverSide: true,
                "method": 'post',
                //"url": urlApi + "/gastosfixo/1",
                "url": urlApi + "/gastosfixofiltro",
                "headers": {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
                "data": function (data) {
                    data.dateini = $('input[name="dateini"]').val();
                    data.datefim = $('input[name="datefim"]').val();
                },
                "dataSrc": function (data) {

                    $("#totalFixo").html(data.totalGasto);
                    $("#totalMes").html(data.totalMes);
                    $("#totalLucro").html(data.totalLucro);
                    if (data.lucro < 0)
                        $("#totalLucro").addClass("text-danger");
                    return data.data;
                },
            },
            "columns": [
                {"data": "id"},
                {"data": "descricao"},
                {
                    "data": "valor",
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                },
                {"data": "created_at"},
                {"data": "updated_at"},
                {
                    "defaultContent": "<div class='text-center'>" +
                        "<div class='btn-group'>" +
                        "<button class='btn btn-primary btn-sm btnEditar' id='1'><i class='material-icons'>edit</i></button>&nbsp;&nbsp;" +
                        "<button class='btn btn-danger btn-sm btnBorrar' id='1'><i class='material-icons'>delete</i></button>" +
                        "</div>" +
                        "</div>"
                }
            ],
            language: {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
            },
            "order": [[3, "desc"]]
        });

    //Tabela 2 Gastos Variáveis
      let table2 =  $('#table2').DataTable({
            "ajax":{
                "method": 'get',
                "url": urlApi + "/gastosfixo/2",
                "data":'',
                "dataSrc":function (data) {
                    $("#totalVariado").html(data.totalGasto);
                    $("#totalMes").html(data.totalMes);
                    $("#totalLucro").html(data.totalLucro);
                    if(data.lucro < 0)
                        $("#totalLucro").addClass( "text-danger" );

                    return data.data;
                }
            },
            "columns": [
                {"data": "id"},
                {"data": "descricao"},
                {
                    "data": "valor",
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                },
                {"data": "created_at"},
                {"data": "updated_at"},
                {
                    "defaultContent": "<div class='text-center'>" +
                        "<div class='btn-group'>" +
                        "<button class='btn btn-primary btn-sm btnEditar' id='2'><i class='material-icons'>edit</i></button>&nbsp;&nbsp;" +
                        "<button class='btn btn-danger btn-sm btnBorrar' id='2'><i class='material-icons'>delete</i></button>" +
                        "</div>" +
                        "</div>"
                }
            ],
            language: {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
            },
            "order": [[3, "desc"]]
        });

    //para limpiar los campos antes de dar de Alta una Persona
    $("#btnNovo").click(function(){
        $("#formModal").trigger("reset");
        //$('form[name="formCategorias"]')[0].reset();
        $("#metodo").val('POST');
        //$(".modal-header").css( "background-color", "#17a2b8");
        //$(".modal-header").css( "color", "white" );
        //$(".modal-title").text("Cadastrar Categorias");
        $('#modal-title').html('<p><img src="../img/expenses.png" alt=""/>&nbsp;<strong>NOVA GASTO FIXO</strong></p>');

        modal('#divModal');

    });

    //submit para el Alta y Actualización
    $('form[name="formModal"]').validate({
        rules: {
            descricao: {
                required: true
            },
            valor: {
                required: true
            },
            tipo_gasto: {
                required: true
            }
        },
        messages: {
            descricao: {
                required: "Informe a Descrição do Gasto?"
            },
            valor: {
                required: "Informe o Valor do Gasto?"
            },
            tipo_gasto: {
                required: "Selecione o Tipo do Gasto?"
            }
        }, submitHandler: function(form,e) {
         //   console.log('Form submitted');
           e.preventDefault();

            metodo = $("#metodo").val();

            if(metodo === 'POST'){
                url = urlApi + "/gastosfixo";
                titulo = "NOVA";

            }else if(metodo === 'PUT'){
                url = urlApi + "/gastosfixo/update";
                titulo = "EDITANDO";
            }

            $.ajax({
                type: metodo,
                url: url,
                data:$('form[name="formModal"]').serialize(),
                dataType:"json",
            success: function(data) {
                //console.log(data);
                //renderTable($("tipo_gato").val());
                id = $("#tipo_gasto").val();
                //console.log(id);
                if(id == 1)
                    table1.ajax.reload(null, false);
                else
                    table2.ajax.reload(null, false);

                if(data.success) {
                    //$("#modal-title").removeClass( "alert alert-danger" );
                    $("#modal-title").removeClass( "alert alert-danger" ).fadeIn(700, function () {
                        $("#modal-title").text(data.message).removeClass("alert alert-secondary").addClass("alert alert-success");
                      /*  window.setTimeout(function () {
                            $('#divModal').modal('dispose');
                        }, 3000);*/
                    });
                }
            },
            error: function(data){
                //console.log(data);
                json = $.parseJSON(data.responseText);

                let errorString = "<ul>";
                $.each(json.message, function( key, value) {
                    errorString += "<li>" + value + "</li>";
                });
                errorString += "</ul>";

                $("#modal-title").removeClass( "alert alert-secondary" ).addClass("alert alert-danger").html(errorString);
                }
        });
          return false;
        }
    });

    //Editar
    $(document).on("click", ".btnEditar", function(){
        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(0)').text());
        descricao = fila.find('td:eq(1)').text();
        valor = fila.find('td:eq(2)').text();

        //status = fila.find('td:eq(3)').text() == 'ATIVO'? 1 : 0;

        $("#metodo").val('PUT');
        $("#id").val(id);
        $("#descricao").val(descricao);
        $("#valor").val(valor);
        //pega o id do gasto para para poder atualizar a datatabble
        idGasto = $(this).attr('id');
        $("#tipo_gasto").val(idGasto);

        $("#modal-title").removeClass( "alert alert-success" ).addClass("alert alert-secondary");
        $('#modal-title').html('<p><img src="../img/expenses.png" alt=""/>&nbsp;<strong>EDITANDO REGISTRO: ID [<span style="color: red"> '+ id  +' </span>] NOME [<span style="color: red"> '+ descricao  +' </span>]</strong></p>');
        modal('#divModal');
    });

    //Remover
    $(document).on("click", ".btnBorrar", function(){
        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(0)').text()) ;
        descricao = fila.find('td:eq(1)').text();
        valor = fila.find('td:eq(2)').text();
        let token = $('form').find('input[name="_token"]').val();

        //pega o id do gasto para para poder atualizar a datatabble
        idGasto = $(this).attr('id');

        const resposta = confirm("Está seguro de remover este registro nome: [ " + descricao + " ] ?");
        if (resposta) {
            $.ajax({
                url: urlApi + "/gastosfixo/" + id ,
                data: {_token: token},
                type: "DELETE",
                datatype:"json",
                success: function() {
                    // console.log(data);
                    if(idGasto == 1)
                        table1.ajax.reload(null, false);
                    else
                        table2.ajax.reload(null, false);
                },
                error: function(data){
                    //console.log(data.responseText);
                    json = $.parseJSON(data.responseText);
                    $("#alert-title-delete").addClass("alert alert-danger").text(json.message);
                },
                complete:function(data){
                 //   console.log(data.responseText);
                    json = $.parseJSON(data.responseText);
                    if(json.success) {
                        $("#alert-title-delete").text(json.message).removeClass("alert alert-danger").addClass("alert alert-success");
                        $('#divDelete').fadeIn('slow').delay(5000).fadeOut(400);

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


    $('.dateini').datepicker({

        format: 'dd/mm/yyyy',
        autoclose:true,
        language:'pt-BR',
        //multidate:true,
        todayBtn:true,
        todayHighlight:true
    });

    $('.datefim').datepicker({

        format: 'dd/mm/yyyy',
        autoclose:true,
        language:'pt-BR',
        //multidate:true,
        // todayBtn:true,
        todayHighlight:true
    });

    function dataTable1(){
        table1 =  $('#table1').DataTable({
            "ajax": {
                "method": 'post',
                //"url": urlApi + "/gastosfixo/1",
                "url": urlApi + "/gastosfixofiltro",
                "headers": {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
                "data": function (data) {
                    data.dateini = $('input[name="dateini"]').val();
                    data.datefim = $('input[name="datefim"]').val();
                },
                "dataSrc": function (data) {

                    $("#totalFixo").html(data.totalGasto);
                    $("#totalMes").html(data.totalMes);
                    $("#totalLucro").html(data.totalLucro);
                    if (data.lucro < 0)
                        $("#totalLucro").addClass("text-danger");
                    return data.data;
                },
            },
            "columns": [
                {"data": "id"},
                {"data": "descricao"},
                {
                    "data": "valor",
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                },
                {"data": "created_at"},
                {"data": "updated_at"},
                {
                    "defaultContent": "<div class='text-center'>" +
                        "<div class='btn-group'>" +
                        "<button class='btn btn-primary btn-sm btnEditar' id='1'><i class='material-icons'>edit</i></button>&nbsp;&nbsp;" +
                        "<button class='btn btn-danger btn-sm btnBorrar' id='1'><i class='material-icons'>delete</i></button>" +
                        "</div>" +
                        "</div>"
                }
            ],
            language: {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
            },
            "order": [[3, "desc"]]
        });
    }
});
