<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\User;     // Importa el modelo User
use App\Models\Oficina;   // Importa el modelo Oficina
use Illuminate\Support\Facades\Hash; // Importa Hash para las contraseñas

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- INICIO DE CAMBIOS ---
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        // Obtenemos las oficinas que creamos en el seeder anterior
        $mesaDePartes = Oficina::where('name', 'Mesa de Partes')->first();
        $gerencia = Oficina::where('name', 'Gerencia General')->first();
        $contabilidad = Oficina::where('name', 'Oficina de Contabilidad')->first();
        $rrhh = Oficina::where('name', 'Oficina de Recursos Humanos')->first();

        // --- Creamos los usuarios de ejemplo ---

        User::create([
            'name' => 'Juan Perez (Mesa de Partes)',
            'dni' => '11111111',
            'email' => 'juan.perez@example.com',
            'password' => Hash::make('password'), // Contraseña para todos: "password"
            'oficina_id' => $mesaDePartes->id,
        ]);

        User::create([
            'name' => 'Maria Rodriguez (Gerencia)',
            'dni' => '22222222',
            'email' => 'maria.rodriguez@example.com',
            'password' => Hash::make('password'),
            'oficina_id' => $gerencia->id,
        ]);

        User::create([
            'name' => 'Carlos Gomez (Contabilidad)',
            'dni' => '33333333',
            'email' => 'carlos.gomez@example.com',
            'password' => Hash::make('password'),
            'oficina_id' => $contabilidad->id,
        ]);

        User::create([
            'name' => 'Ana Torres (Recursos Humanos)',
            'dni' => '44444444',
            'email' => 'ana.torres@example.com',
            'password' => Hash::make('password'),
            'oficina_id' => $rrhh->id,
        ]);
    }
}
