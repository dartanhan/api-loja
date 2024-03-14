$(function() {

    let json,id,table,asyncData;

    const url = fncUrl();

    /**
     * DATATABLES
     * */
    function getdata(){
        const getDados = async () => {
            const data = await fetch(url + "/produto/create", {
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
        table = $('#table').DataTable({
            data:asyncData,
            responsive: true,
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
                        if(row.imagem !== null){
                            return '<img src="../public/storage/product/'+row.id+'/'+ row.imagem + '" class="image img-datatable"></img>';
                        }else{
                            return '<img src="../public/storage/produtos/not-image.png" class="img-datatable"></img>';
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
                {"data": "updated", "defaultContent": ""},
                {
                    "data": "defaultContent",
                    render: function (data, type, row) {
                        let image = "../public/storage/produtos/not-image.png";
                        if(row.imagem !== null){
                            image = '../public/storage/product/'+row.id+'/'+ row.imagem;
                        }
                        return "<div class='text-center'>" +
                            "<i class=\"bi-image btnProductImage\" " +
                            "               style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" " +
                            "               title='Imagem do Produto' data-bs-toggle=\"modal\" " +
                            "               data-bs-target=\"#divModalImageProduct\" data-id='"+row.id+"' "+
                            "               data-image-preview='"+image+"'  data-path='"+row.imagem+"' ></i>"+
                            "</i>&nbsp;" +
                            "</div>" +
                            "</div>";
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
                                    "<th>VAREJO</th>" +
                                    "<th>ATACADO</th>" +
                                    "<th>STATUS</th>" +
                                    "<th>AÇÃO</th>" +
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
                             console.log(response.data.products);
                            if (response.success) {
                                let arrayProducts = JSON.stringify(response.data.products);

                                JSON.parse(arrayProducts).forEach(async function (arrayItem, index, fullArray) {
                                    // console.log(arrayItem.subcodigo);
                                    let image = arrayItem.path !== null ?
                                                            "<img src='../public/storage/"+ arrayItem.path + "' class=\"image img-datatable\" width='120px' height='80px' alt=\"\" title='"+arrayItem.variacao+"'></img>" :
                                                            "<img src='../public/storage/produtos/not-image.png' class=\"image img-datatable\" width='80px' height='80px' alt=\"\" title='"+arrayItem.variacao+"'></img>"

                                    let image_filho = "../public/storage/produtos/not-image.png";
                                    if(arrayItem.path !== null){
                                        image_filho = '../public/storage/'+arrayItem.path;
                                    }

                                    tmpRow += "<tr>" +
                                        "<td>"+image+"</td>" +
                                        "<td>" + arrayItem.subcodigo + "</td>" +
                                        "<td>" + arrayItem.variacao + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_varejo) + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_atacado_10un) + "</td>" +
                                        "<td>" + "<span class='badge bg-success'>"+arrayItem.status+"</span>" + "</td>" +
                                        "<td><i class=\"bi-image\" " +
                                        "               style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" " +
                                        "               title='Imagem da Variação do Produto' data-bs-toggle=\"modal\" " +
                                        "               data-bs-target=\"#divModalImageProduct\" data-variacao-id='"+arrayItem.id+"' " +
                                        "               data-subcodigo='"+arrayItem.subcodigo+"' data-image-preview='"+image_filho+"' data-path='"+ arrayItem.path +"'>"+
                                        "</td>"+
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
    } );


    // Captura o clique no ícone de imagem com a classe "abrir-modal"
    $(document).on("click",".bi-image" ,function(event){
        event.preventDefault();
    
        // Obtém o valor do atributo "data-imagem"
        var imagem = $(this).data('path');
        var imagePreview = $(this).data('image-preview');
        var variacaoId = $(this).data('variacao-id');
        var productId = $(this).data('id');

        // Atribui o valor ID da imagem da variação do produto
        $('#variacaoId').val(variacaoId);

        // Atribui o valor ID da imagem do produto
        $('#productId').val(productId);

        $('#imagemName').val(imagem);

        // Atribui o valor da imagem ao atributo "src" da tag "<img>" no modal
        $('#modal-imagem').attr('src', imagePreview);

        // Abre o modal
       // $('#modal').modal('show');
    });

    /****
     * LOAD DE FUNÇOES
     */
    getdata();
});