
function modal(modal) {
    $(modal).modal(
        {
            backdrop: 'static',
            keyboard: false,
            fadeDuration: 100,
            clickClose: true,
            escapeClose: true,
        });
}

function somenteNumeros(num) {
    let er = /[^0-9.]/;
    er.lastIndex = 0;
    let campo = num;
    if (er.test(campo.value)) {
        campo.value = "";
    }
}
