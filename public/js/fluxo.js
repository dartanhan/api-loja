/* globals Chart:false, feather:false */
$(document).ready(function() {

    const url = fncUrl();

    let ctxBar = document.getElementById("myChartRD");
    let newCtxCharBar = new Chart(ctxBar);
    let ctxBarMes = document.getElementById("myChartMes");
    let newCtxCharBarMes = new Chart(ctxBarMes);
    let ctxBarCard = document.getElementById("myChartCard");
    let newCtxCharBarCard = new Chart(ctxBarMes);
    let ctxPie = document.getElementById("myChartPie");
    let newCtxCharPie = new Chart(ctxPie);
    let year = new Date().getFullYear();
    let labels = [],receita = [], dataFile=[], despesa = [],despesasfixa=[];
    let bgColoR = [],bgColoD = [],bgColoRep=[],bgColoF=[];
    let borderColoR = [], borderColoD = [],borderColoRep=[],borderColoF=[];
    let json,alpha,r,g,b;

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    });

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

    //for para pegar os dados da loja 1 e 2
    //for (let i = 1; i <= 2 ; i++) {

      //  var table = $('#table'+i).DataTable({
		/*var table = $('table').DataTable({
            "ajax": {
                "method": 'get',
                //"url": urlApi + "/flux/"+i, //show
				"url": url + "/flux/2", //show
                //"data": ""
                "dataSrc": ""
            },
            "columns": [
                {"data": "id"},
                {"data": "valor_caixa"},
                {"data": "valor_sangria"},
                {"data": "descricao"},
                {"data": "loja_id"},
				{"data": "total_caixa"},
                {"data": "created_at"},
                {"data": "updated_at"},
				{
                    "className":      'options',
                    "data":           null,
                    "render": function(data, type, full, meta){
                        //console.log(data);
                        if (data.confirme === true) {
                            return '<button class="btn btn-sm btn-success btnConfirme" disabled><span class=\'material-icons\'>monetization_on</span></button>';
                        } else {
                            return '<button class="btn btn-sm btn-danger btnConfirme"><span class=\'material-icons\'>not_listed_location</span></button>';
                        }
                    }
                }*/
                /*{"defaultContent": "<div class='text-center'>" +
                        "<div class='btn-group'>" +
                        "<button class='btn btn-danger btn-sm btnTic'><span class='material-icons'>cancel</span></button>&nbsp;&nbsp;" +
                        "</div>" +
                        "</div>"}*/
           /* ],
            "order": [[0, "desc"]]
            , language: {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
            }
        });*/
    //}

    /**
     * #########################################################################
     * ##############################  FUNÇÕES DE AÇÕES   #################################
     * #########################################################################
     * */
	/* $(document).on("click", ".btnConfirme", function(){
        let fila = $(this).closest("tr");
        let id = parseInt(fila.find('td:eq(0)').text()) ;
        //nome = fila.find('td:eq(1)').text();
        let token = $('div').find('input[name="_token"]').val();

       // console.log(id);
       // console.log(token);
        var resposta = confirm("Marcar como recebido ?");
        if (resposta) {
            $.ajax({
                url: url + "/flux/" + id ,
                data: {_token: token},
                type: "PUT",
                datatype:"json",
                success: function(data) {
                    // console.log(data);
                    table.ajax.reload(null, false);
                },
                error: function(data){
                    //console.log(data.responseText);
                   // alert(data.responseText);
                   var json = $.parseJSON(data.responseText);
                    $("#retorno_fluxo").text(json.message).addClass("alert alert-danger");
                },
                complete:function(data){
                    //console.log(data.responseText);
                    var json = $.parseJSON(data.responseText);
                    if(json.success) {
                       // alert(json.message);

                        //$("#retorno_fluxo").text(json.message).addClass("alert alert-success");
                        $('#retorno_fluxo').fadeIn('slow').text(json.message).addClass("alert alert-success");
                        $('#retorno_fluxo').delay(5000).fadeOut('slow');
*/
                       /* $("#retorno_fluxo").fadeIn(700, function () {
                            //$("#alert-title-delete").text(json.message).addClass("alert alert-success");
                            window.setTimeout(function () {
                                $('#retorno_fluxo').fadeOut('slow');
                            }, 3000);
                        });*/
   /*                 }
                }
            });
            return false;
        }
    });*/

	 /**
      * Atualiza o chart de Receitas x Despesas
      * */
    $(".btn-enviar").click(function () {
        //  console.log($('input[name=data]').val());
        let ano = $('input[name=data]').val() !== "" ? $('input[name=data]').val() : year ;

        fncBarChart(ano).then();
        fncBarChartMes(ano).then();
        fncBarChartCard(ano).then();
        fncCardPrincipal(ano).then();
    });

    /**
     * #########################################################################
     * ##############################  CHART   #################################
     * #########################################################################
     * */

    let fncBarChart = async function (ano) {

        await $.ajax({
            url: url + "/fluxo/chart/" + ano,
            async: true,
            cache: false,
            dataType: "json",
            beforeSend: function () {
                $('#load').css("display", "block");
                $('#load').html('<h4><div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div> Aguarde...</h4>');
            },
            success: function (response) {
                if (response.success) {

                    let myArr = JSON.stringify(response.data);
                    labels = [];
                    receita = [];
                    despesa = [];
                    despesasfixa=[];
                    bgColoR = [];
                    borderColoR = [];
                    bgColoRep = [];
                    borderColoRep = [];

                    JSON.parse(myArr).forEach(function (ret) {
                         console.log(ret);
                        labels.push(ret.nome_mes);
                        receita.push(ret.receita)
                        despesa.push(ret.despesa)
                        despesasfixa.push(ret.despesasfixa)

                        bgColoR.push('rgba(0, 123, 255, 0.75)');
                        borderColoR.push('rgba(0, 123, 255, 1)');
                        bgColoD.push('rgba(227, 53, 47, 0.75)');
                        borderColoD.push('rgba(227, 53, 47, 1)');
                        bgColoRep.push('rgba(149,10,7,0.75)');
                        borderColoRep.push('rgba(149,10,7,1)');
                        bgColoF.push('rgba(238,97,96,0.75)');
                        borderColoF.push('rgba(238,97,96,1)');
                    });


                   /* myArr = JSON.stringify(response.data.despesas);
                    JSON.parse(myArr).forEach(function (ret) {
                         console.log(ret.nome_mes);
                        //labels.push(ret.nome_mes);
                        despesa.push(ret.despesa)
                        bgColoD.push('rgba(227, 53, 47, 0.75)');
                        borderColoD.push('rgba(227, 53, 47, 1)');
                    });*/


                    newCtxCharBar.destroy();
                    newCtxCharBar = new Chart(ctxBar, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Receitas',
                                    data: receita,
                                    backgroundColor: bgColoR,
                                    borderColor: bgColoR,
                                    borderWidth: 1
                                }, {
                                    label: 'Reposição',
                                    data: despesa,
                                    backgroundColor: bgColoRep,
                                    borderColor: borderColoRep,
                                    borderWidth: 1
                                }, {
                                    label: 'Despesas Fixas',
                                    data: despesasfixa,
                                    backgroundColor: bgColoF,
                                    borderColor: borderColoF,
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            title: {
                                display: true,
                                text: "Ano: " + response.data[0].ano
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }, tooltips: {
                                callbacks: {
                                    //label: function (tooltipItem, chart) {
                                    //var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                                    //return datasetLabel + ': R$ ' + number_format(tooltipItem.yLabel, 2, ',', '.');
                                    label: function (tooltipItem) {
                                        return ' R$ ' + number_format(tooltipItem.yLabel, 2, ',', '.');
                                    }
                                }
                            }
                        }
                    });
                    //   fncLoadChartBar("close");
                    $('span[id=load]').html("");
                }
            },
            error: function (response) {
                json = $.parseJSON(response.responseText);
                if(json.message === "Unauthenticated.")
                    window.location.reload();

                Swal.fire(
                    'error!',
                    json.message,
                    'error'
                )
            }
        });
    }
    /**
     * Totais Mensal
     * */
        let fncBarChartMes = async function (ano) {

            await $.ajax({
                url: url + "/relatorio/mes/"+ano,
                async: true,
                cache: false,
                dataType: "json",
                beforeSend: function () {
                    $('#load').css("display", "block");
                    $('#load').html('<h4><div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div> Aguarde...</h4>');
                },
                success: function (response) {
                    if (response.success) {
                        /*swalWithBootstrapButtons.fire({
                            title: "",
                            text: "Aguarde..",
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });*/

                        let myArr = JSON.stringify(response.dados);
                        labels = [];
                        dataFile = [];
                        bgColoR = [];
                        borderColoR = [];

                        JSON.parse(myArr).forEach(function (ret) {
                            // console.log(ret.nome_mes);
                            labels.push(ret.nome_mes);
                            dataFile.push(ret.totalFinal)
                            bgColoR.push(dynamicColors());
                            borderColoR.push(dynamicBorderColors());
                        });

                        newCtxCharBarMes.destroy();
                        newCtxCharBarMes = new Chart(ctxBarMes, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'Mês a Mês',
                                        data: dataFile,
                                        backgroundColor: bgColoR,
                                        borderColor: bgColoR,
                                        borderWidth: 1
                                    }
                                ]
                            },
                            options: {
                                legend: {
                                    display: false,
                                },
                                title: {
                                    display: true,
                                    text: "Ano: " + ano
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }, tooltips: {
                                    callbacks: {
                                        label: function (tooltipItem) {
                                            return ' R$ ' + number_format(tooltipItem.yLabel, 2, ',', '.');
                                        }
                                    }
                                },
                            }
                        });

                        //$('span[id=load]').html("");
                    }
                },
                error: function (response) {
                    json = $.parseJSON(response.responseText);
                    if(json.message === "Unauthenticated.")
                        window.location.reload();

                    Swal.fire(
                        'error!',
                        json.message,
                        'error'
                    )
                }
            });
        }//Fim Total Mensal

    /**
     * Fechamento Cartões
     * */
    let fncBarChartCard = async function (ano) {

        await $.ajax({
            url: url + "/relatorio/card/"+ano,
            async: true,
            cache: false,
            dataType: "json",
            beforeSend: function () {
                $('#load').css("display", "block");
                $('#load').html('<h4><div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div> Aguarde...</h4>');
            },
            success: function (response) {
                if (response.success) {
                    /*swalWithBootstrapButtons.fire({
                        title: "",
                        text: "Aguarde..",
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });*/

                    let myArr = JSON.stringify(response.dados);
                    labels = [];
                    dataFile = [];
                    bgColoR = [];
                    borderColoR = [];

                    JSON.parse(myArr).forEach(function (ret) {
                        // console.log(ret.nome_mes);
                        labels.push(ret.nome_mes);
                        dataFile.push(ret.totalFinal)
                        bgColoR.push(dynamicColors());
                        borderColoR.push(dynamicBorderColors());
                    });

                    newCtxCharBarCard.destroy();
                    newCtxCharBarCard = new Chart(ctxBarCard, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Mês a Mês',
                                    data: dataFile,
                                    backgroundColor: bgColoR,
                                    borderColor: bgColoR,
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: "Ano: " + ano
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }, tooltips: {
                                callbacks: {
                                    label: function (tooltipItem) {
                                        return ' R$ ' + number_format(tooltipItem.yLabel, 2, ',', '.');
                                    }
                                }
                            }
                        }
                    });

                    //$('span[id=load]').html("");
                }
            },
            error: function (response) {
                json = $.parseJSON(response.responseText);
                if(json.message === "Unauthenticated.")
                    window.location.reload();

                //$("#modal-title").addClass("alert alert-danger");
                //$('#modal-title').html('<p><strong>' + json.message + '</strong></p>');
                Swal.fire(
                    'error!',
                    json.message,
                    'error'
                )
            }
        });

    }//Fechamento Cartões

    /**
     * Total de receitas
     * */
    let fncCardPrincipal = async function (ano) {
        await fetch(url + "/fluxo/"+ano+"/edit")
            .then(function (response) {
                //console.log(response);
                return response.json()
            })
            .then(function (response) {
                console.log(response.data);
                $("#totalReceita").html("R$ " +response.data.receita.total_receita);
                $("#totalReposicao").html("R$ " +response.data.reposicao.total_reposicao);
                $("#totalDespesas").html("R$ " +response.data.despesasfixas.total_despesas);

                /***
                 * CHART PIEm DADOS ANUAIS
                 * */
                labels = [];
                dataFile = [];
                bgColoR = [];

                let myArr = JSON.stringify(response.data.years);
                JSON.parse(myArr).forEach(function (ret) {
                  //  console.log(ret.ano);
                    labels.push(ret.ano);
                    dataFile.push(ret.total_receita)
                    bgColoR.push(dynamicColors());
                });

                let data = {
                    labels: labels,
                    datasets: [{
                        data: dataFile,
                        backgroundColor: bgColoR
                    }]
                };

                let options = {
                    responsive: true,
                    animation: {
                        easing: 'easeInOutQuart',
                        duration: 1000,
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem,data) {
                                let allData = data.datasets[tooltipItem.datasetIndex].data;
                                let tooltipData = allData[tooltipItem.index];

                                return ' R$ ' + number_format( tooltipData, 2, ',', '.');
                            }
                        }
                    }
                };

                newCtxCharPie.destroy();
                newCtxCharPie = new Chart(ctxPie, {
                    type: 'pie',
                    data: data,
                    options: options
                });
            });
    }
    /**
     * #########################################################################
     * ##########  ÁREA DE FILTRO DE DATAS   ###################################
     * #########################################################################
     * */
    $('#data input').datepicker({
        'todayHighlight':true,
        'orientation':'bottom',
        autoclose:true,
        language : 'pt-BR',
        format: "yyyy",
        startView: "year",
        minViewMode: "years"
    });

    /**
     * #########################################################################
     * ##########  ÁREA FUNÇÕES FORMATAÇÕES ###################################
     * #########################################################################
     * */

    /**
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

    /**
     * #########################################################################
     * ##########  ÁREA EXECUÇÃO DE FUNCÇÕES ONLOAD ############################
     * #########################################################################
     * */
    fncBarChart(year).then(); //receitasxdespesas
    fncBarChartMes(year).then();
    fncBarChartCard(year).then();
    fncCardPrincipal(year).then();
});
