<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servicio;

class ServicioSeeder extends Seeder
{
    public function run()
    {
        // Servicios de días pasados - Atendidos y Entregados
        Servicio::create([
            'fechaSol' => '2025-11-19',
            'horaSol' => '08:30:00',
            'nroServ' => 'SERV001',
            'fechaAten' => '2025-11-20',
            'horaAten' => '09:00:00',
            'fechaEnt' => '2025-11-20',
            'horaEnt' => '14:30:00',
            'tipoAseg' => 'AsegRegular',
            'nroFicha' => 'F001',
            'estado' => 'Entregado',
            'codPa' => 1,
            'codMed' => 1,
            'codTest' => 1,
            'horaCrono' => '09:00:00',
            'fechaCrono' => '2025-11-20',
        ]);

        Servicio::create([
            'fechaSol' => '2025-11-19',
            'horaSol' => '10:15:00',
            'nroServ' => 'SERV002',
            'fechaAten' => '2025-11-20',
            'horaAten' => '10:30:00',
            'fechaEnt' => '2025-11-20',
            'horaEnt' => '15:00:00',
            'tipoAseg' => 'NoAsegEmergencia',
            'nroFicha' => 'F002',
            'estado' => 'Entregado',
            'codPa' => 2,
            'codMed' => 2,
            'codTest' => 2,
            'horaCrono' => '10:30:00',
            'fechaCrono' => '2025-11-20',
        ]);

        Servicio::create([
            'fechaSol' => '2025-11-20',
            'horaSol' => '07:00:00',
            'nroServ' => 'SERV003',
            'fechaAten' => '2025-11-22',
            'horaAten' => '08:00:00',
            'fechaEnt' => '2025-11-22',
            'horaEnt' => '13:00:00',
            'tipoAseg' => 'AsegRegular',
            'nroFicha' => 'F003',
            'estado' => 'Entregado',
            'codPa' => 3,
            'codMed' => 3,
            'codTest' => 3,
            'horaCrono' => '08:00:00',
            'fechaCrono' => '2025-11-22',
        ]);

        Servicio::create([
            'fechaSol' => '2025-11-21',
            'horaSol' => '14:20:00',
            'nroServ' => 'SERV004',
            'fechaAten' => '2025-11-22',
            'horaAten' => '11:00:00',
            'fechaEnt' => '2025-11-22',
            'horaEnt' => '16:30:00',
            'tipoAseg' => 'AsegEmergencia',
            'nroFicha' => 'F004',
            'estado' => 'Entregado',
            'codPa' => 4,
            'codMed' => 4,
            'codTest' => 4,
            'horaCrono' => '11:00:00',
            'fechaCrono' => '2025-11-22',
        ]);

        // Servicios programados para hoy - En proceso
        Servicio::create([
            'fechaSol' => '2025-11-26',
            'horaSol' => '09:00:00',
            'nroServ' => 'SERV005',
            'fechaAten' => '2025-11-27',
            'horaAten' => '09:30:00',
            'tipoAseg' => 'AsegRegular',
            'nroFicha' => 'F005',
            'estado' => 'EnProceso',
            'codPa' => 5,
            'codMed' => 5,
            'codTest' => 1,
            'horaCrono' => '09:30:00',
            'fechaCrono' => '2025-11-27',
        ]);

        Servicio::create([
            'fechaSol' => '2025-11-27',
            'horaSol' => '08:00:00',
            'nroServ' => 'SERV006',
            'fechaAten' => '2025-11-27',
            'horaAten' => '10:00:00',
            'tipoAseg' => 'NoAsegEmergencia',
            'nroFicha' => 'F006',
            'estado' => 'Atendido',
            'codPa' => 6,
            'codMed' => 6,
            'codTest' => 2,
            'horaCrono' => '10:00:00',
            'fechaCrono' => '2025-11-27',
        ]);

        // Servicios programados para días futuros
        Servicio::create([
            'fechaSol' => '2025-11-27',
            'horaSol' => '11:30:00',
            'nroServ' => 'SERV007',
            'fechaAten' => '2025-11-28',
            'horaAten' => '08:00:00',
            'tipoAseg' => 'AsegRegular',
            'nroFicha' => 'F007',
            'estado' => 'Programado',
            'codPa' => 7,
            'codMed' => 7,
            'codTest' => 3,
            'horaCrono' => '08:00:00',
            'fechaCrono' => '2025-11-28',
        ]);

        Servicio::create([
            'fechaSol' => '2025-11-27',
            'horaSol' => '13:00:00',
            'nroServ' => 'SERV008',
            'fechaAten' => '2025-11-28',
            'horaAten' => '09:00:00',
            'tipoAseg' => 'NoAsegRegular',
            'nroFicha' => 'F008',
            'estado' => 'Programado',
            'codPa' => 8,
            'codMed' => 8,
            'codTest' => 4,
            'horaCrono' => '09:00:00',
            'fechaCrono' => '2025-11-28',
        ]);

        Servicio::create([
            'fechaSol' => '2025-11-27',
            'horaSol' => '15:45:00',
            'nroServ' => 'SERV009',
            'fechaAten' => '2025-11-29',
            'horaAten' => '10:00:00',
            'tipoAseg' => 'AsegRegular',
            'nroFicha' => 'F009',
            'estado' => 'Programado',
            'codPa' => 9,
            'codMed' => 9,
            'codTest' => 1,
            'horaCrono' => '10:00:00',
            'fechaCrono' => '2025-11-29',
        ]);

        Servicio::create([
            'fechaSol' => '2025-11-26',
            'horaSol' => '16:20:00',
            'nroServ' => 'SERV010',
            'fechaAten' => '2025-12-02',
            'horaAten' => '11:00:00',
            'tipoAseg' => 'AsegEmergencia',
            'nroFicha' => 'F010',
            'estado' => 'Programado',
            'codPa' => 10,
            'codMed' => 10,
            'codTest' => 2,
            'horaCrono' => '11:00:00',
            'fechaCrono' => '2025-12-02',
        ]);

        // Servicio cancelado
        Servicio::create([
            'fechaSol' => '2025-11-25',
            'horaSol' => '10:00:00',
            'nroServ' => 'SERV011',
            'tipoAseg' => 'NoAsegRegular',
            'estado' => 'Cancelado',
            'codPa' => 11,
            'codMed' => 11,
            'codTest' => 3,
        ]);

        // Más servicios de emergencia
        Servicio::create([
            'fechaSol' => '2025-11-20',
            'horaSol' => '16:45:00',
            'nroServ' => 'SERV012',
            'fechaAten' => '2025-11-20',
            'horaAten' => '17:00:00',
            'fechaEnt' => '2025-11-20',
            'horaEnt' => '19:30:00',
            'tipoAseg' => 'AsegEmergencia',
            'nroFicha' => 'EMERG001',
            'estado' => 'Entregado',
            'codPa' => 12,
            'codMed' => 1,
            'codTest' => 1,
            'horaCrono' => '17:00:00',
            'fechaCrono' => '2025-11-20',
        ]);
    }
}