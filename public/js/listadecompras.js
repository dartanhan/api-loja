$(function() {
    $('[data-toggle="tooltip"]').tooltip();

    let json,id,table,asyncData;

    const url = fncUrl();

    /**
     * DATATABLES
     * */
    function getdata(){
        // Exibir mensagem de "Aguarde"
       // $('#table').html('<i class="fas fa-spinner fa-spin"></i> Carregando...');

        const getDados = async () => {
            const data = await fetch(url + "/listaCompras/create", {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            asyncData = await data.json();
            initialiseTable();
            return asyncData;
        };
        getDados();
    }
    function initialiseTable() {
     //   $('#table').html('');
        table = $('#table').DataTable({
            data:asyncData,
            responsive: true,
            columns: [
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
                        if(row.imagem !== null){
                            return '<img src="../public/storage/product/'+row.id+'/'+ row.imagem + '" class="image img-datatable"></img>';
                        }else{
                            return '<img src="../public/storage/produtos/not-image.png" class="image img-datatable"></img>';
                        }
                    }
                },
                {"data": "descricao", "defaultContent": ""},
                {"data": "categoria", "defaultContent": ""},
                {
                    "data": "status",
                    render: function (data, type, row) {
                        return "<span class=\"badge bg-success\">" + row.status + "</span>";
                    }
                },
                {"data": "created", "defaultContent": ""},
                {"data": "updated", "defaultContent": ""}

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
    }

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
                                    "<th>VALOR PAGO</th>" +
                                    "<th>30D</th>" +
                                    "<th>60D</th>" +
                                    "<th>90D</th>" +
                                    "<th>STATUS</th>" +
                                "</tr>" +
                            "</thead>";

                    $.ajax({
                        url: url + "/listaCompras/"+row.data().id,
                        type: 'GET',
                        data: '',
                        dataType: 'json',
                        beforeSend: function () {
                            row.child('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>').show();
                        },
                        success: function (response) {
                           //  console.log(response.data[0].produto_variacao.variacao);
                          // return false;
                           if (response.success) {
                           // Iterar sobre cada objeto no array
                           response.data.forEach(function(obj) {
                                let image = obj.produto_variacao.imagem_path !== null ?
                                                "<img src='../public/storage/"+ obj.produto_variacao.imagem_path + "' class=\"image img-datatable\" width='120px' height='80px' alt=\"\" title='"+obj.produto_variacao.variacao+"'></img>" :
                                                "<img src='../public/storage/produtos/not-image.png' class=\"image img-datatable\" width='80px' height='80px' alt=\"\" title='"+obj.produto_variacao.variacao+"'></img>"

                                   // let image_filho = "../public/storage/produtos/not-image.png";
                                  //  if(obj.produto_variacao.imagem !== null){
                                  //      image_filho = '../public/storage/'+obj.produto_variacao.imagem_path;
                                  //  }

                                    tmpRow += "<tr>" +
                                        "<td>"+image+"</td>" +
                                        "<td>" + obj.produto_variacao.subcodigo + "</td>" +
                                        "<td>" + obj.produto_variacao.variacao + "</td>" +
                                        "<td>" + obj.produto_variacao.quantidade + "</td>" +
                                        "<td>" + obj.produto_variacao.estoque + "</td>" +
                                        "<td>" + formatMoney(obj.produto_variacao.valor_produto) + "</td>" +
                                        "<td>" + obj.produto_variacao.qtd_total_venda_30d + "</td>" +
                                        "<td>" + obj.produto_variacao.qtd_total_venda_60d + "</td>" +
                                       "<td>" + obj.produto_variacao.qtd_total_venda_90d + "</td>" +
                                        "<td>" + "<span class='badge bg-success'>"+obj.produto_variacao.status+"</span>" + "</td>" +
                                    "</tr>";
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
    } );


   

    /****
     * LOAD DE FUNÇOES
     */
    getdata();
});