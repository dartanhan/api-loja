$(function() {
    let json,id,grid,table;

    const url = fncUrl();

    /**
     * DATATABLES
     * */
    var asyncData;
    getdata();
    function getdata(){
        const getDados = async () => {
            const data = await fetch(url + "/produtoInativo/0/edit", {
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

            "columns": [
                {
                    "className": 'details-control',
                    "orderable": false,
                    "data": null,
                    "defaultContent": ''
                },
                {"data": "id", "defaultContent": ""},
                {"data": "codigo_produto", "defaultContent": ""},
                {"data": "descricao", "defaultContent": ""},
                {"data": "categoria", "defaultContent": ""},
                {
                    "data": "status",
                    render: function (data, type, row) {
                        let bg = row.status === "INATIVO" ? "bg-danger" : "bg-success";

                        return "<span class=\"badge "+bg+"\">" + row.status + "</span>";
                    }
                },
                {"data": "created", "defaultContent": ""},
                {"data": "updated", "defaultContent": ""},
                {
                    "data": "defaultContent",
                    render: function (data, type, row) {

                        return (row.status === "INATIVO") ? "<div class=\"form-check form-switch\">" +
                            "  <input class=\"form-check-input\" type=\"checkbox\" " +
                            " id=\"form-check-input\" data-id=\"" + row.id + "\" data-flag=\"1\" data-tipo=\"produto\" " +
                            " title='Clique para Ativar o Produto' style='cursor: pointer'>" +
                            "</div>": "";
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
            //console.log("abriu.." + row.data().id);

            let tmpRow  ="<table class='table table-striped table-condensed'>" +
                            "<thead class=\"text-center\">" +
                                "<tr class='bg-secondary '>" +
                                    "<th>IMAGEM</th>" +
                                    "<th>SUB CÓDIGO</th>" +
                                    "<th>VARIAÇÃO</th>" +
                                    "<th>QTD</th>" +
                                    "<th>ESTOQUE</th>" +
                                    "<th>VAREJO</th>" +
                                    "<th>ATA.3UN</th>" +
                                    "<th>ATA.5UN</th>" +
                                    "<th>ATA.10UN</th>" +
                                    "<th>VAL.LISTA</th>" +
                                    "<th>PRODUTO</th>" +
                                    "<th>STATUS</th>" +
                                    "<th>AÇÃO</th>" +
                                "</tr>" +
                            "</thead>";

                    $.ajax({
                        url: url + "/produto/getProdutoInativos/"+row.data().id,
                        type: 'GET',
                        data: '',
                        dataType: 'json',
                        beforeSend: function () {
                            row.child('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>').show();
                        },
                        success: function (response) {
                             console.log(response.data);
                            if (response.success) {
                                let arrayProducts = JSON.stringify(response.data);

                                JSON.parse(arrayProducts).forEach(async function (arrayItem, index, fullArray) {
                                    // console.log(arrayItem.subcodigo);
                                    let image = arrayItem.path !== null ?
                                                            "<img src='../public/storage/"+ arrayItem.path + "' class=\"image\" width='80px' height='80px' alt=\"\" title='"+arrayItem.variacao+"'/>" :
                                                            "<img src='../public/storage/produtos/not-image.png' class=\"image\" width='80px' height='80px' alt=\"\" title='"+arrayItem.variacao+"'/>"

                                    tmpRow += "<tr>" +
                                        "<td>"+image+"</td>" +
                                        "<td>" + arrayItem.subcodigo + "</td>" +
                                        "<td>" + arrayItem.variacao + "</td>" +
                                        "<td>" + arrayItem.quantidade + "</td>" +
                                        "<td>" + arrayItem.estoque + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_varejo) + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_atacado) + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_atacado_5un) + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_atacado_10un) + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_lista) + "</td>" +
                                        "<td>" + formatMoney(arrayItem.valor_produto) + "</td>" +
                                        "<td>" + "<span class='badge bg-danger'>"+arrayItem.status+"</span>" + "</td>" +
                                        "<td>" +
                                                "<div class=\"form-check form-switch \">\n" +
                                                " <input class=\"form-check-input\" type=\"checkbox\" " +
                                                    "id=\"form-check-input\" data-flag=\"1\" data-tipo=\"variacao\" " +
                                                    "data-id=\"" + arrayItem.id + "\" " +
                                                    " title='Ativar Variação do Produto' style='cursor: pointer'>" +
                                                "</div>"+
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
                            Swal.fire(
                                'error!',
                                json.message,
                                'error'
                            )
                        }
                    });

        }
    } );

    /***
     * form-check-input
     * */

    $(document).on("change",".form-check-input" , function(event){
        event.preventDefault();

        Swal.fire({
            title: 'Tem certeza?',
            text: "Está seguro de ativar este produto?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, ativar!'
        }).then((result) => {
            if (result.isConfirmed) {

                // O ID a ser atualizado
                id = $(this).data('id');

                //valor para desativar o produto ou a variação
                let flag = $(this).data('flag');

                //informa se tem que atualizar o produto ou a variação
                let tipo = $(this).data('tipo');


                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: url + '/produtoInativo/'+id,
                    type: 'PUT',
                    data: {
                        id: id,
                        flag:flag,
                        tipo:tipo,
                        _method: 'PUT'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function (response) {
                       // console.log(response);

                        Swal.fire({
                            title: "Sucesso!",
                            text: response.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        table.destroy();
                        getdata();
                    },
                    error: function (response) {
                        json = $.parseJSON(response.responseText);
                        Swal.fire(
                            'error!',
                            json.message,
                            'error'
                        )
                    },
                    complete:function(response){

                    }
                });
            }else{
                $(this).prop("checked",false);
            }
        });
    });


    /**
     * Exibe as imagens das variações dos produtos
     * **/
    $(document).on("click",".btnImageProduct" ,function(event){
        event.preventDefault();

        //console.log($(this).data('variacao-id'));
        id = $(this).data('variacao-id') != null ? $(this).data('variacao-id') : 0; //capturo o ID
       // $("#products_variation_id").val($(this).data('subcodigo'));
        $("#products_variation_id").val(id);

        $.ajax({
            url: url + "/produto/pictures/"+id,
            type:'get',
            cache: false,
            dataType:'json',
            beforeSend: function () {
                $("#modal-title").removeClass( "alert alert-danger" );
                $('#modal-title').html('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>');
                $("#modal-title").addClass( "alert alert-info" );
            },
            success: function(response) {
               // console.log(response);
                grid = "";
                if(response.data.length > 0){
                    $.each(response.data, function (idx, value) {
                        grid += "<div class=\"col\">";
                        grid += "<img src='.,/public/storage/" + value.path + "' width='180px' height='180px' alt=\"\"/>";
                        grid += "<i class=\"bi-trash btnRemoveImage\"  data-id='"+value.id+"' style=\"font-size: 2rem; color: #db9dbe;cursor: pointer;\" title='Remover Imagem'></i>";
                        grid += "</div>";
                    });
                }else{
                    grid = "<img src='../public/storage/produtos/not-image.png' width='180px' height='180px' alt=\"\"/>";
                }
                $("#pictures").html(grid);
            },
            error:function(response){
                json = $.parseJSON(response.responseText);
                $("#modal-title").addClass( "alert alert-danger" );
                $('#modal-title').html('<p><i class="fas fa-exclamation-circle"></i>&nbsp;<strong>'+json.message+'</strong></p>');
                Swal.fire(
                    'error!',
                    json.message,
                    'error'
                )

            },complete: function(response){

            }
        });
    });

    /**
     * Deleta a imagem do produto
     * */
   // $('i[name="btnRemoveImage"]').on('click',function(event) {
    $(document).on("click",".btnRemoveImage" , function(event){
        event.preventDefault();

        Swal.fire({
            title: 'Tem certeza?',
            text: "Está seguro de remover esta imagem ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, deletar!'
        }).then((result) => {
            if (result.isConfirmed) {

            id = $(this).data('id');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
                $.ajax({
                    url: url + '/image/destroy',
                    type: 'POST',
                    data: {
                        id: id,
                        _method: 'DELETE'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function (response) {
                        //console.log(response.message);
                        //table.ajax.reload();
                        //$("#alert-success").html(response.message).fadeIn('slow').fadeOut(3000);
                        swalWithBootstrapButtons.fire({
                            title: "Sucesso!",
                            text: response.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        table.ajax.reload(null, false);
                    },
                    error: function (response) {
                        json = $.parseJSON(response.responseText);
                        Swal.fire(
                            'error!',
                            json.message,
                            'error'
                        )
                    },
                    complete:function(response){
                        json = $.parseJSON(response.responseText);
                        if(json.success) {
                            window.setTimeout(function () {
                                $('#divModalImage').modal('hide');
                            }, 1500);
                        }
                    }
                });
            }
        });
    });

    /*** Fim */



    /**
     *  Preview da imagem ao passar o mause
     * */
    $(document).on("mouseover",".image" , function(e){
            let img = $(this);

            Swal.fire({
                imageUrl:  img[0].currentSrc,
                imageWidth: 350,
                imageHeight: 350,
                showConfirmButton: false,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
    });


    /**************************************
     ******* FUNÇÕES ONLAOD SISTEMA *******
     **************************************
     * */

});


/**
 *  Formatting function for row details - modify as you need
 */
    function format ( d ) {
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
    }

/**
 * Ajusta para exibição nos inputs e etc.. valor moeda!
 * */
function formatMoney(valor)
{
    const v = ((valor.replace(/\D/g, '') / 100).toFixed(2) + '').split('.');

    const m = v[0].split('').reverse().join('').match(/.{1,3}/g);

    for (let i = 0; i < m.length; i++)
        m[i] = m[i].split('').reverse().join('') + '.';

    const r = m.reverse().join('');

    return r.substring(0, r.lastIndexOf('.')) + ',' + v[1];
}
