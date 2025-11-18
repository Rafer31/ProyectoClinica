<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
class PacienteController extends Controller
{
    /**
     * Listar todos los pacientes
     */
    public function index()
    {
        try {
            $pacientes = Paciente::orderBy('codPa', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $pacientes,
                'message' => 'Pacientes obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los pacientes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un paciente específico
     */
    public function show($id)
    {
        try {
            $paciente = Paciente::find($id);

            if (!$paciente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $paciente,
                'message' => 'Paciente encontrado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el paciente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar nuevo paciente
     */
    public function store(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'nomPa' => 'required|string|max:100',
            'paternoPa' => 'nullable|string|max:100',
            'maternoPa' => 'nullable|string|max:100',
            'estado' => 'sometimes|in:activo,inactivo',
            'fechaNac' => 'nullable|date|before:today',
            'sexo' => 'required|in:M,F',
            'nroHCI' => 'nullable|string|max:50|unique:Paciente,nroHCI',
            'tipoPac' => 'required|in:SUS,SINSUS'
        ], [
            'nomPa.required' => 'El nombre es obligatorio',
            'sexo.required' => 'El sexo es obligatorio',
            'sexo.in' => 'El sexo debe ser M o F',
            'fechaNac.date' => 'La fecha de nacimiento no es válida',
            'fechaNac.before' => 'La fecha de nacimiento debe ser anterior a hoy',
            'nroHCI.unique' => 'Este número de HCI ya está registrado',
            'tipoPac.required' => 'El tipo de paciente es obligatorio',
            'tipoPac.in' => 'El tipo de paciente debe ser SUS o SINSUS'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Crear el paciente
            $paciente = Paciente::create([
                'nomPa' => $request->nomPa,
                'paternoPa' => $request->paternoPa,
                'maternoPa' => $request->maternoPa,
                'estado' => $request->estado ?? 'activo',
                'fechaNac' => $request->fechaNac,
                'sexo' => $request->sexo,
                'nroHCI' => $request->nroHCI,
                'tipoPac' => $request->tipoPac
            ]);

            return response()->json([
                'success' => true,
                'data' => $paciente,
                'message' => 'Paciente registrado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el paciente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar paciente
     */
    public function update(Request $request, $id)
    {
        try {
            $paciente = Paciente::find($id);

            if (!$paciente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ], 404);
            }

            // Validación solo de los campos que se envían
            $rules = [];

            if ($request->has('nomPa')) {
                $rules['nomPa'] = 'string|max:100';
            }
            if ($request->has('paternoPa')) {
                $rules['paternoPa'] = 'nullable|string|max:100';
            }
            if ($request->has('maternoPa')) {
                $rules['maternoPa'] = 'nullable|string|max:100';
            }
            if ($request->has('estado')) {
                $rules['estado'] = 'in:activo,inactivo';
            }
            if ($request->has('fechaNac')) {
                $rules['fechaNac'] = 'date|before:today';
            }
            if ($request->has('sexo')) {
                $rules['sexo'] = 'in:M,F';
            }
            if ($request->has('nroHCI')) {
                $rules['nroHCI'] = 'string|max:50|unique:Paciente,nroHCI,' . $id . ',codPa';
            }
            if ($request->has('tipoPac')) {
                $rules['tipoPac'] = 'in:SUS,SINSUS';
            }

            $validator = Validator::make($request->all(), $rules, [
                'sexo.in' => 'El sexo debe ser M o F',
                'fechaNac.date' => 'La fecha de nacimiento no es válida',
                'fechaNac.before' => 'La fecha de nacimiento debe ser anterior a hoy',
                'nroHCI.unique' => 'Este número de HCI ya está registrado',
                'tipoPac.in' => 'El tipo de paciente debe ser SUS o SINSUS',
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
            if ($request->has('nomPa')) {
                $paciente->nomPa = $request->nomPa;
            }
            if ($request->has('paternoPa')) {
                $paciente->paternoPa = $request->paternoPa;
            }
            if ($request->has('maternoPa')) {
                $paciente->maternoPa = $request->maternoPa;
            }
            if ($request->has('estado')) {
                $paciente->estado = $request->estado;
            }
            if ($request->has('fechaNac')) {
                $paciente->fechaNac = $request->fechaNac;
            }
            if ($request->has('sexo')) {
                $paciente->sexo = $request->sexo;
            }
            if ($request->has('nroHCI')) {
                $paciente->nroHCI = $request->nroHCI;
            }
            if ($request->has('tipoPac')) {
                $paciente->tipoPac = $request->tipoPac;
            }

            $paciente->save();

            return response()->json([
                'success' => true,
                'data' => $paciente,
                'message' => 'Paciente actualizado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el paciente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado del paciente (Activar/Inactivar)
     */
    public function cambiarEstado($id)
    {
        try {
            $paciente = Paciente::find($id);

            if (!$paciente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ], 404);
            }

            // Cambiar el estado
            $paciente->estado = $paciente->estado === 'activo' ? 'inactivo' : 'activo';
            $paciente->save();

            return response()->json([
                'success' => true,
                'data' => $paciente,
                'message' => 'Estado cambiado a ' . $paciente->estado . ' exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar solo pacientes activos
     */
    public function activos()
    {
        try {
            $pacientes = Paciente::activos()
                ->orderBy('nomPa')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $pacientes,
                'message' => 'Pacientes activos obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los pacientes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar pacientes por tipo (SUS/SINSUS)
     */
    public function porTipo($tipo)
    {
        try {
            if (!in_array($tipo, ['SUS', 'SINSUS'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de paciente inválido. Use SUS o SINSUS'
                ], 400);
            }

            $pacientes = Paciente::porTipo($tipo)
                ->orderBy('nomPa')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $pacientes,
                'message' => "Pacientes tipo {$tipo} obtenidos correctamente"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los pacientes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar paciente por HCI
     */
    public function buscarPorHCI($nroHCI)
    {
        try {
            $paciente = Paciente::where('nroHCI', $nroHCI)->first();

            if (!$paciente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paciente no encontrado con ese número de HCI'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $paciente,
                'message' => 'Paciente encontrado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar el paciente: ' . $e->getMessage()
            ], 500);
        }
    }
}
