<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SAM - Crear Plantilla</title>
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
        <div class="container p-4 mb-7">
            <div class="row justify-content-md-center">
                <div class="col-md-12">
                    <div class="text-center">
                        <h3 id="titulo-CrearPlantilla" class="fw-bold">Crear Plantilla</h3>
                    </div>
                    <hr>
                    <form action="/post" method="post">
                        @csrf
                        <p class="p-crear-promociones">Nombre de la promoción:</p>
                        <input type="text" class="form-control" name="title" required>
                        <p class="p-crear-promociones" id="descripcion_promocion">Cree la promoción:</p>
                        <textarea name="description" id="description" cols="30" rows="10" required></textarea>
                        <button type="submit" class="btn btn-lg btn-primary">Crear</button>
                        <input type="button" value="Cancelar" onclick="javascript:history.back()" class="btn btn-danger">
                    </form>
                </div>
            </div>
        </div>
        {{-- script para desplegar la herramienta summernote --}}
        <script>
            $('#description').summernote({
                placeholder: 'descripción...',
                tabsize:2,
                height:300
            });
        </script>
        <br>
    </body>
</html>