<?php

namespace App\Http\Controllers;

use App\Models\PersonalSalud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
class PersonalSaludController extends Controller
{
    /**
     * Listar todo el personal de salud
     */
    public function index()
    {
        try {
            $personal = PersonalSalud::with('rol')
                ->orderBy('codPer', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $personal,
                'message' => 'Personal de salud obtenido correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el personal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un personal específico
     */
    public function show($id)
    {
        try {
            $personal = PersonalSalud::with('rol')->find($id);

            if (!$personal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personal no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $personal,
                'message' => 'Personal encontrado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el personal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar nuevo personal de salud
     */
    public function store(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'usuarioPer' => 'required|string|max:255|unique:PersonalSalud,usuarioPer',
            'clavePer' => 'required|string|min:6',
            'nomPer' => 'required|string|max:255',
            'paternoPer' => 'required|string|max:255',
            'maternoPer' => 'nullable|string|max:255',
            'codRol' => 'required|exists:rol,codRol',
            'estado' => 'sometimes|in:activo,inactivo'
        ], [
            'usuarioPer.required' => 'El usuario es obligatorio',
            'usuarioPer.unique' => 'Este usuario ya está registrado',
            'clavePer.required' => 'La contraseña es obligatoria',
            'clavePer.min' => 'La contraseña debe tener al menos 6 caracteres',
            'nomPer.required' => 'El nombre es obligatorio',
            'paternoPer.required' => 'El apellido paterno es obligatorio',
            'codRol.required' => 'El rol es obligatorio',
            'codRol.exists' => 'El rol seleccionado no existe'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Crear el personal
            $personal = PersonalSalud::create([
                'usuarioPer' => $request->usuarioPer,
                'clavePer' => Hash::make($request->clavePer),
                'nomPer' => $request->nomPer,
                'paternoPer' => $request->paternoPer,
                'maternoPer' => $request->maternoPer,
                'codRol' => $request->codRol,
                'estado' => $request->estado ?? 'activo'
            ]);

            // Cargar la relación rol
            $personal->load('rol');

            return response()->json([
                'success' => true,
                'data' => $personal,
                'message' => 'Personal de salud registrado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el personal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar personal de salud
     */
    public function update(Request $request, $id)
    {
        try {
            $personal = PersonalSalud::find($id);

            if (!$personal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personal no encontrado'
                ], 404);
            }

            // Validación solo de los campos que se envían
            $rules = [];

            if ($request->has('usuarioPer')) {
                $rules['usuarioPer'] = 'string|max:255|unique:PersonalSalud,usuarioPer,' . $id . ',codPer';
            }
            if ($request->has('clavePer')) {
                $rules['clavePer'] = 'string|min:6';
            }
            if ($request->has('nomPer')) {
                $rules['nomPer'] = 'string|max:255';
            }
            if ($request->has('paternoPer')) {
                $rules['paternoPer'] = 'string|max:255';
            }
            if ($request->has('maternoPer')) {
                $rules['maternoPer'] = 'nullable|string|max:255';
            }
            if ($request->has('codRol')) {
                $rules['codRol'] = 'exists:rol,codRol';
            }
            if ($request->has('estado')) {
                $rules['estado'] = 'in:activo,inactivo';
            }

            $validator = Validator::make($request->all(), $rules, [
                'usuarioPer.unique' => 'Este usuario ya está registrado',
                'clavePer.min' => 'La contraseña debe tener al menos 6 caracteres',
                'codRol.exists' => 'El rol seleccionado no existe',
                'estado.in' => 'El estado debe ser activo o inactivo'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualizar solo los campos enviados
            if ($request->has('usuarioPer')) {
                $personal->usuarioPer = $request->usuarioPer;
            }
            if ($request->has('clavePer')) {
                $personal->clavePer = Hash::make($request->clavePer);
            }
            if ($request->has('nomPer')) {
                $personal->nomPer = $request->nomPer;
            }
            if ($request->has('paternoPer')) {
                $personal->paternoPer = $request->paternoPer;
            }
            if ($request->has('maternoPer')) {
                $personal->maternoPer = $request->maternoPer;
            }
            if ($request->has('codRol')) {
                $personal->codRol = $request->codRol;
            }
            if ($request->has('estado')) {
                $personal->estado = $request->estado;
            }

            $personal->save();
            $personal->load('rol');

            return response()->json([
                'success' => true,
                'data' => $personal,
                'message' => 'Personal actualizado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el personal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado del personal (Activar/Inactivar)
     */
    public function cambiarEstado($id)
    {
        try {
            $personal = PersonalSalud::find($id);

            if (!$personal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personal no encontrado'
                ], 404);
            }

            // Cambiar el estado
            $personal->estado = $personal->estado === 'activo' ? 'inactivo' : 'activo';
            $personal->save();

            return response()->json([
                'success' => true,
                'data' => $personal,
                'message' => 'Estado cambiado a ' . $personal->estado . ' exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar solo personal activo
     */
    public function activos()
    {
        try {
            $personal = PersonalSalud::with('rol')
                ->where('estado', 'activo')
                ->orderBy('nomPer')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $personal,
                'message' => 'Personal activo obtenido correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el personal: ' . $e->getMessage()
            ], 500);
        }
    }
}
