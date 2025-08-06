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
            // Aquí añadimos la columna y la clave foránea
            $table->foreignId('oficina_id')
                ->nullable()
                ->after('rol') // Opcional: la coloca después de la columna 'rol'
                ->constrained('oficinas')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Esto es para poder revertir la migración si es necesario
            $table->dropForeign(['oficina_id']);
            $table->dropColumn('oficina_id');
        });
    }
};
