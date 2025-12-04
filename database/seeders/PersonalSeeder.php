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

        // Supervisor (ID: 1)
        PersonalSalud::create([
            'usuarioPer' => 'supervisor',
            'clavePer' => Hash::make('12345678'),
            'nomPer' => 'María',
            'paternoPer' => 'González',
            'maternoPer' => 'López',
            'estado' => 'activo',
            'codRol' => $supervisor->codRol,
        ]);

        // Personal de Imagen 1 (ID: 2)
        PersonalSalud::create([
            'usuarioPer' => 'personal1',
            'clavePer' => Hash::make('12345678'),
            'nomPer' => 'Ana',
            'paternoPer' => 'Martínez',
            'maternoPer' => 'Vargas',
            'estado' => 'activo',
            'codRol' => $personalImagen->codRol,
        ]);

        // Personal de Imagen 2 (ID: 3)
        PersonalSalud::create([
            'usuarioPer' => 'personal2',
            'clavePer' => Hash::make('12345678'),
            'nomPer' => 'Carmen',
            'paternoPer' => 'López',
            'maternoPer' => 'Sánchez',
            'estado' => 'activo',
            'codRol' => $personalImagen->codRol,
        ]);

        // Enfermera (ID: 4)
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