import {fncDataDatatable,getDataFormat,botaoLoad,getIconHideMoney} from './comum.js';

const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const url = fncUrl();
let table;
let startDate,endDate,id;
let dataIni = $('input[name=dataIni]');
let dataFim = $('input[name=dataFim]');
let todosVisiveis = false;

$(function () {


    /**
     * #########################################################################
     * ##########  ÁREA DATATABLE ###################################
     * #########################################################################
     * */

    table =  $('#datatablesDiario').DataTable({
        dom: 'Bfrtip', // Ativa os botões de exportação
        "ajax":{
            "method": 'post',
            "url": url + "/dashboardDiario/vendasDia",
            "data":function(data){
                data.id = 2,
                data._token = token,
                data.dataIni = getDataFormat(dataIni.val(),'DD/MM/YYYY','YYYY-MM-DD');
                data.dataFim = getDataFormat(dataFim.val(),'DD/MM/YYYY','YYYY-MM-DD');
            },

            "dataType":"json",
            responsive: true,
            dataSrc: function(json) {

                $("[data-key='taxes']")
                    .attr("data-valor",json.total_imposto)
                    .each(function () {
                        if (!$(this).text().includes("*")) {
                            $(this).text(json.total_imposto);
                        }
                    });

                $("[data-key='tmc']")
                    .attr("data-valor", json.total_mc)
                    .each(function () {
                        if (!$(this).text().includes("*")) {
                            $(this).text(json.total_mc);
                        }
                    });

                $("[data-key='tpmc']")
                    .attr("data-valor", json.total_precentual_mc)
                    .each(function () {
                        if (!$(this).text().includes("*")) {
                            $(this).text(rjson.total_precentual_mc);
                        }
                    });
                /*$("[data-key='taxes']").attr("data-valor", json.total_imposto);
                $("[data-key='tmc']").attr("data-valor", json.total_mc);
                $("[data-key='tpmc']").attr("data-valor", json.total_precentual_mc);*/

                return json.data;
            }
        },
        "columns": [
            {
                "className": 'details-control',
                "orderable": false,
                "data": null,
                "defaultContent": ''
            },
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
            {"data": "nome_pgto"},
            {
                "data": "sub_total",
                "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display

            },
            {
                "data": "total_geral",
                "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display

            },
            {
                "data": "data"
            }, {
                "render": function ( data, type, row, meta ) {
                return "<div class='text-center'>" +
                            "<div class='btn-group'>" +
                                "<button class='btn btn-warning btn-sm btnEdit m-1'  " +
                                " data-target=\"#divModalUpdate\" data-value="+row.venda_id+" data-codigo-venda="+row.codigo_venda+"  " +
                                "   data-toggle=\"tooltip\" data-placement=\"top\" title=\"Alterar Venda\"'>" +
                                "  <i class=\"far fa-edit\"></i>" +
                                "</button>" +


                                "<button  data-toggle='tooltip' "+
                                " data-placement='top' title='Detalhes da Venda' "+
                                " class=\"btn btn-info btn-sm btnView m-1\" data-codigo-venda="+row.codigo_venda+"> "+
                                " <i class=\"far fa-eye\"></i>" +
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
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Exportar para Excel',
                    title: 'Minha Tabela',
                    className: 'btn btn-success',
                    exportOptions: {
                        modifier: {
                            search: 'applied',
                            order: 'applied'
                        }
                    }
                }
            ],
        language: {
            "url": "../public/Portuguese-Brasil.json"
        },
        "order": [[8, "desc"]],
        "initComplete": function(settings, json) {
            $('[data-toggle="tooltip"]').tooltip();
        },"xhr": function(settings, json) {
            $('[data-toggle="tooltip"]').tooltip();
        }

    });//fim datatables

    $('#datatablesDiario tbody').on('click', 'td.details-control', function (event) {
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
             row.child( format(row.data()) ).show();
             tr.addClass('shown');
        }
    });

    function format ( d ) {
        let numero = d.mc.replace(/R\$\s?/g, '').replace(/\./g, '').replace(',', '.');

        // Converter para número
        let numeroFloat = parseFloat(numero);

        let mc = "<span class='text-primary'>"+d.mc+"</span>"
        if(numeroFloat < 0){
            mc = "<span class='text-danger'>"+d.mc+"</span>"
        }

        let pmc = "<span class='text-primary'>"+d.percentual_mc+"</span>";
        if(d.percentual_mc.replace("%","") < 0){
            pmc = "<span class='text-danger'>"+d.percentual_mc+"</span>"
        }


        return '<table class="table table-striped table-condensed">'+
                    "<thead class=\"text-center\">" +
                        "<tr class='bg-secondary '>" +
                            "<th>Sub Total</th>" +
                            "<th>Desconto</th>" +
                            "<th>Cashback</th>" +
                            "<th>Frete(Motoboy)</th>" +
                            "<th>Taxa</th>" +
                            "<th>Imposto</th>" +
                            "<th>Total Final</th>" +
                            "<th>Valor Produto</th>" +
                            "<th>MC</th>" +
                            "<th>% MC</th>" +
                        "</tr>" +
                    "</thead>"+
                    '<tr>'+
                        '<td>'+d.sub_total+'</td>'+
                        '<td>'+d.valor_desconto+'</td>'+
                        '<td>'+d.cashback+'</td>'+
                        '<td>'+d.moto_taxa+'</td>'+
                        '<td>'+d.taxa_pgto+'</td>'+
                        '<td>'+d.imposto+'</td>'+
                        '<td>'+d.total_final+'</td>'+
                        '<td>'+d.valor_produto+'</td>'+
                        '<td>'+mc+'</td>'+
                        '<td>'+pmc+'</td>'+
                    '</tr>'+
                '</table>';
    }
    /**
     * #########################################################################
     * ##########  ÁREA DE FILTRO DE DATAS   ###################################
     * #########################################################################
     * */
    $('#data input').datepicker({
        'language' : 'pt-BR',
        'todayBtn': true,
        'todayHighlight':true,
        'weekStart':0,
        'orientation':'bottom',
        'autoclose':true
    });

    $('#data_ano [name=ano]').datepicker({
        'language' : 'pt-BR',
        'todayHighlight':true,
        'orientation':'bottom',
        'autoclose':true,
        'multidate':false,
        'format': "yyyy",
        'viewMode': "years",
        'minViewMode': "years"
    });

       /**
     * #########################################################################
     * ##########  ÁREA EXECUÇÃO DE FUNÇÕES ONLOAD ############################
     * #########################################################################
     * */

     fncDataBarChart(moment().format('YYYY-MM-DD'),moment().format('YYYY-MM-DD'));

   // getFncDataCardTotalProdutoPorVenda("","",2);
});

     /**
     * DETALHES DA VENDA
     * **/
     $(document).on("click", ".btnView", async function (event) {
        event.preventDefault();

        // Abrir o modal e mostrar o spinner
        $('#divModal').modal('show');

        let fila = $(this).closest("tr");
        let codigo_venda =  $(this).data('codigo-venda');

        // Inicializar a DataTable com a opção ajax
         $('#tableView').DataTable().destroy();
        $('#tableView').DataTable({
            "ajax":{
                "method": 'post',
                "url": url + "/relatorio/detailSales",
                "data":function(data){
                    data.codigo_venda = codigo_venda;
                    data._token = token;
                },
                "dataType":"json",
                responsive: true,
                processing: true,
                serverSide: true,
                destroy : true,
            },
            "columns": [
                { "data": "codigo_produto" },
                { "data": "descricao" },
                {
                    "data": "valor_produto",
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                },
                {
                    "data": "valor_venda",
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                },
                { "data": "quantidade" },
                {
                    "data": "valor_total",
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                }
            ],
            "language": {
                "url": "../public/Portuguese-Brasil.json"
            },
            "order": [[0, "asc"]],
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                const intVal = function (i) {
                    return typeof i === 'string' ? i.replace(/[R$ ,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                };

                // Total over all pages
                let total = api
                    .column(5)
                    .data()
                    .reduce(function (a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);

                // Update footer
                let numFormat = $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display;
                $("#foot").html("");
                $("#foot").append('<td colspan="6" style="background:#000000; color:white; text-align: right;">Total: ' + numFormat(total) + '</td>');
                $('[data-toggle="tooltip"]').tooltip();
                $('span[name="codigo_venda"]').text(codigo_venda);
            },
            "initComplete": function (settings, json) {

            }
        });
    });


    /**
         * Detalhes venda no cartão
         * **/
    $(document).on("click", ".detailCart", async function(event) {
        event.preventDefault();

        let id = $(this).data('content');

        startDate = getDataFormat(dataIni.val(),'DD/MM/YYYY','YYYY-MM-DD');
        endDate = getDataFormat(dataFim.val(),'DD/MM/YYYY','YYYY-MM-DD');

        const data = {
            id: id,
            startDate: startDate,
            endDate:endDate,
            _token:token
        };

        try {
            $('#tableViewCart').DataTable().destroy();
            $('#tableViewCart').DataTable({
                "ajax":{
                    "method": 'post',
                    "url": url + "/relatorio/detailCart",
                    "data":data,
                    "dataType":"json",
                    responsive: true,
                },
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
                       {
                           "data": "taxa",
                           "render": function(data, type, row) {
                               // Converte o valor para percentual
                               return data + '%';
                           }
                       },
                    {
                        "data": "totalFinal",
                        "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display

                    },
                ],
                language: {
                    "url": "../public/Portuguese-Brasil.json"
                },
                "order": [[0, "asc"]],
                "footerCallback": function ( row, data, start, end, display ) {
                        var api = this.api(), data;

                        // Remove the formatting to get integer data for summation
                        const intVal = function (i) {
                            return typeof i === 'string' ? i.replace(/[R$ ,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                        };

                        // Total over all pages
                        let total = api
                            .column(1)
                            .data()
                            .reduce(function (a, b) {
                                // console.log(a);
                                return parseFloat(a) + parseFloat(b);
                            }, 0);

                        // Update footer
                        //$( api.column( 4 ).footer() ).html('R$'+ total +' total)');
                        let numFormat = $.fn.dataTable.render.number( '.', ',', 2, 'R$ ' ).display;
                        $("#foot").html("");
                        $("#foot").append(
                            '<td colspan="2" style="background:#000000;color:white; text-align: right;">'+
                            'Total: '+numFormat(total)+'</td>'
                        );
                    },initComplete: function(settings, json) {
                        $('span[name="periodo"]').text(
                            getDataFormat(startDate,'YYYY-MM-DD','DD/MM/YYYY')
                             + " até " +
                             getDataFormat(endDate,'YYYY-MM-DD','DD/MM/YYYY')
                        );
                    },
            });//fim datatables
        } catch (error) {
            console.error("There was a problem with the fetch operation:", error);
        }
    });


    /**
         * Detalhes vendas no dinheiro po funcionário
         * **/
    $(document).on("click", ".detailDinner", async function(event) {
        event.preventDefault();

        let id = $(this).data('content');

        startDate = getDataFormat(dataIni.val(),'DD/MM/YYYY','YYYY-MM-DD');
        endDate = getDataFormat(dataIni.val(),'DD/MM/YYYY','YYYY-MM-DD');

        const data = {
            id: id,
            startDate: startDate,
            endDate:endDate,
            _token:token
        };

        try {
            $('#dataTableModalDinner').DataTable().destroy();
            $('#dataTableModalDinner').DataTable({
                "ajax":{
                    "method": 'post',
                    "url": url + "/relatorio/detailDinner",
                    "data":data,
                    "dataType":"json",
                    responsive: true,
                },
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
                    "url": "../public/Portuguese-Brasil.json"
                },
                "order": [[0, "asc"]],
                initComplete: function(settings, json) {
                    $('span[name="periodo"]').text(
                        getDataFormat(startDate,'YYYY-MM-DD','DD/MM/YYYY')
                         + " até " +
                         getDataFormat(endDate,'YYYY-MM-DD','DD/MM/YYYY') );
                }
            });//fim datatables

        } catch (error) {
            console.error("There was a problem with the fetch operation:", error);
        }
    });



    /***
     * Editar Venda para alterar a forma de pagamento
     * */
    $(document).on("click", ".btnEdit", async function(event) {
        event.preventDefault();
        let payment_id;
        let venda_id = $(this).data('value');
        let codigo_venda = $(this).data('codigo-venda');

        const response = await fetch(url + "/relatorio/editSales/"+venda_id);
        const data = await response.json();
        // console.log(data.data);
         console.log(data.payments);

        let html = data.data.reduce(function (string, obj) {
            payment_id = obj.id;
            return string + "<option value="+obj.id+" data-taxa="+obj.taxa+">" + obj.payments_list[0].nome +" - taxa("+ obj.taxa +")</option>"
        }, "<option value='' selected='selected'>Pagamento a ser Alterado </option>");

        $("#payments_sale").html(html);

        let filteredPayments = data.payments.filter(obj => obj.payments_taxes && obj.payments_taxes.length > 0 && obj.payments_taxes[0].hasOwnProperty('valor_taxa'));
        html = filteredPayments.reduce(function (string, obj) {
            return string + "<option value='" + obj.id + "' data-taxa='" + obj.payments_taxes[0].valor_taxa + "'>" + obj.nome + " - taxa(" + obj.payments_taxes[0].valor_taxa + ")</option>";
        }, "<option value='' selected='selected'>Forma de Pagamentos </option>");

        $("#payments").html(html);

        // Usar text() em vez de html() para span
        $('span[name="codigo_venda"]').text(codigo_venda);

        // Abrir o modal após preencher os dados
        $('#divModalUpdate').modal('show');
    });


      /**
     * #########################################################################
     * ##########  EXECUÇÃO DA ROTA NO LARAVEL   ###################################
     * #########################################################################
     * */
      let fncDataBarChart = async function(dateOne, dateTwo) {
        const endpoint = `${url}/relatorio/chartDay/`+dateOne+`/`+dateTwo+`/2`;
        await fetch(endpoint)
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
                $("[data-key='diner']")
                    .attr("data-valor", response.totalOrders.orderTotalDiner)
                    .each(function () {
                        if (!$(this).text().includes("*")) {
                            $(this).text(response.totalOrders.orderTotalDiner);
                        }
                    });
                $("[data-key='cart']")
                    .attr("data-valor", response.totalOrders.orderTotalCart)
                    .each(function () {
                        if (!$(this).text().includes("*")) {
                            $(this).text(response.totalOrders.orderTotalCart);
                        }
                    });

                $("[data-key='discount']")
                    .attr("data-valor", response.totalOrderDiscount.totalDiscount)
                    .each(function () {
                        if (!$(this).text().includes("*")) {
                            $(this).text(response.totalOrderDiscount.totalDiscount);
                        }
                    });

                $("[data-key='day']")
                    .attr("data-valor", response.totalOrderDay.orderTotalDay)
                    .each(function () {
                        if (!$(this).text().includes("*")) {
                            $(this).text(response.totalOrderDay.orderTotalDay);
                        }
                    });

                $("[data-key='week']")
                    .attr("data-valor", response.totalsOrderWeek.totalWeek)
                    .each(function () {
                        if (!$(this).text().includes("*")) {
                            $(this).text(response.totalsOrderWeek.totalWeek);
                        }
                    });

                $("[data-key='month']")
                    .attr("data-valor", response.totalOrderMonth.totalMes)
                    .each(function () {
                        if (!$(this).text().includes("*")) {
                            $(this).text(response.totalOrderMonth.totalMes);
                        }
                    });

                // $("[data-key='cart']").attr("data-valor", response.totalOrders.orderTotalCart);
                // $("[data-key='discount']").attr("data-valor", response.totalOrderDiscount.totalDiscount);
                // $("[data-key='day']").attr("data-valor", response.totalOrderDay.orderTotalDay);
                // $("[data-key='week']").attr("data-valor", response.totalsOrderWeek.totalWeek);
                // $("[data-key='month']").attr("data-valor", response.totalOrderMonth.totalMes);

                //esconde o spinner
                $(".spinner-border").hide();
                atualizarValoresVisiveis();
            });
    };
    /**
     * Habilita e desabilita a visão do dinheiro nos cards
     * */
    $(document).ready(function () {

        // Alterna todos os valores ao clicar no botão global
        $("#toggle-todos").on("click", function () {
            todosVisiveis = !todosVisiveis;


            $(".valor-oculto").each(function () {
                const valor = $(this).attr("data-valor");

                if (todosVisiveis) {
                    $(this).text(valor);
                } else {
                    $(this).text("****");
                }
            });

            // Atualiza todos os ícones individuais
            $(".toggle-visibilidade i").each(function () {
                $(this)
                    .removeClass(todosVisiveis ? "fa-eye-slash" : "fa-eye")
                    .addClass(todosVisiveis ? "fa-eye" : "fa-eye-slash");
            });

            // Atualiza o ícone e texto do botão global
            const icon = $(this).find("i");
            const label = todosVisiveis ? "Ocultar Valores" : "Mostrar Valores";

            icon
                .removeClass(todosVisiveis ? "fa-eye-slash" : "fa-eye")
                .addClass(todosVisiveis ? "fa-eye" : "fa-eye-slash");

            $(this).html(`<i class="fas ${todosVisiveis ? 'fa-eye' : 'fa-eye-slash'}"></i> ${label}`);
        });

        // Ainda permite usar o toggle individual
        $(".toggle-visibilidade").on("click", function () {
            const targetId = $(this).data("target");
            const $valor = $("#" + targetId);
            const $icon = $(this).find("i");

            const visivel = !$valor.text().includes("*");

            if (visivel) {
                $valor.text("****");
                $icon.removeClass("fa-eye").addClass("fa-eye-slash");
            } else {
                // PEGA O VALOR ATUALIZADO!
                const novoValor = $valor.attr("data-valor");
                $valor.text(novoValor);
                $icon.removeClass("fa-eye-slash").addClass("fa-eye");
            }
        });

    });


    function atualizarValoresVisiveis() {
        if (todosVisiveis) {
            $(".valor-oculto").each(function () {
                const valorAtual = $(this).attr("data-valor"); // <- aqui está a diferença
                $(this).text(valorAtual);
            });
        }
    }


