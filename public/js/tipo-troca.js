/* globals Chart:false, feather:false */
$(document).ready(function() {
    const urlApi = fncUrl();

    let metodo = '',titulo = '', id ='',url,json,fila,nome,taxa,token;

    $('#valor_taxa').maskMoney();

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    });

    let table  = $('#table').DataTable({
        ajax:{
            method: "get",
            url: urlApi + "/tipoTroca/create",
            processing: true,
            serverSide: true
        },
        "columns":[
            {"data": "id"},
            {"data": "descricao"},
            {"data": "slug"},
            {"data": "created_at"},
            {"data": "updated_at"},
            {"defaultContent": "<div class='text-center'>" +
                    "<div class='btn-group'>" +
                    "<button class='btn btn-primary btn-sm btnEditar' " +
                    "data-bs-toggle=\"modal\" data-bs-target=\"#divModal\">" +
                    "<i class='material-icons'>edit</i></button>&nbsp;&nbsp;" +
                    "<button class='btn btn-danger btn-sm btnBorrar'><i class='material-icons'>delete</i></button>" +
                    "</div>" +
                    "</div>"
            }
        ] ,
        language: {
            "url": "../public/Portuguese-Brasil.json"
        },
        "order": [1, "asc"]
    });

    /**
     * AÇÃO DE ABRIR O MODAL
     * Novo
     * */
    $('button[id="btnNuevo"]').on('click', function(event) {
        event.preventDefault();
        $('form[name="form"]')[0].reset();

        $("#id").val('');
        $("#metodo").val('POST');
        $('#modal-title').html('<strong>NOVO TIPO DE TROCA</strong>');
        // Focar no campo de descrição quando o modal for aberto

        setTimeout(() => {
            $('#descricao').focus();
            //Adicona ao focus ao input, de pesqusia de produtos

        }, 500);

    });

    /**
     * Editar
     * **/
    $(document).on("click", ".btnEditar", function(event){
        event.preventDefault();

        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(0)').text()); //capturo o ID
        nome  = fila.find('td:eq(1)').text();
        let slug = fila.find('td:eq(2)').text();

        let currentRow = $(this).closest("tr");
        let data = $('#table').DataTable().row(currentRow).data();

        $("#metodo").val('PUT');
        $("#id").val(id);
        $("#descricao").val(nome);
        $("#slug").val(slug);

        $('#modal-title').html('<strong>EDITANDO REGISTRO: ID ['+id+'] - NOME ['+nome+']</strong>');
        $('#divModal').on('shown.bs.modal', function () {
            $('#descricao').focus();
        });
    });

    /****
     *
     * SALVA FORMA
     *
     */
    $('form[name="form"]').validate({
        errorClass: "my-error-class",
        validClass: "my-valid-class",
        rules: {
            descricao: {
                required: true
            }
        },
        messages: {
            descricao: {
                required: "Informe a Descrição?"
            }
        }, submitHandler: function(form,e) {
         //   console.log('Form submitted');
           e.preventDefault();

            metodo = $("#metodo").val();
            id = $("#id").val();

            if(metodo === 'POST'){
                url = urlApi + "/tipoTroca";
                titulo = "NOVO TIPO DE TROCA";

            }else if(metodo === 'PUT'){
                url = urlApi + "/tipoTroca/"+id;
                titulo = "EDITANDO TIPO DE TROCA";
            }

            $.ajax({
                type: metodo,
                url: url,
                data:$('form[name="form"]').serialize(),
                dataType:"json",
                beforeSend: function () {
                    $('#btnGuardar').html(`
                        <span>Aguarde...</span>
                        <div class="spinner-border spinner-border-xs ms-auto" role="status" aria-hidden="true"></div>
                    `);
                },
                success: function(data) {
                    if(data.success) {
                        swalWithBootstrapButtons.fire({
                            title: "Sucesso!",
                            text: data.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        table.ajax.reload(null, false);
                    }
                },
                error: function(data){
                    json = $.parseJSON(data.responseText);
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
                            $('#divModal').modal('hide');
                            $('#btnGuardar').html('Salvar');
                        }, 1500);
                    }
                }
            });
        }
    });

    /**
     * Remover
     * **/
    $(document).on("click", ".btnBorrar", function(event){
        event.preventDefault();
        fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(0)').text()) ;
        nome = fila.find('td:eq(1)').text();
        token = $('form').find('input[name="_token"]').val();

        Swal.fire({
            title: 'Tem certeza?',
            text: "Está seguro de remover este registro: [ " + nome + " ] ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, deletar!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: urlApi + "/tipoTroca/" + id ,
                    data: {_token: token},
                    type: "DELETE",
                    datatype:"json",
                    beforeSend: function () {
                        swalWithBootstrapButtons.fire(
                            'Aguarde..',
                            '<div class=\"spinner-border spinner-border-xs ms-auto\" role=\"status\" aria-hidden=\"true\"></div>',
                            'info'
                        )
                    },
                    success: function(data) {
                        if(data.success) {
                            swalWithBootstrapButtons.fire({
                                title: 'Deletado!',
                                text: data.message,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function(data){
                        json = $.parseJSON(data.responseText);
                        Swal.fire(
                            'error!',
                            json.message,
                            'error'
                        )
                    }
                });
            }
        });
    });

});

function generateSlug() {
    const description = document.getElementById('descricao').value;
    const slug = description
        .toLowerCase() // converte para minúsculas
        .replace(/ /g, '-') // troca espaços por hífens
        .normalize('NFD') // normaliza o texto (para remover acentos)
        .replace(/[\u0300-\u036f]/g, '') // remove acentos
        .replace(/[^a-z0-9-]/g, ''); // remove caracteres especiais

    document.getElementById('slug').value = slug;
}
