import {formatMoney,getFormattedDate} from './comum.js';

const url = fncUrl();
let json = "",table = "";

$(function() {

    table = $('#table').DataTable({
        ajax:{
            method: 'get',
            url: url + "/produto/create",
            processing: true,
            serverSide: true,
            cache: false,
            data: function (d) {
                d._ = $.now(); // isso adiciona um timestamp e impede o cache
            }
        },
        "columns": [
            {
                "className": 'details-control',
                "orderable": false,
                "data": null,
                "defaultContent": ''
            },
            {"data": "id", "defaultContent": ""},
            {"data": "codigo_produto", "defaultContent": ""},
            {"data": "imagem",
                render: function (data, type, row) {
                    if(row.produto_imagens.length > 0){
                        let path = row.produto_imagens[0].path; // Pegar o caminho da primeira imagem
                        return '<img src="../public/storage/product/' + row.id + '/' + path + '" class="image img-datatable"/>';
                    }else{
                        return '<img src="../public/storage/produtos/not-image.png" class="img-datatable"/>';
                    }
                }
            },
            {"data": "descricao", "defaultContent": ""},
            {"data": "categoria", "defaultContent": ""},
            {
                "data": "status",
                render: function (data, type, row) {
                    return "<span class=\"badge bg-success\">ATIVO</span>";
                }
            },
            {"data": "created", "defaultContent": ""},
            {"data": "updated", "defaultContent": ""},
            // {
            //     "data": "defaultContent",
            //     render: function (data, type, row) {
            //         let image = "../public/storage/produtos/not-image.png";
            //         let image_id = null;
            //         let path = null;
            //         //if(row.imagem !== null){
            //         if(row.produto_imagens.length > 0){
            //             path = row.produto_imagens[0].path; // Pegar o caminho da primeira imagem
            //             image = '../public/storage/product/'+row.id+'/'+ path;
            //             image_id = row.produto_imagens[0].id;
            //         }
            //
            //         return "<div class='text-center'>" +
            //             "<i class=\"bi-image\" " +
            //             "   style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" " +
            //             "   title='Imagem do Produto' data-bs-toggle=\"modal\" " +
            //             "   data-bs-target=\"#divModalImageProduct\" data-id='"+row.id+"' " +
            //             "   data-image-preview='"+image+"'  data-path='"+path+"' data-flag-image='0'  " +
            //             "   data-image-id='"+image_id+"'></i>"+
            //             "<i class=\"bi-pencil-square btnUpdateProduct\" " +
            //             "               style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" " +
            //             "               title=\"Atualizar Produto\" data-id='"+row.id+"'>" +
            //             "</i>" +
            //             "</div>";
            //     }
            // }

        ],
        scrollX: true,
        select: false,
        "columnDefs": [
            {
                "targets": [],
                "visible": false,
                "searchable": false
            }
        ],
        language: {
            "url": "../public/Portuguese-Brasil.json"
        },
        "order": [[0, "desc"]],
        //"order": [[ 0, 'desc' ], [ 2, 'asc' ]]
    });


    /**
     * Add event listener for opening and closing details
     */
    $('#table tbody').on('click', 'td.details-control', function (event) {
        event.preventDefault();

        let tr = $(this).closest('tr');
        let row = table.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            // row.child( format(row.data()) ).show();
            tr.addClass('shown');

            let tmpRow  ="<table class='table table-striped table-condensed'>" +
                "<thead class=\"text-center\">" +
                "<tr class='bg-secondary '>" +
                "<th>IMAGEM</th>" +
                "<th>SUB CÓDIGO</th>" +
                "<th>VARIAÇÃO</th>" +
                "<th>QTD</th>" +
                "<th>ESTOQUE</th>" +
                "<th>VAREJO</th>" +
                "<th>ATACADO</th>" +
               // "<th>PRODUTO</th>" +
               // "<th>DESCONTO EM %</th>" +
                "<th>STATUS</th>" +
                //"<th>AÇÃO</th>" +
                "</tr>" +
                "</thead>";

            $.ajax({
                url: url + "/produto/getProducts/"+row.data().id,
                type: 'GET',
                data: '',
                dataType: 'json',
                beforeSend: function () {
                    row.child('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>').show();
                },
                success: function (response) {
                    //  console.log(response.data.products);
                    if (response.success) {
                        let arrayProducts = JSON.stringify(response.data.products);

                        JSON.parse(arrayProducts).forEach(async function (arrayItem, index, fullArray) {
                             console.log(arrayItem.subcodigo);
                            let image = arrayItem.path !== null ?
                                "<img src='../public/storage/" + arrayItem.path + "' class=\"image img-datatable\" alt=\"\" title='" + arrayItem.variacao + "'/>" :
                                "<img src='../public/storage/produtos/not-image.png' class=\"image img-datatable\" alt=\"\" title='" + arrayItem.variacao + "'/>"

                            let image_filho = "../public/storage/produtos/not-image.png";
                            if(arrayItem.path !== null){
                                image_filho = '../public/storage/'+arrayItem.path;
                            }

                            tmpRow += "<tr>" +
                                "<td>"+image+"</td>" +
                                "<td>" + arrayItem.subcodigo + "</td>" +
                                "<td>" + arrayItem.variacao + "</td>" +
                                "<td>" + arrayItem.quantidade + "</td>" +
                                "<td>" + arrayItem.estoque + "</td>" +
                                "<td>" + formatMoney(arrayItem.valor_varejo) + "</td>" +
                                "<td>" + formatMoney(arrayItem.valor_atacado_10un) + "</td>" +
                               // "<td>" + formatMoney(arrayItem.valor_produto) + "</td>" +
                               // "<td>" + formatMoney(arrayItem.percentage) + "</td>" +
                                "<td>" + "<span class='badge bg-success'>"+arrayItem.status+"</span>" + "</td>" +
                                // "<td>" +
                                // "   <i class=\"bi-image\" " +
                                // "   style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" " +
                                // "   title='Imagem da Variação do Produto' data-bs-toggle=\"modal\" " +
                                // "   data-bs-target=\"#divModalImageProduct\" data-variacao-id='"+arrayItem.id+"' " +
                                // "   data-subcodigo='"+arrayItem.subcodigo+"' data-image-id='"+arrayItem.id_image+"'" +
                                // "   data-image-preview='"+image_filho+"'  data-path='"+arrayItem.path+"' data-flag-image='1'>" +
                                // "   </i>"+
                                // "   <i class=\"bi-pencil-square openModalBtn\"  " +
                                // "       style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" " +
                                // "       title=\"Atualizar Produto\" data-id='"+arrayItem.id+"'>" +
                                // "   </i>" +
                                // "</td>"+
                                "</tr>"
                        });

                        tmpRow  +=      "</table>";
                        row.child(tmpRow).show();
                    }
                },
                error: function (response) {
                    json = $.parseJSON(response.responseText);
                    $("#modal-title").addClass("alert alert-danger");
                    $('#modal-title').html('<p><i class="fas fa-exclamation-circle"></i>&nbsp;<strong>' + json.message + '</strong></p>');
                    Swal.fire({
                        title: 'error!',
                        text: json.message,
                        icon: 'error'
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                }
            });

        }
    });
    /**
     * Retorna os campos de variações do produto
     * */
    let fnc_variacao = function (i,val,index,arrayItem,selected) {
        let display = '';
        let icon_remove = "";
        let id = arrayItem != null ? arrayItem.id : '';
        let subcodigo = arrayItem != null ? arrayItem.subcodigo.substring(arrayItem.subcodigo.length-2,arrayItem.subcodigo.length) : val;
        let variacao = arrayItem != null ? arrayItem.variacao : '';
        let valor_varejo = arrayItem != null ? formatMoney(arrayItem.valor_varejo) : typeof $("#valor_varejo0").val() !== "undefined" ? $("#valor_varejo0").val() : '';
        let valor_atacado = arrayItem != null ? formatMoney(arrayItem.valor_atacado) : typeof $("#valor_atacado0").val() !== "undefined" ? $("#valor_atacado0").val() : '';
        let valor_atacado_10un = arrayItem != null ? formatMoney(arrayItem.valor_atacado_10un) : typeof $("#valor_atacado_10un0").val() !== "undefined" ? $("#valor_atacado_10un0").val() : '';
        let valor_produto = arrayItem != null ? formatMoney(arrayItem.valor_produto) : typeof $("#valor_produto0").val() !== "undefined" ? $("#valor_produto0").val() : '';
        let quantidade = arrayItem != null ? arrayItem.quantidade : '';
        let estoque = arrayItem != null ? arrayItem.estoque : '';
        let quantidade_minima = arrayItem != null ? arrayItem.quantidade_minima : 2;
        let validade = arrayItem != null ? getFormattedDate(arrayItem.validade) : '00/00/0000';
        let fornecedor_id = arrayItem != null ? arrayItem.fornecedor : 0;
        let percentage = arrayItem != null ? formatMoney(arrayItem.percentage,'') : typeof $("#percetage0").val() !== "undefined" ? $("#percetage0").val() : '';


        /**
         * Adiciona o icone de remover do segundo em diante
         * */
        if(i > 0){
            icon_remove =  "<div class=\"col-md-1\" style='padding:unset;left: -6px;width: 10px' >"+
                "<a href=\"javascript:void(0)\" onclick=\"removeCampo('div_pai" + i + "')\" " +
                "title=\"Remover linha\"><img src=\"../public/img/minus.png\" border=\"0\"/>" +
                "</a>"+
                "</div>" ;
        }

        if(arrayItem !== null ){
            display = arrayItem.status === 'INATIVO' ? 'none' : '';
        }

        ("#tbl").append("<div class=\"row \" style=\"padding: 3px;display: "+display+"\" id=\"div_pai"+i+"\">" +
            "<input type=\"hidden\" name=\"variacao_id[]\" id=\"variacao_id"+i+"\"" +
            " class=\"form-control\" value=\'"+id+"\'/>"+
            "<div class=\"px-80\">" +
            "<span class=\"border-lable-flt\">"+
            "<input type=\"text\" name=\"subcodigo[]\" id=\"subcodigo"+i+"\" " +
            "class=\"form-control format-font\" placeholder=\"Subcodigo\" " +
            "style='width: 63px' value=\'"+subcodigo+"\' readonly/>" +
            "<label for=\"label-subcodigo\">SUBCOD</label>"+
            "</span>"+
            "</div>"+
            "<div class=\"col-md-2\" style='left: -12px;width: 300px'>" +
            "<span class=\"border-lable-flt\">"+
            "<input type=\"text\" name=\"variacao[]\" id=\"variacao"+i+"\" " +
            "class=\"form-control format-font\" placeholder=\"VARIAÇÃO\" " +
            "value=\'" + variacao + "\'/>" +
            "<label for=\"label-variacao\">VARIAÇÃO</label>"+
            "</span>"+
            "</div>"+
            "<div class=\"col-md-2\" style='left: -32px;width: 110px'>"+
            "<span class=\"border-lable-flt\" >"+
            "<input type=\"text\" name=\"valor_varejo[]\"  id=\"valor_varejo"+i+"\""+
            "class=\"form-control\" placeholder=\"VAREJO\""+
            "onkeyup=\"formatMoneyPress(this)\" value=\'" + valor_varejo + "\' required/>"+
            "<label for=\"label-varejo\">VAREJO</label>"+
            "</span>"+
            "</div>"+
            "<div class=\"col-md-2\" style='padding:unset;left: -32px;width: 100px'>"+
            "<span class=\"border-lable-flt\">"+
            "<input type=\"text\" name=\"valor_atacado_10un[]\"  id=\"valor_atacado_10un"+i+"\""+
            "class=\"form-control\" placeholder=\"ATACADO\""+
            "onkeyup=\"formatMoneyPress(this)\" value=\'" + valor_atacado_10un + "\' required/>"+
            "   <label for=\"label-atacado10un\">ATACADO</label>"+
            "</span>"+
            "</div>" +
            "<div  class=\"col-md-2\" style='padding:unset;left: -24px;width: 130px'>"+
            "<span class=\"border-lable-flt\">"+
            "<input type=\"text\" name=\"valor_produto[]\"  id=\"valor_produto"+i+"\""+
            "class=\"form-control\" placeholder=\"VALOR PAGO\""+
            "onkeyup=\"formatMoneyPress(this)\" value=\'" + valor_produto + "\' required/>"+
            "<label for=\"label-produto\">VALOR PAGO</label>"+
            "</span>"+
            "</div>" +
            "<div class=\"col-md-2\" style='padding:unset;left: -20px;width: 70px'>"+
            "<span class=\"border-lable-flt\">"+
            "<input type=\"text\" name=\"quantidade[]\"  id=\"quantidade"+i+"\""+
            "class=\"form-control\" placeholder=\"QTD\" onkeyup=\"SomenteNumeros(this)\" " +
            "value=\'" + quantidade + "\' required/>"+
            "<label for=\"label-qtd\">QTD</label>"+
            "</span>"+
            "</div>" +
            "<div class=\"col-md-2\" style='padding:unset;left: -16px;width: 70px'>"+
            "<span class=\"border-lable-flt\">"+
            "<input type=\"text\" name=\"quantidade_minima[]\"  id=\"quantidade_minima"+i+"\""+
            "class=\"form-control\" placeholder=\"QTD.MIN\" onkeyup=\"SomenteNumeros(this)\" " +
            "value=\'" + quantidade_minima + "\' required/>"+
            "<label for=\"label-qtd\">QTD.MIN</label>"+
            "</span>"+
            "</div>" +
            "<div class=\"col-md-2\" style='padding:unset;left: -12px;width: 100px'>"+
            "<span class=\"border-lable-flt\">"+
            "<input type=\"text\" name=\"estoque[]\"  id=\"estoque"+i+"\""+
            "class=\"form-control\" placeholder=\"ESTOQUE\" onkeyup=\"SomenteNumeros(this)\" " +
            "value=\'" + estoque + "\' required/>"+
            "<label for=\"label-estoque\">ESTOQUE</label>"+
            "</span>"+
            "</div>" +
            "<div class=\"col-md-2\" style='padding:unset;left: -8px;width: 80px'>"+
            "<span class=\"border-lable-flt\">"+
            " <input type=\"text\" name=\"percentage[]\"  maxlength='5' id=\"percentage"+i+"\""+
            " class=\"form-control\" placeholder=\"DESC.EM %\" data-tooltip=\"toggle\" title=\"Desconto em %\"" +
            " onkeyup=\"formatMoneyPress(this);\" value=\'" + percentage + "\' required/>"+
            " <label for=\"label-estoque\">DESC.EM %</label>"+
            "</span>"+
            "</div>" +
            "<div class=\"col-md-2 date\" style='padding:unset;left: -8px;width: 122px' id=\"data_validade"+i+"\">"+
            "<span class=\"border-lable-flt\">"+
            "<input type=\"text\" name=\"validade[]\"  id=\"validade"+i+"\""+
            "class=\"form-control\" placeholder=\"QTD.MIN\" " +
            "onKeyUp=\"formatDate(this)\" maxlength=\"10\" value=\'" + validade + "\'/>"+
            "<label for=\"label-qtd\">VALIDADE</label>"+
            "</span>"+
            "</div>" +
            "<div class=\"col-md-2\" style='padding:unset;left: -4px;width: 78px'>"+
            "<span class=\"border-lable-flt\">"+
            "<SELECT type=\"text\" name=\"status_variacao[]\"  id=\"status_variacao"+i+"\""+
            "class=\"form-control status_variacao\" placeholder=\"STATUS\" required/>"+
            "<option value=\"1\" "+selected+">ATIVO</option>"+
            "<option value=\"0\" "+selected+">INATIVO</option>"+
            "</select>"+
            "<label for=\"label-qtd\">STATUS</label>"+
            "</span>"+
            "</div>" +
            "<div class=\"col-md-2\" style='padding:unset;left: -1px;width: 122px' >"+
            "<span class=\"border-lable-flt\">"+
            "<SELECT type=\"text\" name=\"fornecedor[]\"  id=\"fornecedor"+i+"\""+
            "class=\"form-control\" placeholder=\"FORNECEDOR\" required/>"+
            ""+fnc_fornecedor('#fornecedor'+i,fornecedor_id)+""+
            "</select>"+
            "<label for=\"label-qtd\">FORNECEDOR</label>"+
            "</span>"+
            "</div>" +
            ""+icon_remove+""+
            "</div>");

    }

    /**
     * Abre modal com campos do produto
     * */
    $(document).on('click','.openModalBtn', function () {
        $('#slideInModal').modal('show');
    });
});
/**
 *  Formatting function for row details - modify as you need
 */
function format ( d ) {

    return '<table class="table table-striped table-condensed">'+
        '<tr>'+
        '<td><strong>Categoria:</strong></td>'+
        '<td>'+d.categoria+'</td>'+
        '<td><strong>Fornecedor:</strong></td>'+
        '<td>'+d.fornecedor+'</td>'+
        '</tr>'+
        '<tr>'+
        '<td><strong>Status:</strong></td>'+
        '<td>'+d.status_produto+'</td>'+
        '<td><strong>Quantidade Minima:</strong></td>'+
        '<td>'+d.quantidade_minima+'</td>'+
        '</tr>'+
        '<tr>'+
        '<td><strong>Data Criação:</strong></td>'+
        '<td>'+d.created+'</td>'+
        '<td><strong>Data Atualização:</strong></td>'+
        '<td>'+d.updated+'</td>'+
        '</tr>'+
        '<tr>'+
        '<td><strong>Estoque:</strong></td>'+
        '<td>'+d.estoque+'</td>'+
        '</tr>'+
        '</table>';
}
