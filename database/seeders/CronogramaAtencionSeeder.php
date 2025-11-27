<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CronogramaAtencion;

class CronogramaAtencionSeeder extends Seeder
{
    public function run()
    {
        // Días pasados (con fichas atendidas)
        CronogramaAtencion::create([
            'fechaCrono' => '2025-11-20',
            'cantDispo' => 4,
            'cantFijo' => 20,
            'cantEmergencia' => 5,
            'estado' => 'completado',
            'codPer' => 2, // Personal de Imagen
        ]);

        CronogramaAtencion::create([
            'fechaCrono' => '2025-11-22',
            'cantDispo' => 6,
            'cantFijo' => 20,
            'cantEmergencia' => 5,
            'estado' => 'completado',
            'codPer' => 3, // Personal de Imagen
        ]);

        // Día de hoy
        CronogramaAtencion::create([
            'fechaCrono' => '2025-11-27',
            'cantDispo' => 4,
            'cantFijo' => 20,
            'cantEmergencia' => 5,
            'estado' => 'activo',
            'codPer' => 2,
        ]);

        // Días futuros
        CronogramaAtencion::create([
            'fechaCrono' => '2025-11-28',
            'cantDispo' => 6,
            'cantFijo' => 20,
            'cantEmergencia' => 5,
            'estado' => 'activo',
            'codPer' => 3,
        ]);

        CronogramaAtencion::create([
            'fechaCrono' => '2025-11-29',
            'cantDispo' => 13,
            'cantFijo' => 20,
            'cantEmergencia' => 5,
            'estado' => 'activo',
            'codPer' => 4,
        ]);

        CronogramaAtencion::create([
            'fechaCrono' => '2025-12-02',
            'cantDispo' => 8,
            'cantFijo' => 20,
            'cantEmergencia' => 5,
            'estado' => 'activo',
            'codPer' => 2,
        ]);

        CronogramaAtencion::create([
            'fechaCrono' => '2025-12-03',
            'cantDispo' => 12,
            'cantFijo' => 20,
            'cantEmergencia' => 5,
            'estado' => 'activo',
            'codPer' => 3,
        ]);

        CronogramaAtencion::create([
            'fechaCrono' => '2025-12-04',
            'cantDispo' => 10,
            'cantFijo' => 20,
            'cantEmergencia' => 5,
            'estado' => 'activo',
            'codPer' => 4,
        ]);

        CronogramaAtencion::create([
            'fechaCrono' => '2025-12-05',
            'cantDispo' => 15,
            'cantFijo' => 20,
            'cantEmergencia' => 5,
            'estado' => 'activo',
            'codPer' => 2,
        ]);

        CronogramaAtencion::create([
            'fechaCrono' => '2025-12-06',
            'cantDispo' => 14,
            'cantFijo' => 20,
            'cantEmergencia' => 5,
            'estado' => 'activo',
            'codPer' => 3,
        ]);
    }
}