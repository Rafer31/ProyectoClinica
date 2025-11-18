<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\PersonalSalud;
use App\Models\Paciente;
use App\Models\CronogramaAtencion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller;

class EstadisticasSupervisorController extends Controller
{
    /**
     * Estadísticas generales del sistema
     */
    public function estadisticasGenerales()
    {
        try {
            $hoy = Carbon::today();
            $inicioSemana = Carbon::now()->startOfWeek();
            $inicioMes = Carbon::now()->startOfMonth();
            $inicioAnio = Carbon::now()->startOfYear();

            // Estadísticas de servicios
            $serviciosHoy = Servicio::whereDate('fechaSol', $hoy)->count();
            $serviciosSemana = Servicio::where('fechaSol', '>=', $inicioSemana)->count();
            $serviciosMes = Servicio::where('fechaSol', '>=', $inicioMes)->count();
            $serviciosAnio = Servicio::where('fechaSol', '>=', $inicioAnio)->count();

            // Atendidos
            $atendidosHoy = Servicio::whereDate('fechaAten', $hoy)
                ->whereIn('estado', ['Atendido', 'Entregado'])
                ->count();
            $atendidosSemana = Servicio::where('fechaAten', '>=', $inicioSemana)
                ->whereIn('estado', ['Atendido', 'Entregado'])
                ->count();
            $atendidosMes = Servicio::where('fechaAten', '>=', $inicioMes)
                ->whereIn('estado', ['Atendido', 'Entregado'])
                ->count();

            // Por estado
            $porEstado = Servicio::select('estado', DB::raw('count(*) as total'))
                ->groupBy('estado')
                ->pluck('total', 'estado')
                ->toArray();

            // Por tipo de aseguramiento
            $porTipoAseg = Servicio::select('tipoAseg', DB::raw('count(*) as total'))
                ->groupBy('tipoAseg')
                ->pluck('total', 'tipoAseg')
                ->toArray();

            // Estadísticas de pacientes
            $totalPacientes = Paciente::count();
            $pacientesActivos = Paciente::where('estado', 'activo')->count();
            $pacientesSUS = Paciente::where('tipoPac', 'SUS')->count();
            $pacientesSINSUS = Paciente::where('tipoPac', 'SINSUS')->count();

            // Estadísticas de personal
            $totalPersonal = PersonalSalud::count();
            $personalActivo = PersonalSalud::where('estado', 'activo')->count();

            // Personal más productivo del mes
            $personalProductivo = DB::table('Servicio')
                ->join('CronogramaAtencion', 'Servicio.fechaCrono', '=', 'CronogramaAtencion.fechaCrono')
                ->join('PersonalSalud', 'CronogramaAtencion.codPer', '=', 'PersonalSalud.codPer')
                ->where('Servicio.fechaAten', '>=', $inicioMes)
                ->whereIn('Servicio.estado', ['Atendido', 'Entregado'])
                ->select(
                    'PersonalSalud.codPer',
                    'PersonalSalud.nomPer',
                    'PersonalSalud.paternoPer',
                    'PersonalSalud.maternoPer',
                    DB::raw('COUNT(*) as total_atendidos')
                )
                ->groupBy('PersonalSalud.codPer', 'PersonalSalud.nomPer', 'PersonalSalud.paternoPer', 'PersonalSalud.maternoPer')
                ->orderByDesc('total_atendidos')
                ->limit(5)
                ->get();

            // Servicios por día de los últimos 30 días
            $serviciosPorDia = Servicio::where('fechaSol', '>=', Carbon::now()->subDays(30))
                ->select(
                    DB::raw('DATE(fechaSol) as fecha'),
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get();

            // Atendidos por día de los últimos 30 días
            $atendidosPorDia = Servicio::where('fechaAten', '>=', Carbon::now()->subDays(30))
                ->whereIn('estado', ['Atendido', 'Entregado'])
                ->select(
                    DB::raw('DATE(fechaAten) as fecha'),
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'servicios' => [
                        'hoy' => $serviciosHoy,
                        'semana' => $serviciosSemana,
                        'mes' => $serviciosMes,
                        'anio' => $serviciosAnio,
                        'porEstado' => $porEstado,
                        'porTipoAseg' => $porTipoAseg
                    ],
                    'atendidos' => [
                        'hoy' => $atendidosHoy,
                        'semana' => $atendidosSemana,
                        'mes' => $atendidosMes
                    ],
                    'pacientes' => [
                        'total' => $totalPacientes,
                        'activos' => $pacientesActivos,
                        'SUS' => $pacientesSUS,
                        'SINSUS' => $pacientesSINSUS
                    ],
                    'personal' => [
                        'total' => $totalPersonal,
                        'activo' => $personalActivo
                    ],
                    'personalProductivo' => $personalProductivo,
                    'graficos' => [
                        'serviciosPorDia' => $serviciosPorDia,
                        'atendidosPorDia' => $atendidosPorDia
                    ]
                ],
                'message' => 'Estadísticas generales obtenidas correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Estadísticas de un personal específico
     */
    public function estadisticasPersonal(Request $request)
    {
        try {
            $codPer = $request->input('codPer');
            $fecha = $request->input('fecha');
            $periodo = $request->input('periodo', 'dia'); // dia, semana, mes

            $personal = PersonalSalud::find($codPer);
            if (!$personal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personal no encontrado'
                ], 404);
            }

            $fechaBase = $fecha ? Carbon::parse($fecha) : Carbon::now();

            // Determinar rango de fechas según periodo
            switch ($periodo) {
                case 'semana':
                    $fechaInicio = $fechaBase->copy()->startOfWeek();
                    $fechaFin = $fechaBase->copy()->endOfWeek();
                    break;
                case 'mes':
                    $fechaInicio = $fechaBase->copy()->startOfMonth();
                    $fechaFin = $fechaBase->copy()->endOfMonth();
                    break;
                default: // dia
                    $fechaInicio = $fechaBase->copy()->startOfDay();
                    $fechaFin = $fechaBase->copy()->endOfDay();
                    break;
            }

            // Servicios del personal en el periodo
            $servicios = Servicio::join('CronogramaAtencion', 'Servicio.fechaCrono', '=', 'CronogramaAtencion.fechaCrono')
                ->where('CronogramaAtencion.codPer', $codPer)
                ->whereBetween('Servicio.fechaSol', [$fechaInicio, $fechaFin])
                ->with(['paciente', 'tipoEstudio'])
                ->select('Servicio.*')
                ->get();

            // Atendidos en el periodo
            $atendidos = Servicio::join('CronogramaAtencion', 'Servicio.fechaCrono', '=', 'CronogramaAtencion.fechaCrono')
                ->where('CronogramaAtencion.codPer', $codPer)
                ->whereBetween('Servicio.fechaAten', [$fechaInicio, $fechaFin])
                ->whereIn('Servicio.estado', ['Atendido', 'Entregado'])
                ->count();

            // Por estado
            $porEstado = Servicio::join('CronogramaAtencion', 'Servicio.fechaCrono', '=', 'CronogramaAtencion.fechaCrono')
                ->where('CronogramaAtencion.codPer', $codPer)
                ->whereBetween('Servicio.fechaSol', [$fechaInicio, $fechaFin])
                ->select('Servicio.estado', DB::raw('count(*) as total'))
                ->groupBy('Servicio.estado')
                ->pluck('total', 'estado')
                ->toArray();

            // Por tipo de aseguramiento
            $porTipoAseg = Servicio::join('CronogramaAtencion', 'Servicio.fechaCrono', '=', 'CronogramaAtencion.fechaCrono')
                ->where('CronogramaAtencion.codPer', $codPer)
                ->whereBetween('Servicio.fechaSol', [$fechaInicio, $fechaFin])
                ->select('Servicio.tipoAseg', DB::raw('count(*) as total'))
                ->groupBy('Servicio.tipoAseg')
                ->pluck('total', 'tipoAseg')
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => [
                    'personal' => [
                        'codPer' => $personal->codPer,
                        'nombre' => $personal->nomPer . ' ' . $personal->paternoPer . ' ' . ($personal->maternoPer ?? ''),
                        'usuario' => $personal->usuarioPer
                    ],
                    'periodo' => [
                        'tipo' => $periodo,
                        'fechaInicio' => $fechaInicio->format('Y-m-d'),
                        'fechaFin' => $fechaFin->format('Y-m-d')
                    ],
                    'resumen' => [
                        'totalServicios' => $servicios->count(),
                        'atendidos' => $atendidos,
                        'porEstado' => $porEstado,
                        'porTipoAseg' => $porTipoAseg
                    ],
                    'servicios' => $servicios
                ],
                'message' => 'Estadísticas del personal obtenidas correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas del personal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar reporte PDF de un personal específico
     */
    public function generarReportePersonal(Request $request)
    {
        try {
            $codPer = $request->input('codPer');
            $fecha = $request->input('fecha');
            $periodo = $request->input('periodo', 'dia');

            $personal = PersonalSalud::find($codPer);
            if (!$personal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personal no encontrado'
                ], 404);
            }

            $fechaBase = $fecha ? Carbon::parse($fecha) : Carbon::now();

            // Determinar rango de fechas
            switch ($periodo) {
                case 'semana':
                    $fechaInicio = $fechaBase->copy()->startOfWeek();
                    $fechaFin = $fechaBase->copy()->endOfWeek();
                    $tituloFecha = 'Semana del ' . $fechaInicio->format('d/m/Y') . ' al ' . $fechaFin->format('d/m/Y');
                    break;
                case 'mes':
                    $fechaInicio = $fechaBase->copy()->startOfMonth();
                    $fechaFin = $fechaBase->copy()->endOfMonth();
                    $tituloFecha = 'Mes de ' . $fechaBase->isoFormat('MMMM [de] YYYY');
                    break;
                default:
                    $fechaInicio = $fechaBase->copy()->startOfDay();
                    $fechaFin = $fechaBase->copy()->endOfDay();
                    $tituloFecha = $fechaBase->format('d/m/Y');
                    break;
            }

            $servicios = Servicio::join('CronogramaAtencion', 'Servicio.fechaCrono', '=', 'CronogramaAtencion.fechaCrono')
                ->where('CronogramaAtencion.codPer', $codPer)
                ->whereBetween('Servicio.fechaAten', [$fechaInicio, $fechaFin])
                ->whereIn('Servicio.estado', ['Atendido', 'Entregado'])
                ->with(['paciente', 'medico', 'tipoEstudio', 'diagnosticos'])
                ->select('Servicio.*')
                ->orderBy('Servicio.fechaAten')
                ->orderBy('Servicio.horaAten')
                ->get();

            $data = [
                'titulo' => 'Reporte de Servicios - ' . ucfirst($periodo),
                'fecha' => Carbon::now()->format('d/m/Y'),
                'personal' => $personal->nomPer . ' ' . $personal->paternoPer . ' ' . ($personal->maternoPer ?? ''),
                'servicios' => $servicios,
                'total' => $servicios->count(),
                'periodo' => $tituloFecha
            ];

            $pdf = Pdf::loadView('supervisor.reportes.reporte-personal-pdf', $data);
            $nombreArchivo = 'reporte-' . $periodo . '-' . $personal->codPer . '-' . $fechaBase->format('Y-m-d') . '.pdf';

            return $pdf->download($nombreArchivo);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar reporte: ' . $e->getMessage());
        }
    }

    /**
     * Listar personal para el selector
     */
    public function listarPersonal()
    {
        try {
            $personal = PersonalSalud::select('codPer', 'nomPer', 'paternoPer', 'maternoPer', 'usuarioPer')
                ->where('estado', 'activo')
                ->orderBy('nomPer')
                ->get()
                ->map(function($p) {
                    return [
                        'codPer' => $p->codPer,
                        'nombre' => $p->nomPer . ' ' . $p->paternoPer . ' ' . ($p->maternoPer ?? ''),
                        'usuario' => $p->usuarioPer
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $personal,
                'message' => 'Personal listado correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al listar personal: ' . $e->getMessage()
            ], 500);
        }
    }
}
