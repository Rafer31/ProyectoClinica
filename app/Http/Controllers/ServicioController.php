<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\TipoEstudio;
use App\Models\CronogramaAtencion;
use App\Models\Diagnostico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServicioController extends Controller
{
    /**
     * Listar todos los servicios
     */
    public function index()
    {
        try {
            $this->actualizarEstadosAutomaticos();

            $servicios = Servicio::with([
                'paciente:codPa,nomPa,paternoPa,maternoPa,nroHCI',
                'medico:codMed,nomMed,paternoMed',
                'tipoEstudio:codTest,descripcion',
                'cronograma:fechaCrono,cantDispo',
                'diagnosticos'
            ])
            ->orderBy('fechaSol', 'desc')
            ->orderBy('horaSol', 'desc')
            ->get();

            return response()->json([
                'success' => true,
                'data' => $servicios,
                'message' => 'Servicios obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los servicios: ' . $e->getMessage()
            ], 500);
        }
    }

    private function actualizarEstadosAutomaticos()
    {
        try {
            $ahora = Carbon::now();
            $serviciosProgramados = Servicio::where('estado', 'Programado')->get();

            foreach ($serviciosProgramados as $servicio) {
                $fechaHoraSol = Carbon::parse($servicio->fechaSol . ' ' . $servicio->horaSol);
                if ($fechaHoraSol->lte($ahora)) {
                    $servicio->estado = 'EnProceso';
                    $servicio->save();
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error al actualizar estados automáticos: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $servicio = Servicio::with([
                'paciente',
                'medico',
                'tipoEstudio.requisitos',
                'cronograma.personalSalud',
                'diagnosticos'
            ])->find($id);

            if (!$servicio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servicio no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $servicio,
                'message' => 'Servicio encontrado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el servicio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular número de ficha automáticamente
     * CORREGIDO: Usa cantDispo directamente del cronograma
     */
    public function calcularNumeroFicha($fechaCrono)
    {
        try {
            $cronograma = CronogramaAtencion::find($fechaCrono);

            if (!$cronograma) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cronograma no encontrado'
                ], 404);
            }

            // Contar servicios NO cancelados (activos)
            $serviciosActivos = Servicio::where('fechaCrono', $fechaCrono)
                ->where('estado', '!=', 'Cancelado')
                ->count();

            // El próximo número de ficha
            $nroFicha = $serviciosActivos + 1;

            // Verificar disponibilidad usando cantDispo del cronograma
            if ($cronograma->cantDispo <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay fichas disponibles para esta fecha'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'nroFicha' => $nroFicha,
                    'cantDispo' => $cronograma->cantDispo,
                    'cantFijo' => $cronograma->cantFijo,
                    'cantEmergencia' => $cronograma->cantEmergencia ?? 0,
                    'serviciosActivos' => $serviciosActivos
                ],
                'message' => 'Número de ficha calculado correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular número de ficha: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar nuevo servicio
     * CORREGIDO:
     * - Usa cantDispo directamente
     * - Incrementa cantEmergencia si es emergencia
     * - Decrementa cantDispo solo si NO es emergencia
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fechaSol' => 'required|date',
            'horaSol' => 'required',
            'nroServ' => 'nullable|string|max:50|unique:Servicio,nroServ',
            'tipoAseg' => 'required|in:AsegEmergencia,AsegRegular,NoAsegEmergencia,NoAsegRegular',
            'nroFicha' => 'nullable|string|max:50',
            'codPa' => 'required|exists:Paciente,codPa',
            'codMed' => 'required|exists:Medico,codMed',
            'codTest' => 'required|exists:TipoEstudio,codTest',
            'fechaCrono' => 'required|exists:CronogramaAtencion,fechaCrono'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $cronograma = CronogramaAtencion::find($request->fechaCrono);

            if (!$cronograma) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cronograma no encontrado'
                ], 404);
            }

            // Determinar si es emergencia
            $esEmergencia = in_array($request->tipoAseg, ['AsegEmergencia', 'NoAsegEmergencia']);

            // VALIDACIÓN: Solo si NO es emergencia, verificar cantDispo
            if (!$esEmergencia && $cronograma->cantDispo <= 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No hay fichas disponibles para esta fecha'
                ], 422);
            }

            // Generar número de servicio
            if (!$request->nroServ) {
                $ultimoServicio = Servicio::count();
                $request->merge([
                    'nroServ' => 'SRV-' . date('Ymd') . '-' . str_pad($ultimoServicio + 1, 4, '0', STR_PAD_LEFT)
                ]);
            }

            // Calcular número de ficha
            $serviciosActivos = Servicio::where('fechaCrono', $request->fechaCrono)
                ->where('estado', '!=', 'Cancelado')
                ->count();

            $nroFicha = $request->nroFicha ?: ($serviciosActivos + 1);

            // Determinar estado inicial
            $fechaHoraSol = Carbon::parse($request->fechaSol . ' ' . $request->horaSol);
            $ahora = Carbon::now();
            $estado = $fechaHoraSol->lte($ahora) ? 'EnProceso' : 'Programado';

            // Crear el servicio
            $servicio = Servicio::create([
                'fechaSol' => $request->fechaSol,
                'horaSol' => $request->horaSol,
                'nroServ' => $request->nroServ,
                'tipoAseg' => $request->tipoAseg,
                'nroFicha' => $nroFicha,
                'estado' => $estado,
                'codPa' => $request->codPa,
                'codMed' => $request->codMed,
                'codTest' => $request->codTest,
                'fechaCrono' => $request->fechaCrono
            ]);

            // ACTUALIZAR CRONOGRAMA según tipo de servicio
            if ($esEmergencia) {
                // EMERGENCIA: Incrementar cantEmergencia
                $cronograma->cantEmergencia = ($cronograma->cantEmergencia ?? 0) + 1;
            } else {
                // NORMAL: Decrementar cantDispo
                $cronograma->cantDispo = $cronograma->cantDispo - 1;
            }

            $cronograma->save();

            $servicio->load(['paciente', 'medico', 'tipoEstudio', 'cronograma']);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $servicio,
                'message' => "Servicio registrado exitosamente con estado: {$estado}",
                'cronograma' => [
                    'cantDispo' => $cronograma->cantDispo,
                    'cantEmergencia' => $cronograma->cantEmergencia
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el servicio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar servicio
     * Al agregar diagnóstico, cambia automáticamente a "Atendido"
     */
    public function update(Request $request, $id)
    {
        try {
            $servicio = Servicio::find($id);

            if (!$servicio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servicio no encontrado'
                ], 404);
            }

            $rules = [];
            if ($request->has('fechaSol')) $rules['fechaSol'] = 'date';
            if ($request->has('nroServ')) $rules['nroServ'] = 'string|max:50|unique:Servicio,nroServ,' . $id . ',codServ';
            if ($request->has('tipoAseg')) $rules['tipoAseg'] = 'in:AsegEmergencia,AsegRegular,NoAsegEmergencia,NoAsegRegular';
            if ($request->has('codPa')) $rules['codPa'] = 'exists:Paciente,codPa';
            if ($request->has('codMed')) $rules['codMed'] = 'exists:Medico,codMed';
            if ($request->has('codTest')) $rules['codTest'] = 'exists:TipoEstudio,codTest';
            if ($request->has('fechaCrono')) $rules['fechaCrono'] = 'exists:CronogramaAtencion,fechaCrono';

            if ($request->has('diagnosticoTexto')) {
                $rules['diagnosticoTexto'] = 'required|string|max:500';
                $rules['tipoDiagnostico'] = 'required|in:sol,eco';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Actualizar campos básicos
            if ($request->has('fechaSol')) $servicio->fechaSol = $request->fechaSol;
            if ($request->has('horaSol')) $servicio->horaSol = $request->horaSol;
            if ($request->has('nroServ')) $servicio->nroServ = $request->nroServ;
            if ($request->has('tipoAseg')) $servicio->tipoAseg = $request->tipoAseg;
            if ($request->has('nroFicha')) $servicio->nroFicha = $request->nroFicha;
            if ($request->has('codPa')) $servicio->codPa = $request->codPa;
            if ($request->has('codMed')) $servicio->codMed = $request->codMed;
            if ($request->has('codTest')) $servicio->codTest = $request->codTest;
            if ($request->has('fechaCrono')) $servicio->fechaCrono = $request->fechaCrono;

            // Si se agrega diagnóstico, cambiar a "Atendido"
            if ($request->has('diagnosticoTexto') && $request->has('tipoDiagnostico')) {
                $diagnosticoTexto = trim($request->diagnosticoTexto);
                $tipoDiagnostico = $request->tipoDiagnostico;

                if (!empty($diagnosticoTexto)) {
                    $diagnostico = Diagnostico::where('descripDiag', $diagnosticoTexto)->first();

                    if (!$diagnostico) {
                        $diagnostico = Diagnostico::create([
                            'descripDiag' => $diagnosticoTexto
                        ]);
                    }

                    $servicio->diagnosticos()->sync([
                        $diagnostico->codDiag => ['tipo' => $tipoDiagnostico]
                    ]);

                    $servicio->estado = 'Atendido';

                    if (!$servicio->fechaAten) {
                        $servicio->fechaAten = Carbon::now()->toDateString();
                        $servicio->horaAten = Carbon::now()->toTimeString();
                    }
                }
            }

            $servicio->save();
            $servicio->load(['paciente', 'medico', 'tipoEstudio', 'cronograma', 'diagnosticos']);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $servicio,
                'message' => 'Servicio actualizado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el servicio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancelar servicio
     * CORREGIDO: Devuelve la ficha a cantDispo si NO era emergencia
     */
    public function cancelar($id)
    {
        DB::beginTransaction();
        try {
            $servicio = Servicio::find($id);

            if (!$servicio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servicio no encontrado'
                ], 404);
            }

            if ($servicio->estado === 'Entregado') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cancelar un servicio ya entregado'
                ], 422);
            }

            if ($servicio->estado === 'Atendido') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cancelar un servicio ya atendido'
                ], 422);
            }

            // Determinar si era emergencia
            $esEmergencia = in_array($servicio->tipoAseg, ['AsegEmergencia', 'NoAsegEmergencia']);

            // DEVOLVER FICHA AL CRONOGRAMA
            $cronograma = CronogramaAtencion::find($servicio->fechaCrono);

            if ($cronograma) {
                if ($esEmergencia) {
                    // Si era emergencia, decrementar cantEmergencia
                    $cronograma->cantEmergencia = max(0, ($cronograma->cantEmergencia ?? 0) - 1);
                } else {
                    // Si era normal, incrementar cantDispo
                    $cronograma->cantDispo = $cronograma->cantDispo + 1;
                }
                $cronograma->save();
            }

            // Cambiar estado del servicio
            $servicio->estado = 'Cancelado';
            $servicio->save();

            $servicio->load(['paciente', 'medico', 'tipoEstudio', 'cronograma']);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $servicio,
                'message' => 'Servicio cancelado exitosamente',
                'cronograma' => [
                    'cantDispo' => $cronograma->cantDispo ?? null,
                    'cantEmergencia' => $cronograma->cantEmergencia ?? null
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar el servicio: ' . $e->getMessage()
            ], 500);
        }
    }

    public function entregar($id)
    {
        try {
            $servicio = Servicio::find($id);

            if (!$servicio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servicio no encontrado'
                ], 404);
            }

            if ($servicio->estado !== 'Atendido') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden entregar servicios que estén en estado "Atendido"'
                ], 422);
            }

            $servicio->estado = 'Entregado';
            $servicio->fechaEnt = Carbon::now()->toDateString();
            $servicio->horaEnt = Carbon::now()->toTimeString();
            $servicio->save();

            $servicio->load(['paciente', 'medico', 'tipoEstudio', 'diagnosticos']);

            return response()->json([
                'success' => true,
                'data' => $servicio,
                'message' => 'Servicio marcado como entregado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar como entregado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cambiarEstado(Request $request, $id)
    {
        try {
            $servicio = Servicio::find($id);

            if (!$servicio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servicio no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'estado' => 'required|in:Programado,Atendido,Entregado,EnProceso,Cancelado'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $estadoAnterior = $servicio->estado;
            $nuevoEstado = $request->estado;

            $servicio->estado = $nuevoEstado;

            if ($nuevoEstado === 'Atendido' && $estadoAnterior !== 'Atendido') {
                if (!$servicio->fechaAten) {
                    $servicio->fechaAten = Carbon::now()->toDateString();
                    $servicio->horaAten = Carbon::now()->toTimeString();
                }
            }

            if ($nuevoEstado === 'Entregado' && $estadoAnterior !== 'Entregado') {
                if (!$servicio->fechaAten) {
                    $servicio->fechaAten = Carbon::now()->toDateString();
                    $servicio->horaAten = Carbon::now()->toTimeString();
                }
                if (!$servicio->fechaEnt) {
                    $servicio->fechaEnt = Carbon::now()->toDateString();
                    $servicio->horaEnt = Carbon::now()->toTimeString();
                }
            }

            $servicio->save();
            $servicio->load(['paciente', 'medico', 'tipoEstudio']);

            return response()->json([
                'success' => true,
                'data' => $servicio,
                'message' => "Estado cambiado a {$nuevoEstado} exitosamente"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function asociarDiagnosticos(Request $request, $id)
    {
        try {
            $servicio = Servicio::find($id);

            if (!$servicio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servicio no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'diagnosticos' => 'required|array',
                'diagnosticos.*.codDiag' => 'required|exists:Diagnostico,codDiag',
                'diagnosticos.*.tipo' => 'required|in:sol,eco'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $diagnosticos = [];
            foreach ($request->diagnosticos as $diag) {
                $diagnosticos[$diag['codDiag']] = ['tipo' => $diag['tipo']];
            }

            $servicio->diagnosticos()->sync($diagnosticos);
            $servicio->load('diagnosticos');

            return response()->json([
                'success' => true,
                'data' => $servicio,
                'message' => 'Diagnósticos asociados exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asociar diagnósticos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos para el formulario
     * CORREGIDO: Usa cantDispo directamente
     */
    public function datosFormulario()
    {
        try {
            $pacientes = Paciente::where('estado', 'activo')
                ->select('codPa', 'nomPa', 'paternoPa', 'maternoPa', 'nroHCI')
                ->orderBy('nomPa')
                ->get();

            $medicos = Medico::select('codMed', 'nomMed', 'paternoMed', 'tipoMed')
                ->orderBy('nomMed')
                ->get();

            $tiposEstudio = TipoEstudio::select('codTest', 'descripcion')
                ->orderBy('descripcion')
                ->get();

            $hoy = Carbon::now()->toDateString();

            // Obtener cronogramas con cantDispo > 0
            $cronogramas = CronogramaAtencion::where(function($query) {
                    $query->where('estado', 'activo')
                          ->orWhere('estado', 'inactivoFut');
                })
                ->where('cantDispo', '>', 0)
                ->where('fechaCrono', '>=', $hoy)
                ->select('fechaCrono', 'cantDispo', 'cantFijo', 'cantEmergencia', 'estado', 'codPer')
                ->orderBy('fechaCrono', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'pacientes' => $pacientes,
                    'medicos' => $medicos,
                    'tiposEstudio' => $tiposEstudio,
                    'cronogramas' => $cronogramas
                ],
                'message' => 'Datos obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error en datosFormulario:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function porPaciente($codPa)
    {
        try {
            $servicios = Servicio::with(['medico', 'tipoEstudio', 'diagnosticos'])
                ->where('codPa', $codPa)
                ->orderBy('fechaSol', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $servicios,
                'message' => 'Servicios del paciente obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los servicios: ' . $e->getMessage()
            ], 500);
        }
    }

    public function porEstado($estado)
    {
        try {
            $this->actualizarEstadosAutomaticos();

            $servicios = Servicio::with(['paciente', 'medico', 'tipoEstudio', 'diagnosticos'])
                ->where('estado', $estado)
                ->orderBy('fechaSol', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $servicios,
                'message' => "Servicios con estado {$estado} obtenidos correctamente"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los servicios: ' . $e->getMessage()
            ], 500);
        }
    }

    public function estadisticas()
    {
        try {
            $this->actualizarEstadosAutomaticos();
            $hoy = date('Y-m-d');

            $stats = [
                'hoy' => Servicio::whereDate('fechaSol', $hoy)->count(),
                'programados' => Servicio::where('estado', 'Programado')->count(),
                'enProceso' => Servicio::where('estado', 'EnProceso')->count(),
                'atendidos' => Servicio::where('estado', 'Atendido')->count(),
                'porEstado' => Servicio::select('estado', DB::raw('count(*) as total'))
                    ->groupBy('estado')
                    ->get(),
                'porTipoAseg' => Servicio::select('tipoAseg', DB::raw('count(*) as total'))
                    ->groupBy('tipoAseg')
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Estadísticas obtenidas correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $servicio = Servicio::find($id);

            if (!$servicio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servicio no encontrado'
                ], 404);
            }

            DB::beginTransaction();

            $servicio->diagnosticos()->detach();
            $servicio->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Servicio eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el servicio: ' . $e->getMessage()
            ], 500);
        }
    }
}
