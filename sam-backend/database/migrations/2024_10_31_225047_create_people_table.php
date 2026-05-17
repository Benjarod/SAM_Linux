<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id()->autoIncrement(true);
            $table->string('rut');
            $table->string('nombres');
            $table->string('ap_pat');
            $table->string('ap_mat')->nullable();
            $table->date('fecha_nacimiento');
            $table->string('sexo');
            $table->string('ficha_clinica');
            $table->string('domicilio')->nullable();
            $table->string('fono')->nullable();
            $table->string('celu')->nullable();
            $table->string('email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
