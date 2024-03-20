$(document).ready(function() {
    const url = fncUrl();

    
         $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax" : "datatableAuditUpdate",
            columns: [
                { "data": "usuario" , "defaultContent": ""},
                { "data": "evento" , "defaultContent": ""},
                { "data": "variacao" , "defaultContent": ""},
                {"data": "defaultContent",
                    render: function(data, type, row) {
                      //  console.log(row.old_values.quantidade);
                        
                        // Verifica se a chave 'quantidade'
                        if (row.new_values.quantidade){
                            return row.new_values.quantidade;
                        } else {
                            return '-'; // Retorna vazio se 'quantidade' não estiver presente
                        }
                    }
                },
                {"data": "defaultContent",
                    render: function(data, type, row) {
                      //  console.log(row.old_values.quantidade);
                        
                        // Verifica se a chave 'quantidade'
                        if (row.old_values.quantidade){
                            return row.old_values.quantidade;
                        } else {
                            return '-'; // Retorna vazio se 'quantidade' não estiver presente
                        }
                    }
                },
                { "data": "updated_at", "defaultContent": "" },
            ],
            columnDefs: [
                {
                    "targets": [ ],
                    "visible": false,
                    "searchable":true
                }
            ],
            language: {
                "url": "../public/Portuguese-Brasil.json"
            },
            order: [[ 5, "desc" ]],
            
        });
   

    $('#tableCreate').DataTable({
        //serverSide: true,
        "language": {
            "url": "../public/Portuguese-Brasil.json"
        },
        "order": [[ 5, "desc" ]]

    });

   
});