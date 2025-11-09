<?php

namespace App\Http\Controllers;

use App\Models\TipoEstudio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipoEstudioController extends Controller
{
    /**
     * Listar todos los tipos de estudio
     */
    public function index()
    {
        try {
            $tiposEstudio = TipoEstudio::with('requisitos')->orderBy('descripcion')->get();

            return response()->json([
                'success' => true,
                'data' => $tiposEstudio,
                'message' => 'Tipos de estudio obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los tipos de estudio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un tipo de estudio específico
     */
    public function show($id)
    {
        try {
            $tipoEstudio = TipoEstudio::with('requisitos')->find($id);

            if (!$tipoEstudio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de estudio no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $tipoEstudio,
                'message' => 'Tipo de estudio encontrado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el tipo de estudio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar nuevo tipo de estudio
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:200'
        ], [
            'descripcion.required' => 'La descripción es obligatoria'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tipoEstudio = TipoEstudio::create([
                'descripcion' => $request->descripcion
            ]);

            return response()->json([
                'success' => true,
                'data' => $tipoEstudio,
                'message' => 'Tipo de estudio registrado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el tipo de estudio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar tipo de estudio
     */
    public function update(Request $request, $id)
    {
        try {
            $tipoEstudio = TipoEstudio::find($id);

            if (!$tipoEstudio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de estudio no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'descripcion' => 'required|string|max:200'
            ], [
                'descripcion.required' => 'La descripción es obligatoria'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $tipoEstudio->descripcion = $request->descripcion;
            $tipoEstudio->save();

            return response()->json([
                'success' => true,
                'data' => $tipoEstudio,
                'message' => 'Tipo de estudio actualizado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el tipo de estudio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar tipo de estudio
     */
    public function destroy($id)
    {
        try {
            $tipoEstudio = TipoEstudio::find($id);

            if (!$tipoEstudio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de estudio no encontrado'
                ], 404);
            }

            $tipoEstudio->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de estudio eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tipo de estudio: ' . $e->getMessage()
            ], 500);
        }
    }
}
