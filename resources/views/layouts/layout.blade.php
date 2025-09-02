<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="asset-url" content="{{ asset('') }}">
    <title>KN Cosméticos - Admin</title>
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/bootstrap/css/bootstrap.css')}}">
    <link href="{{asset('css/dashboard/styles.css')}}" rel="stylesheet" />

    <link href="{{ asset('assets/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/filepond/filepond.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/filepond/filepond-plugin-image-preview.css') }}" rel="stylesheet"/>
    <link href="{{asset('assets/fontawesome-free-6.7.2-web/css/all.min.css')}}" rel="stylesheet" />

    <link href="{{ asset('css/chosen.css') }}" rel="stylesheet" type="text/css">

    @stack("styles")

    @livewireStyles
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="{{route('admin.home')}}">Administração</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!">
            <i class="fas fa-bars"></i>
        </button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
{{--            <div class="input-group">--}}
{{--                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />--}}
{{--                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>--}}
{{--            </div>--}}
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-5">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <!--li><a class="dropdown-item" href="#!">Settings</a></li>
                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                    <li><hr class="dropdown-divider" /></li-->
                    <li>
                        <a class="dropdown-item" href="{{route('admin.logout')}}">
                            <i class="fas fa-sign-out"></i> Logout
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            @yield('menu')
        </div>
        <div id="layoutSidenav_content">
            <main>
                @yield('content')
            </main>
            <footer class="py-3 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">
                            Copyright &copy; KN COSMÉTICOS 2017 - {{ date('Y') }} [ {{ date('Y') - 2017 }} Anos ]
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('assets/jquery/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/jquery/jquery.validate.min.js')}}"></script>
    <script src="{{ asset('assets/jquery/jquery.modal.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/scripts.js') }}"></script>
    <script src="{{ asset('assets/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/url.js') }}"></script>

    <!-- Filepond plugins -->
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-encode/dist/filepond-plugin-file-encode.js"></script>
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>

    <!-- Chosen + jQuery Mask -->
    <script src="{{ asset('js/chosen.jquery.js') }}"></script>
    <script src="{{ asset('assets/jquery/jquery.mask.min.js') }}"></script>

{{--    <script src="//unpkg.com/alpinejs" defer></script>--}}

    @livewireScripts
    @stack("scripts")
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

        window.Laravel = {
            assetUrl: "{{ rtrim(asset(''), '/') }}/"
        };
    </script>
    <script src="{{ asset('js/helper/helpers.js') }}"></script>

    <script>
        document.addEventListener('livewire:load', () => {
            // função que inicializa todos os inputs .filepond-input que ainda não tenham sido inicializados
            function initAllFilePonds(root = document) {
                if (typeof FilePond === 'undefined') {
                    console.warn('FilePond não está carregado');
                    return;
                }

                root.querySelectorAll('.filepond-input').forEach(input => {
                    // evita inicializar duas vezes
                    if (input.dataset.pondInitialized === '1') return;

                    // obtém wrapper / meta
                    const wrapper = input.closest('.filepond-wrapper');
                    const variacaoKey = wrapper ? wrapper.dataset.variacaoKey || '' : '';
                    const context = wrapper ? wrapper.dataset.context || 'produto' : 'produto';

                    // pega CSRF (se precisar nas chamadas server)
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

                    // cria o pond (ajuste plugins / options conforme necessário)
                    const pond = FilePond.create(input, {
                        allowMultiple: input.hasAttribute('multiple'),
                        labelIdle: 'Arraste imagens ou <span class="filepond--label-action">clique para escolher</span>',
                    });

                    // store ref para uso futuro
                    input._pond = pond;
                    input.dataset.pondInitialized = '1';

                    // server handlers — ajuste URL conforme sua rota
                    pond.setOptions({
                        server: {
                            process: {
                                url: '/admin/upload/tmp-upload',
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': csrfToken },
                                onload: (serverId) => {
                                    // serverId é o retorno do seu backend (folder ou id)
                                    // notifica o Livewire do componente pai/filho (usa o closest wire:id para encontrar o componente)
                                    const host = input.closest('[wire\\:id]');
                                    if (host) {
                                        const compId = host.getAttribute('wire:id');
                                        const lw = compId ? Livewire.find(compId) : null;
                                        if (lw) {
                                            if (context === 'produto') {
                                                lw.call('setPastasImagensProduto', [serverId]);
                                            } else {
                                                lw.call('setPastasImagensVariacao', { variacao_key: variacaoKey, pastas: [serverId] });
                                            }
                                        }
                                    }
                                    return serverId;
                                }
                            },
                            revert: (serverId, load, error) => {
                                // pedir exclusão temporária ao backend
                                fetch('/admin/upload/tmp-delete', {
                                    method: 'DELETE',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken
                                    },
                                    body: JSON.stringify({ folder: serverId })
                                }).then(res => {
                                    if (!res.ok) throw new Error('Erro ao excluir imagem temporária');
                                    load();
                                }).catch(err => {
                                    console.error(err);
                                    error('Falha na comunicação com o servidor');
                                });
                            }
                        }
                    });

                    // se quiser pré-carregar arquivos locais usando data vindo do backend, você pode setar pond.files aqui
                    // ex: if (window._initialFiles && window._initialFiles[variacaoKey]) pond.files = window._initialFiles[variacaoKey];
                });
            }

            // roda na primeira carga
            initAllFilePonds(document);

            // re-inicializa após qualquer patch do Livewire (quando novos inputs forem inseridos)
            Livewire.hook('message.processed', (message, component) => {
                initAllFilePonds(document);
            });

            // caso você queira disparar manualmente após adicionar variação (opcional)
            window.addEventListener('variacao-adicionada', () => {
                // small delay para garantir DOM atualizado
                setTimeout(() => initAllFilePonds(document), 40);
            });
        });
    </script>

</body>
</html>
