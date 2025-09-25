<div x-data
     x-init="
        const pond = FilePond.create($refs.input, {
            allowMultiple: {{ $multiple ? 'true' : 'false' }},
            labelIdle: '📁 Arraste ou <span class=\'filepond--label-action\'>clique</span> para enviar imagens',
            server: {
                process: (fieldName, file, metadata, load, error, progress, abort) => {
                    @this.upload('uploads', file,
                        (uploadedFilename) => {
                            // uploadedFilename é string (nome temporário)
                            @this.call('addImagem', {
                                context: '{{ $context }}',
                                file: uploadedFilename,
                                variacaoKey: {{ json_encode($variacaoKey ?? null) }}
         });
         load(uploadedFilename);
     },
     (erro) => { error(erro); },
     (event) => { progress(event.detail.progress); }
 );
},
revert: (uniqueFileId, load) => {
 // usar 'uploads' aqui (consistente com o upload)
 @this.removeUpload('uploads', uniqueFileId, () => {
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
        <input type="file" x-ref="input" {{ $multiple ? 'multiple' : '' }}>
    </div>
</div>
