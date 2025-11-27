<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Diagnostico;

class DiagnosticoSeeder extends Seeder
{
    public function run()
    {
        Diagnostico::create([
            'descripDiag' => 'Fractura de fémur',
        ]);

        Diagnostico::create([
            'descripDiag' => 'Neumonía',
        ]);

        Diagnostico::create([
            'descripDiag' => 'Apendicitis aguda',
        ]);

        Diagnostico::create([
            'descripDiag' => 'Hipertensión arterial',
        ]);

        Diagnostico::create([
            'descripDiag' => 'Diabetes mellitus tipo 2',
        ]);

        Diagnostico::create([
            'descripDiag' => 'Embarazo normal',
        ]);

        Diagnostico::create([
            'descripDiag' => 'Gastritis crónica',
        ]);

        Diagnostico::create([
            'descripDiag' => 'Artrosis de rodilla',
        ]);

        Diagnostico::create([
            'descripDiag' => 'Insuficiencia renal',
        ]);

        Diagnostico::create([
            'descripDiag' => 'Cálculos renales',
        ]);

        Diagnostico::create([
            'descripDiag' => 'Quiste ovárico',
        ]);

        Diagnostico::create([
            'descripDiag' => 'Migraña',
        ]);

        Diagnostico::create([
            'descripDiag' => 'Asma bronquial',
        ]);

        Diagnostico::create([
            'descripDiag' => 'Lesión de ligamento cruzado',
        ]);
    }
}