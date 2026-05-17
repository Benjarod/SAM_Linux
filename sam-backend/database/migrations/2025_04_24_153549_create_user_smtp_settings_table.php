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
        Schema::create('user_smtp_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('mailer'); // ej: smtp, mailgun, ses, etc.
            $table->string('host');
            $table->integer('port');
            $table->string('username');
            $table->string('password'); // lo ideal es encriptarlo
            $table->string('encryption')->nullable(); // ej: tls, ssl
            $table->string('from_address');
            $table->string('from_name');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_smtp_settings');
    }
};
