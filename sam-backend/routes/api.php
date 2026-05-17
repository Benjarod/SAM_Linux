<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\CorreoController;
use App\Http\Controllers\DestinatarioTemporalController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\DestinatarioController; 
use App\Http\Controllers\UserSmtpSettingController;
use App\Http\Controllers\ImagenController;

// Ruta de prueba de conexión
Route::get('/ping', fn () => response()->json(['message' => 'API funcionando correctamente']));

// ✅ Ruta de subida de imágenes fuera del middleware de autenticación
Route::post('/upload-imagen', [ImagenController::class, 'subir']);

// Autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/perfil', fn (Request $request) => response()->json([
        'message' => 'Acceso autorizado.',
        'user' => $request->user()
    ]));

    Route::post('/personas', [PersonaController::class, 'guardarVarias']);
    Route::get('/people', [PersonaController::class, 'obtenerPersonas']);
    Route::delete('/people', [PersonaController::class, 'eliminarTodas']);

    Route::post('/importar-personas', [ImportController::class, 'import']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/enviar-correos', [CorreoController::class, 'enviar']);

    Route::post('/destinatarios-seleccionados', [DestinatarioTemporalController::class, 'store']);
    Route::get('/destinatarios-temporales/verificar', [DestinatarioTemporalController::class, 'verificar']);

    // ✅ Plantillas
    Route::post('/plantillas', [PromotionController::class, 'guardarDesdeApi']);
    Route::get('/plantillas', [PromotionController::class, 'obtenerPorUsuario']);
    Route::get('/plantillas/{id}', [PromotionController::class, 'show']);
    Route::put('/plantillas/{id}', [PromotionController::class, 'update']);
    Route::delete('/plantillas/{id}', [PromotionController::class, 'destroy']);

    // Cantidad de destinatarios
    Route::get('/cantidad-destinatarios/{email}', [CorreoController::class, 'contarDestinatarios']);
    Route::get('/cantidad-destinatarios-seleccionados/{email}', [DestinatarioController::class, 'contarSeleccionados']);

    // Configuración SMTP personalizada por usuario
    Route::post('/smtp-settings', [UserSmtpSettingController::class, 'store']);
    Route::middleware('auth:sanctum')->get('/smtp-settings', [UserSmtpSettingController::class, 'show']);

    //Eliiminar destinatarios temporales por usuario
    Route::post('/eliminar-destinatarios-temporales', [DestinatarioTemporalController::class, 'eliminarPorUsuario']);

});
