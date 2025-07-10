// database/migrations/2025_06_17_000002_create_oficina_users_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('oficina_users', function (Blueprint $table) {
            $table->id();                                  // BIGINT PRIMARY KEY

            // FK a oficinas(id) con ON DELETE CASCADE
            $table->foreignId('oficinas_id')
                ->constrained('oficinas')
                ->cascadeOnDelete();

            // FK a users(id) con ON DELETE CASCADE
            $table->foreignId('users_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('fecha_inicio');                  // DATE NOT NULL
            $table->date('fecha_fin')->nullable();         // DATE NULLABLE
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oficina_users');
    }
};
