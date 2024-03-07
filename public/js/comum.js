const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger'
    },
    buttonsStyling: false
});

/**
 * SÓ PERMITE DIGITAR NUMEROS NO CAMPO
 * */
function SomenteNumeros(num) {
    let er = /[^0-9.]/;
    er.lastIndex = 0;
    let campo = num;
    if (er.test(campo.value)) {
        campo.value = "";
    }
}

/***
 * FORMATA CAMPO COM MOEDA
 *
 * OnkeyPress
 * */
function formatMoneyPress(parm) {
    let valor = parm.value;

    valor = valor + '';
    valor = parseInt(valor.replace(/[\D]+/g, ''));
    valor = valor + '';
    valor = valor.replace(/([0-9]{2})$/g, ",$1");

    if (valor.length > 6) {
        valor = valor.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
    }

    parm.value = valor;
    if(valor === 'NaN') parm.value = '';
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

 /***
     * Formata data de yyyy/mm/dd para dd/mm/yyyy
     * */
 function getFormattedDate(parm) {
    let d = parm.split('-');
    return  d[2] + '/' + d[1] + '/' + d[0];
}

/***
 * Ao digitar, formata a data no campo em dd/mm/yyyy
 * */
function formatDate(parm) {

    let tecla = this.keyCode;
    let vr = String(parm.value);
    vr = vr.replace("/", "");
    vr = vr.replace("/", "");
    vr = vr.replace("/", "");
    let tam = vr.length + 1;

    if (tecla !== 8 && tecla !== 8) {
        if (tam > 0 && tam < 2)
            parm.value = vr.substr(0, 2) ;
        if (tam > 2 && tam < 4)
            parm.value = vr.substr(0, 2) + '/' + vr.substr(2, 2);
        if (tam > 4 && tam < 7)
            parm.value = vr.substr(0, 2) + '/' + vr.substr(2, 2) + '/' + vr.substr(4, 7);
    }

}


/**
     *  Preview da imagem ao passar o mause
     * */
$(document).on("mouseover",".image" , function(e){
    let img = $(this);

    swalWithBootstrapButtons.fire({
        imageUrl:  img[0].currentSrc,
        //imageWidth: 420,
        //imageHeight: 240,
        showConfirmButton: false,
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        }
    });
});
