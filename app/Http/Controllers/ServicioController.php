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
            $servicios = Servicio::with([
                'paciente:codPa,nomPa,paternoPa,maternoPa,nroHCI',
                'medico:codMed,nomMed,paternoMed',
                'tipoEstudio:codTest,descripcion',
                'cronograma:fechaCrono,cantDispo',
                'diagnosticos:codDiag,descripDiag'
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

    /**
     * Mostrar un servicio específico
     */
    public function show($id)
    {
        try {
            $servicio = Servicio::with([
                'paciente',
                'medico',
                'tipoEstudio.requisitos',
                'cronograma.personalSalud',
                'diagnosticos',
                'asignaciones.consultorio'
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
     * Registrar nuevo servicio
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fechaSol' => 'required|date',
            'horaSol' => 'required',
            'nroServ' => 'nullable|string|max:50|unique:Servicio,nroServ',
            'tipoAseg' => 'required|in:AsegEmergencia,AsegRegular,NoAsegEmergencia,NoAsegRegular',
            'nroFicha' => 'nullable|string|max:50',
            'estado' => 'required|in:Programado,Atendido,Entregado,EnProceso',
            'codPa' => 'required|exists:Paciente,codPa',
            'codMed' => 'required|exists:Medico,codMed',
            'codTest' => 'required|exists:TipoEstudio,codTest',
            'fechaCrono' => 'required|exists:CronogramaAtencion,fechaCrono',
            'diagnosticos' => 'nullable|array',
            'diagnosticos.*.codDiag' => 'required|exists:Diagnostico,codDiag',
            'diagnosticos.*.tipo' => 'required|in:sol,eco'
        ], [
            'fechaSol.required' => 'La fecha de solicitud es obligatoria',
            'horaSol.required' => 'La hora de solicitud es obligatoria',
            'nroServ.unique' => 'El número de servicio ya existe',
            'tipoAseg.required' => 'El tipo de seguro es obligatorio',
            'tipoAseg.in' => 'El tipo de seguro no es válido',
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado no es válido',
            'codPa.required' => 'El paciente es obligatorio',
            'codPa.exists' => 'El paciente seleccionado no existe',
            'codMed.required' => 'El médico es obligatorio',
            'codMed.exists' => 'El médico seleccionado no existe',
            'codTest.required' => 'El tipo de estudio es obligatorio',
            'codTest.exists' => 'El tipo de estudio seleccionado no existe',
            'fechaCrono.required' => 'La fecha del cronograma es obligatoria',
            'fechaCrono.exists' => 'El cronograma seleccionado no existe'
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
            // Generar número de servicio si no se proporciona
            if (!$request->nroServ) {
                $request->merge([
                    'nroServ' => 'SRV-' . date('Ymd') . '-' . str_pad(Servicio::count() + 1, 4, '0', STR_PAD_LEFT)
                ]);
            }

            $data = $request->only([
                'fechaSol', 'horaSol', 'nroServ', 'tipoAseg', 'nroFicha', 'estado',
                'codPa', 'codMed', 'codTest', 'fechaCrono'
            ]);

            // Establecer fechas automáticamente según el estado inicial
            if ($request->estado === 'Atendido') {
                $data['fechaAten'] = Carbon::now()->toDateString();
                $data['horaAten'] = Carbon::now()->toTimeString();
            }

            if ($request->estado === 'Entregado') {
                // Si se marca como entregado, debe tener fecha de atención
                if (!isset($data['fechaAten'])) {
                    $data['fechaAten'] = Carbon::now()->toDateString();
                    $data['horaAten'] = Carbon::now()->toTimeString();
                }
                $data['fechaEnt'] = Carbon::now()->toDateString();
                $data['horaEnt'] = Carbon::now()->toTimeString();
            }

            $servicio = Servicio::create($data);

            // Asociar diagnósticos si existen
            if ($request->has('diagnosticos') && is_array($request->diagnosticos)) {
                $diagnosticos = [];
                foreach ($request->diagnosticos as $diag) {
                    $diagnosticos[$diag['codDiag']] = ['tipo' => $diag['tipo']];
                }
                $servicio->diagnosticos()->attach($diagnosticos);
            }

            $servicio->load(['paciente', 'medico', 'tipoEstudio', 'cronograma', 'diagnosticos']);

            DB::commit();
            return response()->json([
                'success' => true,
                'data' => $servicio,
                'message' => 'Servicio registrado exitosamente'
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
            if ($request->has('estado')) $rules['estado'] = 'in:Programado,Atendido,Entregado,EnProceso';
            if ($request->has('codPa')) $rules['codPa'] = 'exists:Paciente,codPa';
            if ($request->has('codMed')) $rules['codMed'] = 'exists:Medico,codMed';
            if ($request->has('codTest')) $rules['codTest'] = 'exists:TipoEstudio,codTest';
            if ($request->has('fechaCrono')) $rules['fechaCrono'] = 'exists:CronogramaAtencion,fechaCrono';

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Detectar cambio de estado y actualizar fechas automáticamente
            $estadoAnterior = $servicio->estado;

            $servicio->fill($request->only([
                'fechaSol', 'horaSol', 'nroServ', 'tipoAseg', 'nroFicha', 'estado',
                'codPa', 'codMed', 'codTest', 'fechaCrono'
            ]));

            // Si el estado cambió a "Atendido" y no tenía fecha de atención
            if ($request->has('estado') && $request->estado === 'Atendido' && $estadoAnterior !== 'Atendido') {
                if (!$servicio->fechaAten) {
                    $servicio->fechaAten = Carbon::now()->toDateString();
                    $servicio->horaAten = Carbon::now()->toTimeString();
                }
            }

            // Si el estado cambió a "Entregado" y no tenía fecha de entrega
            if ($request->has('estado') && $request->estado === 'Entregado' && $estadoAnterior !== 'Entregado') {
                // Asegurar que tenga fecha de atención
                if (!$servicio->fechaAten) {
                    $servicio->fechaAten = Carbon::now()->toDateString();
                    $servicio->horaAten = Carbon::now()->toTimeString();
                }
                // Establecer fecha de entrega
                if (!$servicio->fechaEnt) {
                    $servicio->fechaEnt = Carbon::now()->toDateString();
                    $servicio->horaEnt = Carbon::now()->toTimeString();
                }
            }

            // Actualizar diagnósticos si se proporcionan
            if ($request->has('diagnosticos') && is_array($request->diagnosticos)) {
                $diagnosticos = [];
                foreach ($request->diagnosticos as $diag) {
                    $diagnosticos[$diag['codDiag']] = ['tipo' => $diag['tipo']];
                }
                $servicio->diagnosticos()->sync($diagnosticos);
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
     * Cambiar estado del servicio
     */
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
                'estado' => 'required|in:Programado,Atendido,Entregado,EnProceso'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $estadoAnterior = $servicio->estado;
            $nuevoEstado = $request->estado;

            // Actualizar el estado
            $servicio->estado = $nuevoEstado;

            // Lógica automática de fechas según el cambio de estado
            if ($nuevoEstado === 'Atendido' && $estadoAnterior !== 'Atendido') {
                // Al cambiar a "Atendido", registrar fecha y hora de atención
                if (!$servicio->fechaAten) {
                    $servicio->fechaAten = Carbon::now()->toDateString();
                    $servicio->horaAten = Carbon::now()->toTimeString();
                }
            }

            if ($nuevoEstado === 'Entregado' && $estadoAnterior !== 'Entregado') {
                // Al cambiar a "Entregado", asegurar que tenga fecha de atención
                if (!$servicio->fechaAten) {
                    $servicio->fechaAten = Carbon::now()->toDateString();
                    $servicio->horaAten = Carbon::now()->toTimeString();
                }
                // Registrar fecha y hora de entrega
                if (!$servicio->fechaEnt) {
                    $servicio->fechaEnt = Carbon::now()->toDateString();
                    $servicio->horaEnt = Carbon::now()->toTimeString();
                }
            }

            $servicio->save();
            $servicio->load(['paciente', 'medico', 'tipoEstudio']);

            DB::commit();

            $mensaje = "Estado cambiado a {$nuevoEstado} exitosamente";
            if ($nuevoEstado === 'Atendido') {
                $mensaje .= " - Fecha de atención registrada automáticamente";
            } elseif ($nuevoEstado === 'Entregado') {
                $mensaje .= " - Fecha de entrega registrada automáticamente";
            }

            return response()->json([
                'success' => true,
                'data' => $servicio,
                'message' => $mensaje
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asociar diagnósticos a un servicio
     */
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
     * Obtener datos necesarios para el formulario
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

            $cronogramas = CronogramaAtencion::where('estado', 'activo')
                ->where('cantDispo', '>', 0)
                ->where('fechaCrono', '>=', date('Y-m-d'))
                ->with('personalSalud:codPer,nomPer,paternoPer')
                ->orderBy('fechaCrono')
                ->get();

            $diagnosticos = Diagnostico::select('codDiag', 'descripDiag')
                ->orderBy('descripDiag')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'pacientes' => $pacientes,
                    'medicos' => $medicos,
                    'tiposEstudio' => $tiposEstudio,
                    'cronogramas' => $cronogramas,
                    'diagnosticos' => $diagnosticos
                ],
                'message' => 'Datos obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar servicios por paciente
     */
    public function porPaciente($codPa)
    {
        try {
            $servicios = Servicio::with(['medico', 'tipoEstudio', 'diagnosticos'])
                ->porPaciente($codPa)
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

    /**
     * Listar servicios por estado
     */
    public function porEstado($estado)
    {
        try {
            $servicios = Servicio::with(['paciente', 'medico', 'tipoEstudio'])
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

    /**
     * Estadísticas de servicios
     */
    public function estadisticas()
    {
        try {
            $hoy = date('Y-m-d');

            $stats = [
                'hoy' => Servicio::whereDate('fechaSol', $hoy)->count(),
                'programados' => Servicio::where('estado', 'Programado')->count(),
                'enProceso' => Servicio::where('estado', 'EnProceso')->count(),
                'atendidos' => Servicio::where('estado', 'Atendido')->whereDate('fechaAten', $hoy)->count(),
                'tiposEstudio' => TipoEstudio::count(),
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

    /**
     * Eliminar servicio
     */
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

            // Eliminar relaciones con diagnósticos
            $servicio->diagnosticos()->detach();

            // Eliminar el servicio
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
