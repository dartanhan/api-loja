const url = fncUrl();
let json,table;


row.usuario = undefined;
row.clientes = undefined;
$(function() {

    table = $('#table').DataTable({
        ajax:{
            method: 'get',
            processing: true,
            serverSide: true,
            url: url + "/sale/table",
        },
        "columns": [
            {
                "className": 'details-control',
                "orderable": false,
                "data": null,
                "defaultContent": ''
            },
            {"data": "atendente",
                render: function (data, type, row) {
                    return row.usuario[0].nome;
                }
            },
            {
                "data": "cliente",
                render: function (data, type, row) {
                    return row.clientes[0].nome;
                }
            },
            {
                "data": "status",
                render: function (data, type, row) {
                        return "<span class=\"badge bg-warning\">" + row.status + "</span>";
                }
            },
            {"data": "created_at", "defaultContent": ""},
            {
                "data": "defaultContent",
                render: function (data, type, row) {
                    return 0;
                }
            }

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

});
