<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Oficina;
use Illuminate\Support\Facades\Schema; // <-- AÑADE ESTA LÍNEA

class OficinaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- INICIO DE CAMBIOS ---
        Schema::disableForeignKeyConstraints(); // Deshabilita las revisiones
        Oficina::truncate(); // Ahora sí podemos truncar la tabla
        Schema::enableForeignKeyConstraints();  // Rehabilita las revisiones
        // --- FIN DE CAMBIOS ---

        Oficina::create(['name' => 'Mesa de Partes']);
        Oficina::create(['name' => 'Gerencia General']);
        Oficina::create(['name' => 'Oficina de Contabilidad']);
        Oficina::create(['name' => 'Oficina de Recursos Humanos']);
        Oficina::create(['name' => 'Unidad de Logística']);
    }
}
