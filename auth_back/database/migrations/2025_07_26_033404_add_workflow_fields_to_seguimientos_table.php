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
        Schema::table('seguimientos', function (Blueprint $table) {
            // Se añade después de la columna 'oficinas_destino' para mantener el orden
            $table->string('estado_destino')->nullable()->after('oficinas_destino');
            $table->text('proveido')->nullable()->after('estado_destino');
            $table->string('numero_libro')->nullable()->after('proveido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seguimientos', function (Blueprint $table) {
            $table->dropColumn(['estado_destino', 'proveido', 'numero_libro']);
        });
    }
};
