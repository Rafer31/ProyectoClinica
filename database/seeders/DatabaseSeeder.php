<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Orden de ejecución de seeders
        $this->call([
            // 1. Tablas independientes (sin FK)
            PersonalSeeder::class,
            TipoEstudioSeeder::class, // Ya lo tienes
            PacienteSeeder::class,
            MedicoSeeder::class,
            DiagnosticoSeeder::class,
            ConsultorioSeeder::class,
            // RequisitoSeeder::class, // Ya lo tienes
            
            // 2. Tablas con FK simples
            CronogramaAtencionSeeder::class,
            
            // 3. Tablas con múltiples FK
            ServicioSeeder::class,
            
            // 4. Tablas intermedias
            ServicioDiagnosticoSeeder::class,
            // TipoEstudioRequisitoSeeder::class, // Si lo tienes
        ]);
    }
}