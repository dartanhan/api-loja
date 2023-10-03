const inputElement = document.querySelector('input[type="file"]');

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginImageResize);

// Create a FilePond instance
const pond = FilePond.create(inputElement,{
    imageResizeTargetWidth: 500, // Largura alvo para redimensionamento (opcional)
    imageResizeTargetHeight: 500, // Altura alvo para redimensionamento (opcional)
    imageResizeMode: 'contain', // Modo de redimensionamento (opcional)
    plugins: [FilePondPluginImageResize, FilePondPluginImagePreview]
});
pond.setOptions({
    server: {
        process: './upload/tmp-upload',
        revert: './upload/tmp-delete',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        }
    }
});
