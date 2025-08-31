<div>
    <div class="row" id="filepond-wrapper-{{ $this->id }}" wire:ignore xmlns:wire="http://www.w3.org/1999/xhtml">
        @dump($imagensExistentes)
        @if(count($imagensExistentes) > 0)
            @foreach($imagensExistentes as $imagem)

                <div class="col-md-2 mb-3 imagem-item" id="imagem-{{ $imagem['id'] }}" wire:key="imagem-{{ $imagem['id'] }}">
                    <div class="border rounded p-2 text-center position-relative">
                        <img src="{{ asset('storage/product/'. $imagem['produto_id'] .'/'. $imagem['path']) }}"
                             alt="Imagem"
                             class="img-fluid mb-2 rounded"
                             style="max-height: 150px; min-height: 120px; object-fit: cover;">

                        <div class="d-flex justify-content-center">
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="confirmarExclusaoImagem({{ $imagem['id'] }}, {{ $context === 'variacao' ? 'true' : 'false' }})"
                                    data-toggle="tooltip" title="Excluir imagem">
                                <i class="fas fa-trash-alt" id="icon-trash-{{ $imagem['id'] }}"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <input type="file" class="filepond" {{ $multiple ? 'multiple' : '' }} />
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:load', () => {
        (function initPond_{{ $this->id }}() {
            const wrapperId = 'filepond-wrapper-{{ $this->id }}';
            const container = document.getElementById(wrapperId);
            if (!container) return;

            const input = container.querySelector('input');
            if (!input || typeof FilePond === 'undefined') return;

            // pega o csrf
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

            // contexto desta instância (produto | variacao) e chave da variacao
            const context = @json($context);
            const variacaoKey = @json($variacaoKey);
            const allowMultiple = @json($multiple);

            // estado local desta instância
            let foldersEnviados = [];

            const lw = @this; // Livewire proxy desta instância

            const pond = FilePond.create(input, {
                allowMultiple: allowMultiple,
                labelIdle: 'Arraste imagens ou <span class="filepond--label-action">clique para escolher</span>',
                // se usar plugins de imagem, registre-os globalmente no layout antes
                // plugins: [FilePondPluginImagePreview, FilePondPluginImageResize, FilePondPluginFileEncode],
            });

            pond.setOptions({
                server: {
                    process: {
                        url: '/admin/upload/tmp-upload',
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        onload: (serverId) => {
                            // serverId deve ser a "pasta" retornada pelo seu endpoint
                            try {
                                // se sua API retorna JSON, ajuste aqui:
                                // const { folder } = JSON.parse(serverId);
                                // foldersEnviados.push(folder);

                                foldersEnviados.push(serverId);

                                if (context === 'produto') {
                                    lw.call('setPastasImagensProduto', foldersEnviados);
                                } else {
                                    lw.call('setPastasImagensVariacao', { variacao_key: variacaoKey, pastas: foldersEnviados });
                                }
                                return serverId;
                            } catch (e) {
                                console.error('Erro process onload:', e);
                                return serverId;
                            }
                        }
                    },
                    revert: (serverId, load, error) => {
                        fetch('/admin/upload/tmp-delete', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ folder: serverId })
                        }).then(res => {
                            if (!res.ok) throw new Error('Erro ao excluir imagem temporária');
                            // remove do array local e sincroniza com Livewire
                            foldersEnviados = foldersEnviados.filter(f => f !== serverId);
                            if (context === 'produto') {
                                lw.call('setPastasImagensProduto', foldersEnviados);
                            } else {
                                lw.call('setPastasImagensVariacao', { variacao_key: variacaoKey, pastas: foldersEnviados });
                            }
                            load();
                        }).catch(err => {
                            console.error(err);
                            error('Falha na comunicação com o servidor');
                        });
                    }
                }
            });

            // Se quiser pré-carregar imagens existentes (edição), descomente e alimente $imagensExistentes com urls:
            @if(!empty($imagensExistentes))
                pond.files = [
                    @foreach($imagensExistentes as $img)
                {
                    source: "{{ is_string($img) ? $img : ($img['url'] ?? $img['path'] ?? '') }}",
                    options: {
                        type: 'local'
                    }
                },
                @endforeach
            ];
            @endif

        })();
    });
</script>
