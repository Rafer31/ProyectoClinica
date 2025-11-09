<?php

namespace App\Http\Controllers;

use App\Models\CronogramaAtencion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CronogramaAtencionController extends Controller
{
    /**
     * Listar todos los cronogramas
     */
    public function index()
    {
        try {
            $cronogramas = CronogramaAtencion::with('personal')
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

    /**
     * Mostrar un cronograma específico por fecha
     */
    public function show($fecha)
    {
        try {
            $cronograma = CronogramaAtencion::with('personal')->find($fecha);

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
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fechaCrono' => 'required|date|unique:CronogramaAtencion,fechaCrono',
            'cantDispo' => 'required|integer|min:0',
            'cantFijo' => 'required|integer|min:0',
            'estado' => 'required|in:activo,inactivoPas,inactivoFut',
            'codPer' => 'required|exists:PersonalSalud,codPer'
        ], [
            'fechaCrono.required' => 'La fecha es obligatoria',
            'fechaCrono.unique' => 'Ya existe un cronograma para esta fecha',
            'cantDispo.required' => 'La cantidad disponible es obligatoria',
            'cantFijo.required' => 'La cantidad fija es obligatoria',
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado debe ser: activo, inactivoPas o inactivoFut',
            'codPer.required' => 'El personal es obligatorio',
            'codPer.exists' => 'El personal seleccionado no existe'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cronograma = CronogramaAtencion::create([
                'fechaCrono' => $request->fechaCrono,
                'cantDispo' => $request->cantDispo,
                'cantFijo' => $request->cantFijo,
                'estado' => $request->estado,
                'codPer' => $request->codPer
            ]);

            $cronograma->load('personal');

            return response()->json([
                'success' => true,
                'data' => $cronograma,
                'message' => 'Cronograma registrado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el cronograma: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar cronograma
     */
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
            if ($request->has('cantDispo')) {
                $rules['cantDispo'] = 'integer|min:0';
            }
            if ($request->has('cantFijo')) {
                $rules['cantFijo'] = 'integer|min:0';
            }
            if ($request->has('estado')) {
                $rules['estado'] = 'in:activo,inactivoPas,inactivoFut';
            }
            if ($request->has('codPer')) {
                $rules['codPer'] = 'exists:PersonalSalud,codPer';
            }

            $validator = Validator::make($request->all(), $rules, [
                'estado.in' => 'El estado debe ser: activo, inactivoPas o inactivoFut',
                'codPer.exists' => 'El personal seleccionado no existe'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->has('cantDispo')) {
                $cronograma->cantDispo = $request->cantDispo;
            }
            if ($request->has('cantFijo')) {
                $cronograma->cantFijo = $request->cantFijo;
            }
            if ($request->has('estado')) {
                $cronograma->estado = $request->estado;
            }
            if ($request->has('codPer')) {
                $cronograma->codPer = $request->codPer;
            }

            $cronograma->save();
            $cronograma->load('personal');

            return response()->json([
                'success' => true,
                'data' => $cronograma,
                'message' => 'Cronograma actualizado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el cronograma: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado del cronograma
     */
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

    /**
     * Listar cronogramas activos
     */
    public function activos()
    {
        try {
            $cronogramas = CronogramaAtencion::with('personal')
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

    /**
     * Listar cronogramas por personal
     */
    public function porPersonal($codPer)
    {
        try {
            $cronogramas = CronogramaAtencion::with('personal')
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

    /**
     * Listar cronogramas entre fechas
     */
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
            $cronogramas = CronogramaAtencion::with('personal')
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

    /**
     * Eliminar cronograma
     */
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
