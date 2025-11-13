<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicoController extends Controller
{
    /**
     * Listar todos los médicos
     */
    public function index()
    {
        try {
            $medicos = Medico::orderBy('nomMed')->get();

            return response()->json([
                'success' => true,
                'data' => $medicos,
                'message' => 'Médicos obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los médicos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un médico específico
     */
    public function show($id)
    {
        try {
            $medico = Medico::find($id);

            if (!$medico) {
                return response()->json([
                    'success' => false,
                    'message' => 'Médico no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $medico,
                'message' => 'Médico encontrado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el médico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar nuevo médico
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomMed' => 'required|string|max:100',
            'paternoMed' => 'required|string|max:100',
            'tipoMed' => 'required|in:Interno,Externo'
        ], [
            'nomMed.required' => 'El nombre es obligatorio',
            'paternoMed.required' => 'El apellido es obligatorio',
            'tipoMed.required' => 'El tipo de médico es obligatorio',
            'tipoMed.in' => 'El tipo de médico debe ser Interno o Externo'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $medico = Medico::create([
                'nomMed' => $request->nomMed,
                'paternoMed' => $request->paternoMed,
                'tipoMed' => $request->tipoMed
            ]);

            return response()->json([
                'success' => true,
                'data' => $medico,
                'message' => 'Médico registrado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el médico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar médico
     */
    public function update(Request $request, $id)
    {
        try {
            $medico = Medico::find($id);

            if (!$medico) {
                return response()->json([
                    'success' => false,
                    'message' => 'Médico no encontrado'
                ], 404);
            }

            $rules = [];
            if ($request->has('nomMed')) {
                $rules['nomMed'] = 'string|max:100';
            }
            if ($request->has('paternoMed')) {
                $rules['paternoMed'] = 'string|max:100';
            }
            if ($request->has('tipoMed')) {
                $rules['tipoMed'] = 'in:Interno,Externo';
            }

            $validator = Validator::make($request->all(), $rules, [
                'tipoMed.in' => 'El tipo de médico debe ser Interno o Externo'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->has('nomMed')) {
                $medico->nomMed = $request->nomMed;
            }
            if ($request->has('paternoMed')) {
                $medico->paternoMed = $request->paternoMed;
            }
            if ($request->has('tipoMed')) {
                $medico->tipoMed = $request->tipoMed;
            }

            $medico->save();

            return response()->json([
                'success' => true,
                'data' => $medico,
                'message' => 'Médico actualizado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el médico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar médico
     */
    public function destroy($id)
    {
        try {
            $medico = Medico::find($id);

            if (!$medico) {
                return response()->json([
                    'success' => false,
                    'message' => 'Médico no encontrado'
                ], 404);
            }

            $medico->delete();

            return response()->json([
                'success' => true,
                'message' => 'Médico eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el médico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar médicos por tipo
     */
    public function porTipo($tipo)
    {
        try {
            $medicos = Medico::porTipo($tipo)->orderBy('nomMed')->get();

            return response()->json([
                'success' => true,
                'data' => $medicos,
                'message' => "Médicos tipo {$tipo} obtenidos correctamente"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los médicos: ' . $e->getMessage()
            ], 500);
        }
    }

}
