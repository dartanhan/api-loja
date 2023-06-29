/* globals Chart:false, feather:false */

(function () {
  'use strict'
    let url = window.location.protocol +"//"+ window.location.hostname + "/api-loja/admin";
    feather.replace()

    let labels = [];
    var dados = [];
    var bgColoR = [];
    var borderColoR = [];
    var r , g ,b, alpha;
    var ctx = "";
    var myChart = "";
    var titulo = "";
    var typeChart =null;

    var dynamicColors = function() {
         r = Math.floor(Math.random() * 255);
         g = Math.floor(Math.random() * 255);
         b = Math.floor(Math.random() * 255);
         alpha =  0.75;
        return "rgb(" + r + "," + g + "," + b + ", "+ alpha +")";
    };

    var dynamicBorderColors = function(r,g,b) {
        return "rgb(" + r + "," + g + "," + b + ")";
    };

    // Graphs
    //var ctx = document.getElementById('myChart');
   var fncCtx = function(ctx) {

       myChart = new Chart(ctx, {
           type: typeChart,
           data: {
               labels: labels,
               datasets: [{
                   label: '# Valores ',
                   data: dados,
                   backgroundColor: bgColoR,
                   borderColor: borderColoR,
                   borderWidth: 2
               }]
           },
           options: {
               scales: {
                   yAxes: [{
                       ticks: {
                           beginAtZero: true
                       }
                   }]
               },
               tooltips: {
                   callbacks: {
                       label: function (tooltipItem, chart) {
                           var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                           return datasetLabel + ': R$ ' + number_format(tooltipItem.yLabel, 2, ',', '.');
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
   }


        fetch(url+ "/relatorio/chartDay/0/0")
            .then(function (response) {
                console.log(response);
                return response.json()
            })
            .then(function (response) {
                typeChart = 'bar';
               // console.log(response.chart);
               // var countArray = JSON.parse(response);
              //  console.log(countArray);
                var count = Object.keys(response.chart).length;
                //console.log(count);

                //for (let i = 1; i <= count; i++) {
					for (let i = 2; i <= count; i++) {
                    var myArr = JSON.stringify(response.chart[i]);
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


                    if(i == 1){
                        //titulo = 'Gráfico Vendas Dia Feira';
                        $("#totalDinheiroFeira").html(response.totalOrders[i].orderTotalDiner);
                        $("#totalCartFeira").html(response.totalOrders[i].orderTotalCart);
                        $("#totalDescFeira").html(response.totalOrderDiscount[i].totalDiscount);
                        $("#totalDiaFeira").html(response.totalOrderDay[i].orderTotalDay);
                        $("#totalMesFeira").html(response.totalOrderMonth[i].totalMes);
                        $("#totalSemanaFeira").html(response.totalsOrderWeek[i].totalWeek);

                        ctx = document.getElementById('myChartFeira');
                    }else{
                        //titulo = 'Gráfico Vendas Dia Barão';
                        $("#totalDinheiroBarao").html(response.totalOrders[i].orderTotalDiner);
                        $("#totalCartBarao").html(response.totalOrders[i].orderTotalCart);
                        $("#totalDescBarao").html(response.totalOrderDiscount[i].totalDiscount);
                        $("#totalDiaBarao").html(response.totalOrderDay[i].orderTotalDay);
                        $("#totalMesBarao").html(response.totalOrderMonth[i].totalMes);
                        $("#totalSemanaBarao").html(response.totalsOrderWeek[i].totalWeek);

                        ctx = document.getElementById('myChartBarao');
                    }

                    fncCtx(ctx);
                    myChart.update();
                }

    });

	for (let i = 2; i <= 2; i++) {
    //for (let i = 1; i <= 2; i++) {

        fetch(url + "/relatorio/dailySalesList/"+i)
            .then(function (response) {
                //  console.log(response);
                return response.json()
            })
            .then(function (response) {

                $('#tableVendaDia'+i).DataTable({
                    "data": response.dados,
                    "columns": [
                        {"data": "codigo_venda"},
						{"data": "nome_pgto"},
                        {
                            "data": "sub_total",
                            "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display

                        },
                        {
                            "data": "valor_desconto",
                            "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display

                        },
                        {
                            "data": "total",
                            "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                        }, {
							"data": "data"
						},{
                            "defaultContent":
                                "<div class='text-center'>" +
                                    "<div class='btn-group'>" +
										 "<button class='btn btn-info btn-xs btnView'>" +
                                            "  <i class='material-icons '>remove_red_eye</i>" +
                                        "</button>" +
                                    "</div>" +
                                "</div>"
                        }
                    ],
                    language: {
                        "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
                    },
                    "order": [[5, "desc"]]

                });//fim datatables

            });
    } //fim for

   $(document).on("click", ".btnView", function(event) {
        event.preventDefault();
        let fila = $(this).closest("tr");
		var table = "";
		var total = "";
        fetch(url + "/relatorio/detailSales/" + fila.find('td:eq(0)').text() )
            .then(function (response) {
                //console.log(response);
                return response.json()
            })
            .then(function (response) {

                table = $('#tableView').DataTable({
                    "data": response.dados,
                    "bInfo" : true,
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

                       //var soma =0;
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
                       var numFormat = $.fn.dataTable.render.number( '.', ',', 2, 'R$ ' ).display;
                       $("#foot").html("");
                       $("#foot").append('<td colspan="5" style="background:#000000; color:white; text-align: right;">Total: '+numFormat(total)+'</td>');
                   },

                });//fim datatables
            });
        modal('#divModal');
    });

    //
    $(document).on("click", ".detailCart", function(event) {
        event.preventDefault();
        let table = "";
        const id = $(this).data('content');
        //var id = document.getElementById("detailCart").getAttribute('data-content');
        //console.log($(this).data('content'));
        console.log(id);

        fetch(url + "/relatorio/detailCart/" + id )
            .then(function (response) {
                //console.log(response);
                return response.json()
            })
            .then(function (response) {
                table =  $('#tableViewCart').DataTable({

                    "data": response.dados,
                    "bInfo" : true,
                    "paging": false,
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

        modal('#divModalCart');
    });

	 //
    fetch(url + "/relatorio/totalMes")
        .then(function (response) {
            //  console.log(response);
            return response.json()
        })
        .then(function (response) {

            $('#tableViewTotalMeses').DataTable({
                "data": response.dados,
                "columns": [
                    {"data": "mes"},
                    {
                        "data": "totalFinal",
                        "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                    }
                ],
                language: {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
                },
                "columnDefs" : [{"targets":0, "type":"date"}],
                "order": [[0, "desc"]],
                "bFilter": false

            });//fim datatables

        });

    //
    fetch(url + "/relatorio/fechamentoMes")
        .then(function (response) {
            //  console.log(response);
            return response.json()
        })
        .then(function (response) {

            $('#tableViewFechamentoMeses').DataTable({
                "data": response.dados,
                "columns": [
                    {"data": "mes"},
                    {
                        "data": "totalFinal",
                        "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                    }
                ],
                language: {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
                },
                "columnDefs" : [{"targets":0, "type":"date"}],
                "order": [[0, "desc"]],
                "bFilter": false

            });//fim datatables

        });
}());

function number_format(number, decimals, dec_point, thousands_sep) {
// *     example: number_format(1234.56, 2, ',', ' ');
// *     return: '1 234,56'
    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
