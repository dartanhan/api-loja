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

                    return "<div class='text-center'>" +
                            "<span data-toggle=\"tooltip\" data-placement=\"right\"  title='Alterar status da venda'> "+
                                "<select name='status-venda' id='status-venda' class='form-select form-select-sm select-status-venda'" +
                                    " data-cart-id="+row.id+" data-user-id="+row.usuario[0].id+" data-cliente-id="+row.clientes[0].id+">"+
                                    "<option value=''>ALTERAR O STATUS</option>"+
                                    "<option value='ABERTO'>ABERTO</option>"+
                                    "<option value='CANCELADO'>CANCELADO</option>"+
                                    //"<option value='PAGO'>PAGO</option>"+
                                "</select>"+
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
            "url": "../public/Portuguese-Brasil.json"
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

    // Usar delegação de eventos para capturar a mudança nos selects dinâmicos
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
