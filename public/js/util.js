    window.aplicarMascaraMoeda = function () {
        $('.moeda').mask('R$ 000.000.000,00', {
            reverse: true,
            placeholder: 'R$ 0,00'
        });
    };

    window.aplicarMascaraQuantidade = function () {
        $('.quantidade').mask('000000', { reverse: false });
    };

    // Aplica na primeira renderização e após Livewire atualizar o DOM
    document.addEventListener("livewire:load", function () {
        aplicarMascaraMoeda();
        aplicarMascaraQuantidade();

        Livewire.hook('message.processed', () => {
            aplicarMascaraMoeda();
            aplicarMascaraQuantidade();
        });
    });

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

    Livewire.on('confirmarAlteracaoStatus', produtoId => {
        Swal.fire({
            title: 'Desativar Produto?',
            text: "Isso também desativará todas as variações deste produto.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, alterar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                Livewire.emit('alterarStatusConfirmado', produtoId);
            }
        });
    });

    window.addEventListener('confirmar-desativacao-pai', function (event) {
        Swal.fire({
            title: 'Desativar produto também?',
            text: 'Essa é a última variação ativa. O produto pai será desativado também.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, desativar tudo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.emit('alterarStatusConfirmado', event.detail.produto_id);
            }
        });
    });

    window.addEventListener('status-alterado', event => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            title: 'Sucesso!',
            text: event.detail.status === 'desativado'
                ? 'Produto e variações desativados com sucesso.'
                : 'Produto ativado com sucesso.',
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
                break;

            // Você pode adicionar mais tipos aqui no futuro
            default:
                console.warn('Tipo de evento Livewire não tratado:', type);
        }
    });



    window.addEventListener('confirmarDesativacaoVariacao', function (event) {
        Swal.fire({
            title: 'Desativar produto também?',
            text: 'Essa é a última variação ativa. O produto pai será desativado também.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, desativar tudo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.emit('atualizarCampo', event.detail.variacao_id,event.detail.campo, event.detail.valor);
            }
        });
    });



    function confirmarDesativacaoVariacao(variacaoId, campo, inputElement) {
        let statusNovo = inputElement.checked ? 1 : 0;

        Swal.fire({
            title: 'Tem certeza?',
            text: statusNovo === 0
                ? 'Essa variação será desativada.'
                : 'Essa variação será ativada.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.emit('atualizarCampo', variacaoId, campo, statusNovo);
            } else {
                // Reverter toggle visualmente se cancelado
                inputElement.checked = !inputElement.checked;
            }
        });
    }
