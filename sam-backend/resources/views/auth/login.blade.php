@extends('layouts.auth')

@section('title', 'Acceso')

@section('content')
    
    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card mt-4">
                    <div id="titulo_acceso" class="card-header">Acceder</div>
                    <div class="card-body">
                        <form action="{{ route('acceder') }}" method="POST">
                            @csrf
                            @method('POST')

                            <label for="email">Correo</label>
                            <input type="email" name="email" id="email" class="form-control mb-3">

                            <label for="password">Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control mb-4">

                            <button class="btn btn-outline-primary mt-4">Ingresar</button>
                            <a href="{{ route('registro') }}" class="btn btn-outline-secondary mt-4">Registrate aquí</a>

                            <!-- ⚠️ Advertencia para múltiples sesiones -->
                            <div class="alert alert-warning mt-4" role="alert">
                                <strong>Importante:</strong> Si deseas ingresar con más de una cuenta al mismo tiempo, utiliza una ventana privada (Ctrl+Shift+N) o un navegador distinto para evitar conflictos de sesión.
                            </div>
                        </form>

                        @if(session('error'))
                        <div class="alert alert-danger mt-2">
                            {{ session('error') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    window.onload = function () {
        // Forzar que el formulario actualice el token CSRF al enviar
        const form = document.querySelector('form');
        form.addEventListener('submit', function (e) {
            const token = document.querySelector('input[name="_token"]');
            if (!token || token.value.length === 0) {
                e.preventDefault();
                alert('El token CSRF ha expirado. Por favor, actualiza la página.');
                location.reload();
            }
        });
    };
</script>
@endsection
