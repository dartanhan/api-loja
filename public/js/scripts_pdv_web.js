/**
 * */
let url = window.location.protocol +"//"+ window.location.hostname + "/api-loja/api";
$(document).ready(function() {
    let min = 1;
    let max = 99999;
    $("#codigo_venda").val("KN"+Math.floor(Math.random() * (max - min + 1)) + min);

    document.body.addEventListener('keypress', function (event) {
        const key = event.key;
        const code = event.keyCode;
        console.log(`Key: ${key}, Code ${code}`);
    });
});
const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger'
    },
    buttonsStyling: false
});

let table = $('#table').DataTable({
    "createdRow": function(row, data) {
        //console.log(data.qtdMinFeira);
        if (data.quantidade >= 3) {
            $(row).addClass('blue').attr("title", "Produto no Atacado! ");
        }

    },
    "ajax":{
        "method": 'get',
        "processing": true,
        "serverSide": true,
        "url": url + "/vendaapi/getPdv",
        "data":'',
        "dataSrc":"",
        cache: false,
    },

    "columns": [
        { "data": "id", "defaultContent": "" },
        { "data": "codigo_produto", "defaultContent": "" },
        { "data": "descricao" , "defaultContent": ""},
        { "data": "quantidade" , "defaultContent": ""},
        {
            "data": "valor",
            "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
        },
        {"defaultContent":
                "<div class='text-center'>" +
                "<div class='btn-group'>" +
                //"<button class='btn btn-primary btn-xs btnEditar' title='Editar Produto' ><i class='material-icons'>edit</i></button>&nbsp;" +
                "<button class='btn btn-danger btn-xs btnBorrar' title='Excluir Produto'><i class='material-icons'>delete</i></button>&nbsp;" +
                "<button class='btn btn-primary btn-xs btnQtd' title='Diminuir Quantidade'><i class='material-icons'>format_list_numbered_rtl</i></button>&nbsp;" +
                "</div>"
        }
    ],
    scrollX:true,
    select: false,
    "columnDefs": [
        {
            "targets": [ 0 ],
            "visible": false,
            "searchable":false
        }
    ],
    language: {
        "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
    },
   // "order": [[ 1, "asc" ]],
});

$( "#codigo_produto" ).autocomplete({
    minLength: 2,
    minChars:0,
    max:10,
    selectFirst: false,
    delay:10,
    source: function (request,response) {
        $.ajax({
            type: "GET",
            url: "http://127.0.0.1/api-loja/api/vendaapi/getProducts",
            dataType: "json",
            data: request,
            success: function (data) {
                //response($.ui.autocomplete.filter(resp, request.term));
                 //console.log(data);
                response(data);
            },
            error: function (response) {
                console.log(response);
            }
        })
    },focus: function (event, ui) {
        $('#descricao').val(ui.item.label);
        return false;
    },select: function (event, ui) {
        $('#descricao').val(ui.item.label);
        $('#codigo_produto').val(ui.item.value);
        carregarDados(ui.item.value);
        return false;
    }
}).autocomplete("instance")._renderItem = function(ul,item){
    return $("<li>").append("<a href=\"#\" class=\"list-group-item \"><b>Código:</b>" + item.value + "<br>"+ item.product + " - "+ item.label+"</a>").appendTo(ul);
};


// Função para carregar os dados da consulta nos respectivos campos
function carregarDados(codigo_produto){
   // let codigo_produto = $('#codigo_produto').val();


    if(codigo_produto !== "" && codigo_produto.length >= 2){
        $.ajax({
            //url: "http://127.0.0.1/api-loja/api/auth/vendaapi",
            url: "http://127.0.0.1/api-loja/api/vendaapi",
            type: 'GET',
            dataType: "json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.setRequestHeader('product-code', codigo_produto);
               // console.log(codigo_produto);
            },
            success: function( data ) {

                if (data.success) {
                    //console.log(data);
                    $('#codigo_produto').val('');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                   // table.row.add([data.codigo_produto,data.descricao,1,data.valor_venda]).draw();
                    $.ajax({
                        //url: "http://127.0.0.1/api-loja/api/auth/vendaapi",
                        url: "http://127.0.0.1/api-loja/api/vendaapi/saveProductsSale",
                        type: 'POST',
                        dataType: "json",
                        data: {
                            id: data.id,
                            valor_atacado: data.valor_atacado,
                            valor_venda: data.valor_venda,
                            quantidade: data.quantidade,
                            codigo_produto: data.codigo_produto,
                            descricao: data.descricao,
                            fornecedor_id: data.fornecedor_id,
                            categoria_id: data.categoria_id
                        },
                        success: function( data ) {
                        //    console.log(data);
                            table.ajax.reload();
                        },
                        error : function (data) {
                            swalWithBootstrapButtons.fire({
                                title: "Algo errado ocorreu!",
                                text: data,
                                icon: 'error',
                                showConfirmButton: false,
                                //timer: 1500
                            });
                        }
                    });
                } else {
                    //console.log("Algo errado ocorreu" + data);
                    swalWithBootstrapButtons.fire({
                        title: "Algo errado ocorreu!",
                        text: data,
                        icon: 'error',
                        showConfirmButton: false,
                        //timer: 1500
                    });
                }
            },error:function (data) {
                swalWithBootstrapButtons.fire({
                    title: "Algo errado ocorreu!",
                    text: data,
                    icon: 'error',
                    showConfirmButton: false,
                    //timer: 1500
                });
            }
        });
    }
}


function detecta(event)
{
    var tecla = event.key;
    if (tecla == "a")
        alert ("Você apertou 'a'");
    if (tecla == "e")
        alert ("Você apertou 'e'");
    if (tecla == "i")
        alert ("Você apertou 'i'");
    if (tecla == "o")
        alert ("Você apertou 'o'");
    if (tecla == "u")
        alert ("Você apertou 'u'");
}
