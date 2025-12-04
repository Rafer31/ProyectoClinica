<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CronogramaAtencion;

class CronogramaAtencionSeeder extends Seeder
{
    public function run()
    {
        // Días pasados (con fichas atendidas) - Diciembre 1-3
        CronogramaAtencion::create([
            'fechaCrono' => '2025-12-01',
            'cantDispo' => 5,
            'cantFijo' => 20,
            'cantEmergencia' => 5,
            'estado' => 'completado',
            'codPer' => 2, // Ana Martínez
        ]);

        CronogramaAtencion::create([
            'fechaCrono' => '2025-12-02',
            'cantDispo' => 8,
            'cantFijo' => 20,
            'cantEmergencia' => 5,
            'estado' => 'completado',
            'codPer' => 3, // Carmen López
        ]);

        CronogramaAtencion::create([
            'fechaCrono' => '2025-12-03',
            'cantDispo' => 6,
            'cantFijo' => 20,
            'cantEmergencia' => 5,
            'estado' => 'completado',
            'codPer' => 2, // Ana Martínez
        ]);

        // Día actual (preparación para defensa) - Diciembre 4
        CronogramaAtencion::create([
            'fechaCrono' => '2025-12-04',
            'cantDispo' => 10,
            'cantFijo' => 20,
            'cantEmergencia' => 5,
            'estado' => 'activo',
            'codPer' => 3, // Carmen López
        ]);

        // Día de defensa - Diciembre 5
        CronogramaAtencion::create([
            'fechaCrono' => '2025-12-05',
            'cantDispo' => 15,
            'cantFijo' => 20,
            'cantEmergencia' => 5,
            'estado' => 'activo',
            'codPer' => 2, // Ana Martínez
        ]);

        // Días futuros - Diciembre 6-7
        CronogramaAtencion::create([
            'fechaCrono' => '2025-12-06',
            'cantDispo' => 12,
            'cantFijo' => 20,
            'cantEmergencia' => 5,
            'estado' => 'activo',
            'codPer' => 3, // Carmen López
        ]);

        CronogramaAtencion::create([
            'fechaCrono' => '2025-12-07',
            'cantDispo' => 14,
            'cantFijo' => 20,
            'cantEmergencia' => 5,
            'estado' => 'activo',
            'codPer' => 2, // Ana Martínez
        ]);
    }
}