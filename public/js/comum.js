// Anexar funções ao objeto window para torná-las globais
window.formatMoneyPress = formatMoneyPress;
window.formatMoney = formatMoney;
window.getFormattedDate = getFormattedDate;
window.SomenteNumeros = SomenteNumeros;
window.formatDate = formatDate;
window.sweetAlert = sweetAlert;
window.sweetAlertClose = sweetAlertClose;
window.createSlug = createSlug;
window.botaoLoad = botaoLoad;
window.removeCampo = removeCampo;



const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger'
    },
    buttonsStyling: false
});

export function sweetAlert(json){
    swalWithBootstrapButtons.fire(json);
}

export function sweetAlertClose(){
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
export function formatMoney(valor, cifrao = "R$ ")
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

        return cifrao + r.substring(0, r.lastIndexOf('.')) + ',' + v[1];
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
     if (parm === '0000-00-00') {
         return '00/00/0000';
     }
     return moment(parm, 'YYYY-MM-DD').format('DD/MM/YYYY');
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
Retorna por padrão um icone de hide de money
* */
export function getIconHideMoney(){
    return " <strong class=\"fs-5\"><svg xmlns=\"http://www.w3.org/2000/svg\" height=\"24px\" " +
        "viewBox=\"0 -960 960 960\" width=\"24px\" fill=\"#e3e3e3\"><path d=\"m644-428-58-58q9-47-27-88t-93-32l-58-58q17-8 " +
        "34.5-12t37.5-4q75 0 127.5 52.5T660-500q0 20-4 37.5T644-428Zm128 126-58-56q38-29 67.5-63.5T832-500q-50-101-143.5-160.5T480-720q-29 " +
        "0-57 4t-55 12l-62-62q41-17 84-25.5t90-8.5q151 0 269 83.5T920-500q-23 59-60.5 109.5T772-302Zm20 246L624-222q-35 11-70.5 " +
        "16.5T480-200q-151 0-269-83.5T40-500q21-53 53-98.5t73-81.5L56-792l56-56 736 736-56 56ZM222-624q-29 26-53 57t-41 67q50 " +
        "101 143.5 160.5T480-280q20 0 39-2.5t39-5.5l-36-38q-11 3-21 4.5t-21 1.5q-75 0-127.5-52.5T300-500q0-11 " +
        "1.5-21t4.5-21l-84-82Zm319 93Zm-151 75Z\"/></svg></strong>";
}


/***
 * Ao digitar, formata a data no campo em dd/mm/yyyy
 * */
export function formatDate(parm) {

    let tecla = parm.keyCode;
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
                const json = $.parseJSON(data.responseText);
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
                const json = $.parseJSON(data.responseText);
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

    // Função para criar o slug
    export function createSlug(value) {
        return value
            .toLowerCase() // Converter para minúsculas
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Remover acentos e diacríticos
            .trim() // Remover espaços em branco das extremidades
            .replace(/[^\w\s-]/g, '') // Remover caracteres especiais
            .replace(/[\s_-]+/g, '-') // Substituir espaços e underscores por hífens
            .replace(/^-+|-+$/g, ''); // Remover hífens das extremidades
    }

    export function botaoLoad(parm) {
        $('#'+parm+'').html('Aguarde... <span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span>');
    }


    /**
     * REMOVE OS INPUTS DINAMICOS DAS VARIAÇÕES
     * */
    export function removeCampo(parm) {
        document.getElementById(parm).remove();
    }
