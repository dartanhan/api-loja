import { sweetAlert,getDataFormat,fncDataDatatable } from "./comum.js";

    let table;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = fncUrl();
    let dataIni = $('input[name=dataini]');
    let dataFim = $('input[name=datafim]');
    let periodo;

$(function() {


     /**
     * #########################################################################
     * ##########  ÁREA DATATABLE ###################################
     * #########################################################################
     * */

        table = $('#table').DataTable({
            responsive: true,
            processing: true,
            //serverSide: true,
            dom: 'Bfrtip', // Ativa os botões de exportação
           "ajax":{
                "method": 'post',
                "dataType":"json",
                "url": url + "/reposicao/filter",
                "data":function(data){
                    data._token = csrfToken,
                    data.startDate = getDataFormat(dataIni.val(),'DD/MM/YYYY','YYYY-MM-DD');
                    data.endDate = getDataFormat(dataFim.val(),'DD/MM/YYYY','YYYY-MM-DD');
                },
            },
            columns: [
                {"data": "imagem",name: 'imagem'},
                { data: 'codigo_produto', name: 'codigo_produto'},
                { data: 'descricao', name: 'descricao'},
                { data: 'valor_produto', name: 'valor_produto'},
                { data: 'quantidade', name: 'quantidade'},
                { data: 'valor_total', name: 'valor_total'},
                {
                    "data": "defaultContent",
                    render: function(data, type, row) {
                        return "ações";
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
            //page: 50, // Define o número de linhas exibidas por padrão
            "order": [[3, "desc"]],
            initComplete: function(settings, json) {
                //console.log(json);
            }
        });


        table.on('draw', function() {
            $('[data-toggle="tooltip"]').tooltip();

            if(dataIni.val() !== ''){
                periodo = "- Período pesquisado : " + dataIni.val() + " até " + dataFim.val();
            }else{
                periodo = "- Período pesquisado : " +
                    moment().format('DD/MM/YYYY')
                    + " até " +
                    moment().format('DD/MM/YYYY');
            }
            $("#data-periodo").html(periodo);
        });
    /*******************************************************
     *********** FILTRO **********************
     * *****************************************************/
     $( ".btn-enviar" ).on("click", function() {

        let isValid = true;
        let msg = '';

        //console.log(dataIni);

        // Check if dataIni is filled
        if (!dataIni) {
            msg = "Por favor, preencha a Data Inicio."
            $('input[name=dataini]').trigger( "focus" );
           isValid = false;
       }

       if (!dataFim && isValid === true) {
           msg = "Por favor, preencha a Data Fim.";
           $('input[name=datafim]').trigger( "focus" );
           isValid = false;
       }

       // If both fields are filled, submit the form
       if (isValid) {
            fncDataDatatable(table);
       }else{
           sweetAlert.fire({
               title: "Atenção",
               text: msg,
               icon: 'warning',
               showConfirmButton: false,
               timer: 1500
           });

       }
   });

   $(".btn-limpar").on("click", function () {
       fncLoad("<div class=\"card-body\">Aguarde...</div><div class=\"spinner-border spinner-border-sm ms-auto\" role=\"status\" aria-hidden=\"true\"></div>");
      // fncLoadChartBar("");

       $('input[name=dataini]').val("");
       $('input[name=datafim]').val("");

       fncDataDatatable(table);
   });


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


    /****
     * LOAD DE FUNÇOES
     */
    // Obter a data atual
   // const currentDate = moment().format('YYYY-MM-DD');

   // fncDataDatatable(currentDate, currentDate).then();
});
