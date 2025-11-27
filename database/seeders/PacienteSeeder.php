<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paciente;

class PacienteSeeder extends Seeder
{
    public function run()
    {
        Paciente::create([
            'nomPa' => 'Pedro',
            'paternoPa' => 'López',
            'maternoPa' => 'García',
            'estado' => 'activo',
            'fechaNac' => '1985-03-15',
            'sexo' => 'M',
            'nroHCI' => 'HCI001',
            'tipoPac' => 'SUS',
        ]);

        Paciente::create([
            'nomPa' => 'María',
            'paternoPa' => 'Fernández',
            'maternoPa' => 'Morales',
            'estado' => 'activo',
            'fechaNac' => '1990-07-22',
            'sexo' => 'F',
            'nroHCI' => 'HCI002',
            'tipoPac' => 'SINSUS',
        ]);

        Paciente::create([
            'nomPa' => 'Jorge',
            'paternoPa' => 'Quispe',
            'maternoPa' => 'Mamani',
            'estado' => 'activo',
            'fechaNac' => '1978-11-10',
            'sexo' => 'M',
            'nroHCI' => 'HCI003',
            'tipoPac' => 'SUS',
        ]);

        Paciente::create([
            'nomPa' => 'Ana',
            'paternoPa' => 'Condori',
            'maternoPa' => 'Apaza',
            'estado' => 'activo',
            'fechaNac' => '1995-05-18',
            'sexo' => 'F',
            'nroHCI' => 'HCI004',
            'tipoPac' => 'SUS',
        ]);

        Paciente::create([
            'nomPa' => 'Luis',
            'paternoPa' => 'Vargas',
            'maternoPa' => 'Flores',
            'estado' => 'activo',
            'fechaNac' => '1982-09-30',
            'sexo' => 'M',
            'nroHCI' => 'HCI005',
            'tipoPac' => 'SINSUS',
        ]);

        Paciente::create([
            'nomPa' => 'Rosa',
            'paternoPa' => 'Choque',
            'maternoPa' => 'Nina',
            'estado' => 'activo',
            'fechaNac' => '1988-12-05',
            'sexo' => 'F',
            'nroHCI' => 'HCI006',
            'tipoPac' => 'SUS',
        ]);

        Paciente::create([
            'nomPa' => 'Ricardo',
            'paternoPa' => 'Huanca',
            'maternoPa' => 'Ticona',
            'estado' => 'activo',
            'fechaNac' => '1975-02-14',
            'sexo' => 'M',
            'nroHCI' => 'HCI007',
            'tipoPac' => 'SINSUS',
        ]);

        Paciente::create([
            'nomPa' => 'Carmen',
            'paternoPa' => 'Pari',
            'maternoPa' => 'Calla',
            'estado' => 'activo',
            'fechaNac' => '1992-08-20',
            'sexo' => 'F',
            'nroHCI' => 'HCI008',
            'tipoPac' => 'SUS',
        ]);

        Paciente::create([
            'nomPa' => 'Alberto',
            'paternoPa' => 'Miranda',
            'maternoPa' => 'Castro',
            'estado' => 'activo',
            'fechaNac' => '1980-04-25',
            'sexo' => 'M',
            'nroHCI' => 'HCI009',
            'tipoPac' => 'SINSUS',
        ]);

        Paciente::create([
            'nomPa' => 'Lucía',
            'paternoPa' => 'Ramírez',
            'maternoPa' => 'Velasco',
            'estado' => 'activo',
            'fechaNac' => '1987-06-12',
            'sexo' => 'F',
            'nroHCI' => 'HCI010',
            'tipoPac' => 'SUS',
        ]);

        Paciente::create([
            'nomPa' => 'Fernando',
            'paternoPa' => 'Gutiérrez',
            'maternoPa' => 'Rojas',
            'estado' => 'inactivo',
            'fechaNac' => '1970-01-08',
            'sexo' => 'M',
            'nroHCI' => 'HCI011',
            'tipoPac' => 'SUS',
        ]);

        Paciente::create([
            'nomPa' => 'Silvia',
            'paternoPa' => 'Mendoza',
            'maternoPa' => 'Paredes',
            'estado' => 'activo',
            'fechaNac' => '1998-10-03',
            'sexo' => 'F',
            'nroHCI' => 'HCI012',
            'tipoPac' => 'SINSUS',
        ]);
    }
}