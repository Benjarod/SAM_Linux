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
        Schema::table('user_smtp_settings', function (Blueprint $table) {
            // Añadir el campo email_from si no existe
            if (!Schema::hasColumn('user_smtp_settings', 'email_from')) {
                $table->string('email_from')->nullable()->after('encryption');
            }
            
            // Eliminar columnas antiguas si existen
            if (Schema::hasColumn('user_smtp_settings', 'from_address')) {
                $table->dropColumn('from_address');
            }
            
            if (Schema::hasColumn('user_smtp_settings', 'from_name')) {
                $table->dropColumn('from_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_smtp_settings', function (Blueprint $table) {
            // Revertir los cambios
            if (Schema::hasColumn('user_smtp_settings', 'email_from')) {
                $table->dropColumn('email_from');
            }
            
            // No recreamos from_address y from_name para evitar problemas
        });
    }
};
