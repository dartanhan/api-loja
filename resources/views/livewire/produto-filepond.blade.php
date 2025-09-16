<div
    x-data
    x-init="
        const pond = FilePond.create($refs.input, {
            allowMultiple: {{ $multiple ? 'true' : 'false' }},
            labelIdle: '📁 Arraste ou <span class=\'filepond--label-action\'>clique</span> para enviar imagens',
            server: {
                process: (fieldName, file, metadata, load, error, progress, abort) => {
                    @this.upload('uploads', file,
                        (uploadedFilename) => {
                            @this.call('addImagem', {
                                context: '{{ $context }}',
                                file: uploadedFilename,
                                variacaoKey: '{{ $variacaoKey ?? null }}'
                            });
                            load(uploadedFilename);
                        },
                        (erro) => { error(erro); },
                        (event) => { progress(event.detail.progress); }
                    );
                },
                revert: (uniqueFileId, load) => {
                    @this.removeUpload('images', uniqueFileId, () => {
                        @this.call('removeImagem', uniqueFileId);
                        load();
                    });
                }
            }
        });
    "
    xmlns:wire="http://www.w3.org/1999/xhtml"
>
    <div wire:ignore>
        <input type="file" x-ref="input"  {{ $multiple ? 'multiple' : '' }}>
    </div>
    <input type="hidden" wire:model="images">
</div>




{{--<div
    id="filepond-wrapper-{{ $this->id }}"
    class="filepond-wrapper"
    data-variacao-key="{{ $variacaoKey ?? '' }}"
    data-context="{{ $context ?? 'produto' }}"
    wire:id="{{ $this->id }}"
    wire:ignore
    xmlns:wire="http://www.w3.org/1999/xhtml">
    <input
        type="file"
        class="filepond-input"
        {{ $multiple ? 'multiple' : '' }}
    />
</div>--}}



{{-- NÃO coloque aqui o script de inicialização que só roda uma vez. --}}
{{-- Usaremos um script global (abaixo) para inicializar todos os inputs e re-inicializar. --}}

{{--<div xmlns:wire="http://www.w3.org/1999/xhtml">--}}
{{--    <div class="row" id="filepond-wrapper-{{ $this->id }}" wire:ignore>--}}
{{--        --}}{{-- Sempre renderiza o input do FilePond --}}
{{--        <input type="file" class="filepond" {{ $multiple ? 'multiple' : '' }} />--}}
{{--    </div>--}}
{{--</div>--}}

{{--<script>--}}
{{--    document.addEventListener('livewire:load', () => {--}}
{{--        (function initPond_{{ $this->id }}() {--}}
{{--            const wrapperId = 'filepond-wrapper-{{ $this->id }}';--}}
{{--            const container = document.getElementById(wrapperId);--}}
{{--            if (!container) return;--}}

{{--            const input = container.querySelector('input');--}}
{{--            if (!input || typeof FilePond === 'undefined') return;--}}

{{--            // pega o csrf--}}
{{--            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';--}}

{{--            // contexto desta instância (produto | variacao) e chave da variacao--}}
{{--            const context = @json($context);--}}
{{--            const variacaoKey = @json($variacaoKey);--}}
{{--            const allowMultiple = @json($multiple);--}}

{{--            // estado local desta instância--}}
{{--            let foldersEnviados = [];--}}

{{--            const lw = @this; // Livewire proxy desta instância--}}

{{--            const pond = FilePond.create(input, {--}}
{{--                allowMultiple: allowMultiple,--}}
{{--                labelIdle: 'Arraste imagens ou <span class="filepond--label-action">clique para escolher</span>',--}}
{{--            });--}}

{{--            pond.setOptions({--}}
{{--                server: {--}}
{{--                    process: {--}}
{{--                        url: '/admin/upload/tmp-upload',--}}
{{--                        method: 'POST',--}}
{{--                        headers: { 'X-CSRF-TOKEN': csrfToken },--}}
{{--                        onload: (serverId) => {--}}
{{--                            try {--}}
{{--                                foldersEnviados.push(serverId);--}}

{{--                                if (context === 'produto') {--}}
{{--                                    lw.call('setPastasImagensProduto', foldersEnviados);--}}
{{--                                } else {--}}
{{--                                    lw.call('setPastasImagensVariacao', {--}}
{{--                                        variacao_key: variacaoKey,--}}
{{--                                        pastas: foldersEnviados--}}
{{--                                    });--}}
{{--                                }--}}
{{--                                return serverId;--}}
{{--                            } catch (e) {--}}
{{--                                console.error('Erro process onload:', e);--}}
{{--                                return serverId;--}}
{{--                            }--}}
{{--                        }--}}
{{--                    },--}}
{{--                    revert: (serverId, load, error) => {--}}
{{--                        fetch('/admin/upload/tmp-delete', {--}}
{{--                            method: 'DELETE',--}}
{{--                            headers: {--}}
{{--                                'Content-Type': 'application/json',--}}
{{--                                'X-CSRF-TOKEN': csrfToken--}}
{{--                            },--}}
{{--                            body: JSON.stringify({ folder: serverId })--}}
{{--                        }).then(res => {--}}
{{--                            if (!res.ok) throw new Error('Erro ao excluir imagem temporária');--}}
{{--                            foldersEnviados = foldersEnviados.filter(f => f !== serverId);--}}

{{--                            if (context === 'produto') {--}}
{{--                                lw.call('setPastasImagensProduto', foldersEnviados);--}}
{{--                            } else {--}}
{{--                                lw.call('setPastasImagensVariacao', {--}}
{{--                                    variacao_key: variacaoKey,--}}
{{--                                    pastas: foldersEnviados--}}
{{--                                });--}}
{{--                            }--}}
{{--                            load();--}}
{{--                        }).catch(err => {--}}
{{--                            console.error(err);--}}
{{--                            error('Falha na comunicação com o servidor');--}}
{{--                        });--}}
{{--                    }--}}
{{--                }--}}
{{--            });--}}

{{--            // Pré-carregar imagens já salvas (edição)--}}
{{--            @if(!empty($imagensExistentes))--}}
{{--                pond.files = [--}}
{{--                    @foreach($imagensExistentes as $img)--}}
{{--                {--}}
{{--                    source: "{{ is_string($img) ? $img : ($img['url'] ?? asset('storage/product/'.($img['produto_id'] ?? '').'/'.($img['path'] ?? ''))) }}",--}}
{{--                    options: { type: 'local' }--}}
{{--                },--}}
{{--                @endforeach--}}
{{--            ];--}}
{{--            @endif--}}

{{--        })();--}}
{{--    });--}}
{{--</script>--}}
