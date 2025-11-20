<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Rol;
use App\Models\PersonalSalud;

class PersonalSeeder extends Seeder
{
    public function run()
    {
        // Crear roles
        $supervisor = Rol::create(['nombreRol' => 'Supervisor']);
        $personalImagen = Rol::create(['nombreRol' => 'PersonalImagen']);
        $enfermera = Rol::create(['nombreRol' => 'Enfermera']);
        // Crear usuario Supervisor
        PersonalSalud::create([
            'usuarioPer' => 'supervisor',
            'clavePer' => Hash::make('12345678'),
            'nomPer' => 'María',
            'paternoPer' => 'González',
            'maternoPer' => 'López',
            'estado' => 'activo',
            'codRol' => $supervisor->codRol,
        ]);

        // Crear usuario Personal de Imagen
        PersonalSalud::create([
            'usuarioPer' => 'personal1',
            'clavePer' => Hash::make('12345678'),
            'nomPer' => 'Juan',
            'paternoPer' => 'Pérez',
            'maternoPer' => 'Mamani',
            'estado' => 'activo',
            'codRol' => $personalImagen->codRol,
        ]);
        // Crear usuario Supervisor
        PersonalSalud::create([
            'usuarioPer' => 'enfermera',
            'clavePer' => Hash::make('12345678'),
            'nomPer' => 'Vanesa',
            'paternoPer' => 'Quispe',
            'maternoPer' => 'López',
            'estado' => 'activo',
            'codRol' => $enfermera->codRol,
        ]);
    }
}
