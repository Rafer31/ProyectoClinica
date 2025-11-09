<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServicioController extends Controller
{
    /**
     * Listar todos los servicios
     */
    public function index()
    {
        try {
            $servicios = Servicio::with(['paciente', 'medico', 'tipoEstudio', 'cronograma', 'diagnosticos'])
                ->orderBy('codServ', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $servicios,
                'message' => 'Servicios obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los servicios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un servicio específico
     */
    public function show($id)
    {
        try {
            $servicio = Servicio::with(['paciente', 'medico', 'tipoEstudio', 'cronograma', 'diagnosticos'])
                ->find($id);

            if (!$servicio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servicio no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $servicio,
                'message' => 'Servicio encontrado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el servicio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar nuevo servicio
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fechaSol' => 'required|date',
            'horaSol' => 'required',
            'nroServ' => 'nullable|string|max:50|unique:Servicio,nroServ',
            'fechaAten' => 'nullable|date',
            'horaAten' => 'nullable',
            'fechaEnt' => 'nullable|date',
            'horaEnt' => 'nullable',
            'tipoSeg' => 'required|in:AsegEmergencia,AsegRegular,NoAsegEmergencia,NoAsegRegular',
            'nroFicha' => 'nullable|string|max:50',
            'estado' => 'required|in:Programado,Atendido,Entregado,EnProceso',
            'codPa' => 'required|exists:Paciente,codPa',
            'codMed' => 'required|exists:Medico,codMed',
            'codTest' => 'required|exists:TipoEstudio,codTest',
            'fechaCrono' => 'required|exists:CronogramaAtencion,fechaCrono'
        ], [
            'fechaSol.required' => 'La fecha de solicitud es obligatoria',
            'horaSol.required' => 'La hora de solicitud es obligatoria',
            'nroServ.unique' => 'El número de servicio ya existe',
            'tipoSeg.required' => 'El tipo de seguro es obligatorio',
            'tipoSeg.in' => 'El tipo de seguro debe ser: AsegEmergencia, AsegRegular, NoAsegEmergencia o NoAsegRegular',
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado debe ser: Programado, Atendido, Entregado o EnProceso',
            'codPa.required' => 'El paciente es obligatorio',
            'codPa.exists' => 'El paciente seleccionado no existe',
            'codMed.required' => 'El médico es obligatorio',
            'codMed.exists' => 'El médico seleccionado no existe',
            'codTest.required' => 'El tipo de estudio es obligatorio',
            'codTest.exists' => 'El tipo de estudio seleccionado no existe',
            'fechaCrono.required' => 'La fecha del cronograma es obligatoria',
            'fechaCrono.exists' => 'El cronograma seleccionado no existe'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $servicio = Servicio::create($request->all());
            $servicio->load(['paciente', 'medico', 'tipoEstudio', 'cronograma']);

            return response()->json([
                'success' => true,
                'data' => $servicio,
                'message' => 'Servicio registrado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el servicio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar servicio
     */
    public function update(Request $request, $id)
    {
        try {
            $servicio = Servicio::find($id);

            if (!$servicio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servicio no encontrado'
                ], 404);
            }

            $rules = [];
            if ($request->has('fechaSol')) $rules['fechaSol'] = 'date';
            if ($request->has('nroServ')) $rules['nroServ'] = 'string|max:50|unique:Servicio,nroServ,' . $id . ',codServ';
            if ($request->has('fechaAten')) $rules['fechaAten'] = 'date';
            if ($request->has('fechaEnt')) $rules['fechaEnt'] = 'date';
            if ($request->has('tipoSeg')) $rules['tipoSeg'] = 'in:AsegEmergencia,AsegRegular,NoAsegEmergencia,NoAsegRegular';
            if ($request->has('estado')) $rules['estado'] = 'in:Programado,Atendido,Entregado,EnProceso';
            if ($request->has('codPa')) $rules['codPa'] = 'exists:Paciente,codPa';
            if ($request->has('codMed')) $rules['codMed'] = 'exists:Medico,codMed';
            if ($request->has('codTest')) $rules['codTest'] = 'exists:TipoEstudio,codTest';
            if ($request->has('fechaCrono')) $rules['fechaCrono'] = 'exists:CronogramaAtencion,fechaCrono';

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $servicio->fill($request->only([
                'fechaSol', 'horaSol', 'nroServ', 'fechaAten', 'horaAten',
                'fechaEnt', 'horaEnt', 'tipoSeg', 'nroFicha', 'estado',
                'codPa', 'codMed', 'codTest', 'fechaCrono'
            ]));

            $servicio->save();
            $servicio->load(['paciente', 'medico', 'tipoEstudio', 'cronograma']);

            return response()->json([
                'success' => true,
                'data' => $servicio,
                'message' => 'Servicio actualizado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el servicio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado del servicio
     */
    public function cambiarEstado(Request $request, $id)
    {
        try {
            $servicio = Servicio::find($id);

            if (!$servicio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servicio no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'estado' => 'required|in:Programado,Atendido,Entregado,EnProceso'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $servicio->estado = $request->estado;
            $servicio->save();

            return response()->json([
                'success' => true,
                'data' => $servicio,
                'message' => "Estado cambiado a {$request->estado} exitosamente"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asociar diagnósticos a un servicio
     */
    public function asociarDiagnosticos(Request $request, $id)
    {
        try {
            $servicio = Servicio::find($id);

            if (!$servicio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servicio no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'diagnosticos' => 'required|array',
                'diagnosticos.*.codDiag' => 'required|exists:Diagnostico,codDiag',
                'diagnosticos.*.tipo' => 'required|in:sol,eco'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $diagnosticos = [];
            foreach ($request->diagnosticos as $diag) {
                $diagnosticos[$diag['codDiag']] = ['tipo' => $diag['tipo']];
            }

            $servicio->diagnosticos()->sync($diagnosticos);
            $servicio->load('diagnosticos');

            return response()->json([
                'success' => true,
                'data' => $servicio,
                'message' => 'Diagnósticos asociados exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asociar diagnósticos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar servicios por paciente
     */
    public function porPaciente($codPa)
    {
        try {
            $servicios = Servicio::with(['medico', 'tipoEstudio', 'diagnosticos'])
                ->porPaciente($codPa)
                ->orderBy('fechaSol', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $servicios,
                'message' => 'Servicios del paciente obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los servicios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar servicios por estado
     */
    public function porEstado($estado)
    {
        try {
            $servicios = Servicio::with(['paciente', 'medico', 'tipoEstudio'])
                ->where('estado', $estado)
                ->orderBy('fechaSol', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $servicios,
                'message' => "Servicios con estado {$estado} obtenidos correctamente"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los servicios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar servicio
     */
    public function destroy($id)
    {
        try {
            $servicio = Servicio::find($id);

            if (!$servicio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servicio no encontrado'
                ], 404);
            }

            $servicio->delete();

            return response()->json([
                'success' => true,
                'message' => 'Servicio eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el servicio: ' . $e->getMessage()
            ], 500);
        }
    }
}
