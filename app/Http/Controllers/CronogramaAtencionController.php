<?php

namespace App\Http\Controllers;

use App\Models\CronogramaAtencion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CronogramaAtencionController extends Controller
{
    public function index()
    {
        try {
            $cronogramas = CronogramaAtencion::with('personalSalud')
                ->orderBy('fechaCrono', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cronogramas,
                'message' => 'Cronogramas obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los cronogramas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($fecha)
    {
        try {
            $cronograma = CronogramaAtencion::with('personalSalud')->find($fecha);

            if (!$cronograma) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cronograma no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $cronograma,
                'message' => 'Cronograma encontrado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el cronograma: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar nuevo cronograma
     * VALIDACIÓN: No permite crear cronogramas en fechas pasadas
     */
    public function store(Request $request)
    {
        // Validación inicial
        $validator = Validator::make($request->all(), [
            'fechaCrono' => 'required|date|after_or_equal:today',
            'cantFijo' => 'required|integer|min:0',
            'cantEmergencia' => 'nullable|integer|min:0',
        ], [
            'fechaCrono.required' => 'La fecha es obligatoria',
            'fechaCrono.date' => 'La fecha no es válida',
            'fechaCrono.after_or_equal' => 'No se puede crear un cronograma para una fecha pasada',
            'cantFijo.required' => 'La cantidad fija es obligatoria',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            if (!$user || !$user->codPer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado o sin código de personal'
                ], 401);
            }

            $codPer = $user->codPer;
            $fechaCrono = $request->fechaCrono;

            // VALIDACIÓN ADICIONAL: Verificar que no sea fecha pasada
            $fechaSeleccionada = new \DateTime($fechaCrono);
            $hoy = new \DateTime(date('Y-m-d'));

            if ($fechaSeleccionada < $hoy) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ No se puede crear un cronograma para una fecha pasada'
                ], 422);
            }

            // Verificar si ya existe un cronograma para esta fecha
            $existe = CronogramaAtencion::where('fechaCrono', $fechaCrono)->exists();
            if ($existe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un cronograma para esta fecha'
                ], 422);
            }

            // Calcular el estado automáticamente según la fecha
            $hoy = date('Y-m-d');

            if ($fechaCrono == $hoy) {
                $estado = 'activo';
            } elseif ($fechaCrono > $hoy) {
                $estado = 'inactivoFut';
            } else {
                // Esto nunca debería ocurrir por la validación anterior
                $estado = 'inactivoPas';
            }

            // Calcular cantDispo automáticamente
            $cantFijo = $request->cantFijo;
            $cantEmergencia = $request->cantEmergencia ?? 0;
            $cantDispo = $cantFijo + $cantEmergencia;

            // Crear el cronograma
            $cronograma = CronogramaAtencion::create([
                'fechaCrono' => $fechaCrono,
                'cantDispo' => $cantDispo,
                'cantFijo' => $cantFijo,
                'cantEmergencia' => $cantEmergencia > 0 ? $cantEmergencia : null,
                'estado' => $estado,
                'codPer' => $codPer
            ]);

            $cronograma->load('personalSalud');

            return response()->json([
                'success' => true,
                'data' => $cronograma,
                'message' => "Cronograma registrado exitosamente con estado: {$estado}",
                'debug' => [
                    'cantFijo' => $cantFijo,
                    'cantEmergencia' => $cantEmergencia,
                    'cantDispo' => $cantDispo
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el cronograma: ' . $e->getMessage(),
                'debug' => [
                    'fecha' => $request->fechaCrono,
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    public function update(Request $request, $fecha)
    {
        try {
            $cronograma = CronogramaAtencion::find($fecha);

            if (!$cronograma) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cronograma no encontrado'
                ], 404);
            }

            $rules = [];
            if ($request->has('cantFijo')) {
                $rules['cantFijo'] = 'integer|min:0';
            }
            if ($request->has('cantEmergencia')) {
                $rules['cantEmergencia'] = 'nullable|integer|min:0';
            }
            if ($request->has('estado')) {
                $rules['estado'] = 'in:activo,inactivoPas,inactivoFut';
            }

            $validator = Validator::make($request->all(), $rules, [
                'estado.in' => 'El estado debe ser: activo, inactivoPas o inactivoFut',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->has('cantFijo')) {
                $cronograma->cantFijo = $request->cantFijo;
            }
            if ($request->has('cantEmergencia')) {
                $cronograma->cantEmergencia = $request->cantEmergencia;
            }

            // Recalcular cantDispo automáticamente
            $cantEmergencia = $cronograma->cantEmergencia ?? 0;
            $cronograma->cantDispo = $cronograma->cantFijo + $cantEmergencia;

            if ($request->has('estado')) {
                $cronograma->estado = $request->estado;
            }

            $cronograma->save();
            $cronograma->load('personalSalud');

            return response()->json([
                'success' => true,
                'data' => $cronograma,
                'message' => 'Cronograma actualizado exitosamente',
                'debug' => [
                    'cantFijo' => $cronograma->cantFijo,
                    'cantEmergencia' => $cronograma->cantEmergencia,
                    'cantDispo' => $cronograma->cantDispo
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el cronograma: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cambiarEstado(Request $request, $fecha)
    {
        try {
            $cronograma = CronogramaAtencion::find($fecha);

            if (!$cronograma) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cronograma no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'estado' => 'required|in:activo,inactivoPas,inactivoFut'
            ], [
                'estado.required' => 'El nuevo estado es obligatorio',
                'estado.in' => 'El estado debe ser: activo, inactivoPas o inactivoFut'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $cronograma->estado = $request->estado;
            $cronograma->save();

            return response()->json([
                'success' => true,
                'data' => $cronograma,
                'message' => "Estado cambiado a {$request->estado} exitosamente"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function activos()
    {
        try {
            $cronogramas = CronogramaAtencion::with('personalSalud')
                ->activos()
                ->orderBy('fechaCrono')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cronogramas,
                'message' => 'Cronogramas activos obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los cronogramas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function porPersonal($codPer)
    {
        try {
            $cronogramas = CronogramaAtencion::with('personalSalud')
                ->porPersonal($codPer)
                ->orderBy('fechaCrono', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cronogramas,
                'message' => 'Cronogramas del personal obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los cronogramas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function entreFechas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|date|after_or_equal:fechaInicio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cronogramas = CronogramaAtencion::with('personalSalud')
                ->entreFechas($request->fechaInicio, $request->fechaFin)
                ->orderBy('fechaCrono')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cronogramas,
                'message' => 'Cronogramas obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los cronogramas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($fecha)
    {
        try {
            $cronograma = CronogramaAtencion::find($fecha);

            if (!$cronograma) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cronograma no encontrado'
                ], 404);
            }

            $cronograma->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cronograma eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el cronograma: ' . $e->getMessage()
            ], 500);
        }
    }
}
