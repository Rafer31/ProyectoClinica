<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;

class RolController extends Controller
{
    /**
     * Listar todos los roles
     */
    public function index()
    {
        try {
            $roles = Rol::orderBy('nombreRol', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $roles,
                'message' => 'Roles obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los roles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un rol específico
     */
    public function show($id)
    {
        try {
            $rol = Rol::find($id);

            if (!$rol) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rol no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $rol,
                'message' => 'Rol encontrado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el rol: ' . $e->getMessage()
            ], 500);
        }
    }
}