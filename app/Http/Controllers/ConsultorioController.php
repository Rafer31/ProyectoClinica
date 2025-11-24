<?php

namespace App\Http\Controllers;

use App\Models\Consultorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ConsultorioController extends Controller
{
    /**
     * Listar todos los consultorios
     */
    public function index()
    {
        try {
            $consultorios = Consultorio::orderBy('numCons', 'asc')->get();

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
            'numCons' => 'required|integer|unique:Consultorio,numCons'
        ], [
            'numCons.required' => 'El número de consultorio es obligatorio',
            'numCons.integer' => 'El número de consultorio debe ser un número entero',
            'numCons.unique' => 'Este número de consultorio ya está registrado'
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
                'numCons' => 'integer|unique:Consultorio,numCons,' . $id . ',codCons'
            ], [
                'numCons.integer' => 'El número de consultorio debe ser un número entero',
                'numCons.unique' => 'Este número de consultorio ya está registrado'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->has('numCons')) {
                $consultorio->numCons = $request->numCons;
            }

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

            // Verificar si tiene asignaciones
            $tieneAsignaciones = $consultorio->asignaciones()->exists();

            if ($tieneAsignaciones) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el consultorio porque tiene asignaciones registradas'
                ], 409);
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

    /**
     * Listar consultorios disponibles en un rango de fechas
     * Si no se proporcionan fechas, devuelve consultorios sin asignaciones activas indefinidas
     */
    public function disponibles(Request $request)
    {
        try {
            $fechaInicio = $request->query('fechaInicio');
            $fechaFin = $request->query('fechaFin');
            $codPerExcluir = $request->query('codPer'); // Para excluir al personal actual al editar

            $query = Consultorio::whereDoesntHave('asignaciones', function($q) use ($fechaInicio, $fechaFin, $codPerExcluir) {
                // Excluir asignaciones del personal actual si se está editando
                if ($codPerExcluir) {
                    $q->where('codPer', '!=', $codPerExcluir);
                }

                if ($fechaInicio && $fechaFin) {
                    // Verificar conflictos de fechas
                    $q->where(function($query) use ($fechaInicio, $fechaFin) {
                        $query->where(function($q) use ($fechaInicio, $fechaFin) {
                            // Asignaciones que se solapan con el rango solicitado
                            $q->where('fechaInicio', '<=', $fechaFin)
                              ->where(function($sq) use ($fechaInicio) {
                                  $sq->where('fechaFin', '>=', $fechaInicio)
                                    ->orWhereNull('fechaFin');
                              });
                        });
                    });
                } elseif ($fechaInicio) {
                    // Solo fecha inicio proporcionada (asignación sin fin)
                    $q->where(function($query) use ($fechaInicio) {
                        $query->whereNull('fechaFin')
                              ->orWhere('fechaFin', '>=', $fechaInicio);
                    })
                    ->where('fechaInicio', '<=', $fechaInicio);
                } else {
                    // Sin fechas: excluir consultorios con asignaciones activas indefinidas
                    $q->where(function($query) {
                        $query->whereNull('fechaFin')
                              ->orWhere('fechaFin', '>=', now());
                    });
                }
            });

            $consultorios = $query->orderBy('numCons', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $consultorios,
                'message' => 'Consultorios disponibles obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los consultorios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si un consultorio está disponible en un rango de fechas
     */
    public function verificarDisponibilidad(Request $request, $codCons)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fechaInicio' => 'required|date',
                'fechaFin' => 'nullable|date|after_or_equal:fechaInicio',
                'codPer' => 'nullable|exists:PersonalSalud,codPer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $fechaInicio = $request->fechaInicio;
            $fechaFin = $request->fechaFin;
            $codPerExcluir = $request->codPer;

            $consultorio = Consultorio::find($codCons);

            if (!$consultorio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Consultorio no encontrado'
                ], 404);
            }

            // Buscar conflictos
            $conflicto = $consultorio->asignaciones()
                ->when($codPerExcluir, function($q) use ($codPerExcluir) {
                    $q->where('codPer', '!=', $codPerExcluir);
                })
                ->where(function($query) use ($fechaInicio, $fechaFin) {
                    $query->where('fechaInicio', '<=', $fechaFin ?: '9999-12-31')
                          ->where(function($sq) use ($fechaInicio) {
                              $sq->where('fechaFin', '>=', $fechaInicio)
                                ->orWhereNull('fechaFin');
                          });
                })
                ->exists();

            return response()->json([
                'success' => true,
                'disponible' => !$conflicto,
                'message' => $conflicto ? 'El consultorio no está disponible en ese rango de fechas' : 'El consultorio está disponible'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar disponibilidad: ' . $e->getMessage()
            ], 500);
        }
    }
}
