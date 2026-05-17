<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destinatarios_temporales', function (Blueprint $table) {
            $table->id();
            $table->string('rut')->nullable();
            $table->string('nombres')->nullable();
            $table->string('ap_pat')->nullable();
            $table->string('ap_mat')->nullable();
            $table->string('sexo')->nullable();
            $table->string('ficha_clinica')->nullable();
            $table->string('domicilio')->nullable();
            $table->string('fono')->nullable();
            $table->string('celu')->nullable();
            $table->string('email')->nullable();
            $table->string('usuario_email'); // clave de privacidad por usuario
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destinatarios_temporales');
    }
};
