// database/migrations/2025_06_17_000001_create_oficinas_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('oficinas', function (Blueprint $table) {
            $table->id();                                  // BIGINT PRIMARY KEY
            $table->string('name');                        // TEXT NOT NULL
            $table->text('descripcion')->nullable();       // TEXT NULLABLE
            // Self-reference FK: oficinas.oficinas_id -> oficinas.id
            $table->foreignId('oficinas_id')
                ->nullable()
                ->constrained('oficinas')
                ->nullOnDelete();
            $table->smallInteger('estado')->default(1);    // SMALLINT NOT NULL DEFAULT 1
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oficinas');
    }
};
