/* globals Chart:false, feather:false */
$(document).ready(function() {

    feather.replace();
    
    const urlApi = fncUrl();

    var metodo = '',titulo = '';

    $('.dateini').datepicker({

        format: 'dd/mm/yyyy',
        autoclose:true,
        language:'pt-BR',
        //multidate:true,
        todayBtn:true,
        todayHighlight:true
    });

    $('.datefim').datepicker({

        format: 'dd/mm/yyyy',
        autoclose:true,
        language:'pt-BR',
        //multidate:true,
        // todayBtn:true,
        todayHighlight:true
    });


    var table = $('#tableVendaDia').DataTable({
        "createdRow": function(row, data, dataIndex) {
            //se data de venda igual data dia, coloca cor na tabela
            var ret =  compare(data.data, new Date());
            if(ret === 0)
                $(row).addClass('orange');

        },
        "ajax":{
            "processing": true,
            "serverSide": true,
            "method": 'get',
            "url": urlApi + "/relatorio/1",
            "data":{
                "loja_id": 1,
            },
            "dataSrc":""
        },
        "columns":[
            {"data": "venda_id"},
            {"data": "codigo_venda"},
            {"data": "tipo_pgto"},
            {
                "data": "sub_total",
                "render": $.fn.dataTable.render.number( '.', ',', 2, 'R$ ' ).display
            },
            {"data": "valor_percentual"},
            {
                "data": "total",
                "render": $.fn.dataTable.render.number( '.', ',', 2, 'R$ ' ).display
            },
            {
                "data": "data"
            },
            {
                "data": "hora"
            },
            {
                "defaultContent":
                    "<div class='text-center'>" +
                    "<div class='btn-group'>" +
                    "<button class='btn btn-info btn-sm btnVisibility'><i class='material-icons'>visibility</i></button>" +
                    "</div>" +
                    "</div>"
            },
        ] ,
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
        },
        "order": [[ 6, "desc" ]]

    });//fim datatables


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //
    $("#totalMesFeira").html('Consultando..');
    $("#totalDiaFeira").html('Consultando..');
    $("#totalSemanaFeira").html('Consultando..');
    setInterval(function () {

        $.ajax({
            type:'POST',
            url: urlApi + "/relatorio"+'?_token=' + '{{ csrf_token() }}',
            data:{
                "loja_id": 1,
                "dataini": $("#dateini").val(),
                "datafim": $("#datefim").val()
            },
            dataType:'json',
            success: function(data) {
                // console.log(data.status);
                $("#totalMesFeira").html(data["mes"] != null ? data["mes"].total : "R$ 0,00");
                $("#totalDiaFeira").html(data["dia"] != null ? data["dia"].total : "R$ 0,00");
                $("#totalSemanaFeira").html(data["semana"] != null ? data["semana"].total : "R$ 0,00");
                table.ajax.reload(null, false);
            },
            error: function(data){
                // console.log(data.status);
                if(data.status == 401){
                    window.location.href = urlApi + "/login";
                }
                //var json = $.parseJSON(data.responseText);
                //$("#modal-title").text(json.message);
            }/*,
                complete:function(data){
                    console.log(data.responseText);
                 }*/
        });
    },5000);


});//fim on read

//Comparea a data para colocar cor na tabela
function compare(dateTimeA, dateTimeB) {
    //  console.log(dateTimeA);
    //   console.log(dateTimeB);

    var momentA = moment(dateTimeA,"DD/MM/YYYY", true).format();
    var momentB = moment(dateTimeB,"DD/MM/YYYY", true).set({"hour": "00", "minute": "00", "second": "00"}).format();

    //   console.log("momentA " + momentA);
    //   console.log("momentB " + momentB);

    if (momentA > momentB)
        return 1;
    else if
    (momentA < momentB) return -1;
    else
        return 0;
}
