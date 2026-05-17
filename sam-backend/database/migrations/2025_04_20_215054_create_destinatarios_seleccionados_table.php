<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('destinatarios_seleccionados', function (Blueprint $table) {
            $table->id();
            $table->string('usuario_email'); // quién seleccionó
            $table->string('rut'); // RUT del destinatario seleccionado
            $table->string('email'); // correo del destinatario
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destinatarios_seleccionados');
    }
};
