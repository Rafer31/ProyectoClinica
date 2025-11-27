<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FichaCitaController extends Controller
{
    /**
     * Generar PDF de ficha de cita para un servicio
     */
    public function generarPDF($codServ)
    {
        try {
            // Obtener el servicio completo con todas las relaciones necesarias
            $servicio = Servicio::with([
                'paciente',
                'medico',
                'tipoEstudio.requisitos',
                'diagnosticos'
            ])->findOrFail($codServ);

            // Preparar datos para la vista
            $data = [
                'servicio' => $servicio,
                'paciente' => $servicio->paciente,
                'medico' => $servicio->medico,
                'tipoEstudio' => $servicio->tipoEstudio,
               'requisitos' => $servicio->tipoEstudio && $servicio->tipoEstudio->requisitos ? $servicio->tipoEstudio->requisitos : [],
                'fechaCita' => $servicio->fechaCrono ? \Carbon\Carbon::parse($servicio->fechaCrono)->format('d/m/Y') : 'No programada',
                'horaCita' => $servicio->horaCrono ?? 'No asignada',
                'fechaSolicitud' => $servicio->fechaSol ? \Carbon\Carbon::parse($servicio->fechaSol)->format('d/m/Y') : '-',
                'horaSolicitud' => $servicio->horaSol ?? '-',
                'nroFicha' => $servicio->nroFicha ?? 'N/A',
                'nroServicio' => $servicio->nroServ ?? 'N/A'
            ];

            // Generar PDF
            $pdf = Pdf::loadView('enfermera.calendario.ficha-cita-pdf', $data);
            $pdf->setPaper('a4', 'portrait');

            // Nombre del archivo
            $nombreArchivo = 'Ficha_Cita_' . $servicio->nroServ . '_' . ($servicio->paciente ? str_replace(' ', '_', $servicio->paciente->nomPa) : 'SinNombre') . '.pdf';

            return $pdf->download($nombreArchivo);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
