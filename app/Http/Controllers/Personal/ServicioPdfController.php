<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;

use App\Models\Servicio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ServicioPdfController extends \Illuminate\Routing\Controller
{
    /**
     * Generar PDF de ficha de servicio (descarga)
     */
    public function generarFichaServicio($nroServ)
    {
        try {
            $servicio = Servicio::where('nroServ', $nroServ)
                ->with(['paciente', 'medico', 'tipoEstudio.requisitos', 'diagnosticos'])
                ->firstOrFail();

            $data = [
                'servicio' => $servicio,
                'fecha' => \Carbon\Carbon::now()->format('d/m/Y'),
                'hora' => \Carbon\Carbon::now()->format('H:i:s'),
            ];

            $pdf = Pdf::loadView('personal.servicios.pdf-ficha', $data);
            return $pdf->download('ficha-servicio-' . $nroServ . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Visualizar PDF de ficha de servicio (en navegador)
     */
    public function visualizarFichaServicio($nroServ)
    {
        try {
            $servicio = Servicio::where('nroServ', $nroServ)
                ->with(['paciente', 'medico', 'tipoEstudio.requisitos', 'diagnosticos'])
                ->firstOrFail();

            $data = [
                'servicio' => $servicio,
                'fecha' => \Carbon\Carbon::now()->format('d/m/Y'),
                'hora' => \Carbon\Carbon::now()->format('H:i:s'),
            ];

            $pdf = Pdf::loadView('personal.servicios.pdf-ficha', $data);
            return $pdf->stream('ficha-servicio-' . $nroServ . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
