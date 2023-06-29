/* globals Chart:false, feather:false */
$(document).ready(function() {

    feather.replace();

    let token = '', produto_id = '', fila='', json = '', id='', nome='', resposta = '';
    let url = window.location.protocol +"//"+ window.location.hostname + "/api-loja/admin";

    $('#formFiltro').on('submit', function(e) {
        //$('form[name="formFiltro"]').submit(function (e) {
        table.ajax.reload();
        e.preventDefault();
    });

    let table = $('#table').DataTable({
        "ajax":{
            "method": 'post',
            "processing": true,
            "serverSide": true,
            "headers": {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
            "url": url + "/productSaleDay",
            "data": function (data) {
                data.dating = $('input[name="dating"]').val();
            },
            cache: false,
        },
        scrollX:true,

        "columns": [
            {
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
            { "data": "codigo_produto", "defaultContent": "" },
            { "data": "descricao" , "defaultContent": ""},
            {
                "data": "valor_produto" ,
                "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
            },
            { "data": "quantidade" , "defaultContent": ""},
            {
                "data": "total" ,
                "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
            },
            { "data": "data" , "defaultContent": ""}
        ],
        "columnDefs": [
        {
            "targets": [0],
            "visible": false,
            "defaultContent": ""
        }
    ],
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
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
});
