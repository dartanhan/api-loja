/* globals Chart:false, feather:false */
$(document).ready(function() {

    feather.replace();

    let urlApi = window.location.protocol + "//" + window.location.hostname + "/api-loja/public/api";
    //console.log(urlApi);

    $('.datames').datepicker({
        format: 'mm/yyyy',
        viewMode: 'months',
        minViewMode: "months",
        autoclose:true,
        language:'pt-BR',
        //multidate:true,
        todayBtn:true,
        todayHighlight:true,
        todayHighlight:true,
        startView:1

    }).datepicker("setDate",'now');

    //carrega as lojas no select
    fetch(urlApi + "/empresaapi/0")
        .then(function (response) {
           // console.log(response);
            return response.json()
        })
        .then(function (response) {
          //  console.log(response);
            $("#lojas").html("");
            response.forEach(function (ret) {
             //    console.log(ret.id + " - " + ret.nome);
                $("#lojas").append('<option value="'+ret.id+'">'+ret.nome+'</option>');
            });
        });

    //chama a rota
    var formSend = document.querySelector("form");
    formSend.addEventListener('submit', function (event) {
        event.preventDefault();

        console.log("send form");
    });

});
