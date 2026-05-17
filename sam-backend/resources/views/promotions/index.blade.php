@extends('layouts.main')

@section('title', 'Plantillas Promocionales')

@section('content')
    <div class="container p-4">
        @if(session('error'))
            <div class="alert alert-danger mt-2">
                {{ session('error') }}
            </div>
        @endif
        <div>
            <h3 class="fw-bold" id="titulo_plantillas_promociones">Plantillas Promocionales</h3>
        </div>
        <hr>
        <div class="crear-plantilla-container">
            <p class="p-index-promociones">Crear una nueva promoción:</p>
            <a id="btn_crear_plantilla" href="/crear" class="btn ms-3 mb-3">Crear Plantilla</a>
        </div>
        <hr>
        <br>
        <p class="p-index-promociones">Promociones creadas:</p>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Titulo</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Acción</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($promotions as $promotion)
                <tr>
                    <td>{{ $promotion->title }}</td>
                    <td>{{ $promotion->updated_at }}</td>
                    <td>
                        <a href="show/{{ $promotion->id }}" id="btn_ver" class="btn">Ver</a>
                        <a href="editar/{{ $promotion->id }}" id="btn_editar" class="btn">Editar</a>
                        <a href="delete/{{ $promotion->id }}" id="btn_eliminar" class="btn">Eliminar</a>
                    </td>
                </tr>
                @endforeach
                
            </tbody>

        </table>
    </div>
    
    @endsection