// const inputElement = document.querySelector('input[id="image"]');
//
// const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
//
// FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginImageResize,FilePondPluginFileEncode);
//
// // Create a FilePond instance
// const pond = FilePond.create(inputElement,{
//     imageResizeTargetWidth: 500, // Largura alvo para redimensionamento (opcional)
//     imageResizeTargetHeight: 500, // Altura alvo para redimensionamento (opcional)
//     imageResizeMode: 'contain', // Modo de redimensionamento (opcional)
//     plugins: [FilePondPluginImageResize, FilePondPluginImagePreview,FilePondPluginFileEncode],
//
// });
//
// const baseUrl = window.location.origin + '/admin';
// let foldersEnviados = [];
//
// pond.setOptions({
//     server: {
//         process:{
//             method: 'POST',
//             url: baseUrl + '/upload/tmp-upload',
//             headers: {
//                 'X-CSRF-TOKEN': csrfToken,
//             },
//             onload: (res) => {
//                 foldersEnviados.push(res); // <-- adiciona o nome da pasta retornada
//                 return res;
//             }
//         },
//         revert: baseUrl + '/upload/tmp-delete',
//
//     },labelIdle: 'Arraste imagens ou <span class="filepond--label-action">clique para escolher</span>'
// });
//
//
