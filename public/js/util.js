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

    Livewire.on('confirmarAlteracaoStatus', produtoId => {
        Swal.fire({
            title: 'Alterar status?',
            text: 'Deseja realmente alterar o status do produto?',
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


    window.addEventListener('confirmar-desativacao-produto', event => {
        Swal.fire({
            title: 'Desativar Produto?',
            text: "Isso também desativará todas as variações deste produto.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, desativar tudo!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.emit('desativarProdutoComVariacoes', event.detail.produto_id);
            }
        });
    });

    window.addEventListener('status-alterado', event => {
        Swal.fire({
            title: 'Sucesso!',
            text: event.detail.status === 'desativado'
                ? 'Produto e variações desativados com sucesso.'
                : 'Produto ativado com sucesso.',
            icon: 'success',
            timer: 3000,
            showConfirmButton: false,
        });
    });

