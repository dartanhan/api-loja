$(document).ready(function() {
    let json = '', table = '';
    let url = window.location.protocol +"//"+ window.location.hostname + "/api-loja/admin";
    let now = new Date();
    let yearMonth = moment(now).format('YYYY-MM');


  /*  const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    });*/

    $('#formFiltro').on('submit', function(e) {
        //$('form[name="formFiltro"]').submit(function (e) {
        table.ajax.reload();
        e.preventDefault();
    });

	/*  let table = $('#table').DataTable({
          "ajax":{
              "method": 'post',
              "processing": true,
              "serverSide": true,
              "headers": {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
              "url": url + "/productbestbellers",
              "data": function (data) {
                  data.dateini = $('input[name="dateini"]').val();
                  data.datefim = $('input[name="datefim"]').val();
              },
          },
		"columns": [
                { "data": "data", "defaultContent": "" },
                { "data": "quantidade" , "defaultContent": ""},
                { "data": "codigo_produto", "defaultContent": "" },
                { "data": "descricao" , "defaultContent": ""},
                {
                    "data": "total_produto" ,
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                },
                {
                    "data": "total_venda" ,
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                }
		],
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
        },
	});*/

   /* $('.dateini').datepicker({
        viewMode: "months",
        minViewMode: "months",
        format: 'mm/yyyy',
        autoclose:true,
        language:'pt-BR',
        //multidate:true,
        todayBtn:true,
        todayHighlight:true
    });

    $('.datefim').datepicker({
        viewMode: "months",
        minViewMode: "months",
        format: 'mm/yyyy',
        autoclose:true,
        language:'pt-BR',
        //multidate:true,
        // todayBtn:true,
        todayHighlight:true
    });*/

    /***
     * ########################################################
     * ########### CARDS COM AS CATEGORIAS  ###################
     * ########################################################
     * */

    let fncDataCards = async function(data,limpar) {
        await fetch(url+ "/productbestsellers/cards/"+data)
            .then(function (response) {
                return response.json()
            }).then(function (response) {
                //console.log(response.success);
                //console.log(response);
                fncCards(response,data);

        });
        if(limpar)
            $('.btn-limpar').html('Limpar');
    };

    let fncCards = function(response,data){
        let cards =  $("#cards").html('');
        $('#filterLabel').html('[' + viewFormattedDate(data)+']');
        if(response.success) {
            let myArr = JSON.stringify(response.cards);

            // console.log(response.cards.length);

            if(response.cards.length > 0){
                //$('#cards').html('<h6>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h6>');
                JSON.parse(myArr).forEach(function (ret) {
                    cards.append("<div class=\"col-xl-2 col-md-4\">" +
                        "<div class=\"card bg-primary text-white mb-1\">" +
                        "<div class=\"card-header d-flex align-items-center justify-content-between\">" + ret.nome + "</div>" +
                        "<div class=\"d-flex align-items-center\" id=\"totalDinner\" name=\"load\">" +
                        "   <div class=\"card-body text-center\" name=\"card-body\">" +
                            "   <div class='bg-gradient'><strong class=\"fs-5\"><a href='#' onclick=\"detail("+ret.id+",'"+url+"','"+data+"');\" class=\"small link-cards\" data-toggle=\"modal\" data-target=\"#divModalDetail\">R$ " +  number_format(ret.total, 2, ',', '.') + "</a></strong></div>" +
                            "   <div class='bg-danger bg-gradient'> <strong class=\"fs-5\"><a href='#' onclick=\"detailCost("+ret.id+",'"+url+"','"+data+"');\" class=\"small link-cards\" data-toggle=\"modal\" data-target=\"#divModalDetailCost\">R$ " +  number_format(ret.total_custo, 2, ',', '.') + "</a></strong></div>" +
                            "   <div class='bg-success bg-gradient'> <strong class=\"fs-5\">R$ " +  number_format(ret.total - ret.total_custo, 2, ',', '.') + "</strong></div>"+
                            "</div>" +
                        "   </div>" +
                       // "   <div class=\"card-footer d-flex align-items-center justify-content-between\" id=\"detail\">" +
                       // "   <a href=\"#\" onclick=\"detail("+ret.id+",'"+url+"','"+data+"');\" class=\"small link-cards\" data-toggle=\"modal\" data-target=\"#divModalDetail\">Detalhes </a>" +
                        "</div>" +
                        "</div>" +
                        "</div>"
                    );
                });

            }else{
                cards.html("<span class='alert alert-info'> Sem informações para o período informado: " + viewFormattedDate(data) + "</span>");
            }
        }else {
            let json = $.parseJSON(response.responseText);
            Swal.fire(
                'error!',
                json.message,
                'error'
            )
        }
    }

    /***
     * #########################################################
     * ###########   ACIONA O FILTRO DE DATAS   ################
     * ########################################################
     * */
    $("#form").submit(function(evt){
        evt.preventDefault();

    }).validate({
        errorClass: "my-error-class",
        validClass: "my-valid-class",
        rules: {
            mes_ano: {
                required: true
            },
        },
        messages: {
            mes_ano: {
                required: "Informe a data de pesquisa?"
            },
        }, submitHandler:  function (form, event) {
            event.preventDefault();
            let formData = setFormattedDate($('input[name=mes_ano]').val());

            //console.log(formData);
           // fncDataCards(formData).then();'

            $.ajax({
                url: url + "/productbestsellers/cards/"+formData,
                type: 'GET',
                data: formData,
                enctype: 'multipart/form-data',
                processData: false,
                dataType: 'json',
                beforeSend:  function () {
                    $('#btn-enviar').html('<span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span> Aguarde...');
                },
                success:  function (response) {
                    //console.log(response);
                    if (response.success) {
                        fncCards(response,formData);
                        fncTable(formData);
                    }
                },
                error:  function (response) {
                    //{"message":"Unauthenticated."}
                    json = $.parseJSON(response.responseText);
                    $("#modal-title").addClass("alert alert-danger");
                    $('#modal-title').html('<p><i class="fas fa-exclamation-circle"></i>&nbsp;<strong>' + json.message + '</strong></p>');
                    Swal.fire(
                        'error!',
                        json.message,
                        'error'
                    )
                },complete:  function (){
                   $('#btn-enviar').html('Filtrar');
                }
            });
        }
    });

    let fncTable = function(data){
        $('#datatable').DataTable().destroy();
      table = $('#datatable').DataTable({
            "ajax":{
                "method": 'get',
                "processing": true,
                "serverSide": true,
                "headers": {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
                "url": url + "/productbestsellers/"+data+"/edit"
            },
            "columns": [
                {
                    "className":      'details-control',
                    "orderable":      false,
                    "data":           null,
                    "defaultContent": ''
                },
                { "data": "id" , "defaultContent": ""},
                { "data": "codigo_produto" , "defaultContent": ""},
                { "data": "descricao" , "defaultContent": ""},
                { "data": "quantidade" , "defaultContent": ""},
                { "data": "data", "defaultContent": "" },
            ],
              "columnDefs": [
                  {
                      "targets": [ 1 ],
                      "visible": false,
                      "searchable":true
                  }
              ],
            language: {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
            },
            order: [[ 4, "desc" ]],
        });
    }

    /**
     * Add event listener for opening and closing details
     */
    $('#datatable tbody').on('click', 'td.details-control', function (event) {
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
           // console.log($('input[name="mes_ano"]').val());

            let data = $('input[name="mes_ano"]').val() === "" ? yearMonth : setFormattedDate($('input[name="mes_ano"]').val());

            let tmpRow  ="<div class='container-fluid'>" +
                "<table id='tableDetail' class='table compact table-striped table-bordered table-hover'>" +
                    "<thead class=\"text-center\">" +
                        "<tr class='bg-secondary '>" +
                            "<th>SUB CÓDIGO</th>" +
                            "<th>VARIAÇÃO</th>" +
                            "<th>VENDIDOS</th>" +
                            "<th>VENDIDOS(3M)</th>" +
                            "<th>QTD EST.</th>" +
                            //"<th>EST.</th>" +
                            "<th>VAL.VEN.</th>" +
                            "<th>VAL.MERC.</th>" +
                            "<th>LUCRO</th>" +
                            "<th>DATA</th>" +
                        "</tr>" +
                    "</thead>";

            $.ajax({
                url: url + "/productbestsellers/getListProductsSales/"+row.data().codigo_produto+"/"+data,
                type: 'GET',
                data: '',
                dataType: 'json',
                beforeSend: function () {
                    row.child('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>').show();
                },
                success: function (response) {
                    // console.log(response.data.products);
                    //if (response.success) {
                        let arrayProducts = JSON.stringify(response.data);

                        JSON.parse(arrayProducts).forEach(async function (arrayItem, index, fullArray) {
                            // console.log(arrayItem.subcodigo);

                            tmpRow += "<tr>" +
                                           // "<td>" + arrayItem.id + "</td>" +
                                            "<td>" + arrayItem.codigo_produto + "</td>" +
                                            "<td>" + arrayItem.descricao + "</td>" +
                                            "<td>" + arrayItem.qtd_vendidos + "</td>" +
                                            "<td>" + arrayItem.qtd_3m + "</td>" +
                                            "<td>" + arrayItem.quantidade + "</td>" +
                                            "<td>" + formatMoney(arrayItem.valor_vendido) + "</td>" +
                                            "<td>" + formatMoney(arrayItem.valor_produto) + "</td>" +
                                            "<td>" + formatMoney(arrayItem.valor_lucro) + "</td>" +
                                            "<td>" + viewFormattedDate(arrayItem.data) + "</td>"+
                                        "</tr>"
                        });

                        tmpRow  +=      "</table></div>";
                        row.child(tmpRow).show();

                        $('#tableDetail').DataTable({
                            "searching": false,
                            "info": true,
                            "lengthChange": false,
                            language: {
                                        "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
                            },
                            order: [[ 2, "desc" ]],
                        });
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
    } );

    /***
     * #########################################################
     * ###########      ONLOAD   ##############################
     * ########################################################
     * */
    fncDataCards(yearMonth,false).then();
    fncTable(yearMonth);

    /**
     * #########################################################################
     * ##########  ÁREA DE FILTRO DE DATAS   ###################################
     * #########################################################################
     * */

    $('#data_mes_ano [name=mes_ano]').datepicker({
        'language' : 'pt-BR',
        'todayHighlight':true,
        'orientation':'bottom',
        'autoclose':true,
        'multidate':false,
        'format': "mm/yyyy",
        'viewMode': "months",
        'minViewMode': "months"
    });

    /***
     * Formata data de mm/yyyy para yyyy-mm
     * */
    function setFormattedDate(parm) {
        //console.log(parm);
        let d = parm.split('/');
        return  d[1] + '-' + d[0];
    }
    function viewFormattedDate(parm) {
        //console.log(parm);
        let d = parm.split('-');
        return  d[1] + '\\' + d[0];
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

    /**
     * Retonar ao mês corrente
     * */
    $(".btn-limpar").click(function () {
        $('input[name=mes_ano]').val("");
        $('.btn-limpar').html('<span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span> Aguarde...');
        fncDataCards(yearMonth,true).then();
        fncTable(yearMonth);
    });

    /**
     * Formata Valor Moeda
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
});

/***
 * #########################################################
 * ########### Detalhes da Venda ###########################
 * #########################################################
 * */
    function detail(id, url,date){
        $('#tableDetail').DataTable().destroy();
       // console.log(url + "/productbestsellers/" + id);

       $('#tableDetail').DataTable({
           "createdRow": function(row, data) {
               if (data.lucro < 0) {
                   $(row).addClass('red');
                }
           },
            "ajax":{
                "method": 'get',
                "processing": true,
                "serverSide": true,
                "url": url + "/productbestsellers/details/" + id + "/"+date,
            },
            "columns": [
                { "data": "descricao" , "defaultContent": ""},
                { "data": "quantidade" , "defaultContent": ""},
                {
                    "data": "valor_produto" ,
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                },
                {
                    "data": "total_produto" ,
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                },
                {
                    "data": "total_venda" ,
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
                },
                {
                    "data": "lucro",
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display

                },
                { "data": "data", "defaultContent": "" },
            ],
            language: {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
            },
           "order": [[ 1, "desc" ]],
        });
    }

/***
 * Detalhes custo
 * */
function detailCost(id, url,date){
    $('#tableDetailCost').DataTable().destroy();

    $('#tableDetailCost').DataTable({
        "ajax":{
            "method": 'get',
            "processing": true,
            "serverSide": true,
            "url": url + "/productbestsellers/detailsCost/" + id + "/"+date,
        },
        "columns": [
            { "data": "descricao" , "defaultContent": ""},
            { "data": "quantidade" , "defaultContent": ""},
            {
                "data": "valor_produto" ,
                "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
            },
            {
                "data": "total_produto" ,
                "render": $.fn.dataTable.render.number('.', ',', 2, 'R$ ').display
            },
            { "data": "data", "defaultContent": "" },
        ],
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
        },
        "order": [[ 3, "desc" ]],
    });
}
