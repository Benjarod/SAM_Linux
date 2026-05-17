@extends('layouts.auth')

@section('title', 'Bienvenidos')

@section('content')
    
    <div class="container">
        <div class="row">
            <div class="col-md-7 mx-auto">
                <div class="card mt-4">
                    <div id="titulo-home" class="card-header">Bienvenidos</div>
                    <div class="card-body">
                        <img src="{{ asset('images/logosam.png') }}" alt="SAM" class="mx-auto d-block">
                    </div>
                </div>
                <p id="p-home" class="text-center mt-5">Desarrollado por Singa S.A - 2024</p>
            </div>
        </div>
    </div>
    
    @endsection