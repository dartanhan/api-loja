<div x-data x-init="
    FilePond.registerPlugin(FilePondPluginImagePreview);
    const pond = FilePond.create($refs.input);
    let foldersEnviados = [];

    pond.setOptions({
        server: {
             process: (fieldName, file, metadata, load, error, progress, abort) => {
                @this.upload('files', file, (res) => {
                    // intercepta a resposta (res) antes de repassar para o FilePond
                    foldersEnviados.push(res);
                    load(res);
                }, error, progress);
            },
            revert: (filename, load) => {
                const file = @js($uploadedFiles->firstWhere('filename', filename));
                if(file) {
                    @this.deleteFile(file.id);
                }
                load();
            },
            load: (source, load) => {
                fetch(source).then(res => res.blob()).then(load);
            }
        },
        allowMultiple: true,
        maxFiles: 10,
    });
" class="filepond-wrapper">
    <input type="file" x-ref="input">
</div>

{{-- Lista de arquivos já enviados --}}
@if(!empty($uploadedFiles))
    <ul class="list-group mt-2">
        @foreach($uploadedFiles as $f)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ basename($f->filename) }}
                <button class="btn btn-sm btn-outline-danger"
                        wire:click="deleteFile({{ $f->id }})">
                    <i class="fas fa-trash"></i>
                </button>
            </li>
        @endforeach
    </ul>
@endif



{{--<div wire:ignore xmlns:wire="http://www.w3.org/1999/xhtml">--}}
{{--    <input type="file"--}}
{{--           id="filepond-produto"--}}
{{--           class="filepond"--}}
{{--           multiple--}}
{{--           data-max-files="5"--}}
{{--           data-allow-reorder="true"--}}
{{--           data-max-file-size="3MB"--}}
{{--           data-allow-multiple="true"--}}
{{--    />--}}
{{--</div>--}}

{{--@push('scripts')--}}
{{--    <script>--}}
{{--        document.addEventListener('livewire:load', () => {--}}
{{--            FilePond.create(document.getElementById('filepond-produto'), {--}}
{{--                server: {--}}
{{--                    process: {--}}
{{--                        url: '/admin/upload/tmp-upload',--}}
{{--                        method: 'POST',--}}
{{--                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }--}}
{{--                    },--}}
{{--                    revert: (folder, load, error) => {--}}
{{--                        fetch('/admin/upload/tmp-delete', {--}}
{{--                            method: 'DELETE',--}}
{{--                            headers: {--}}
{{--                                'Content-Type': 'application/json',--}}
{{--                                'X-CSRF-TOKEN': '{{ csrf_token() }}'--}}
{{--                            },--}}
{{--                            body: JSON.stringify({ folder: folder })--}}
{{--                        }).then(res => res.ok ? load() : error('Erro ao excluir'))--}}
{{--                            .catch(() => error('Erro na comunicação'));--}}
{{--                    }--}}
{{--                },--}}
{{--                labelIdle: 'Arraste ou clique para enviar imagens',--}}
{{--                acceptedFileTypes: ['image/*'],--}}
{{--                maxFiles: 5--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
{{--@endpush--}}
