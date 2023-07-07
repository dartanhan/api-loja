/* globals Chart:false, feather:false */
$(document).ready(function() {

    feather.replace();
    
    const urlApi = fncUrl();

    let token = '', produto_id = '', fila='', json = '', id='', nome='', resposta = '';
    
	  let table = $('#table').DataTable({
          "ajax":{
              "method": 'get',
              "processing": true,
              "serverSide": true,
              "url": url + "/productMin/create",
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
                { "data": "quantidade" , "defaultContent": ""},
                { "data": "quantidade_minima" , "defaultContent": ""},
                {
                    "data": "valor_produto" ,
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                },{
                    "data": "valor_dinheiro",
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                },{
                    "data": "valor_cartao",
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display

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
});
