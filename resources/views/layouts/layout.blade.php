<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KN Cosméticos - Admin</title>
    <link rel="stylesheet"  type="text/css" href="{{URL::asset('assets/bootstrap/css/bootstrap.css')}}">
    <link href="{{URL::asset('css/dashboard/styles.css')}}" rel="stylesheet" />
    <script src="{{URL::asset('assets/jquery/jquery-3.6.0.min.js')}}"></script>
    <script src="{{URL::asset('assets/jquery/jquery.modal.min.js')}}"></script>
{{--    <script src="{{URL::asset('assets/font-awesome/all.min.js')}}"></script>--}}
    <script src="{{URL::asset('assets/bootstrap/js/popper.min.js')}}"></script>
    <script src="{{URL::asset('assets/bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{URL::asset('js/bootstrap/bootstrap.bundle.min.js')}}"></script>
    <script src="{{URL::asset('assets/dashboard/js/scripts.js')}}"></script>
    <script src="{{URL::asset('assets/sweetalert2/dist/sweetalert2.min.js')}}"></script>

    <link href="{{URL::asset('assets/sweetalert2/dist/sweetalert2.min.css')}}" rel="stylesheet" />
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet"/>
    <link href="{{URL::asset('assets/fontawesome-free-6.7.2-web/css/all.min.css')}}" rel="stylesheet" />

    <script src="{{URL::asset('js/url.js')}}"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-encode/dist/filepond-plugin-file-encode.js"></script>

    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>

    <link href="{{ asset('css/chosen.css') }}" rel="stylesheet" type="text/css">
    <script src="{{URL::asset('js/chosen.jquery.js')}}"></script>


    @stack("styles")

</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="{{route('admin.home')}}">Administração</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <!--li><a class="dropdown-item" href="#!">Settings</a></li>
                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                    <li><hr class="dropdown-divider" /></li-->
                    <li><a class="dropdown-item" href="{{route('admin.logout')}}">Logout</a></li>
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

    @stack("scripts")
</body>
</html>
