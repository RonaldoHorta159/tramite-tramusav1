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
        Schema::create('seguimientos', function (Blueprint $table) {
            $table->id();
            $table->string('CU', 15); // VARCHAR(15) NOT NULL UNIQUE
            // FK a oficinas(id) con ON DELETE CASCADE
            $table->foreignId('documentos_id')
                ->constrained('documentos')
                ->cascadeOnDelete();
            $table->string('asunto');
            $table->date('fecha_seguimiento'); // DATE NOT NULL
            $table->string('estado', 15); // VARCHAR(15) NOT NULL
            // FK a oficinas(id) con ON DELETE CASCADE
            $table->foreignId('oficinas_origen')
                ->constrained('oficinas')
                ->cascadeOnDelete();
            // FK a oficinas(id) con ON DELETE CASCADE
            $table->foreignId('oficinas_destino')
                ->constrained('oficinas')
                ->cascadeOnDelete();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguimientos');
    }
};
