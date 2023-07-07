$(document).ready(function() {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    const urlApi = fncUrl();

    let table,json;

    /**
     * DATATABLES
     * */

    table = $('#table').DataTable({
        "ajax":{
            "method": 'get',
            "processing": true,
            "serverSide": true,
            "url": url + "/estoque/create",
            "data":'',
            "dataSrc":"",
            cache: false,
        },

        "columns": [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
            { "data" : "descricao", "defaultContent": ""},
            { "data": "codigo_produto", "defaultContent": "" },
            { "data": "total" ,  render: $.fn.dataTable.render.number('.', ',', 2, 'R$ ')},
            { "data": "qtd" , "defaultContent": ""},
            { "data": "categoria" , "defaultContent": ""},
           // { "data": "data" , "defaultContent": ""},
            // { "data": "data_sort", "defaultContent": "" },
            /* { "data": "variacao" , "defaultContent": ""},
             { "data": "status" , "defaultContent": ""},
             { "data": "quantidade" , "defaultContent": ""},
             {"data": "valor_varejo", render: $.fn.dataTable.render.number('.', ',', 2, 'R$ ')},
             { "data": "valor_atacado", render: $.fn.dataTable.render.number('.', ',', 2, 'R$ ') },
             { "data": "valor_produto", render: $.fn.dataTable.render.number('.', ',', 2, 'R$ ') },
             { "data": "quantidade_minima" , "defaultContent": ""},
             { "data": "percentage" , "defaultContent": ""},
             { "data": "fornecedor" , "defaultContent": ""},
             { "data": "categoria" , "defaultContent": ""},*/
            // { "data": "created_at", "defaultContent": "" },
            // { "data": "updated_at" , "defaultContent": ""},
           /* {"data": "defaultContent",
                render: function ( data, type, row ) {
                    return "<div class='text-center'>" +
                        "<div class='btn-group'>" +
                        "<i class=\"bi-pencil-square btnUpdateProduct\" " +
                        "               style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" " +
                        "               title='Atualizar Produto' data-id=\""+row.id+"\" >" +
                        "</i>"+
                        "</div>" +
                        "</div>"
                }
            }*/

        ],
        scrollX:true,
        select: false,
        "columnDefs": [
            {
               // "targets": [ 6 ],
               // "visible": false,
                //"searchable":false
            }
        ],
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
        },
       "order": [[ 4, "desc"]],
        "pageLength": 5,
        "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
        //"order": [[ 0, 'desc' ], [ 2, 'desc' ]]
    }).draw();


    // Add event listener for opening and closing details
    $('#table tbody').on('click', 'td.details-control', function () {
        let tr = $(this).closest('tr');
        let row = table.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {

            // Open this row
            //row.child( format(row.data()) ).show();
            tr.addClass('shown');

            let tmpRow  ="<table class='table table-striped table-condensed'>" +
                "<thead class=\"text-center\">" +
                    "<tr class='bg-secondary '>" +
                        "<th>PRODUTO</th>" +
                        "<th>CODIGO</th>" +
                        "<th>QTD</th>" +
                        "<th>VALOR</th>" +
                        "<th>DATA</th>" +
                    "</tr>" +
                "</thead>";

            $.ajax({
                url: url + "/estoque/"+row.data().codigo_produto+"/edit",
                type: 'GET',
                data: '',
                dataType: 'json',
                beforeSend: function () {
                    row.child('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>').show();
                },
                success: function (response) {
                    // console.log(response.success);
                    if (response.success) {
                       // console.log(response.data);
                        let arrayProducts = JSON.stringify(response.data);

                        JSON.parse(arrayProducts).forEach(function (arrayItem, index, fullArray) {
                            // console.log(arrayItem.descricao);

                            tmpRow += "<tr>" +
                                            "<td>" + arrayItem.descricao + "</td>" +
                                            "<td>" + arrayItem.codigo_produto + "</td>" +
                                            "<td>" + arrayItem.quantidade + "</td>" +
                                            "<td>" + arrayItem.valor + "</td>" +
                                            "<td>" + arrayItem.data + "</td>" +
                                        "</tr>"
                        });
                        tmpRow  +=      "</table>";
                        row.child(tmpRow).show();
                    }
                },
                error: function (response) {
                   // console.log(response.responseText);
                    //json = $.parseJSON(response.responseText);
                   // $("#modal-title").addClass("alert alert-danger");
                    //$('#modal-title').html('<p><i class="fas fa-exclamation-circle"></i>&nbsp;<strong>' + json.message + '</strong></p>');
                    Swal.fire(
                        'error!',
                        response.responseText,
                        'error'
                    )
                }
            });
           // console.log(tmpRow);

        }
    } );

    /**
     *  Formatting function for row details - modify as you need
     */
    /*function format ( d ) {
        // `d` is the original data object for the row
        //console.log(d);

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
    }*/
});
