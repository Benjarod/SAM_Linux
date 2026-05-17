@extends('layouts.main')

@section('title', 'Mail Promoción')

@section('content')

        <h3 id="titulo_EnvioCorreos" class="fw-bold">Envío de Correos</h3>
        <hr>
        <div id="form-correo">
            <form method="POST" action="{{   route('MailPromocion.sendMail') }}" enctype="multipart/form-data" >

                @csrf
                <div class="mb-4">
                    <div class="promocion-container">
                        <h4>Promoción</h4>
                        <p>Escoja la promoción a enviar:</p>
                    </div>
                    <select name="promotion_id" id="promotion" class="select_promotion" onchange="updatePreview()">
                        @foreach($promotions as $promotion)
                            <option value="{{$promotion->id}}"data-title="{{ $promotion->title }}" data-description="{{ $promotion->description }}">{{$promotion->title}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <div class="remitente-container">
                        <h4>Correo del remitente</h4>
                        <p>Ingrese el correo remitente:</p>
                    </div>
                    <input type="email" name="desde" id="desde" class="input-correo">
                </div>
                <div class="mb-4">
                    <div class="asunto-container">
                        <h4>Asunto</h4>
                        <p>Ingrese el asunto del correo a enviar:</p>
                    </div>
                    <input type="text" name="asunto" id="asunto" class="input-asunto">
                </div>

                <div class="vista_previa-container">
                    <h4>Vista previa</h4>
                    <p>Vista previa del mail a enviar:</p>
                </div>
                <br>
                <div class="outer-container">
                    <div class="container-promotion" id="promotion-container">
                        <h2 id="preview-title">Señor (a)</h2>
                        <h2 id="preview-title">{{ $randomPerson->nombres }}</h2>
                        <h2 id="preview-title">Por medio del presente le queremos informar lo siguiente:</h2>
                        <div id="preview-description">
                            <!--contenido promocion-->
                        </div>
                    </div>
                </div>
                <hr>
                <div>
                    <button type="submit"  class="btn-enviar-promocion">Enviar Promoción</button>
                </div>
                <br>
        </div>
        
        {{-- este es el script para que se muestre la vista previa de la promoción seleccionada --}}
        <script>
            function updatePreview() {
                const select = document.getElementById('promotion');
                const selectedOption = select.options[select.selectedIndex];
                
                const description = selectedOption.getAttribute('data-description');
                
                document.getElementById('preview-description').innerHTML = description;
            }
        
            // Inicializar vista previa con la primera opción al cargar la página
            document.addEventListener('DOMContentLoaded', updatePreview);
        </script>
@endsection