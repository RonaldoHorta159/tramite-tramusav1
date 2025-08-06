<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1) Añadir DNI único

            // 2) Añadir estado (smallint, default=1)
            $table->smallInteger('estado')->default(1)->after('password');

            // 3) Eliminar columnas que ya no usaremos
            $table->dropColumn(['email_verified_at', 'remember_token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1) Volver a crear las columnas eliminadas
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->rememberToken()->after('password');

            // 2) Eliminar las columnas añadidas
            $table->dropColumn(['dni', 'estado']);
        });
    }
};
