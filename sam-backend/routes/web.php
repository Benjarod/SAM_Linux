<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GestionBDController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\PromotionMailController;
use Illuminate\Support\Facades\Route;

Route::middleware("guest")->group(function () {
    #vista de inicio
    Route::get('/', [AuthController::class, 'home'])->name('home');
    #Login y Registro
    Route::get('/registro', [AuthController::class, 'registro'])->name('registro');
    Route::post('/registrar', [AuthController::class, 'registrar'])->name('registrar');

    Route::get('/acceso', [AuthController::class, 'acceso'])->name('login');
    Route::post('/acceder', [AuthController::class, 'acceder'])->name('acceder');
});
Route::middleware("auth")->group(function () {
    #salir del sistema
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    #Gestion base de datos
    Route::get('gestionBd', [GestionBDController::class,'index'])->name('gestionBd');

    Route::post('gestionBd', [GestionBDController::class,'import']);

    Route::get('delete',[GestionBDController::class,'destroy']);

    Route::get('save', [GestionBDController::class, 'save']);

    #Promociones
    Route::get('PlantillasPromociones', [PromotionController::class,'index'])->name('promociones.index');

    Route::get('crear', [PromotionController::class,'crear']);

    Route::post('post', [PromotionController::class,'guardar']);

    Route::get('show/{id}', [PromotionController::class,'show']);

    Route::get('editar/{id}', [PromotionController::class,'editar']);

    Route::post('update/{id}', [PromotionController::class,'update']);

    Route::get('delete/{id}', [PromotionController::class,'destroy']);

    #Mails promociones
    Route::get('MailPromocion', [PromotionMailController::class,'promocion']);

    Route::post('MailPromocion', [PromotionMailController::class,'sendMail'])->name('MailPromocion.sendMail');
});
