/* globals Chart:false, feather:false */
$(document).ready(function() {

    feather.replace();

    let token = '', produto_id = '', fila='', json = '', id='', nome='', resposta = '';
    let url = window.location.protocol +"//"+ window.location.hostname + "/api-loja/admin";

	  let table = $('#table').DataTable({
          "ajax":{
              "method": 'get',
              "processing": true,
              "serverSide": true,
              "url": url + "/productBlock/create",
              "data":'',
              "dataSrc":"",
              cache: false,
          },
          scrollX:true,
		"columns": [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
                { "data" : "id", "defaultContent": ""},
                { "data": "codigo_produto", "defaultContent": "" },
                { "data": "descricao" , "defaultContent": ""},
                { "data": "status" , "defaultContent": ""},
                {
                    "data": "valor_produto" ,
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                },{
                    "data": "valor_dinheiro",
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                },{
                    "data": "valor_cartao",
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display

                },
                {
                    "defaultContent":
                        "<div class='text-center'>" +
                            "<div class='btn-group'>" +
                                "<button class='btn btn-danger btn-xs btnBorrar' title='Excluir Produto'><i class='material-icons'>delete</i></button>&nbsp;" +
                                "<button class='btn btn-success btn-xs btnBlock' title='Desbloquear Produto'><i class='material-icons'>lock_open</i></button>" +
                            "</div>" +
                        "</div>"
                }
		],"columnDefs": [
            {
                "targets": [0],
                "visible": false,
                "defaultContent": ""
            }
        ],
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
        },
        "order": [[ 0, "desc" ]]
	});


    /**
     * DESBLOQUEAR PRODUTO
     * */
    $(document).on("click", ".btnBlock", function(){
        fila = $(this).closest("tr");
        produto_id = parseInt(fila.find('td:eq(0)').text()) ;
        token = $('div').find('input[name="_token"]').val();
        nome = fila.find('td:eq(2)').text();

        resposta = confirm("Está seguro de desbloquear este produto : [ "+ nome +" ] ?");
        if (resposta) {
            $.ajax({
                url: url + "/productBlock/" + produto_id ,
                data: {_token: token},
                cache: false,
                type: "PUT",
                datatype:"json",
                success: function(data) {
                    // console.log(data);
                    table.ajax.reload(null, false);
                },
                error: function(data){
                    //console.log(data.responseText);
                    json = $.parseJSON(data.responseText);
                    $("#alert-title").addClass("alert alert-danger").text(json.message);
                },
                complete:function(data){
                  //  console.log(data.responseText);
                    json = $.parseJSON(data.responseText);
                    if(json.success) {
                        $("#alert-title").text(json.message).removeClass("alert alert-danger").addClass("alert alert-success");
                        $('#divAlert').fadeIn('slow').delay(5000).fadeOut(400);

                    }
                }
            });
            return false;
        }
    });
});
