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
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_documento');
            $table->string('dni', 20)->unique(); // VARCHAR(20) NOT NULL UNIQUE
            $table->string('nombre', 100); // VARCHAR(100) NOT NULL
            $table->string('tipo_documento', 50); // VARCHAR(50) NOT NULL
            $table->date('fecha_emision'); // DATE NOT NULL
            $table->integer('numero_folios');
            $table->string('file_archivo', 255)->nullable(); // VARCHAR(255) NULLABLE
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
