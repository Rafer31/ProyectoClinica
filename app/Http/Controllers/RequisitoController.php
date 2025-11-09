<?php

namespace App\Http\Controllers;

use App\Models\Requisito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequisitoController extends Controller
{
    /**
     * Listar todos los requisitos
     */
    public function index()
    {
        try {
            $requisitos = Requisito::orderBy('descripRequisito')->get();

            return response()->json([
                'success' => true,
                'data' => $requisitos,
                'message' => 'Requisitos obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los requisitos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un requisito específico
     */
    public function show($id)
    {
        try {
            $requisito = Requisito::find($id);

            if (!$requisito) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requisito no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $requisito,
                'message' => 'Requisito encontrado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el requisito: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar nuevo requisito
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripRequisito' => 'required|string|max:200'
        ], [
            'descripRequisito.required' => 'La descripción es obligatoria'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $requisito = Requisito::create([
                'descripRequisito' => $request->descripRequisito
            ]);

            return response()->json([
                'success' => true,
                'data' => $requisito,
                'message' => 'Requisito registrado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el requisito: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar requisito
     */
    public function update(Request $request, $id)
    {
        try {
            $requisito = Requisito::find($id);

            if (!$requisito) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requisito no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'descripRequisito' => 'required|string|max:200'
            ], [
                'descripRequisito.required' => 'La descripción es obligatoria'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $requisito->descripRequisito = $request->descripRequisito;
            $requisito->save();

            return response()->json([
                'success' => true,
                'data' => $requisito,
                'message' => 'Requisito actualizado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el requisito: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar requisito
     */
    public function destroy($id)
    {
        try {
            $requisito = Requisito::find($id);

            if (!$requisito) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requisito no encontrado'
                ], 404);
            }

            $requisito->delete();

            return response()->json([
                'success' => true,
                'message' => 'Requisito eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el requisito: ' . $e->getMessage()
            ], 500);
        }
    }
}
