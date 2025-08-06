<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Llamamos a nuestros seeders en el orden correcto
        $this->call([
            OficinaSeeder::class,
            UserSeeder::class,
        ]);
    }
}
