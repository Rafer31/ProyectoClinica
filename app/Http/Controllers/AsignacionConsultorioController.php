<?php

namespace App\Http\Controllers;

use App\Models\AsignacionConsultorio;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AsignacionConsultorioController extends Controller
{
    /**
     * Listar todas las asignaciones
     */
    public function index()
    {
        try {
            $asignaciones = AsignacionConsultorio::with(['personal', 'consultorio'])
                ->orderBy('idAsignacion', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $asignaciones,
                'message' => 'Asignaciones obtenidas correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las asignaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar una asignación específica
     */
    public function show($id)
    {
        try {
            $asignacion = AsignacionConsultorio::with(['personal', 'consultorio'])
                ->find($id);

            if (!$asignacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asignación no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $asignacion,
                'message' => 'Asignación encontrada'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la asignación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar nueva asignación
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fechaInicio' => 'required|date',
            'fechaFin' => 'nullable|date|after_or_equal:fechaInicio',
            'codPer' => 'required|exists:PersonalSalud,codPer',
            'codCons' => 'required|exists:Consultorio,codCons',
        ], [
            'fechaInicio.required' => 'La fecha de inicio es obligatoria',
            'fechaInicio.date' => 'La fecha de inicio debe ser una fecha válida',
            'fechaFin.date' => 'La fecha fin debe ser una fecha válida',
            'fechaFin.after_or_equal' => 'La fecha fin debe ser posterior o igual a la fecha de inicio',
            'codPer.required' => 'El personal es obligatorio',
            'codPer.exists' => 'El personal seleccionado no existe',
            'codCons.required' => 'El consultorio es obligatorio',
            'codCons.exists' => 'El consultorio seleccionado no existe',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verificar si el consultorio está disponible en el rango de fechas
            $fechaInicio = $request->fechaInicio;
            $fechaFin = $request->fechaFin;
            $codCons = $request->codCons;

            $conflicto = AsignacionConsultorio::where('codCons', $codCons)
                ->where(function($query) use ($fechaInicio, $fechaFin) {
                    $query->where('fechaInicio', '<=', $fechaFin ?: '9999-12-31')
                          ->where(function($sq) use ($fechaInicio) {
                              $sq->where('fechaFin', '>=', $fechaInicio)
                                ->orWhereNull('fechaFin');
                          });
                })
                ->exists();

            if ($conflicto) {
                return response()->json([
                    'success' => false,
                    'message' => 'El consultorio ya está asignado en ese rango de fechas'
                ], 409);
            }

            // Verificar si el personal ya tiene una asignación activa en ese rango
            $personalConflicto = AsignacionConsultorio::where('codPer', $request->codPer)
                ->where(function($query) use ($fechaInicio, $fechaFin) {
                    $query->where('fechaInicio', '<=', $fechaFin ?: '9999-12-31')
                          ->where(function($sq) use ($fechaInicio) {
                              $sq->where('fechaFin', '>=', $fechaInicio)
                                ->orWhereNull('fechaFin');
                          });
                })
                ->exists();

            if ($personalConflicto) {
                return response()->json([
                    'success' => false,
                    'message' => 'El personal ya tiene un consultorio asignado en ese rango de fechas'
                ], 409);
            }

            $asignacion = AsignacionConsultorio::create($request->all());
            $asignacion->load(['personal', 'consultorio']);

            return response()->json([
                'success' => true,
                'data' => $asignacion,
                'message' => 'Consultorio asignado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la asignación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar asignación
     */
    public function update(Request $request, $id)
    {
        try {
            $asignacion = AsignacionConsultorio::find($id);

            if (!$asignacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asignación no encontrada'
                ], 404);
            }

            $rules = [];
            if ($request->has('fechaInicio')) $rules['fechaInicio'] = 'date';
            if ($request->has('fechaFin')) $rules['fechaFin'] = 'nullable|date|after_or_equal:fechaInicio';
            if ($request->has('codPer')) $rules['codPer'] = 'exists:PersonalSalud,codPer';
            if ($request->has('codCons')) $rules['codCons'] = 'exists:Consultorio,codCons';

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verificar conflictos si se están cambiando fechas o consultorio
            if ($request->has('fechaInicio') || $request->has('fechaFin') || $request->has('codCons')) {
                $fechaInicio = $request->fechaInicio ?? $asignacion->fechaInicio;
                $fechaFin = $request->fechaFin ?? $asignacion->fechaFin;
                $codCons = $request->codCons ?? $asignacion->codCons;

                $conflicto = AsignacionConsultorio::where('codCons', $codCons)
                    ->where('idAsignacion', '!=', $id)
                    ->where(function($query) use ($fechaInicio, $fechaFin) {
                        $query->where('fechaInicio', '<=', $fechaFin ?: '9999-12-31')
                              ->where(function($sq) use ($fechaInicio) {
                                  $sq->where('fechaFin', '>=', $fechaInicio)
                                    ->orWhereNull('fechaFin');
                              });
                    })
                    ->exists();

                if ($conflicto) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El consultorio ya está asignado en ese rango de fechas'
                    ], 409);
                }
            }

            $asignacion->fill($request->only(['fechaInicio', 'fechaFin', 'codPer', 'codCons']));
            $asignacion->save();
            $asignacion->load(['personal', 'consultorio']);

            return response()->json([
                'success' => true,
                'data' => $asignacion,
                'message' => 'Asignación actualizada exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la asignación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar asignaciones activas (aquellas que están vigentes hoy o sin fecha fin)
     */
    public function activas()
{
    try {
        $hoy = now()->toDateString();

        $asignaciones = AsignacionConsultorio::with(['personal', 'consultorio']) // IMPORTANTE: Incluir consultorio
            ->where('fechaInicio', '<=', $hoy)
            ->where(function($query) use ($hoy) {
                $query->where('fechaFin', '>=', $hoy)
                      ->orWhereNull('fechaFin');
            })
            ->get();

        // Debug: verificar que las relaciones están cargadas
        foreach ($asignaciones as $asignacion) {
            \Log::info('Asignación activa', [
                'codPer' => $asignacion->codPer,
                'codCons' => $asignacion->codCons,
                'consultorio' => $asignacion->consultorio ? $asignacion->consultorio->numCons : 'NO CARGADO',
                'fechaInicio' => $asignacion->fechaInicio,
                'fechaFin' => $asignacion->fechaFin
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $asignaciones,
            'message' => 'Asignaciones activas obtenidas correctamente'
        ], 200);
    } catch (\Exception $e) {
        \Log::error('Error al obtener asignaciones activas: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener las asignaciones: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Listar asignaciones por personal
     */
    public function porPersonal($codPer)
    {
        try {
            $asignaciones = AsignacionConsultorio::with(['consultorio'])
                ->where('codPer', $codPer)
                ->orderBy('fechaInicio', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $asignaciones,
                'message' => 'Asignaciones del personal obtenidas correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las asignaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar asignaciones por consultorio
     */
    public function porConsultorio($codCons)
    {
        try {
            $asignaciones = AsignacionConsultorio::with(['personal'])
                ->where('codCons', $codCons)
                ->orderBy('fechaInicio', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $asignaciones,
                'message' => 'Asignaciones del consultorio obtenidas correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las asignaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar asignación
     */
    public function destroy($id)
    {
        try {
            $asignacion = AsignacionConsultorio::find($id);

            if (!$asignacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asignación no encontrada'
                ], 404);
            }

            $asignacion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Asignación eliminada exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la asignación: ' . $e->getMessage()
            ], 500);
        }
    }
}
