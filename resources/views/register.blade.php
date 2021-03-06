<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Crear cuenta - YT-copia</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="fonts/all.css">
        @include('estilos')
    </head>
    <body>
        @include('navbar')

        <div class="row mx-0">
            <div class="col-12 col-md-6 col-lg-4 mx-auto p-3 text-center">
                <p class="h3 mx-auto">
                    Crear cuenta
                </p>
                <?php
                if (isset($mensaje) && isset($exito)) {
                    if ($exito) {
                        ?>
                        <div class="rounded bg-success text-center p-1 mb-1">{{$mensaje}}</div>
                        <?php
                    } else {
                        ?>
                        <div class="rounded bg-danger text-center p-1 mb-1 font-weight-bold">{{$mensaje}}</div>
                        <?php
                    }
                }
                ?>

                <form class="border rounded p-3" action="register" name="registerForm" method="POST">
                    {{ csrf_field() }}
                    <input class="form-control" name="username" type="text" placeholder="Nombre de usuario" required>
                    <input class="form-control mt-2" name="correo" type="email" placeholder="Correo electrónico" required>
                    <input class="form-control mt-2" name="pass" type="password" placeholder="Contraseña" required minlength="6">
                    <input type="submit" class="btn btn-dark mt-2" name="botonRegistrar" value="Crear cuenta">
                    <p class="d-block mt-2 mb-0">¿Ya tienes cuenta? <a href="{{url('login')}}">Iniciar sesión</a></p>
                  </form>


            </div>
        </div>

        @include('footer')
        @include('scripts')
    </body>
</html>
