<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SAM - Ver Plantilla</title>
        <!-- include libraries(jQuery, bootstrap) -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

        <!-- include summernote css/js -->
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script> 
        @vite('resources/css/app.css')  
    </head>
    <body>
        <div class="container">
            <div>
                <div id="container-nombre-promocion">
                    <div>
                        <h3 id="h1-nombre-plantilla">Nombre plantilla: <span>{{ $promotion->title }}</span></h3>
                    </div>
                    <h3 id="titulo-promocion">Promoción:</h3>
                    <div id="contenido-promocion">
                        {!! $promotion->description !!}
                    </div>
                    <hr>
                    <input type="button" value="Volver" onclick="javascript:history.back()" class="btn btn-primary">
                </div>
            </div>
        </div>
        <br>
    </body>
</html>