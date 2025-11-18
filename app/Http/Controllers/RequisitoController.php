<?php

namespace App\Http\Controllers;

use App\Models\Requisito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
class RequisitoController extends Controller
{
    /**
     * Mostrar la vista de gestión de requisitos
     */
    public function indexView()
    {
        return view('personal.tipos-estudio.requisitos');
    }

    /**
     * API: Listar todos los requisitos
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
     * API: Mostrar un requisito específico
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
     * API: Registrar nuevo requisito
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripRequisito' => 'required|string|max:200|unique:Requisito,descripRequisito'
        ], [
            'descripRequisito.required' => 'La descripción es obligatoria',
            'descripRequisito.unique' => 'Ya existe un requisito con esta descripción'
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
     * API: Actualizar requisito
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
                'descripRequisito' => 'required|string|max:200|unique:Requisito,descripRequisito,' . $id . ',codRequisito'
            ], [
                'descripRequisito.required' => 'La descripción es obligatoria',
                'descripRequisito.unique' => 'Ya existe un requisito con esta descripción'
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
     * API: Eliminar requisito
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

            // Verificar si el requisito está siendo usado por algún tipo de estudio
            $enUso = $requisito->tiposEstudio()->count();

            if ($enUso > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "No se puede eliminar este requisito porque está siendo usado por {$enUso} tipo(s) de estudio"
                ], 409);
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
