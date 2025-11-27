<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medico;

class MedicoSeeder extends Seeder
{
    public function run()
    {
        Medico::create([
            'nomMed' => 'Dr. José',
            'paternoMed' => 'Rodríguez',
            'tipoMed' => 'Interno',
            'estado' => 'Activo',
        ]);

        Medico::create([
            'nomMed' => 'Dra. Laura',
            'paternoMed' => 'Martínez',
            'tipoMed' => 'Externo',
            'estado' => 'Activo',
        ]);

        Medico::create([
            'nomMed' => 'Dr. Roberto',
            'paternoMed' => 'Sánchez',
            'tipoMed' => 'Interno',
            'estado' => 'Activo',
        ]);

        Medico::create([
            'nomMed' => 'Dra. Patricia',
            'paternoMed' => 'Flores',
            'tipoMed' => 'Externo',
            'estado' => 'Activo',
        ]);

        Medico::create([
            'nomMed' => 'Dr. Manuel',
            'paternoMed' => 'Vargas',
            'tipoMed' => 'Externo',
            'estado' => 'Activo',
        ]);

        Medico::create([
            'nomMed' => 'Dra. Elena',
            'paternoMed' => 'Guzmán',
            'tipoMed' => 'Interno',
            'estado' => 'Activo',
        ]);

        Medico::create([
            'nomMed' => 'Dr. Fernando',
            'paternoMed' => 'Castro',
            'tipoMed' => 'Externo',
            'estado' => 'Activo',
        ]);

        Medico::create([
            'nomMed' => 'Dra. Mónica',
            'paternoMed' => 'Herrera',
            'tipoMed' => 'Externo',
            'estado' => 'Activo',
        ]);

        Medico::create([
            'nomMed' => 'Dr. Diego',
            'paternoMed' => 'Ortiz',
            'tipoMed' => 'Interno',
            'estado' => 'Inactivo',
        ]);

        Medico::create([
            'nomMed' => 'Dra. Sandra',
            'paternoMed' => 'Mendoza',
            'tipoMed' => 'Externo',
            'estado' => 'Activo',
        ]);

        Medico::create([
            'nomMed' => 'Dr. Andrés',
            'paternoMed' => 'Paz',
            'tipoMed' => 'Interno',
            'estado' => 'Activo',
        ]);

        Medico::create([
            'nomMed' => 'Dra. Julia',
            'paternoMed' => 'Romero',
            'tipoMed' => 'Externo',
            'estado' => 'Activo',
        ]);
    }
}