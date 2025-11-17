<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\TipoEstudio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class HomeController extends Controller
{
    /**
     * Estadísticas para el home del personal
     */
    public function estadisticas()
    {
        try {
            $user = Auth::user();
            $codPer = $user->codPer;

            $hoy = Carbon::today();
            $inicioSemana = Carbon::now()->startOfWeek();
            $inicioMes = Carbon::now()->startOfMonth();

            // Servicios del día (atendidos HOY por este personal)
            $serviciosHoy = Servicio::join('CronogramaAtencion', 'Servicio.fechaCrono', '=', 'CronogramaAtencion.fechaCrono')
                ->where('CronogramaAtencion.codPer', $codPer)
                ->whereDate('Servicio.fechaAten', $hoy)
                ->whereIn('Servicio.estado', ['Atendido', 'Entregado'])
                ->count();

            // Servicios de la semana (atendidos en los últimos 7 días)
            $serviciosSemana = Servicio::join('CronogramaAtencion', 'Servicio.fechaCrono', '=', 'CronogramaAtencion.fechaCrono')
                ->where('CronogramaAtencion.codPer', $codPer)
                ->where('Servicio.fechaAten', '>=', $inicioSemana)
                ->whereIn('Servicio.estado', ['Atendido', 'Entregado'])
                ->count();

            // Servicios del mes (atendidos este mes)
            $serviciosMes = Servicio::join('CronogramaAtencion', 'Servicio.fechaCrono', '=', 'CronogramaAtencion.fechaCrono')
                ->where('CronogramaAtencion.codPer', $codPer)
                ->where('Servicio.fechaAten', '>=', $inicioMes)
                ->whereIn('Servicio.estado', ['Atendido', 'Entregado'])
                ->count();

            // Total de servicios atendidos (todos los que tienen fechaAten)
            $serviciosTotal = Servicio::join('CronogramaAtencion', 'Servicio.fechaCrono', '=', 'CronogramaAtencion.fechaCrono')
                ->where('CronogramaAtencion.codPer', $codPer)
                ->whereNotNull('Servicio.fechaAten')
                ->whereIn('Servicio.estado', ['Atendido', 'Entregado'])
                ->count();

            // Servicios por estado (TODOS los estados del personal)
            $porEstado = Servicio::join('CronogramaAtencion', 'Servicio.fechaCrono', '=', 'CronogramaAtencion.fechaCrono')
                ->where('CronogramaAtencion.codPer', $codPer)
                ->select('Servicio.estado', DB::raw('count(*) as total'))
                ->groupBy('Servicio.estado')
                ->pluck('total', 'estado')
                ->toArray();

            // Servicios por tipo de seguro (TODOS los del personal)
            $porTipoAseg = Servicio::join('CronogramaAtencion', 'Servicio.fechaCrono', '=', 'CronogramaAtencion.fechaCrono')
                ->where('CronogramaAtencion.codPer', $codPer)
                ->select('Servicio.tipoAseg', DB::raw('count(*) as total'))
                ->groupBy('Servicio.tipoAseg')
                ->pluck('total', 'tipoAseg')
                ->toArray();

            // Últimos 10 servicios atendidos o entregados
            $ultimosServicios = Servicio::join('CronogramaAtencion', 'Servicio.fechaCrono', '=', 'CronogramaAtencion.fechaCrono')
                ->where('CronogramaAtencion.codPer', $codPer)
                ->with(['paciente:codPa,nomPa,paternoPa,maternoPa', 'tipoEstudio:codTest,descripcion'])
                ->select('Servicio.*')
                ->whereNotNull('Servicio.fechaAten')
                ->whereIn('Servicio.estado', ['Atendido', 'Entregado'])
                ->orderBy('Servicio.fechaAten', 'desc')
                ->orderBy('Servicio.horaAten', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'hoy' => $serviciosHoy,
                    'semana' => $serviciosSemana,
                    'mes' => $serviciosMes,
                    'total' => $serviciosTotal,
                    'porEstado' => $porEstado,
                    'porTipoAseg' => $porTipoAseg,
                    'ultimosServicios' => $ultimosServicios
                ]
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error en estadísticas del home:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Generar reporte PDF del día
     */
    public function reporteDia()
    {
        try {
            $user = Auth::user();
            $codPer = $user->codPer;
            $hoy = Carbon::today();

            $servicios = Servicio::join('CronogramaAtencion', 'Servicio.fechaCrono', '=', 'CronogramaAtencion.fechaCrono')
                ->where('CronogramaAtencion.codPer', $codPer)
                ->whereDate('Servicio.fechaAten', $hoy)
                ->whereIn('Servicio.estado', ['Atendido', 'Entregado'])
                ->with(['paciente', 'medico', 'tipoEstudio', 'diagnosticos'])
                ->select('Servicio.*')
                ->orderBy('Servicio.horaAten')
                ->get();

            $data = [
                'titulo' => 'Reporte Diario de Servicios',
                'fecha' => $hoy->format('d/m/Y'),
                'personal' => $user->nomPer . ' ' . $user->paternoPer,
                'servicios' => $servicios,
                'total' => $servicios->count(),
                'periodo' => 'Día: ' . $hoy->isoFormat('dddd, D [de] MMMM [de] YYYY')
            ];

            $pdf = Pdf::loadView('personal.reportes.home-pdf', $data);
            return $pdf->download('reporte-diario-' . $hoy->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar reporte: ' . $e->getMessage());
        }
    }

    /**
     * Generar reporte PDF semanal
     */
    public function reporteSemana()
    {
        try {
            $user = Auth::user();
            $codPer = $user->codPer;
            $inicioSemana = Carbon::now()->startOfWeek();
            $finSemana = Carbon::now()->endOfWeek();

            $servicios = Servicio::join('CronogramaAtencion', 'Servicio.fechaCrono', '=', 'CronogramaAtencion.fechaCrono')
                ->where('CronogramaAtencion.codPer', $codPer)
                ->whereBetween('Servicio.fechaAten', [$inicioSemana, $finSemana])
                ->whereIn('Servicio.estado', ['Atendido', 'Entregado'])
                ->with(['paciente', 'medico', 'tipoEstudio', 'diagnosticos'])
                ->select('Servicio.*')
                ->orderBy('Servicio.fechaAten')
                ->orderBy('Servicio.horaAten')
                ->get();

            $data = [
                'titulo' => 'Reporte Semanal de Servicios',
                'fecha' => Carbon::now()->format('d/m/Y'),
                'personal' => $user->nomPer . ' ' . $user->paternoPer,
                'servicios' => $servicios,
                'total' => $servicios->count(),
                'periodo' => 'Semana del ' . $inicioSemana->format('d/m/Y') . ' al ' . $finSemana->format('d/m/Y')
            ];

            $pdf = Pdf::loadView('personal.reportes.home-pdf', $data);
            return $pdf->download('reporte-semanal-' . Carbon::now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar reporte: ' . $e->getMessage());
        }
    }

    /**
     * Generar reporte PDF mensual
     */
    public function reporteMes()
    {
        try {
            $user = Auth::user();
            $codPer = $user->codPer;
            $inicioMes = Carbon::now()->startOfMonth();
            $finMes = Carbon::now()->endOfMonth();

            $servicios = Servicio::join('CronogramaAtencion', 'Servicio.fechaCrono', '=', 'CronogramaAtencion.fechaCrono')
                ->where('CronogramaAtencion.codPer', $codPer)
                ->whereBetween('Servicio.fechaAten', [$inicioMes, $finMes])
                ->whereIn('Servicio.estado', ['Atendido', 'Entregado'])
                ->with(['paciente', 'medico', 'tipoEstudio', 'diagnosticos'])
                ->select('Servicio.*')
                ->orderBy('Servicio.fechaAten')
                ->orderBy('Servicio.horaAten')
                ->get();

            $data = [
                'titulo' => 'Reporte Mensual de Servicios',
                'fecha' => Carbon::now()->format('d/m/Y'),
                'personal' => $user->nomPer . ' ' . $user->paternoPer,
                'servicios' => $servicios,
                'total' => $servicios->count(),
                'periodo' => 'Mes de ' . Carbon::now()->isoFormat('MMMM [de] YYYY')
            ];

            $pdf = Pdf::loadView('personal.reportes.home-pdf', $data);
            return $pdf->download('reporte-mensual-' . Carbon::now()->format('Y-m') . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar reporte: ' . $e->getMessage());
        }
    }
}
