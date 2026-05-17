@extends('layouts.auth')

@section('title', 'Registro')

@section('content')
    
    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card mt-4">
                    <div id="titulo_registro" class="card-header">Registro</div>
                    <div class="card-body">
                        <form action="{{ route('registrar') }}" method="POST">
                            @csrf
                            <label for="name">Nombre</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                            <label for="institucion">Institución</label>
                            <input type="text" name="institucion" id="institucion" class="form-control" required>
                            <label for="email">Correo</label>
                            <input type="email" name="email" id="email" class="form-control">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                            <label for="password_confirmation">Confirmar Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                            <button class="btn btn-outline-primary mt-4">Registrarse</button>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary mt-4">Ingresa aquí</a>
                            @if ($errors->any())
                                <div class="alert alert-danger mt-2">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    
    @endsection