<?php

namespace App\Http\Controllers;

use App\Models\Consultorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConsultorioController extends Controller
{
    /**
     * Listar todos los consultorios
     */
    public function index()
    {
        try {
            $consultorios = Consultorio::orderBy('numCons')->get();

            return response()->json([
                'success' => true,
                'data' => $consultorios,
                'message' => 'Consultorios obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los consultorios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un consultorio específico
     */
    public function show($id)
    {
        try {
            $consultorio = Consultorio::find($id);

            if (!$consultorio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Consultorio no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $consultorio,
                'message' => 'Consultorio encontrado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el consultorio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar nuevo consultorio
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numCons' => 'required|string|max:50'
        ], [
            'numCons.required' => 'El número de consultorio es obligatorio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $consultorio = Consultorio::create([
                'numCons' => $request->numCons
            ]);

            return response()->json([
                'success' => true,
                'data' => $consultorio,
                'message' => 'Consultorio registrado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el consultorio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar consultorio
     */
    public function update(Request $request, $id)
    {
        try {
            $consultorio = Consultorio::find($id);

            if (!$consultorio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Consultorio no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'numCons' => 'required|string|max:50'
            ], [
                'numCons.required' => 'El número de consultorio es obligatorio'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $consultorio->numCons = $request->numCons;
            $consultorio->save();

            return response()->json([
                'success' => true,
                'data' => $consultorio,
                'message' => 'Consultorio actualizado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el consultorio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar consultorio
     */
    public function destroy($id)
    {
        try {
            $consultorio = Consultorio::find($id);

            if (!$consultorio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Consultorio no encontrado'
                ], 404);
            }

            $consultorio->delete();

            return response()->json([
                'success' => true,
                'message' => 'Consultorio eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el consultorio: ' . $e->getMessage()
            ], 500);
        }
    }
}
