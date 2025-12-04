<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servicio;

class ServicioSeeder extends Seeder
{
    public function run()
    {
        // Servicios de días pasados - Atendidos y Entregados (Diciembre 1)
        Servicio::create([
            'fechaSol' => '2025-11-30',
            'horaSol' => '08:30:00',
            'nroServ' => 'SERV001',
            'fechaAten' => '2025-12-01',
            'horaAten' => '09:00:00',
            'fechaEnt' => '2025-12-01',
            'horaEnt' => '14:30:00',
            'tipoAseg' => 'AsegRegular',
            'nroFicha' => 'F001',
            'estado' => 'Archivado',
            'codPa' => 1,
            'codMed' => 1,
            'codTest' => 1,
            'horaCrono' => '09:00:00',
            'fechaCrono' => '2025-12-01',
        ]);

        Servicio::create([
            'fechaSol' => '2025-11-30',
            'horaSol' => '10:15:00',
            'nroServ' => 'SERV002',
            'fechaAten' => '2025-12-01',
            'horaAten' => '10:30:00',
            'fechaEnt' => '2025-12-01',
            'horaEnt' => '15:00:00',
            'tipoAseg' => 'NoAsegEmergencia',
            'nroFicha' => 'F002',
            'estado' => 'Entregado',
            'codPa' => 2,
            'codMed' => 2,
            'codTest' => 2,
            'horaCrono' => '10:30:00',
            'fechaCrono' => '2025-12-01',
        ]);

        // Servicios Diciembre 2
        Servicio::create([
            'fechaSol' => '2025-12-01',
            'horaSol' => '07:00:00',
            'nroServ' => 'SERV003',
            'fechaAten' => '2025-12-02',
            'horaAten' => '08:00:00',
            'fechaEnt' => '2025-12-02',
            'horaEnt' => '13:00:00',
            'tipoAseg' => 'AsegRegular',
            'nroFicha' => 'F003',
            'estado' => 'Archivado',
            'codPa' => 3,
            'codMed' => 3,
            'codTest' => 3,
            'horaCrono' => '08:00:00',
            'fechaCrono' => '2025-12-02',
        ]);

        Servicio::create([
            'fechaSol' => '2025-12-01',
            'horaSol' => '14:20:00',
            'nroServ' => 'SERV004',
            'fechaAten' => '2025-12-02',
            'horaAten' => '11:00:00',
            'fechaEnt' => '2025-12-02',
            'horaEnt' => '16:30:00',
            'tipoAseg' => 'AsegEmergencia',
            'nroFicha' => 'F004',
            'estado' => 'Archivado',
            'codPa' => 4,
            'codMed' => 4,
            'codTest' => 4,
            'horaCrono' => '11:00:00',
            'fechaCrono' => '2025-12-02',
        ]);

        // Servicios Diciembre 3
        Servicio::create([
            'fechaSol' => '2025-12-02',
            'horaSol' => '09:00:00',
            'nroServ' => 'SERV005',
            'fechaAten' => '2025-12-03',
            'horaAten' => '09:30:00',
            'fechaEnt' => '2025-12-03',
            'horaEnt' => '14:00:00',
            'tipoAseg' => 'AsegRegular',
            'nroFicha' => 'F005',
            'estado' => 'Entregado',
            'codPa' => 5,
            'codMed' => 5,
            'codTest' => 1,
            'horaCrono' => '09:30:00',
            'fechaCrono' => '2025-12-03',
        ]);

        Servicio::create([
            'fechaSol' => '2025-12-02',
            'horaSol' => '16:45:00',
            'nroServ' => 'SERV006',
            'fechaAten' => '2025-12-03',
            'horaAten' => '17:00:00',
            'fechaEnt' => '2025-12-03',
            'horaEnt' => '19:30:00',
            'tipoAseg' => 'AsegEmergencia',
            'nroFicha' => 'EMERG001',
            'estado' => 'Entregado',
            'codPa' => 12,
            'codMed' => 1,
            'codTest' => 1,
            'horaCrono' => '17:00:00',
            'fechaCrono' => '2025-12-03',
        ]);

        // Servicios programados para hoy Diciembre 4 - En proceso/Atendido
        Servicio::create([
            'fechaSol' => '2025-12-03',
            'horaSol' => '08:00:00',
            'nroServ' => 'SERV007',
            'fechaAten' => '2025-12-04',
            'horaAten' => '10:00:00',
            'tipoAseg' => 'NoAsegEmergencia',
            'nroFicha' => 'F006',
            'estado' => 'Atendido',
            'codPa' => 6,
            'codMed' => 6,
            'codTest' => 2,
            'horaCrono' => '10:00:00',
            'fechaCrono' => '2025-12-04',
        ]);

        Servicio::create([
            'fechaSol' => '2025-12-03',
            'horaSol' => '11:30:00',
            'nroServ' => 'SERV008',
            'fechaAten' => '2025-12-04',
            'horaAten' => '08:00:00',
            'tipoAseg' => 'AsegRegular',
            'nroFicha' => 'F007',
            'estado' => 'EnProceso',
            'codPa' => 7,
            'codMed' => 7,
            'codTest' => 3,
            'horaCrono' => '08:00:00',
            'fechaCrono' => '2025-12-04',
        ]);

        // Servicios programados para día de defensa - Diciembre 5
        Servicio::create([
            'fechaSol' => '2025-12-04',
            'horaSol' => '13:00:00',
            'nroServ' => 'SERV009',
            'fechaAten' => '2025-12-05',
            'horaAten' => '09:00:00',
            'tipoAseg' => 'NoAsegRegular',
            'nroFicha' => 'F008',
            'estado' => 'Programado',
            'codPa' => 8,
            'codMed' => 8,
            'codTest' => 4,
            'horaCrono' => '09:00:00',
            'fechaCrono' => '2025-12-05',
        ]);

        Servicio::create([
            'fechaSol' => '2025-12-04',
            'horaSol' => '15:45:00',
            'nroServ' => 'SERV010',
            'fechaAten' => '2025-12-05',
            'horaAten' => '10:00:00',
            'tipoAseg' => 'AsegRegular',
            'nroFicha' => 'F009',
            'estado' => 'Programado',
            'codPa' => 9,
            'codMed' => 9,
            'codTest' => 1,
            'horaCrono' => '10:00:00',
            'fechaCrono' => '2025-12-05',
        ]);

        // Servicios programados para días futuros - Diciembre 6-7
        Servicio::create([
            'fechaSol' => '2025-12-04',
            'horaSol' => '16:20:00',
            'nroServ' => 'SERV011',
            'fechaAten' => '2025-12-06',
            'horaAten' => '11:00:00',
            'tipoAseg' => 'AsegEmergencia',
            'nroFicha' => 'F010',
            'estado' => 'Programado',
            'codPa' => 10,
            'codMed' => 10,
            'codTest' => 2,
            'horaCrono' => '11:00:00',
            'fechaCrono' => '2025-12-06',
        ]);

        Servicio::create([
            'fechaSol' => '2025-12-05',
            'horaSol' => '10:00:00',
            'nroServ' => 'SERV012',
            'fechaAten' => '2025-12-07',
            'horaAten' => '08:30:00',
            'tipoAseg' => 'NoAsegRegular',
            'nroFicha' => 'F011',
            'estado' => 'Programado',
            'codPa' => 7,
            'codMed' => 11,
            'codTest' => 3,
            'horaCrono' => '08:30:00',
            'fechaCrono' => '2025-12-07',
        ]);

        // Servicio cancelado
        Servicio::create([
            'fechaSol' => '2025-12-02',
            'horaSol' => '10:00:00',
            'nroServ' => 'SERV013',
            'tipoAseg' => 'NoAsegRegular',
            'estado' => 'Cancelado',
            'codPa' => 11,
            'codMed' => 11,
            'codTest' => 3,
        ]);
    }
}