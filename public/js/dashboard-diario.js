$(function () {

    const url = fncUrl();
    let table;
     /**
     * #########################################################################
     * ##########  ÁREA DATATABLE ###################################
     * #########################################################################
     * */

     let fncDataDatatable = async function(dataOne, dataTwo) {

        $('#datatablesDiario').DataTable().destroy();
        await $('#datatablesDiario').DataTable({
            "render": function ( data, type, row, meta ) {
                return '<a href="'+data+'">Download</a>';
            },
            "ajax":{
                "method": 'post',
                "url": url + "/dashboardDiario/vendasDia",
                "data":{dataOne: dataOne,dataTwo: dataTwo,id: 2,_token:$('meta[name="csrf-token"]').attr('content')},
                "dataType":"json",
                responsive: true,
            },
            "columns": [
                {
                    //"data": "codigo_venda",
                    "render": function ( data, type, row, meta ) {
                        return "<span data-toggle-tip=\"tooltip\" data-placement=\"top\" title="+row.venda_id+">"+row.codigo_venda+"</span>";
                    }
                },
                {"data": "nome_cli"},
                {"data": "usuario"},
                {
                    //"data": "tipo_venda",
                    "render": function ( data, type, row, meta ) {
                        if(row.tipo_venda.toUpperCase() === "PRESENCIAL"){
                            return "<span class='text-primary'>"+row.tipo_venda+"</span>"
                        }
                        return "<span class='text-danger'>"+row.tipo_venda+"</span>"
                    }
                },
                {
                    "data": "sub_total",
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display

                },
                {"data": "nome_pgto"},
                {
                    "data": "total",
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                }, 
                {
                    "data": "valor_desconto",
                    render: $.fn.dataTable.render.number('.', ',', 2, 'R$', '')
                    //render: $.fn.dataTable.render.number(',', '.', 0, '', '%')
                },
                {
                    "data": "cashback",
                    render: $.fn.dataTable.render.number('.', ',', 2, 'R$', '')
                    //render: $.fn.dataTable.render.number(',', '.', 0, '', '%')
                },
                {"data": "imposto"},
                {"data": "taxa_pgto"},
                {"data": "valor_produto"},
                {"data": "mc"},
                {"data": "percentual_mc"},
                {
                    "data": "data"
                }, {
                    "render": function ( data, type, row, meta ) {
                        return "<div class='text-center'>" +
                            "<div class='btn-group'>" +
                            "<button class='btn btn-warning btn-sm btnEdit m-1' data-toggle=\"modal\" data-value="+row.venda_id+" data-codigo-venda="+row.codigo_venda+"  " +
                            "  data-target=\"#divModalUpdate\" data-toggle-tip=\"tooltip\" data-placement=\"top\" title=\"Alterar Venda\"'>" +
                            "  <i class=\"far fa-edit\"></i>" +
                            "</button>" +
                            "<button class='btn btn-info btn-sm btnView m-1' data-toggle=\"modal\" data-codigo-venda="+row.codigo_venda+" " +
                            "  data-target=\"#divModal\" data-toggle-tip=\"tooltip\" data-placement=\"top\" title=\"Detalhes da Venda\"'>" +
                            "  <i class=\"far fa-eye\"></i>" +
                            "</button>" +
                            // "<button class='btn btn-danger btn-sm btnDelete' data-toggle=\"modal\" data-value="+row.venda_id+" " +
                            // "  data-target=\"#divModalDelete\" data-toggle-tip=\"tooltip\" data-placement=\"top\" title=\"Deletar Venda\"'>" +
                            // "  <i class=\"far fa-trash-alt\"></i>" +
                            // "</button>" +
                            "</div>" +
                            "</div>"
                    }
                }
            ],
            language: {
                "url": "../public/Portuguese-Brasil.json"
            },
            "order": [[8, "desc"]]

        });//fim datatables
    }

     /**
     * DETALHES DA VENDA
     * **/
     $(document).on("click", ".btnView", async function(event) {
        event.preventDefault();
        fncLoadDataTableModel();
        let fila = $(this).closest("tr");
        let codigo_venda = $(this).data('codigo-venda');

        await fetch(url + "/relatorio/detailSales/" + fila.find('td:eq(0)').text() )
            .then(function (response) {
                //console.log(response);
                return response.json()
            })
            .then(function (response) {

                table = $('#tableView').DataTable({
                    "data": response.dados,
                    "bInfo" : false,
                    "paging": true,
                    "ordering": true,
                    "searching": false,
                    "destroy": true,
                    "columns": [
                        {"data": "codigo_produto"},
                        {"data": "descricao"},
                        {
                            "data": "valor_produto",
                            "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display

                        },
                        {"data": "quantidade"},
                        {
                            "data": "total",
                            "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display

                        }
                    ],
                    language: {
                        "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
                    },
                    "order": [[0, "asc"]],
                    "footerCallback": function ( row, data, start, end, display ) {
                        var api = this.api(), data;

                        // Remove the formatting to get integer data for summation
                        const intVal = function (i) {
                            return typeof i === 'string' ? i.replace(/[R$ ,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                        };

                        // Total over all pages
                        total = api
                            .column( 4 )
                            .data()
                            .reduce( function (a, b) {
                                // console.log(a);
                                return parseFloat(a) + parseFloat(b);
                            }, 0 );

                        // Update footer
                        //$( api.column( 4 ).footer() ).html('R$'+ total +' total)');
                        let numFormat = $.fn.dataTable.render.number( '.', ',', 2, 'R$ ' ).display;
                        $("#foot").html("");
                        $("#foot").append('<td colspan="5" style="background:#000000; color:white; text-align: right;">Total: '+numFormat(total)+'</td>');
                    },

                });//fim datatables
            });
            $("#codigo_venda").html(codigo_venda);
    });

    /**
         * Detalhes venda no cartão
         * **/
    $(document).on("click", ".detailCart", async function(event) {
        event.preventDefault();

        let id = $(this).data('content');
       // fncLoadDataTableModel();

        await fetch(url + "/relatorio/detailCart/" + id )
            .then(function (response) {
                //console.log(response);
                return response.json()
            })
            .then(function (response) {
                table =  $('#tableViewCart').DataTable({
                    "data": response.dados,
                    "bInfo" : false,
                    "paging": true,
                    "ordering": true,
                    "searching": false,
                    "destroy": true,
                    "columns": [
                        {"data": "nome"},
                        {
                            "data": "total",
                            "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display

                        },
                        //   {"data": "taxa"},
                        {
                            "data": "totalFinal",
                            "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display

                        },
                    ],
                    language: {
                        "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
                    },
                    "order": [[0, "asc"]]
                });//fim datatables
            });
    });


    /**
         * Detalhes vendas no dinheiro po funcionário
         * **/
    $(document).on("click", ".detailDinner", async function(event) {
        event.preventDefault();

        let id = $(this).data('content');
        //fncLoadDataTableModel();

        await fetch(url + "/relatorio/detailDinner/" + id )
            .then(function (response) {
                //console.log(response);
                return response.json()
            })
            .then(function (response) {
                table =  $('#dataTableModalDinner').DataTable({
                    "data": response.dados,
                    "bInfo" : false,
                    "paging": true,
                    "ordering": true,
                    "searching": false,
                    "destroy": true,
                    "columns": [
                        {"data": "nome_usu"},
                        {
                            "data": "total",
                            "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display

                        },
                        //   {"data": "taxa"},
                        {
                            "data": "totalFinal",
                            "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display

                        },
                    ],
                    language: {
                        "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
                    },
                    "order": [[0, "asc"]]
                });//fim datatables
            });
    });
    


    /***
     * Editar Venda para alterar a forma de pagamento
     * */
    $(document).on("click", ".btnEdit", async function(event) {
        event.preventDefault();

        let venda_id = $(this).data('value');
        let codigo_venda = $(this).data('codigo-venda');
        
        const response = await fetch(url + "/relatorio/editSales/"+venda_id);
        const data = await response.json();
        // console.log(data.data);
        // console.log(data.payments);

        let html = data.data.reduce(function (string, obj) {
            return string + "<option value="+obj.id+" data-taxa="+obj.taxa+">" + obj.payments_list[0].nome +" - taxa("+ obj.taxa +")</option>"
        }, "<option value='' selected='selected'>Pagamento a ser Alterado </option>");

        $("#payments_sale").html(html);

        html = data.payments.reduce(function (string, obj) {
            return string + "<option value=" + obj.id + " data-taxa="+obj.payments_taxes[0].valor_taxa+">" + obj.nome +" - taxa("+obj.payments_taxes[0].valor_taxa+")</option>"
        }, "<option value='' selected='selected'>Forma de Pagamentos </option>");

        $("#payments").html(html);
        $("#codigo_venda").html(codigo_venda);
    });

        /**
     * #########################################################################
     * ##########  ÁREA DE FUNÇÕES DO SISTEMA   ###################################
     * #########################################################################
     * */
    let fncLoadDataTableModel = function() {
        $('tbody[id=dataTableModal]').html("<div class=\"row\">\n" +
            "    <div class=\"col-md-8 mx-auto\">\n" +
            "      <h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>\n" +
            "    </div>\n" +
            "  </div>");
    }
    

      /**
     * #########################################################################
     * ##########  EXECUÇÃO DA ROTA NO LARAVEL   ###################################
     * #########################################################################
     * */
      let fncDataBarChart = async function(dateOne, dateTwo) {
        await fetch(url+ "/relatorio/chartDay/"+dateOne+"/"+dateTwo+"/2")
            .then(function (response) {

                return response.json()
            }).then(function (response) {
                //  console.log(response);
              /*  typeChart = 'bar';

                let myArr = JSON.stringify(response.chart);
                labels = [];
                dados = [];
                bgColoR = [];
                borderColoR = [];

                JSON.parse(myArr).forEach(function (ret) {
                    // console.log(ret.data);
                    labels.push(ret.data);
                    dados.push(ret.total)
                    bgColoR.push(dynamicColors());
                    borderColoR.push(dynamicBorderColors(r, g, b));
                });*/

                $("#totalDinner").html("<div class=\"card-body text-center\">Total Dinheiro <br>" +
                    " <strong class=\"fs-5\">" +response.totalOrders.orderTotalDiner+"</strong>" +
                    "</div>");
                $("#totalCartao").html("<div class=\"card-body text-center\">Total Cartão <br>" +
                    " <strong class=\"fs-5\">" +response.totalOrders.orderTotalCart+"</strong>" +
                    "</div>");
                $("#totalDesconto").html("<div class=\"card-body text-center\">Total Desconto <br>" +
                    " <strong class=\"fs-5\">" +response.totalOrderDiscount.totalDiscount+"</strong>" +
                    "</div>");
                $("#totalDia").html("<div class=\"card-body text-center\">Total Dia <br>" +
                    " <strong class=\"fs-5\">" +response.totalOrderDay.orderTotalDay+"</strong>" +
                    "</div>");
                $("#totalMes").html("<div class=\"card-body text-center\">Total Mês <br>" +
                    " <strong class=\"fs-5\">" +response.totalOrderMonth.totalMes+"</strong></div>");
                $("#totalSemana").html("<div class=\"card-body text-center\">Total Semana <br>" +
                    " <strong class=\"fs-5\">" +response.totalsOrderWeek.totalWeek+"</strong>" +
                    " </div>");

             //   fncBarChart();
            });
    };

    /***
     * Alterar a forma de pagamento
     * */
    $("#form").submit(function(evt){
        evt.preventDefault();
    }).validate({
        errorClass: "my-error-class",
        validClass: "my-valid-class",
        rules: {
            payments_sale: {
                required: true
            },
            payments: {
                required: true
            },
        },
        messages: {
            payments_sale: {
                required: "Selecione o Pagamento a ser Alterado?"
            },
            payments: {
                required: "Selecione o Pagamento Novo?"
            },
        }, submitHandler: function(form,event) {
            event.preventDefault();
            /**
             * Recupero nova taxa no selected (Pagamento a ser Recuperado)
             * */
            let taxa = $('#payments').find(':selected').data('taxa');
            $('input[name=new_taxa]').val(taxa);

            let myForm = $(form).serialize();

            $.ajax({
                url: url+ "/relatorio/1",
                type: 'PUT',
                data: myForm,
                dataType: 'json',
                beforeSend: function () {
                    $('#salvar').html('Aguarde... <span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span>');
                },
                success: function (response) {
                    //console.log(response);

                    if(response.success) {
                        swalWithBootstrapButtons.fire({
                            title: 'OK!',
                            text: response.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        let d1 =  $('input[name=dataini]').val() !=="" ? $('input[name=dataini]').val().replaceAll("/","") : 0;
                        let d2 =  $('input[name=dataini]').val() !=="" ? $('input[name=datafim]').val().replaceAll("/","") : 0;
                        fncDataDatatable(d1, d2).then();
                    }

                },
                error: function (response) {
                   // console.log(response);
                    let json = $.parseJSON(response.responseText);
                    Swal.fire(
                        'error!',
                        json.message,
                        'error'
                    )
                },
                complete: function () {
                    $('#salvar').html('Salvar');
                }
            });
        }
    });
    /**
     * #########################################################################
     * ##########  ÁREA EXECUÇÃO DE FUNCÇÕES ONLOAD ############################
     * #########################################################################
     * */
    fncDataDatatable("", "").then();
    fncDataBarChart(0,0).then();
});