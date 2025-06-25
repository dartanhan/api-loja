<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <script src="{{asset('assets/jquery/jquery-3.3.1.min.js')}}"></script>
        <link href="{{asset('assets/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" id="bootstrap-css">
        <script src="{{asset('assets/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <link href="{{asset('assets/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" id="bootstrap-css">
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <link rel="stylesheet"  type="text/css" href="{{URL::asset('css/custom-input-float.css')}}">

        <script>
            function onSubmit(token) {
                document.getElementById("login-form").submit();
            }
        </script>
    </head>
    <body>
    <section class="">
            <div class="vh-100">
                <div class="container-fluid h-custom">
                    <div class="row d-flex justify-content-center align-items-center h-100">
                        <div class="col-md-9 col-lg-6 col-xl-5">
                            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
                                 class="img-fluid" alt="Sample image">
                        </div>
                        <div class="card col-md-8 col-lg-6 col-xl-4 offset-xl-1 bg-opacity-10">
                            <form role="form" action="{{route('admin.login.do')}}" method="post" id="login-form" name="login-form">
                                @csrf
                                <div class="divider d-flex align-items-center my-4">
                                    <p class="text-center fw-bold mx-3 mb-0">
                                        <h3> <i class="fa fa-lock text-muted"></i> Acesso Restrito</h3>
                                    </p>
                                </div>

                                <!-- Email input -->
                                <div class="form-group border-lable-flt ">
                                      <input  id="login" type="text" name="login" class="form-control" placeholder="Login" required autofocus>
                                      <label for="label-name">Login</label>
                                </div>

                                <!-- Password input -->
                                <div class="form-group border-lable-flt">
                                       <input  id="password" type="password" name="password" placeholder="Senha" class="form-control" required autofocus>
                                        <label for="label-password">Senha</label>
                                </div>

                                <div class="form-group col-lg-14 mb-4">
                                    <button class="btn bg-primary btn-block py-12 g-recaptcha" data-sitekey="{{ env('DATA_SITEKEY') }}" data-callback='onSubmit'>
                                        <span class="font-weight-bold"><i class="fa fa-sign-in"></i> Entrar</span>
                                    </button>
                                </div>
                                <!-- Divider Text -->
                                <div class="form-group col-lg-8 mx-auto d-flex align-items-center my-4">
                                    @if($errors->all())
                                        @foreach($errors->all() as $error)
                                            <div class="alert alert-danger" role="alert">
                                                {{ $error }}
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-primary">
                    <!-- Copyright -->
                    <div class="text-white mb-3 mb-md-0">
                        Copyright © {{ date('Y') }} . All rights reserved.
                    </div>
                    <!-- Copyright -->
                </div>
            </div>
        </section>
    </body>
</html>
