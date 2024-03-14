$(function() {
    $('[data-toggle="tooltip"]').tooltip();
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
                        return "<i class=\"bi-image btnProductImage\" " +
                                    "  style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" " +
                                    "  title='Imagem do Produto' data-bs-toggle=\"modal\" " +
                                    "  data-bs-target=\"#divModalImageProduct\" data-id='"+row.id+"' "+
                                    "  data-image-preview='"+image+"'  data-path='"+row.imagem+"' data-flag-image='0'></i>";
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
        const perfisPermitidos = ["barbara", "karla", "darta"];

        let tr = $(this).closest('tr');
        let row = table.row( tr );
      
        //para adicionar a coluna de quantidade dependendo do usuário
        let isPerfil = $("#login").val();
              
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
                                    "<th>SUB CÓDIGO</th>";

                                    tmpRow  += (perfisPermitidos.includes(isPerfil) === true)  ? "<th>QUANTIDADE</th>"  : "";
                                    
                                    tmpRow  += "<th>VARIAÇÃO</th>" +
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
                            /// console.log(response.data.products);
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
                                        "<td>" + arrayItem.subcodigo + "</td>";

                                    tmpRow  += (perfisPermitidos.includes(isPerfil) === true)  ? 
                                            "<td> " +
                                                " <i class=\"bi-up fas fa-arrow-up action\" data-id="+arrayItem.id+" data-sentido=\"up\" " +
                                                " title=\"Adiconar Quantidade\"></i>&nbsp;" + arrayItem.quantidade + "&nbsp;<i class=\"bi-down fas fa-arrow-down action\" "+
                                                " data-id="+arrayItem.id+" data-sentido=\"down\"  title=\"Diminuir Quantidade\"></i>" +
                                                " </td>"
                                                : "";

                                        tmpRow  += "<td>" + arrayItem.variacao + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_varejo) + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_atacado_10un) + "</td>" +
                                        "<td>" + "<span class='badge bg-success'>"+arrayItem.status+"</span>" + "</td>" +
                                        "<td><i class=\"bi-image\" " +
                                        "               style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" " +
                                        "               title='Imagem da Variação do Produto' data-bs-toggle=\"modal\" " +
                                        "               data-bs-target=\"#divModalImageProduct\" data-variacao-id='"+arrayItem.id+"' " +
                                        "               data-subcodigo='"+arrayItem.subcodigo+"' data-image-preview='"+image_filho+"' "+
                                        "               data-path='"+ arrayItem.path +"' data-flag-image='1' data-image-id='"+arrayItem.id_image+"'/>"+
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

    /***
     * Função que adiciona quantidade
     */
    $(document).on("click",".action" ,function(event){
        event.preventDefault();
        var id = $(this).data('id');
        var sentido = $(this).data('sentido');

       // console.log(sentido);

        $.ajax({
                url: url + "/product/"+id, //product.update
                cache: false,
                type:'put',
                data:{ // Objeto de dados que você deseja enviar
                    sentido: sentido // Informação adicional que você quer passar
                },
                dataType:'json',
                success: function(response){
                   // console.log(response);
                    swalWithBootstrapButtons.fire({
                                title: 'Atualizado!',
                                text: response.message,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            table.destroy();
                            getdata();
                },
                error:function(response){
                   // console.log(response);
                    swalWithBootstrapButtons.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
        });
    });

     /* LOAD DE FUNÇOES
     */
    getdata();
});