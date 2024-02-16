// Set new default font family and font color to mimic Bootstrap's default styling
$(function () {
    Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#292b2c';

    const url = fncUrl();

    let table;
    let total = "";
    let labels = [];
    let dados = [];
    let bgColoR = [];
    let borderColoR = [];
    let r , g ,b, alpha,d1,d2;
    let titulo = "";
    let typeChart =null;
    let year =  new Date().getFullYear();
    let ctx = document.getElementById("myBarChart");
    let newCtxChart = new Chart(ctx);
    let ctxLine = document.getElementById("myAreaChart");
    let newCtxChartLine = new Chart(ctxLine);
    let ctxLineMulti = document.getElementById("myLineMultiChart");
    let newCtxChartLineMulti = new Chart(ctxLineMulti);

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    });

    /***
     * Id da loja, para retorno do dados referente a loja
     * */
    let fncIdStore = function(){
        return $('input[name=store_id]').val();
    };

    /**
     * Data Corrente ou data da pesquisa
     * */
    /*let periodDate = function () {
        return (new Date()).toISOString().split('T')[0];
    }*/


    /**
     * #########################################################################
     * ##########  EXECUÇÃO DA ROTA NO LARAVEL   ###################################
     * #########################################################################
     * */
    let fncDataBarChart = async function(dateOne, dateTwo) {
        await fetch(url+ "/relatorio/chartDay/"+dateOne+"/"+dateTwo+"/"+fncIdStore())
            .then(function (response) {

                return response.json()
            }).then(function (response) {
                //  console.log(response);
                typeChart = 'bar';

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
                });

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

                fncBarChart();
            });
    };

    let fncDataLineChart = async function(ano) {
        await fetch(url+ "/relatorio/chartLineGroupYear/"+ano)
            .then(function (response) {
                return response.json();
            }).then(function (response) {
                fncLineChart(response);
            });
    };

    let fncDataLineMultiChart = async function() {
        await fetch(url+ "/relatorio/chartLineMultiGroupYear/")
            .then(function (response) {
                return response.json();
            }).then(function (response) {
                fncLineMultiChart(response);
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
                    console.log(response);

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
     * ##########  CRIAÇÃO DO CHART E CONFIGURAÇÕES  ###########################
     * #########################################################################
     * */
    let fncBarChart = function() {
        // Bar Chart Example
        newCtxChart.destroy();
        newCtxChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    //label: "# ",
                    backgroundColor: bgColoR,
                    borderColor: borderColoR,
                    data: dados,
                    borderWidth: 2
                }],
            }, options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        //label: function (tooltipItem, chart) {
                        //var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        //return datasetLabel + ': R$ ' + number_format(tooltipItem.yLabel, 2, ',', '.');
                        label: function (tooltipItem) {
                            return ' R$ ' + number_format(tooltipItem.yLabel, 2, ',', '.');
                        }
                    }
                },
                layout: {
                    padding: {
                        left: 0,
                        right: 0,
                        top: 0,
                        bottom: 0
                    }
                },
                title: {
                    display: false,
                    text: titulo,
                    fontColor: "#333",
                    fontSize: 20,
                    padding: 20
                },
                legend: {
                    display: false,
                }
            }
        });
        fncLoadChartBar("close");

    }

    let fncLineChart = function(response) {

        newCtxChartLine.destroy();
        newCtxChartLine = new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: response.months,
                datasets: [{
                    label: "R$ ",
                    lineTension: 0.3,
                    backgroundColor: "rgba(2,117,216,0.2)",
                    borderColor: "rgba(2,117,216,1)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(2,117,216,1)",
                    pointBorderColor: "rgba(255,255,255,0.8)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(2,117,216,1)",
                    pointHitRadius: 50,
                    pointBorderWidth: 2,
                    data: response.values,
                }],
            },
            options: {
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'date'
                        },
                        gridLines: {
                            display: false
                        },
                        //ticks: {
                        //   maxTicksLimit: 7
                        // }
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            //max: 40000,
                            //maxTicksLimit: 5
                        },
                        gridLines: {
                            color: "rgba(0, 0, 0, .125)",
                        }
                    }],
                },tooltips: {
                    callbacks: {
                        //label: function (tooltipItem, chart) {
                        //var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        //return datasetLabel + ': R$ ' + number_format(tooltipItem.yLabel, 2, ',', '.');
                        label: function (tooltipItem) {
                            return ' R$ ' + number_format(tooltipItem.yLabel, 2, ',', '.');
                        }
                    }
                },
                legend: {
                    display: false
                }
            }
        });
        fncLoadCharLine("close");
    }


    let fncLineMultiChart = function(response) {
       // console.log(response);

        newCtxChartLineMulti.destroy();
        newCtxChartLineMulti = new Chart(ctxLineMulti, {
            type: 'line',
            data: {
                labels: response.months,
                datasets: response.data
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },tooltips: {
                    callbacks: {
                        label: function (tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': R$ ' + number_format(tooltipItem.yLabel, 2, ',', '.');
                        }
                    }
                }
            }
        });
        //fncLoadCharLine("close");
    }

    /**
     * #########################################################################
     * ##########  ÁREA DATATABLE ###################################
     * #########################################################################
     * */

    let fncDataDatatable = async function(dataOne, dataTwo) {

        $('#datatablesSimple').DataTable().destroy();
        await $('#datatablesSimple').DataTable({
            "render": function ( data, type, row, meta ) {
                return '<a href="'+data+'">Download</a>';
            },
            "ajax":{
                "method": 'post',
                "url": url + "/relatorio/dailySalesList",
                "data":{dataOne: dataOne,dataTwo: dataTwo,id: fncIdStore(),_token:$('meta[name="csrf-token"]').attr('content')},
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
                {"data": "usuario"},
                {"data": "nome_pgto"},
                {
                    "data": "sub_total",
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
                {
                    "data": "total",
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                }, {
                    "data": "data"
                }, {
                    "render": function ( data, type, row, meta ) {
                        return "<div class='text-center'>" +
                            "<div class='btn-group'>" +
                            "<button class='btn btn-warning btn-sm btnEdit' data-toggle=\"modal\" data-value="+row.venda_id+" " +
                            "  data-target=\"#divModalUpdate\" data-toggle-tip=\"tooltip\" data-placement=\"top\" title=\"Alterar Venda\"'>" +
                            "  <i class=\"far fa-edit\"></i>" +
                            "</button>" +
                            "<button class='btn btn-info btn-sm btnView' data-toggle=\"modal\" " +
                            "  data-target=\"#divModal\" data-toggle-tip=\"tooltip\" data-placement=\"top\" title=\"Detalhes da Venda\"'>" +
                            "  <i class=\"far fa-eye\"></i>" +
                            "</button>" +
                            "<button class='btn btn-danger btn-sm btnDelete' data-toggle=\"modal\" data-value="+row.venda_id+" " +
                            "  data-target=\"#divModalDelete\" data-toggle-tip=\"tooltip\" data-placement=\"top\" title=\"Deletar Venda\"'>" +
                            "  <i class=\"far fa-trash-alt\"></i>" +
                            "</button>" +
                            "</div>" +
                            "</div>"
                    }
                }
            ],
            language: {
                "url": "../public/Portuguese-Brasil.json"
            },
            "order": [[7, "desc"]]

        });//fim datatables
    }

    /**
     * Pega o valor do tipo de pagamento no selected para alteração da forma de pagamento e suas taxas
     * **/
    /*$(document).on("change", "#payments_sale",  function(event) {
        event.preventDefault();
        let taxa = $(this).find(':selected').data('taxa');

        console.log(taxa);
    });*/

    /***
     * Salva a alteração da forma de pagamento
     * */
    /* $(document).on("click", "#salvar", function(event) {
         event.preventDefault();
         let id = $("#payments_sale option:selected").attr('value');
         let taxa = $("#payments_sale").find(':selected').data('taxa');

         console.log(id +" - " +taxa);
         fncUpdatePayment(id,taxa).then();

     });*/

    /**
     * DETALHES DA VENDA
     * **/
    $(document).on("click", ".btnView", async function(event) {
        event.preventDefault();
        fncLoadDataTableModel();
        let fila = $(this).closest("tr");

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
    });

    /**
     * Detalhes venda no cartão
     * **/
    $(document).on("click", ".detailCart", async function(event) {
        event.preventDefault();

        let id = $(this).data('content');
        fncLoadDataTableModel();

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
     * #########################################################################
     * ##########  ÁREA FUNÇÕES FORMATAÇÕES ###################################
     * #########################################################################
     * */

    /***
     * Formata os valores monetários para o gráfico tooltip
     * */
    let number_format = function(number, decimals, dec_point, thousands_sep) {
// *     example: number_format(1234.56, 2, ',', ' ');
// *     return: '1 234,56'
        let num = (number + '').replace(',', '').replace(' ', '');
        let n = !isFinite(+num) ? 0 : +num,
            pre = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s,
            toFixedFix = function (n, pre) {
                let k = Math.pow(10, pre);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (pre ? toFixedFix(n, pre) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < pre) {
            s[1] = s[1] || '';
            s[1] += new Array(pre - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }


    let somenteNumeros = function (num) {
        let er = /[^0-9.]/;
        er.lastIndex = 0;
        let campo = num;
        if (er.test(campo.value)) {
            campo.value = "";
        }
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

    /*    let fncLoad = function() {
            $('div[name=load]').html("<div class=\"card-body\">Aguarde...</div><div class=\"spinner-border spinner-border-sm ms-auto\" role=\"status\" aria-hidden=\"true\"></div>");
        };*/

    let fncLoadCharLine = function(param) {
        if(param === "close"){
            $('span[id=loadChartLine]').html("");
        }else {
            $('span[id=loadChartLine]').html("&nbsp;Aguarde... <div class=\"spinner-border spinner-border-sm ms-auto\" role=\"status\" aria-hidden=\"true\"></div>");
        }
    };

    let fncLoadChartBar = function(param) {
        if(param === "close"){
            $('span[id=loadChartBar]').html("");
        }else{
            $('span[id=loadChartBar]').html("&nbsp;Aguarde... <div class=\"spinner-border spinner-border-sm ms-auto\" role=\"status\" aria-hidden=\"true\"></div>");
        }
    };

    let dynamicColors = function() {
        r = Math.floor(Math.random() * 255);
        g = Math.floor(Math.random() * 255);
        b = Math.floor(Math.random() * 255);
        alpha =  0.75;
        return "rgb(" + r + "," + g + "," + b + ", "+ alpha +")";
    };

    let dynamicBorderColors = function(r,g,b) {
        return "rgb(" + r + "," + g + "," + b + ")";
    };

    let fncCardBody = function(param){
        if(param === "close")
            $('div[name=card-body]').html("");

        $('div[name=card-body]').html("Aguarde..");
    };

    /***
     * Editar Venda para alterar a forma de pagamento
     * */
    $(document).on("click", ".btnEdit", async function(event) {
        event.preventDefault();

        let venda_id = $(this).data('value');
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
    });

    /*******************************************************
     *********** FILTRO ALL BAR CHART **********************
     * *****************************************************/
    $(".btn-enviar").click(function () {
        fncLoad("<div class=\"card-body\">Aguarde...</div><div class=\"spinner-border spinner-border-sm ms-auto\" role=\"status\" aria-hidden=\"true\"></div>");
        fncLoadChartBar();
        d1 =  $('input[name=dataini]').val() !=="" ? $('input[name=dataini]').val().replaceAll("/","") : 0;
        d2 =  $('input[name=datafim]').val() !=="" ? $('input[name=datafim]').val().replaceAll("/","") : 0;

        fncDataBarChart(d1,d2).then();
        fncDataDatatable(d1,d2).then();
    });

    $(".btn-limpar").click(function () {
        fncLoad("<div class=\"card-body\">Aguarde...</div><div class=\"spinner-border spinner-border-sm ms-auto\" role=\"status\" aria-hidden=\"true\"></div>");
        fncLoadChartBar("");

        $('input[name=dataini]').val("");
        $('input[name=datafim]').val("");

        fncDataBarChart(0,0).then();
        fncDataDatatable(0,0).then();
    });

    /*******************************************************
     *********** FILTRO ANO CHART LINE *********************
     * *****************************************************/
    $(".btn-filtro-ano").click(function () {
        fncLoadCharLine("");

        let ano =  $('input[name=ano]').val() !=="" ? $('input[name=ano]').val() : year;
        fncDataLineChart(ano).then();
    });

    $(".btn-filtro-limpar-ano").click(function () {
        fncLoadCharLine("");

        $('input[name=ano]').val("");
        fncDataLineChart(new Date().getFullYear()).then();
    });

    /**
     * #########################################################################
     * ##########  ÁREA TIMER ATUALIZA AUTOMATICAMENTE OS DADOS ################
     * #########################################################################
     * */
    let startTimer = function(duration, display) {
        let timer = duration, minutes, seconds;
        setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;
            display.textContent = minutes + ":" + seconds;
            if (--timer < 0) {
                timer = duration;

                d1 =  $('input[name=dataini]').val() !=="" ? $('input[name=dataini]').val().replaceAll("/","") : 0;
                d2 =  $('input[name=datafim]').val() !=="" ? $('input[name=datafim]').val().replaceAll("/","") : 0;

                fncDataBarChart(d1,d2).then();
                fncDataDatatable(d1,d2).then();
            }
        }, 1000);
    }

    /**
     * #########################################################################
     * ##########  ÁREA EXECUÇÃO DE FUNCÇÕES ONLOAD ############################
     * #########################################################################
     * */
    let duration = 60 * 10; // Converter para segundos
    let display = document.querySelector('#contador'); // selecionando o timer
    startTimer(duration, display); // iniciando o timer

    fncDataBarChart(0,0).then();
    fncDataLineChart(year).then();
    fncDataLineMultiChart().then();
    fncCardBody("close");
    fncDataDatatable("", "").then();
});
