<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicioDiagnosticoSeeder extends Seeder
{
    public function run()
    {
        // Servicio 1 - Fractura de fémur
        DB::table('servicio_diagnostico')->insert([
            'codServ' => 1,
            'codDiag' => 1,
            'tipo' => 'sol',
        ]);

        DB::table('servicio_diagnostico')->insert([
            'codServ' => 2,
            'codDiag' => 2,
            'tipo' => 'eco',
        ]);

        // Servicio 3 - Apendicitis
        DB::table('servicio_diagnostico')->insert([
            'codServ' => 3,
            'codDiag' => 3,
            'tipo' => 'sol',
        ]);

        // Servicio 4 - Hipertensión
        DB::table('servicio_diagnostico')->insert([
            'codServ' => 4,
            'codDiag' => 4,
            'tipo' => 'sol',
        ]);

        // Servicio 5 - Diabetes
        DB::table('servicio_diagnostico')->insert([
            'codServ' => 5,
            'codDiag' => 5,
            'tipo' => 'sol',
        ]);

        // Servicio 6 - Embarazo
        DB::table('servicio_diagnostico')->insert([
            'codServ' => 6,
            'codDiag' => 6,
            'tipo' => 'eco',
        ]);

        // Servicio 7 - Gastritis
        DB::table('servicio_diagnostico')->insert([
            'codServ' => 7,
            'codDiag' => 7,
            'tipo' => 'sol',
        ]);

        // Servicio 8 - Artrosis
        DB::table('servicio_diagnostico')->insert([
            'codServ' => 8,
            'codDiag' => 8,
            'tipo' => 'sol',
        ]);


        // Servicio 9 - Insuficiencia renal
        DB::table('servicio_diagnostico')->insert([
            'codServ' => 9,
            'codDiag' => 9,
            'tipo' => 'sol',
        ]);

        // Servicio 10 - Cálculos renales
        DB::table('servicio_diagnostico')->insert([
            'codServ' => 10,
            'codDiag' => 10,
            'tipo' => 'sol',
        ]);

        // Servicio 11 - Quiste ovárico
        DB::table('servicio_diagnostico')->insert([
            'codServ' => 11,
            'codDiag' => 11,
            'tipo' => 'sol',
        ]);

        // Servicio 12 - Fractura (emergencia)
        DB::table('servicio_diagnostico')->insert([
            'codServ' => 12,
            'codDiag' => 1,
            'tipo' => 'sol',
        ]);

    }
}