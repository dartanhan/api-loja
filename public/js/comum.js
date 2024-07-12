const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger'
    },
    buttonsStyling: false
});

export const sweetAlert = function(json){
    swalWithBootstrapButtons.fire(json);
}

export const sweetAlertClose = function(){
    swalWithBootstrapButtons.close();
}

/**
 * SÓ PERMITE DIGITAR NUMEROS NO CAMPO
 * */
export function SomenteNumeros(num) {
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
export function formatMoneyPress(parm) {
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
export function formatMoney(valor)
{
    try {

        // Verifica se o valor passado é uma string
        if (typeof valor !== 'string') {
            throw new Error('O valor deve ser uma string. ' + valor);
        }

        const v = ((valor.replace(/\D/g, '') / 100).toFixed(2) + '').split('.');

        const m = v[0].split('').reverse().join('').match(/.{1,3}/g);

        if (!m) {
            throw new Error('Não foi possível formatar o valor.' + m);
        }

        for (let i = 0; i < m.length; i++)
            m[i] = m[i].split('').reverse().join('') + '.';

        const r = m.reverse().join('');

        return r.substring(0, r.lastIndexOf('.')) + ',' + v[1];
    } catch (error) {
        console.error('Erro na função formatMoney:', error.message);
        // Você pode decidir o que fazer aqui em caso de erro, como retornar um valor padrão ou lançar novamente o erro
        throw error; // Lança novamente o erro para que quem chama a função possa lidar com ele
    }
}

 /***
     * Formata data de yyyy/mm/dd para dd/mm/yyyy
     * */
 export function getFormattedDate(parm) {
    //let d = parm.split('-');
    //return  d[2] + '/' + d[1] + '/' + d[0];
    return moment(parm, 'YYYY/MM/DD').format('DD/MM/YYYY');
}

/***
 * @param data data a ser convertida
 * @param mascara_to formato/mascara da data a ser convertida ex.: 'DD/MM/YYYY'
 * @param mascara_old formato/mascara da data de retorno ex.: 'YYYY-MM-DD'
 * @returns Se a data não for fornecida , retornar a data corrente no formato 'YYYY-MM-DD'
 */
export function getDataFormat(data, mascara_to, mascara_old){
    return (data !== "") ? moment(data, mascara_to).format(mascara_old) : moment().format('YYYY-MM-DD');
}



/***
 * Ao digitar, formata a data no campo em dd/mm/yyyy
 * */
export function formatDate(parm) {

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
$(document).on("click",".image" , function(e){
    e.preventDefault;

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

/**
 * Captura o clique no ícone de imagem com a classe "bi-image"
 * Usado para atualizar as fotos dos produtos
 */
$("#table").on("click",".bi-image" ,function(event){
    event.preventDefault();

    // Obtém o valor do atributo "imagem"
    let imagem = $(this).data('path');
    let imagePreview = $(this).data('image-preview');
    let variacaoId = $(this).data('variacao-id');
    let productId = $(this).data('id');
    let imageId = $(this).data('image-id');//id da variação da imagem do produto
    let flagImage = $(this).data('flag-image');//0 = pai 1 = filho

    // Atribui o valor ID da imagem da variação do produto
    $('#variacaoId').val(variacaoId);

    // Atribui o valor ID da imagem do produto
    $('#productId').val(productId);

    $('#imageId').val(imageId);

    $('#imagemName').val(imagem);

    $('#flagImage').val(flagImage);

    // Atribui o valor da imagem ao atributo "src" da tag "<img>" no modal
    $('#modal-imagem').attr('src', imagePreview);
});

/***
 * Salva a imagem no produto PAI e Variação
 * Comomum nas telas de Produtos e Product
 * */
$('form[name="formImageProduct"]').validate({
    errorClass: "my-error-class",
    validClass: "my-valid-class",
    rules: {
        image: {
            required: false
        }
    },
    messages: {
        image: {
            required: "Informe a imagem!"
        }
    }, submitHandler: function(form,e) {
        //  console.log('Form submitted');
        e.preventDefault();

        let metodo = $("#metodo").val();
        //console.log(fncUrl() + "/image/"+$("#flagImage").val());

        $.ajax({
            type: metodo,
            url: fncUrl() + "/image/"+$("#flagImage").val(),
            data:$('form[name="formImageProduct"]').serialize(),
            dataType:"json",
            beforeSend: function () {
                //$("#modal-title").removeClass( "alert alert-danger" );
                $('#modal-title').html('<h4>Aguarde... <div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div></h4>');
                //$("#modal-title").addClass( "alert alert-info" );
            },
            success: function(data) {
                //console.log(data.success);

                if(data.success) {
                    sweetAlert({
                        title: "Sucesso!",
                        text: data.message,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    // table.destroy();
                    //  getdata();
                }
            },
            error: function(data){
                //console.log(data.responseText);
                json = $.parseJSON(data.responseText);
                $("#modal-title").addClass( "alert alert-danger" );
                $('#modal-title').html('<p><strong>'+json.message+'</strong></p>');
                Swal.fire(
                    'error!',
                    json.message,
                    'error'
                )
            },
            complete:function(data){
                // console.log(data.responseText);
                json = $.parseJSON(data.responseText);
                if(json.success) {
                    window.setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                }
            }
        });
    }
});


/***
     * Ação de gravar na tabela de Lista de Compras
     */
$(document).on("click","#addListaCompra" ,function(event){
    event.preventDefault();
    var produto_new_id = $(this).data('produto_new_id');
    var produto_variacao_id = $(this).data('produto_variacao_id');
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      $.ajax({
            url: fncUrl() + "/reposicao", //product.update
            cache: false,
            type:'post',
            data:{ // Objeto de dados que você deseja enviar
                produto_new_id: produto_new_id,
                produto_variacao_id: produto_variacao_id, // Informação adicional que você quer passar
                _token: csrfToken
            },
            dataType:'json',
            success: function(response){
            //    console.log(response);
            if(response.success){
                sweetAlert({
                    title: 'Sucesso!',
                    text: response.message,
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                });

            }else{
                sweetAlert({
                    title: 'Atenção!',
                    text: response.message,
                    icon: 'warning',
                    showConfirmButton: false,
                    timer: 2500
                });
            }

            },
            error:function(response){
              //  console.log(response.responseJSON);
              sweetAlert({
                    title: 'Error!',
                    text: response.responseJSON.message,
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
    });
});


/***
 * Faz o HTTP post
 * @returns retorna um json com as informações
 */
export const httpFetchPost = async function(url, token, data) {
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token // Adicione o token CSRF no cabeçalho
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`Error: ${response.statusText}`);
        }

        return await response.json();

    } catch (error) {
        console.error("There was a problem with the fetch operation:", error);
        throw error; // Re-throw the error so it can be handled by the caller
    }
}

 /**
     *   ATUALIZA A DATATABLE
     * */
 export function fncDataDatatable(table) {
    table.ajax.reload(null, false);
    return false;
}

export function fncPreLoadModal(){
    return "<div class=\"spinner-border text-primary\" role=\"status\">"+
                "<span class=\"sr-only\">Loading...</span>"+
            "</div>";
        
}