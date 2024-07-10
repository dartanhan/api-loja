$(function() {

    let table;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = fncUrl();

    
     /**
     * #########################################################################
     * ##########  ÁREA DATATABLE ###################################
     * #########################################################################
     * */

    let fncDataDatatable = async function(startDate, endDate) {
                
        if (table) {
            table.destroy();
            sweetAlert({
                title: 'Aguarde!',
                text: "Carregando dados da reposição...",
                icon: 'info',
                showConfirmButton: false
            });
        }
        table = await $('#table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
           "ajax":{
                "method": 'post',
                "url": url + "/reposicao/filter",
                "data":
                    {
                        startDate: startDate,
                        endDate: endDate,
                        _token:csrfToken
                    },
                "dataType":"json",
                responsive: true,
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
            language: {
                "url": "../public/Portuguese-Brasil.json"
            },
            "order": [[3, "desc"]],
            initComplete: function(settings, json) {
                swalWithBootstrapButtons.close();
                $("#data-periodo").html(" - Período pesquisado : " + 
                    getDataFormat(startDate,'YYYY-MM-DD','DD/MM/YYYY') 
                    + " até " + 
                    getDataFormat(endDate,'YYYY-MM-DD','DD/MM/YYYY'));
                $('[data-toggle="tooltip"]').tooltip();               
            }
        });
    }

  
    /*******************************************************
     *********** FILTRO **********************
     * *****************************************************/
     $( ".btn-enviar" ).on("click", function() {
        
        const dataIni = $('input[name=dataini]').val();
        const dataFim = $('input[name=datafim]').val();
        let isValid = true;
        let msg = '';

        //console.log(dataIni);

        // Check if dataIni is filled
        if (!dataIni) {
            msg = "Por favor, preencha a Data Inicio."
            $('input[name=dataini]').focus();
           isValid = false;
       }

       if (!dataFim && isValid === true) {
           msg = "Por favor, preencha a Data Fim.";
           $('input[name=datafim]').focus();
           isValid = false;
       }

       // If both fields are filled, submit the form
       if (isValid) {
           fncLoad("<div class=\"card-body\">Aguarde...</div><div class=\"spinner-border spinner-border-sm ms-auto\" role=\"status\" aria-hidden=\"true\"></div>");
       
           let startDate;
           let endDate;

            if (dataIni) {
              startDate = moment(dataIni, 'DD/MM/YYYY').format('YYYY-MM-DD');
            }
            if (dataFim) {
             endDate = moment(dataFim, 'DD/MM/YYYY').format('YYYY-MM-DD');
            }
            
            fncDataDatatable(startDate,endDate).then();
       }else{
           swalWithBootstrapButtons.fire({
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

       fncDataDatatable("", "").then();
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
    const currentDate = moment().format('YYYY-MM-DD');

    fncDataDatatable(currentDate, currentDate).then();
});
