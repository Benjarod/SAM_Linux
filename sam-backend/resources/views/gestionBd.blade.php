<!-- resources/views/child.blade.php -->

@extends('layouts.main')

@section('title', 'Gestion BD')

@section('content')
    <div>
        @if(session('error'))
            <div class="alert alert-danger mt-2">
                {{ session('error') }}
            </div>
        @endif
        <h3 class="fw-bold" id="titulo_GBD">Gestión de datos</h3>
        <hr>
        <p class="p-gestion-datos">1. Importe su archivo Excel</p>
        <form action="" method="post" enctype="multipart/form-data">
            @csrf
            <input type="file" name="excel_file" id="file">
            <input type="submit" value="Importar" id="btn-importar">
        </form>
        <hr>
        <br>
        <p class="p-gestion-datos">2.-Seleccione los campos para crear la base de datos de Promoción</p>
        <!--Se inserta la tabla creada con powergrid-->
        <livewire:people-table/>
        @livewireScripts
        <hr>
        <a  href="delete" class="btn btn-danger mb-4">Eliminar registros</a>
        <a  href="{{ route('promociones.index') }}" class="btn btn-primary mb-4">Continuar</a>

    </div>
    
    @endsection