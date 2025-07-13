import {sweetAlert,formatMoney} from './comum.js';

const url = fncUrl();
let json,table;


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
            {
                "data": "user_id",
                render: function (data, type, row) {
                    return row.usuario[0].id;
                }
            },
            {
                "data": "cliente_id",
                render: function (data, type, row) {
                    return row.clientes[0].id;
                }
            },
            {"data": "atendente",
                render: function (data, type, row) {
                    return row.usuario[0].nome;
                }
            },
            {
                "data": "cliente",
                render: function (data, type, row) {
                    if (row.clientes && row.clientes.length > 0) {
                        return row.clientes[0].nome;
                    } else {
                        // Caso não exista ou esteja vazio, retorne uma mensagem ou valor padrão
                        return 'Cliente não disponível';
                    }
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

                    return "<div class='text-center d-flex align-items-center'>" +
                                "<span class='whatsapp' data-telefone='"+row.clientes[0].telefone+"' style='margin-right: 10px; color: green;cursor: pointer'  data-toggle=\"tooltip\" data-placement=\"right\"  title='Enviar para para o whatsapp'>" +
                                "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\" style='width: 25px; height: 20px; fill: currentColor;'>" +
                                    "<!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->" +
                                    "<path d=\"M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 " +
                                    "17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 " +
                                    "18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 " +
                                    "56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 " +
                                    "21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z\"/>" +
                                "</svg>"+
                                "</span>" +
                                "<span data-toggle=\"tooltip\" data-placement=\"right\"  title='Alterar status da venda'> "+
                                    "<select name='status-venda' id='status-venda' class='form-select form-select-sm select-status-venda'" +
                                        " data-cart-id="+row.id+" data-user-id="+row.usuario[0].id+" data-cliente-id="+row.clientes[0].id+">"+
                                        "<option value=''>STATUS</option>"+
                                        "<option value='ABERTO'>ABERTO</option>"+
                                        "<option value='CANCELADO'>CANCELADO</option>"+
                                        //"<option value='PAGO'>PAGO</option>"+
                                    "</select>"+
                                "</span>" +
                        "</div>";
                }
            }

        ],
        "columnDefs": [
            {
                "targets": [1,2],
                "visible": false,
                "searchable": false
            }
        ],
        language: {
            "url": Helpers.asset("Portuguese-Brasil.json")
        },
        "order": [[0, "desc"]],
        drawCallback: function() {
            $('[data-toggle="tooltip"]').tooltip();
        }

    });


    /**
     * Detalhes da venda
     * */
    $('#table tbody').on('click', 'td.details-control', function (event) {
        event.preventDefault();

        let tr = $(this).closest('tr');
        let row = table.row( tr );

        //console.log(row.data().user_id, row.data().cliente_id);
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            // row.child( format(row.data()) ).show();
            tr.addClass('shown');

            // Tabela inicial para os detalhes
            let tmpRow = `
                <table class='table table-striped table-condensed'>
                    <thead class="text-center">
                        <tr class='bg-secondary'>
                            <th>IMAGEM</th>
                            <th>CÓDIGO PRODUTO</th>
                            <th>NOME</th>
                            <th>PREÇO</th>
                            <th>QUANTIDADE</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
            `;


            $.ajax({
                url: url + "/sale/tableItemSale",
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    user_id: row.data().user_id,
                    cliente_id : row.data().cliente_id
                },
                dataType: 'json',
                beforeSend: function () {
                    row.child('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>').show();
                },
                success: function (response) {
                      console.log(response.data);
                    if (response.data) {
                        let arrayProducts = response.data;

                        // Loop para preencher as linhas da tabela
                        arrayProducts.forEach(function (arrayItem) {
                            tmpRow += `
                            <tr>
                                <td><img src="../public/storage/${arrayItem.imagem}" data-toggle="tooltip"
                                    data-placement="right" title="Imagem do Produto" class="img-thumbnail"
                                    style="width: 50px; height: 50px;">
                                </td>
                                <td>${arrayItem.codigo_produto}</td>
                                <td>${arrayItem.name}</td>
                                <td>${formatMoney(arrayItem.price)}</td>
                                <td>${arrayItem.quantidade}</td>
                                <td><span class="badge bg-warning">${arrayItem.status}</span></td>
                            </tr>
                        `;
                        });

                        // Fechar a tabela
                        tmpRow += `</tbody></table>`;
                        row.child(tmpRow).show();

                        $('[data-toggle="tooltip"]').tooltip();

                    } else {
                        // Tratamento para quando não houver produtos ou houver um erro
                        row.child('<div class="text-center text-danger">Erro ao carregar os detalhes.</div>').show();

                    }
                },
                error: function () {
                    // Caso a requisição falhe
                    row.child('<div class="text-center text-danger">Erro ao carregar os detalhes.</div>').show();
                }

            });
        }
    });

    /***
     * whatsapp
     * */
    $(document).on('click', '.whatsapp', function() {
        let pedidoId = 1234;
        let numeroTelefone = "55"+$(this).data('telefone');

        const linkVenda = `https://kncosmeticos.com/pedido/validar?pedido_id=${pedidoId}`;
        const mensagem = `Olá, por favor valide seu pedido neste link: ${encodeURIComponent(linkVenda)}`;
        const linkWhatsapp = `https://wa.me/${numeroTelefone}?text=${mensagem}`;

        // Abre o link no WhatsApp
        window.open(linkWhatsapp, '_blank');

    });

    /**
     *  Usar delegação de eventos para capturar a mudança nos selects dinâmicos
    **/
    $(document).on('change', '.select-status-venda', function() {
       // console.log("status-venda", $(this).val());
        let cartId = $(this).data('cart-id');
        let usuarioId = $(this).data('user-id');
        let clienteId = $(this).data('cliente-id');
        let status = $(this).val();
        let selectElement = $(this);

       // console.log(usuarioId,clienteId);
        if(status !== ''){
            Swal.fire({
                title: 'Tem certeza?',
                text: "Deseja alterar o status da venda e retornar ao carrinho?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, alterar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Envia a requisição para o Controller
                    $.ajax({
                        url: url +'/sale/updateStatus',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: cartId,
                            status: status,
                            usuarioId: usuarioId,
                            clienteId:clienteId
                        },
                        success: function(response) {
                            if(response.success){
                                sweetAlert({
                                        title : 'Alterado!',
                                        text: response.message,
                                        icon: 'success',
                                        showConfirmButton: false,
                                        timer: 1500
                                });

                                // Atualize a tabela para refletir a mudança
                                table.ajax.reload(null, false); // Recarrega a tabela sem resetar a paginação
                            }else{
                                sweetAlert({
                                        title: 'Atenção!',
                                        text: response.message,
                                        icon: 'warning'
                                    });
                                selectElement.val('');
                            }
                        },
                        error: function() {
                            sweetAlert({
                                title: 'Erro!',
                                text: 'Ocorreu um erro ao atualizar o status da venda.',
                                icon: 'error'
                            });
                        }
                    });
                }else {
                    // Caso o usuário cancele, reseta o select para a opção "SELECIONE"
                    selectElement.val('');
                }
            });
        }
    });
});
