<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ServicioPdfController extends \Illuminate\Routing\Controller
{
    /**
     * Genera el PDF de la ficha del servicio con información del paciente
     */
    public function generarFichaServicio($nroServ)
    {
        try {
            // Buscar el servicio con sus relaciones
            $servicio = Servicio::with([
                'paciente',
                'medico',
                'tipoEstudio.requisitos',
                'cronograma',
                'diagnosticos'
            ])->where('nroServ', $nroServ)->firstOrFail();

            // Obtener fecha y hora actual
            $fecha = now()->format('d/m/Y');
            $hora = now()->format('H:i:s');

            // Generar el PDF
            $pdf = Pdf::loadView('personal.servicios.pdf-ficha', compact('servicio', 'fecha', 'hora'));

            // Configurar el tamaño de página
            $pdf->setPaper('letter', 'portrait');

            // Generar nombre del archivo
            $nombreArchivo = 'Ficha_Servicio_' . $servicio->nroServ . '_' .
                             str_replace(' ', '_', $servicio->paciente->apellidoPaterno) . '_' .
                             now()->format('YmdHis') . '.pdf';

            // Retornar el PDF para descarga
            return $pdf->download($nombreArchivo);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Visualiza el PDF en el navegador sin descargarlo
     */
    public function visualizarFichaServicio($nroServ)
    {
        try {
            $servicio = Servicio::with([
                'paciente',
                'medico',
                'tipoEstudio.requisitos',
                'cronograma',
                'diagnosticos'
            ])->where('nroServ', $nroServ)->firstOrFail();

            $fecha = now()->format('d/m/Y');
            $hora = now()->format('H:i:s');

            $pdf = Pdf::loadView('personal.servicios.pdf-ficha', compact('servicio', 'fecha', 'hora'));
            $pdf->setPaper('letter', 'portrait');

            // Stream para visualizar en el navegador
            return $pdf->stream('Ficha_Servicio_' . $servicio->nroServ . '.pdf');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al visualizar el PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