/*******************************************************
     *********** FILTRO ALL BAR CHART **********************
     * *****************************************************/
     $( ".btn-enviar" ).on("click", function() {
         $(".spinner-border").show();//exibe o spinner

         let isValid = true;
         let msg = '';

         //console.log(dataIni);

         // Check if dataIni is filled
         if (!dataIni) {
             msg = "Por favor, preencha a Data Inicio."
            isValid = false;
        }

        if (!dataFim && isValid === true) {
            msg = "Por favor, preencha a Data Fim.";
            isValid = false;
        }

        // If both fields are filled, submit the form
        if (isValid) {
            fncLoad("<div class=\"card-body\">Aguarde...</div><div class=\"spinner-border spinner-border-sm ms-auto\" role=\"status\" aria-hidden=\"true\"></div>");

            startDate =  getDataFormat(dataIni.val(),'DD/MM/YYYY','YYYY-MM-DD');
            endDate =  getDataFormat(dataFim.val(),'DD/MM/YYYY','YYYY-MM-DD');

            fncDataBarChart(startDate,endDate); // atualiza os cards de totais
            fncDataDatatable(table);

        }else{
            sweetAlert({
                title: "Atenção",
                text: msg,
                icon: 'warning',
                showConfirmButton: false,
                timer: 1500
            });
        }
    });

    /***
     * Reseta as informações
     */
    $(".btn-limpar").on("click", function () {
        //fncLoad("<div class=\"card-body\">Aguarde...</div><div class=\"spinner-border spinner-border-sm ms-auto\" role=\"status\" aria-hidden=\"true\"></div>");

        dataIni.val("");
        dataFim.val("");

        fncDataBarChart(moment().format('YYYY-MM-DD'),moment().format('YYYY-MM-DD')).then();
        fncDataDatatable(table);
        atualizarValoresVisiveis();
    });

    /***
     * Alterar a forma de pagamento
     * */
    $("#form").on("submit",function(evt){
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
                    //$('#salvar').html('Aguarde... <span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span>');
                    botaoLoad('salvar');
                },
                success: function (response) {
                    //console.log(response);

                    if(response.success) {
                        sweetAlert({
                            title: 'OK!',
                            text: response.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                      //  let d1 =  $('input[name=dataini]').val() !=="" ? $('input[name=dataini]').val().replaceAll("/","") : 0;
                      //  let d2 =  $('input[name=dataini]').val() !=="" ? $('input[name=datafim]').val().replaceAll("/","") : 0;
                        fncDataDatatable(table);
                        $('#divModalUpdate').modal('hide');
                    }

                },
                error: function (response) {
                   // console.log(response);
                   let json = $.parseJSON(response.responseText);
                   sweetAlert(
                        {
                            title: 'Error!',
                            text: json.message,
                            icon: 'danger',
                            showConfirmButton: false,
                            timer: 1500
                        }
                    );
                },
                complete: function () {
                    $('#salvar').html('Salvar');
                }
            });
        }
    });

    /**
     verificar esse status e exibir o botão de alerta se necessário:
    * */
    document.addEventListener('DOMContentLoaded', function () {
        const alertaButton = document.getElementById('alertaBaixoEstoque');

        function verificarEstoque() {
            fetch(url + "/dashboardDiario/estoqueBaixo")
                .then(response => response.json())
                .then(data => {
                    if (data.existe_estoque_baixo) {
                        alertaButton.style.display = 'block'; // Exibe o botão se houver estoque baixo
                    } else {
                        alertaButton.style.display = 'none'; // Caso contrário, esconde
                    }
                })
                .catch(error => {
                    console.error('Erro ao verificar estoque:', error);
                });
        }

        // Verifica a cada 30 segundos
        //setInterval(verificarEstoque, 30000);
        verificarEstoque(); // Primeira verificação ao carregar a página
    });
