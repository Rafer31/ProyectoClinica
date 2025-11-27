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

        // Supervisor
        PersonalSalud::create([
            'usuarioPer' => 'supervisor',
            'clavePer' => Hash::make('12345678'),
            'nomPer' => 'María',
            'paternoPer' => 'González',
            'maternoPer' => 'López',
            'estado' => 'activo',
            'codRol' => $supervisor->codRol,
        ]);

        // Personal de Imagen
        PersonalSalud::create([
            'usuarioPer' => 'personal1',
            'clavePer' => Hash::make('12345678'),
            'nomPer' => 'Juan',
            'paternoPer' => 'Pérez',
            'maternoPer' => 'Mamani',
            'estado' => 'activo',
            'codRol' => $personalImagen->codRol,
        ]);

        PersonalSalud::create([
            'usuarioPer' => 'personal2',
            'clavePer' => Hash::make('12345678'),
            'nomPer' => 'Carlos',
            'paternoPer' => 'Rodríguez',
            'maternoPer' => 'Flores',
            'estado' => 'activo',
            'codRol' => $personalImagen->codRol,
        ]);

        PersonalSalud::create([
            'usuarioPer' => 'personal3',
            'clavePer' => Hash::make('12345678'),
            'nomPer' => 'Ana',
            'paternoPer' => 'Martínez',
            'maternoPer' => 'Vargas',
            'estado' => 'activo',
            'codRol' => $personalImagen->codRol,
        ]);

        PersonalSalud::create([
            'usuarioPer' => 'personal4',
            'clavePer' => Hash::make('12345678'),
            'nomPer' => 'Luis',
            'paternoPer' => 'Sánchez',
            'maternoPer' => 'Condori',
            'estado' => 'inactivo',
            'codRol' => $personalImagen->codRol,
        ]);

        // Enfermeras
        PersonalSalud::create([
            'usuarioPer' => 'enfermera',
            'clavePer' => Hash::make('12345678'),
            'nomPer' => 'Vanesa',
            'paternoPer' => 'Quispe',
            'maternoPer' => 'López',
            'estado' => 'activo',
            'codRol' => $enfermera->codRol,
        ]);

        PersonalSalud::create([
            'usuarioPer' => 'enfermera2',
            'clavePer' => Hash::make('12345678'),
            'nomPer' => 'Patricia',
            'paternoPer' => 'Huanca',
            'maternoPer' => 'Apaza',
            'estado' => 'activo',
            'codRol' => $enfermera->codRol,
        ]);

        PersonalSalud::create([
            'usuarioPer' => 'enfermera3',
            'clavePer' => Hash::make('12345678'),
            'nomPer' => 'Rosa',
            'paternoPer' => 'Choque',
            'maternoPer' => 'Nina',
            'estado' => 'activo',
            'codRol' => $enfermera->codRol,
        ]);

        PersonalSalud::create([
            'usuarioPer' => 'enfermera4',
            'clavePer' => Hash::make('12345678'),
            'nomPer' => 'Elena',
            'paternoPer' => 'Mamani',
            'maternoPer' => 'Ticona',
            'estado' => 'activo',
            'codRol' => $enfermera->codRol,
        ]);
    }
}