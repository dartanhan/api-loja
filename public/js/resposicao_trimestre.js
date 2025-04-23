import { sweetAlert,formatMoney } from "./comum.js";

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
            const data = await fetch(url + "/reposicao/create", {
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
       // $('#table').html('');
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
                "url": "../public/Portuguese-Brasil.json",
            },
            initComplete: function(settings, json) {
                $('#loadingMessage').hide();
                //$('#table').show();
            },
            "order": [[0, "desc"]],
            //"order": [[ 0, 'desc' ], [ 2, 'asc' ]]
        });
       // $('#table').hide();
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

            let tmpRow  = "";
            /*let tmpRow  ="<table class='table table-striped table-condensed'>" +
                            "<thead class=\"text-center\">" +
                                "<tr class='bg-secondary '>" +
                                    "<th>IMAGEM</th>" +
                                    "<th>SUB CÓDIGO</th>" +
                                    "<th>VARIAÇÃO</th>" +
                                    "<th>QTD</th>" +
                                    "<th>ESTOQUE</th>" +
                                    "<th>VALOR PAGO</th>"+
                                    "<th>30D</th>" +
                                    "<th>60D</th>" +
                                    "<th>90D</th>" +
                                    "<th>STATUS</th>" +
                                    "<th>AÇÕES</th>" +
                                "</tr>" +
                            "</thead>";*/

                    $.ajax({
                        url: url + "/reposicao/"+row.data().id,
                        type: 'GET',
                        data: '',
                        dataType: 'json',
                        beforeSend: function () {
                            row.child('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>').show();
                        },
                        success: function (response) {
                            // console.log(response.is_admin);

                           if (response.success) {
                           // Iterar sobre cada objeto no array
                           response.data.forEach(function(obj) {
                                let image = obj.imagem !== null ?
                                                "<img src='../public/storage/" + obj.imagem + "' class=\"image img-datatable\" width='120px' height='80px' alt=\"\" title='" + obj.variacao + "'/>" :
                                                "<img src='../public/storage/produtos/not-image.png' class=\"image img-datatable\" width='80px' height='80px' alt=\"\" title='" + obj.variacao + "'/>"

                                    let image_filho = "../public/storage/produtos/not-image.png";
                                    if(obj.imagem !== null){
                                        image_filho = '../public/storage/'+obj.imagem;
                                    }

                                    //monta o header diferetne para casao não seja ADMIN
                                    tmpRow = getTmpRow(response.is_admin);

                                    let valor_pago ="";
                                    if(response.is_admin === 1){
                                        valor_pago = "<td>" + formatMoney(obj.valor_pago) + "</td>" ;
                                    }
                                    tmpRow += "<tr>" +
                                        "<td>"+image+"</td>" +
                                        "<td>" + obj.subcodigo + "</td>" +
                                        "<td>" + obj.variacao + "</td>" +
                                        "<td>" + obj.qtd + "</td>" +
                                        "<td>" + obj.estoque + "</td>"
                                        + valor_pago +
                                        "<td>" + obj.qtd_total_venda_30d + "</td>" +
                                        "<td>" + obj.qtd_total_venda_60d + "</td>" +
                                        "<td>" + obj.qtd_total_venda_90d + "</td>" +
                                        "<td>" + "<span class='badge bg-success'>"+obj.status+"</span>" + "</td>" +
                                        "<td>" + "<button type=\"button\" class=\"btn btn-primary rounded btn-sm\" id=\"addListaCompra\" "+
                                                    " data-produto_new_id="+row.data().id+" data-produto_variacao_id="+obj.variacao_id+">"+
                                                    "<i class=\"fa-brands fa-shopify fa-2x\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Adicionar à Lista de Compras\">"+
                                                    "</i>"+
                                                "</button>"+
                                        "</td>" +
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
                            sweetAlert({
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

   function  getTmpRow(valor){
       let addCampo = "";
       if(valor === 1){
           addCampo = "<th>VALOR PAGO</th>";
       }

       return  "<table class='table table-striped table-condensed'>" +
        "<thead class=\"text-center\">" +
        "<tr class='bg-secondary '>" +
        "<th>IMAGEM</th>" +
        "<th>SUB CÓDIGO</th>" +
        "<th>VARIAÇÃO</th>" +
        "<th>QTD</th>" +
        "<th>ESTOQUE</th>"
           +addCampo +
        "<th>30D</th>" +
        "<th>60D</th>" +
        "<th>90D</th>" +
        "<th>STATUS</th>" +
        "<th>AÇÕES</th>" +
        "</tr>" +
        "</thead>";
    }

    // Captura o clique no ícone de imagem com a classe "abrir-modal"
    $(document).on("click",".bi-image" ,function(event){
        event.preventDefault();

        // Obtém o valor do atributo "data-imagem"
        var imagem = $(this).data('image');
        var variacaoId = $(this).data('variacao-id');
        var productId = $(this).data('id');

        // Atribui o valor ID da imagem da variação do produto
        $('#variacaoId').val(variacaoId);

        // Atribui o valor ID da imagem do produto
        $('#productId').val(productId);

        // Atribui o valor da imagem ao atributo "src" da tag "<img>" no modal
        $('#modal-imagem').attr('src', imagem);

        // Abre o modal
       // $('#modal').modal('show');
    });

    /***
     * Ação de gravar na tabela de Lista de Compras
     */
    /*$(document).on("click","#addListaCompra" ,function(event){
        event.preventDefault();
        var produto_new_id = $(this).data('produto_new_id');
        var produto_variacao_id = $(this).data('produto_variacao_id');
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

          $.ajax({
                url: url + "/reposicao", //product.update
                cache: false,
                type:'post',
                data:{ // Objeto de dados que você deseja enviar
                    produto_new_id: produto_new_id,
                    produto_variacao_id: produto_variacao_id, // Informação adicional que você quer passar
                    _token: csrfToken
                },
                dataType:'json',
                success: function(response){
                //    console.log(response);
                if(response.success){
                    swalWithBootstrapButtons.fire({
                        title: 'Sucesso!',
                        text: response.message,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }else{
                    swalWithBootstrapButtons.fire({
                        title: 'Atenção!',
                        text: response.message,
                        icon: 'warning',
                        showConfirmButton: false,
                        timer: 2500
                    });
                }

                },
                error:function(response){
                  //  console.log(response.responseJSON);
                    swalWithBootstrapButtons.fire({
                        title: 'Error!',
                        text: response.responseJSON.message,
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 2500
                    });
                }
        });
    });*/



     /**
     * *********************
     * *** TELA REPOSIÇÃO
     * ********************
    */
     async function getFncDataCardTotalProdutoPorVenda(dateOne, dateTwo, idLoja) {
        const endpoint = `${url}/dashboardDiario/totalProdutoVenda`;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const data = {
            dateOne: dateOne,
            dateTwo: dateTwo,
            idLoja: idLoja
        };

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log("totalProdutoVenda", result);
        } catch (error) {
            console.error('Error:', error);
        }
    }

    /****
     * LOAD DE FUNÇOES
     */
    getdata();
});
