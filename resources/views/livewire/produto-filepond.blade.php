<div id="filepond-wrapper-{{ $this->id }}" wire:ignore xmlns:wire="http://www.w3.org/1999/xhtml">
    <input type="file" name="filepond" {{ $multiple ? 'multiple' : '' }}>
</div>

@push('scripts')
    <script>
        document.addEventListener("livewire:load", function () {
            const container = document.getElementById('filepond-wrapper-{{ $this->id }}');
            if (!container) return;

            const inputElement = container.querySelector('input');
            const pond = FilePond.create(inputElement, {
                imageResizeTargetWidth: 500,
                imageResizeTargetHeight: 500,
                imageResizeMode: 'contain',
                plugins: [FilePondPluginImageResize, FilePondPluginImagePreview, FilePondPluginFileEncode],
                allowMultiple: {{ $multiple ? 'true' : 'false' }},
                labelIdle: 'Arraste imagens ou <span class="filepond--label-action">clique para escolher</span>',
            });

            // Arquivos já existentes do produto
            @if(!empty($imagensExistentes))
                pond.files = [
                    @foreach($imagensExistentes as $img)
                {
                    source: "{{ asset('storage/product/'.$img) }}",
                    options: {
                        type: 'local',
                        file: {
                            name: "{{ basename($img) }}",
                            size: 12345, // tamanho fictício, pode ajustar
                            type: "image/jpeg"
                        },
                        metadata: {
                            folder: "{{ $img }}"
                        }
                    }
                },
                @endforeach
            ];
            @endif

            pond.setOptions({
                server: {
                    process: {
                        url: '/admin/upload/tmp-upload',
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        onload: (res) => {
                            Livewire.emitTo('{{ $this->getName() }}', 'setPastasImagens', [res]);
                            return res;
                        }
                    },
                    revert: (folder, load, error) => {
                        fetch('/admin/upload/tmp-delete', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ folder: folder })
                        }).then(res => {
                            if (res.ok) load();
                            else error('Erro ao excluir imagem temporária');
                        }).catch(() => {
                            error('Falha na comunicação com o servidor');
                        });
                    }
                }
            });
        });
    </script>
@endpush
