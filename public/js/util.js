    FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginImageResize, FilePondPluginFileEncode);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let foldersEnviados = [];

    window.aplicarMascaraMoeda = function () {
        if ($('.moeda').length > 0) {
            $('.moeda').mask('R$ 000.000.000,00', {
                reverse: true,
                placeholder: 'R$ 0,00'
            });
        }
    };

    window.aplicarMascaraQuantidade = function () {
        if ($('.quantidade').length > 0) {
            $('.quantidade').mask('000000', {reverse: false});
        }
   };

    window.aplicarMascaraDataDDMMYYYY = function () {
        if ($('.data-mask').length > 0) {
            $('.data-mask').mask('##/##/####', {reverse: false});
        }
    };

    // function inicializarTudo(seletor) {
    //     const activeTab = sessionStorage.getItem('activeTab');
    //
    //     if (activeTab && $('#myTab').length) {
    //         $('#myTab a[href="' + activeTab + '"], #myTab button[data-target="' + activeTab + '"]').tab('show');
    //     }
    //
    //    // loadFilePond?.(seletor);
    //     aplicarMascaraMoeda?.();
    //     aplicarMascaraQuantidade?.();
    //     aplicarMascaraDataDDMMYYYY?.();
    //     initTooltips?.();
    // }

    //Aplica na primeira renderização e após Livewire atualizar o DOM
    // document.addEventListener("livewire:load", function () {
    //     aplicarMascaraMoeda();
    //     aplicarMascaraQuantidade();
    //     aplicarMascaraDataDDMMYYYY();
    //     initTooltips();
    //
    //     Livewire.hook('message.processed', () => {
    //         aplicarMascaraMoeda();
    //         aplicarMascaraQuantidade();
    //         aplicarMascaraDataDDMMYYYY();
    //         initTooltips();
    //     });
    // });

    window.addEventListener('status-generico', event => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            title: event.detail.title,
            text: event.detail.text,
            icon: event.detail.icon,
            timer: 3000,
            showConfirmButton: false,
        }).then(() => {
            // Após o SweetAlert fechar, emite evento Livewire
            Livewire.emit('voltar');
        });
    });


    /***
     * Altera o status do produto ou variação
     */
    function confirmarAlteracaoStatus(tipo, variacaoId, produtoId, inputElement) {
        let data = {};
        // Em JS, salvar o input para revertê-lo depois caso necessário
        window.inputStatusTemp = inputElement;

        if(tipo === "produto"){
            data.title = 'Desativar Produto?';
            data.text ='Isso também desativará todas as variações deste produto.';
        }else{
            data.title = 'Desativar Variação?';
            data.text ='Deseja desativar realmente esta variação?';

        }
        data.tipo = tipo;
        data.icon = 'warning';
        data.variacaoId = variacaoId;
        data.produtoId = produtoId;

        confirmAlert(data,inputElement);
    };

    window.addEventListener('confirmarDesativacaoStatus', event => {
        let data = {};
        data.title = event.detail.title;
        data.text = event.detail.text;
        data.produtoId = event.detail.produtoId;
        data.tipo = event.detail.tipo;

        confirmAlert(data);
    });

    function confirmAlert(data){

        Swal.fire({
            title: data.title, //'Desativar Produto?',
            text: data.text, //"Isso também desativará todas as variações deste produto.",
            icon: 'warning', //'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, alterar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                Livewire.emit('alterarStatusConfirmado', data);
            }else {
                // Reverter toggle visualmente se cancelado
                //inputElement.checked = !inputElement.checked;
                // Reverte o checkbox visualmente
                if (window.inputStatusTemp) {
                    window.inputStatusTemp.checked = !window.inputStatusTemp.checked;
                }
            }
        });
    }

    window.addEventListener('status-alterado', event => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            title: 'Sucesso!',
            text: event.detail.text,
            icon: 'success',
            timer: 3000,
            showConfirmButton: false,
        }).then(() => {
            // Após o SweetAlert fechar, emite evento Livewire
            Livewire.emit('voltar');
        });
    });

    //exibe a mesangem an tela
    window.addEventListener('livewire:event', event => {
        const { type, message, id } = event.detail;

        switch (type) {
            case 'alert':
                Swal.fire({
                    toast: true,
                    icon: event.detail.icon || 'success',
                    title: message,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                break;

            case 'imagemRemovida':
                const el = document.getElementById(`imagem-${id}`);
                if (el) {
                    $(el).fadeOut(300, () => el.remove());
                }
                Livewire.emitTo('filepond-upload', 'imagemDeletada', id);
                break;

            // Você pode adicionar mais tipos aqui no futuro
            default:
                console.warn('Tipo de evento Livewire não tratado:', type);
        }
    });


    function previewImagem(src) {
        const imgElement = document.getElementById('imagemPreviewGrande');
        imgElement.src = src;

        const modal = new bootstrap.Modal(document.getElementById('previewImagemModal'));
        modal.show();
    }

    function initTooltips() {
        $('[data-toggle="tooltip"]').tooltip();
    }

    //para centralizar re-render o Filepond quando for rendreizado pleo livewire
    // function loadFilePond(targetSelector, options = {}) {
    //     const container = document.querySelector(targetSelector);
    //     if (!container) return;
    //
    //     const baseUrl = window.location.origin + '/admin';
    //     const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    //
    //     // Limpa o container antes
    //     container.innerHTML = `
    //     <input type="file"
    //         multiple
    //         id="${options.inputId || 'filepond'}"
    //         name="${options.inputName || 'images[]'}"
    //         class="filepond"
    //         data-max-files="${options.maxFiles || 10}"
    //         data-max-file-size="${options.maxFileSize || '3MB'}"
    //         data-allow-reorder="true"
    //         data-allow-multiple="true"
    //     />
    // `;
    //
    //     const inputElement = container.querySelector('input');
    //
    //     FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginImageResize, FilePondPluginFileEncode);
    //
    //     const pond = FilePond.create(inputElement, {
    //         imageResizeTargetWidth: options.resizeWidth || 500,
    //         imageResizeTargetHeight: options.resizeHeight || 500,
    //         imageResizeMode: options.resizeMode || 'contain',
    //         server: {
    //             process: {
    //                 method: 'POST',
    //                 url: baseUrl + '/upload/tmp-upload',
    //                 headers: {
    //                     'X-CSRF-TOKEN': csrfToken
    //                 },
    //                 name: 'image',
    //                 onload: options.onProcessLoad || ((res) => {
    //                     foldersEnviados.push(res);
    //                     return res;
    //                 })
    //             },
    //             revert: baseUrl + '/upload/tmp-delete',
    //         },
    //         labelIdle: options.labelIdle || 'Arraste imagens ou <span class="filepond--label-action">clique para escolher</span>',
    //         allowMultiple: true
    //     });
    //
    //     return pond;
    // }

    /**
     * Função SweetAlert de confirmação deleção
     * @param id
     * @param isVariacao
     */
    function confirmarExclusaoImagem(id, isVariacao ) {
        Swal.fire({
            title: 'Excluir imagem?',
            text: "Essa ação não poderá ser desfeita!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Muda ícone para spinner
                const icon = document.getElementById(`icon-trash-${id}`);
                icon.classList.remove('fa-trash-alt');
                icon.classList.add('fa-spinner', 'fa-spin');

                // Dispara o evento Livewire
                Livewire.emitTo('filepond-upload','deletarImagem', id, isVariacao);

            }
        });
    }



    //Ao clicar em salvar aciona o Livewire
    const btnSalvar = document.getElementById('btn-salvar-produto');
    if (btnSalvar) {
        document.getElementById('btn-salvar-produto').addEventListener('click', function () {
            //sessionStorage.removeItem('activeTab'); // <-- volta pra aba 1 após salvar
            // Chama o botão invisível com wire:click="salvar"
            document.getElementById('btn-livewire-salvar').click();
            //if (typeof Livewire !== 'undefined') {
            console.log("salvar",  foldersEnviados);
                Livewire.emit('salvar');
                //loadFilePondProduto();
           // }
        });
    }

    /**
     * Seta as abas para permanecer onde forma clciadas ao dar refesh
     * */
    loadSetAbas = function () {
        const activeTab = sessionStorage.getItem('activeTab');
        if (activeTab) {
            $('#myTab button[data-target="' + activeTab + '"]').tab('show');
        }
    }

    /**
     * Altera a descrição do status
     * */
    toggleStatusDescription = function(checkbox){
        const statusLabel = document.getElementById('statusLabel');
        statusLabel.textContent = checkbox.checked ? 'Ativo' : 'Inativo';
    }

    /***
     * Recria o obeto input do filepond devido ao livewire recriar o DOM
     */
    containerUpadate = function() {
        return  `
                <input type="file"
                       multiple
                       id="image"
                       name="image[]"
                       data-max-files="10"
                       data-allow-reorder="true"
                       data-max-file-size="3MB"
                       data-allow-multiple="true"
                       class="filepond" />
            `;
    }

    /***
     * Para centralizar re-render o Filepond quando for rendreizado pelo livewire
     */
    // function loadFilePondProduto() {
    //     const container = document.getElementById('filepond-wrapper');
    //     if (!container) return;
    //
    //     // Remove conteúdo anterior
    //    // container.innerHTML = containerUpadate();
    //
    //     const inputElement = container.querySelector('input');
    //
    //     // Create a FilePond instance
    //     const pond = FilePond.create(inputElement,{
    //      imageResizeTargetWidth: 500, // Largura alvo para redimensionamento (opcional)
    //      imageResizeTargetHeight: 500, // Altura alvo para redimensionamento (opcional)
    //      imageResizeMode: 'contain', // Modo de redimensionamento (opcional)
    //      plugins: [FilePondPluginImageResize, FilePondPluginImagePreview,FilePondPluginFileEncode]
    //     });
    //
    //     pond.setOptions({
    //         server: {
    //             process: {
    //                 url: '/admin/upload/tmp-upload',
    //                 method: 'POST',
    //                 headers: { 'X-CSRF-TOKEN': csrfToken },
    //                 onload: (res) => {
    //                     // Atualiza lista de arquivos no Livewire
    //                     //Livewire.emit('refreshTemporaryFiles');
    //                     foldersEnviados.push(res);
    //                     Livewire.emit('setPastasImagens', foldersEnviados);
    //                     //console.log("res > ", res, 'foldersEnviados' , foldersEnviados);
    //                     return res;
    //                 }
    //             },
    //             revert: (folder, load, error) => {
    //                 fetch('/admin/upload/tmp-delete', {
    //                     method: 'DELETE',
    //                     headers: {
    //                         'Content-Type': 'application/json',
    //                         'X-CSRF-TOKEN': csrfToken
    //                     },
    //                     body: JSON.stringify({ folder: folder })
    //                 }).then(res => {
    //                     if (res.ok) {
    //                         load();
    //                     } else {
    //                         error('Erro ao excluir imagem temporária');
    //                     }
    //                 }).catch(err => {
    //                     error('Falha na comunicação com o servidor');
    //                 });
    //             }
    //         },
    //         labelIdle: 'Arraste imagens ou <span class="filepond--label-action">clique para escolher</span>',
    //         allowMultiple: true
    //     });
    // }

    function inicializaFilePondVariacoes() {
        document.querySelectorAll('[id^="filepond-wrapper-variacao-"]').forEach(wrapper => {
            const variacaoId = wrapper.id.split('-').pop();  // aqui captura o ID real da variação

            // Se o input ainda não existe, cria
            if (!wrapper.querySelector('input[type="file"]')) {
                const input = document.createElement('input');
                input.type = 'file';
                input.id = `variacao-imagem-${variacaoId}`;
                input.name = `variacao_imagem[${variacaoId}][]`;
                input.className = 'filepond';
                input.multiple = true;
                input.dataset.maxFiles = '5';

                wrapper.appendChild(input);
            }

            // Inicializa o FilePond com o variacaoId como parâmetro
            inicializaFilePondVariacao(`#variacao-imagem-${variacaoId}`, variacaoId);
        });
    }

    function inicializaFilePondVariacao(selector, variacaoId, options = {}) {
        const inputElement = document.querySelector(selector);
        if (!inputElement) return;

        // Create a FilePond instance
        const pond = FilePond.create(inputElement,{
            imageResizeTargetWidth: 500, // Largura alvo para redimensionamento (opcional)
            imageResizeTargetHeight: 500, // Altura alvo para redimensionamento (opcional)
            imageResizeMode: 'contain', // Modo de redimensionamento (opcional)
            plugins: [FilePondPluginImageResize, FilePondPluginImagePreview,FilePondPluginFileEncode]
        });

        pond.setOptions({
            labelIdle: 'Arraste imagens ou <span class="filepond--label-action">clique para escolher</span>',
            acceptedFileTypes: ['image/*'],
            maxFiles: 5,
            server: {
                process: {
                    url: '/admin/upload/tmp-upload',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    ondata: (formData) => {
                        formData.append('variacao_id', variacaoId);
                        return formData;
                    },
                    onerror: (err) => console.error('Erro upload variação:', err),
                    onload: (res) => {
                        console.log('process retorno:', res);
                        return res; // necessário para que o revert saiba qual ID usar
                    }
                },
                revert: (folder, load, error) => {
                    console.log('Chamando revert com folder:', folder);
                    fetch('/admin/upload/tmp-delete', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ folder: folder })
                    }).then(res => {
                        if (res.ok) {
                            load();
                        } else {
                            error('Erro ao excluir imagem temporária');
                        }
                    }).catch(err => {
                        error('Falha na comunicação com o servidor');
                    });
                }
            },
            ...options
        });

    }

