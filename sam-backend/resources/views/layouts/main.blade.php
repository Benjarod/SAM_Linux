<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SAM - @yield('title')</title>
        <link rel="icon" href="{{asset('images/sam-icon.png')}}">
        @vite('resources/css/app.css')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>
    <body>
        @section('sidebar')
        <div class="sidebar mb-5">
            <nav id="navbar_custom" class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <a id="logo_sam_navbar" class="navbar-brand" href="#">
                        <img src="{{ asset('images/logosam.png') }}" alt="SAM" height="100dp">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse d-grid gap-3 d-md-flex justify-content-md-center me-5" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item main-nav-item">
                                <a class="nav-link text-white" href="gestionBd">Gestión de Datos</a>
                            </li>
                            <li class="nav-item main-nav-item">
                                <a class="nav-link text-white" href="PlantillasPromociones">Plantillas</a>
                            </li>
                            <li class="nav-item main-nav-item">
                                <a class="nav-link text-white" href="MailPromocion">Gestión de correos</a>
                            </li>
                            <li class="main-nav-item">
                                <a href="{{ route('logout') }}" class="nav-link text-white">Salir</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
            @show
        <main class="container">
            @yield('content')
            
        </main>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        @vite('resources/js/app.js')
    </body>
</html>