<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SAM - Editar Plantilla</title>
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
        <div class="container p-4">
            <div class="row justify-content-md-center">
                <div class="col-md-12">
                    <div class="text-center">
                        <h3 id="titulo-editar-promocion" class="fw-bold">Editar Promoción</h3>
                    </div>
                    <form action="/update/{{ $promotion->id }}" method="post">
                        @csrf
                        <p class="p-editar-promociones" id="editar-titulo-promocion">Titulo:</p>
                        <input type="text" class="form-control" name="title" value="{{ $promotion->title }}" required>
                        <p class="p-editar-promociones" id="editar-descripcion-promocion">Descripción:</p>
                        <textarea name="description" id="description" cols="30" rows="10">{{ $promotion->description }}</textarea>
                        <button type="submit" class="btn btn-lg btn-primary">Guardar</button>
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