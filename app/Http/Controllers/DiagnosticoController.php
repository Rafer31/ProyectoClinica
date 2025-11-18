<?php

namespace App\Http\Controllers;

use App\Models\Diagnostico;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class DiagnosticoController extends Controller
{
    /**
     * Listar todos los diagnósticos
     */
    public function index()
    {
        try {
            $diagnosticos = Diagnostico::orderBy('descripDiag')->get();

            return response()->json([
                'success' => true,
                'data' => $diagnosticos,
                'message' => 'Diagnósticos obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los diagnósticos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un diagnóstico específico
     */
    public function show($id)
    {
        try {
            $diagnostico = Diagnostico::find($id);

            if (!$diagnostico) {
                return response()->json([
                    'success' => false,
                    'message' => 'Diagnóstico no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $diagnostico,
                'message' => 'Diagnóstico encontrado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el diagnóstico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar nuevo diagnóstico
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripDiag' => 'required|string|max:200'
        ], [
            'descripDiag.required' => 'La descripción es obligatoria'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $diagnostico = Diagnostico::create([
                'descripDiag' => $request->descripDiag
            ]);

            return response()->json([
                'success' => true,
                'data' => $diagnostico,
                'message' => 'Diagnóstico registrado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el diagnóstico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar diagnóstico
     */
    public function update(Request $request, $id)
    {
        try {
            $diagnostico = Diagnostico::find($id);

            if (!$diagnostico) {
                return response()->json([
                    'success' => false,
                    'message' => 'Diagnóstico no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'descripDiag' => 'required|string|max:200'
            ], [
                'descripDiag.required' => 'La descripción es obligatoria'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $diagnostico->descripDiag = $request->descripDiag;
            $diagnostico->save();

            return response()->json([
                'success' => true,
                'data' => $diagnostico,
                'message' => 'Diagnóstico actualizado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el diagnóstico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar diagnóstico
     */
    public function destroy($id)
    {
        try {
            $diagnostico = Diagnostico::find($id);

            if (!$diagnostico) {
                return response()->json([
                    'success' => false,
                    'message' => 'Diagnóstico no encontrado'
                ], 404);
            }

            $diagnostico->delete();

            return response()->json([
                'success' => true,
                'message' => 'Diagnóstico eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el diagnóstico: ' . $e->getMessage()
            ], 500);
        }
    }
}
