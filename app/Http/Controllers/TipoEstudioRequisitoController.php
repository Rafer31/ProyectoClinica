<?php

namespace App\Http\Controllers;

use App\Models\TipoEstudio;
use App\Models\Requisito;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TipoEstudioRequisitoController extends Controller
{
    /**
     * Asignar requisitos a un tipo de estudio
     */
    public function asignarRequisitos(Request $request, $codTest)
    {
        $validator = Validator::make($request->all(), [
            'requisitos' => 'required|array',
            'requisitos.*.codRequisito' => 'required|exists:Requisito,codRequisito',
            'requisitos.*.observacion' => 'nullable|string|max:500'
        ], [
            'requisitos.required' => 'Debe proporcionar al menos un requisito',
            'requisitos.array' => 'Los requisitos deben ser un array',
            'requisitos.*.codRequisito.required' => 'El código de requisito es obligatorio',
            'requisitos.*.codRequisito.exists' => 'El requisito no existe'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tipoEstudio = TipoEstudio::find($codTest);

            if (!$tipoEstudio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de estudio no encontrado'
                ], 404);
            }

            // Preparar datos para la sincronización
            $syncData = [];
            foreach ($request->requisitos as $requisito) {
                $syncData[$requisito['codRequisito']] = [
                    'observacion' => $requisito['observacion'] ?? null
                ];
            }

            // Sincronizar requisitos (elimina los antiguos y agrega los nuevos)
            $tipoEstudio->requisitos()->sync($syncData);

            // Recargar el tipo de estudio con sus requisitos
            $tipoEstudio->load('requisitos');

            return response()->json([
                'success' => true,
                'data' => $tipoEstudio,
                'message' => 'Requisitos asignados exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar requisitos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Agregar un requisito a un tipo de estudio
     */
    public function agregarRequisito(Request $request, $codTest)
    {
        $validator = Validator::make($request->all(), [
            'codRequisito' => 'required|exists:Requisito,codRequisito',
            'observacion' => 'nullable|string|max:500'
        ], [
            'codRequisito.required' => 'El código de requisito es obligatorio',
            'codRequisito.exists' => 'El requisito no existe'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tipoEstudio = TipoEstudio::find($codTest);

            if (!$tipoEstudio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de estudio no encontrado'
                ], 404);
            }

            // Verificar si el requisito ya está asignado
            if ($tipoEstudio->requisitos()->where('codRequisito', $request->codRequisito)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este requisito ya está asignado a este tipo de estudio'
                ], 409);
            }

            // Agregar el requisito
            $tipoEstudio->requisitos()->attach($request->codRequisito, [
                'observacion' => $request->observacion
            ]);

            // Recargar el tipo de estudio con sus requisitos
            $tipoEstudio->load('requisitos');

            return response()->json([
                'success' => true,
                'data' => $tipoEstudio,
                'message' => 'Requisito agregado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar requisito: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar la observación de un requisito de un tipo de estudio
     */
    public function actualizarObservacion(Request $request, $codTest, $codRequisito)
    {
        $validator = Validator::make($request->all(), [
            'observacion' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tipoEstudio = TipoEstudio::find($codTest);

            if (!$tipoEstudio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de estudio no encontrado'
                ], 404);
            }

            // Verificar si el requisito está asignado
            if (!$tipoEstudio->requisitos()->where('codRequisito', $codRequisito)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este requisito no está asignado a este tipo de estudio'
                ], 404);
            }

            // Actualizar la observación
            $tipoEstudio->requisitos()->updateExistingPivot($codRequisito, [
                'observacion' => $request->observacion
            ]);

            // Recargar el tipo de estudio con sus requisitos
            $tipoEstudio->load('requisitos');

            return response()->json([
                'success' => true,
                'data' => $tipoEstudio,
                'message' => 'Observación actualizada exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar observación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un requisito de un tipo de estudio
     */
    public function eliminarRequisito($codTest, $codRequisito)
    {
        try {
            $tipoEstudio = TipoEstudio::find($codTest);

            if (!$tipoEstudio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de estudio no encontrado'
                ], 404);
            }

            // Verificar si el requisito está asignado
            if (!$tipoEstudio->requisitos()->where('codRequisito', $codRequisito)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este requisito no está asignado a este tipo de estudio'
                ], 404);
            }

            // Eliminar el requisito
            $tipoEstudio->requisitos()->detach($codRequisito);

            return response()->json([
                'success' => true,
                'message' => 'Requisito eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar requisito: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar todos los requisitos de un tipo de estudio
     */
    public function listarRequisitos($codTest)
    {
        try {
            $tipoEstudio = TipoEstudio::with('requisitos')->find($codTest);

            if (!$tipoEstudio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de estudio no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $tipoEstudio->requisitos,
                'message' => 'Requisitos obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener requisitos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear tipo de estudio con sus requisitos
     */
    public function crearConRequisitos(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:200',
            'requisitos' => 'required|array',
            'requisitos.*.codRequisito' => 'required|exists:Requisito,codRequisito',
            'requisitos.*.observacion' => 'nullable|string|max:500'
        ], [
            'descripcion.required' => 'La descripción es obligatoria',
            'requisitos.required' => 'Debe proporcionar al menos un requisito',
            'requisitos.array' => 'Los requisitos deben ser un array',
            'requisitos.*.codRequisito.required' => 'El código de requisito es obligatorio',
            'requisitos.*.codRequisito.exists' => 'El requisito no existe'
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
            // Crear el tipo de estudio
            $tipoEstudio = TipoEstudio::create([
                'descripcion' => $request->descripcion
            ]);

            // Preparar datos para asignar requisitos
            $syncData = [];
            foreach ($request->requisitos as $requisito) {
                $syncData[$requisito['codRequisito']] = [
                    'observacion' => $requisito['observacion'] ?? null
                ];
            }

            // Asignar requisitos
            $tipoEstudio->requisitos()->sync($syncData);

            // Recargar el tipo de estudio con sus requisitos
            $tipoEstudio->load('requisitos');

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $tipoEstudio,
                'message' => 'Tipo de estudio creado con requisitos exitosamente'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear tipo de estudio: ' . $e->getMessage()
            ], 500);
        }
    }

}
