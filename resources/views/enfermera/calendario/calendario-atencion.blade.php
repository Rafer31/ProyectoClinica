@extends('enfermera.layouts.enfermera')
@section('title', 'Calendario de Atención')
@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('enfermera.home')],
            ['label' => 'Calendario de Atención']
        ];
    @endphp

    <style>
        /* ===== SLOTS DE HORARIO ===== */
        .hora-slot {
            min-height: 180px;
            transition: all 0.2s ease;
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }

        .hora-slot:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: #d1d5db;
        }

        .hora-slot.drag-over {
            background: #fdf2f8 !important;
            border: 2px dashed #ec4899 !important;
        }

        /* ===== TARJETAS DE SERVICIO ===== */
        .servicio-card {
            cursor: move;
            transition: all 0.2s ease;
            user-select: none;
            padding: 0.625rem;
            border-radius: 8px;
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }

        .servicio-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .servicio-card.dragging {
            opacity: 0.6;
        }

        /* ===== ESTADOS PROFESIONALES ===== */
        .estado-programado {
            background: #fff7ed;
            border: 1px solid #fdba74;
            border-left: 4px solid #f97316;
        }

        .estado-programado .estado-texto {
            color: #c2410c;
        }

        .estado-programado .titulo-paciente {
            color: #9a3412;
        }

        .estado-programado .subtexto {
            color: #ea580c;
        }

        .estado-en-proceso {
            background: #eff6ff;
            border: 1px solid #93c5fd;
            border-left: 4px solid #3b82f6;
        }

        .estado-en-proceso .estado-texto {
            color: #1d4ed8;
        }

        .estado-en-proceso .titulo-paciente {
            color: #1e40af;
        }

        .estado-en-proceso .subtexto {
            color: #2563eb;
        }

        .estado-atendido {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-left: 4px solid #22c55e;
        }

        .estado-atendido .estado-texto {
            color: #15803d;
        }

        .estado-atendido .titulo-paciente {
            color: #166534;
        }

        .estado-atendido .subtexto {
            color: #16a34a;
        }

        .estado-entregado {
            background: #faf5ff;
            border: 1px solid #d8b4fe;
            border-left: 4px solid #a855f7;
        }

        .estado-entregado .estado-texto {
            color: #7e22ce;
        }

        .estado-entregado .titulo-paciente {
            color: #6b21a8;
        }

        .estado-entregado .subtexto {
            color: #9333ea;
        }

        .estado-emergencia {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-left: 4px solid #ef4444;
        }

        .estado-emergencia .estado-texto {
            color: #b91c1c;
        }

        .estado-emergencia .titulo-paciente {
            color: #991b1b;
        }

        .estado-emergencia .subtexto {
            color: #dc2626;
        }

        /* ===== CONTENEDOR DE SLOTS ===== */
        .slots-container {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
        }

        /* ===== SLOT DISPONIBLE ===== */
        .slot-disponible {
            background: #f9fafb;
            border: 1px dashed #d1d5db;
            border-radius: 8px;
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .slot-disponible:hover {
            border-color: #ec4899;
            background: #fdf2f8;
        }

        /* ===== ANIMACIÓN DROP ===== */
        @keyframes dropAnimation {
            0% {
                transform: scale(1.02);
            }

            100% {
                transform: scale(1);
            }
        }

        .drop-success {
            animation: dropAnimation 0.2s ease-out;
        }

        /* ===== BOTONES HORARIO MODAL ===== */
        .horario-btn {
            transition: all 0.2s ease;
        }

        .horario-btn:hover:not(:disabled) {
            transform: scale(1.02);
            box-shadow: 0 2px 8px rgba(236, 72, 153, 0.2);
        }

        .horario-btn.selected {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            color: white;
            font-weight: bold;
            border-color: #db2777;
            box-shadow: 0 4px 12px rgba(236, 72, 153, 0.4);
        }

        .horario-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
    <div class="space-y-6">
        <!-- Alerta -->
        <div id="alerta" class="hidden"></div>

        <!-- Encabezado -->
        <div class="bg-gradient-to-r from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="material-icons text-white text-2xl">event_note</span>
                        </div>
                        Calendario de Atención
                    </h1>
                    <p class="text-gray-600 ml-15">
                        <span class="material-icons text-sm align-middle">info</span>
                        Haz clic en un horario disponible para asignar una cita • 8:00 AM - 8:00 PM
                    </p>
                </div>
            </div>
        </div>

        <!-- Alerta de cambio de horario -->
        <div id="alerta-cambio" class="hidden"></div>

        <!-- Selector de Cronograma -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                        <span class="material-icons text-pink-600">calendar_today</span>
                        Seleccionar Fecha de Cronograma
                    </label>
                    <select id="select-cronograma"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500 transition-all font-medium">
                        <option value="">Seleccione una fecha...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                        <span class="material-icons text-pink-600">info</span>
                        Información del Cronograma
                    </label>
                    <div id="info-cronograma"
                        class="px-4 py-3 bg-gradient-to-r from-pink-50 to-rose-50 rounded-lg border-2 border-pink-200 font-medium text-gray-700">
                        Seleccione un cronograma para ver la información
                    </div>
                </div>
            </div>
        </div>

        <!-- Leyenda actualizada para coincidir con el diseño profesional -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="flex items-center gap-2 mb-4">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-lg flex items-center justify-center shadow-md">
                    <span class="material-icons text-white text-xl">palette</span>
                </div>
                <h4 class="text-lg font-bold text-gray-800">Leyenda de Estados</h4>
            </div>
            <div class="flex flex-wrap gap-4">
                <!-- Disponible -->
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-gray-50 border border-dashed border-gray-300 rounded"></div>
                    <span class="text-sm font-medium text-gray-700">Disponible</span>
                </div>
                <!-- Programado -->
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-orange-50 border border-orange-200 border-l-4 border-l-orange-500 rounded"></div>
                    <span class="text-sm font-medium text-gray-700">Programado</span>
                </div>
                <!-- En Proceso -->
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-blue-50 border border-blue-200 border-l-4 border-l-blue-500 rounded"></div>
                    <span class="text-sm font-medium text-gray-700">En Proceso</span>
                </div>
                <!-- Atendido -->
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-green-50 border border-green-200 border-l-4 border-l-green-500 rounded"></div>
                    <span class="text-sm font-medium text-gray-700">Atendido</span>
                </div>
                <!-- Entregado -->
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-purple-50 border border-purple-200 border-l-4 border-l-purple-500 rounded"></div>
                    <span class="text-sm font-medium text-gray-700">Entregado</span>
                </div>
                <!-- Emergencia -->
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-red-50 border border-red-200 border-l-4 border-l-red-500 rounded"></div>
                    <span class="text-sm font-medium text-gray-700">Emergencia</span>
                </div>
            </div>
        </div>

        <!-- Calendario de Horarios -->
        <div id="calendario-container" class="hidden">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-pink-500 to-rose-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <span class="material-icons">schedule</span>
                        Horarios del Día (08:00 - 20:00) - Intervalos de 30 minutos, 1 servicio por slot
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4" id="horarios-dia">
                        <!-- Se llenará dinámicamente -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensaje sin selección -->
        <div id="sin-seleccion" class="bg-white rounded-xl shadow-lg border border-gray-200 p-12">
            <div class="flex flex-col items-center justify-center">
                <div
                    class="w-32 h-32 bg-gradient-to-br from-pink-100 to-rose-100 rounded-full flex items-center justify-center mb-4">
                    <span class="material-icons text-pink-600" style="font-size: 80px;">event_available</span>
                </div>
                <p class="text-gray-800 text-xl font-bold mb-2">Selecciona un Cronograma</p>
                <p class="text-gray-500 text-sm">Elige una fecha para ver los horarios disponibles</p>
            </div>
        </div>
    </div>

    <!-- Modal Asignar Cita con formularios integrados -->
    <div id="modal-asignar-cita"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
        <div
            class="relative top-10 mx-auto p-6 border border-gray-200 w-full max-w-4xl shadow-2xl rounded-2xl bg-white mb-10">

            <!-- Header del Modal -->
            <div class="flex justify-between items-center mb-6">
                <h3 id="modal-titulo" class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center">
                        <span class="material-icons text-white" id="modal-icono">add</span>
                    </div>
                    <span id="modal-titulo-texto">Asignar Nueva Cita</span>
                </h3>
                <button onclick="cerrarModalAsignarCita()"
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-colors">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <!-- ========== VISTA: FORMULARIO CITA ========== -->
            <div id="vista-cita">
                <form id="form-asignar-cita" class="space-y-6">
                    @csrf

                    <!-- Info horario preseleccionado -->
                    <div id="horario-preseleccionado-info"
                        class="bg-gradient-to-r from-pink-50 to-rose-50 border-2 border-pink-300 rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <span class="material-icons text-white text-2xl">schedule</span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Horario seleccionado</p>
                                    <p id="horario-preseleccionado-texto" class="text-2xl font-bold text-pink-600">--:--</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-pink-100 text-pink-700 rounded-full text-sm font-semibold">
                                <span class="material-icons text-sm align-middle">check_circle</span>
                                Confirmado
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Fecha Solicitud <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="fechaSol" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Hora Solicitud <span class="text-red-500">*</span>
                            </label>
                            <input type="time" id="horaSol" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Tipo de Seguro <span class="text-red-500">*</span>
                            </label>
                            <select id="tipoAseg" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Seleccione...</option>
                                <option value="AsegEmergencia">Asegurado - Emergencia</option>
                                <option value="AsegRegular">Asegurado - Regular</option>
                                <option value="NoAsegEmergencia">No Asegurado - Emergencia</option>
                                <option value="NoAsegRegular">No Asegurado - Regular</option>
                            </select>
                        </div>
                    </div>

                    <!-- Paciente -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                        <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <span class="material-icons text-blue-600">person</span>
                            Paciente
                        </h4>
                        <div class="flex gap-2">
                            <select id="codPa" required
                                class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Seleccione un paciente</option>
                            </select>
                            <button type="button" onclick="mostrarVista('paciente')"
                                class="px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all font-semibold shadow-md flex items-center gap-2">
                                <span class="material-icons text-sm">add</span>
                                Nuevo
                            </button>
                        </div>
                    </div>

                    <!-- Médico -->
                    <div class="bg-purple-50 border-l-4 border-purple-500 p-4 rounded-lg">
                        <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <span class="material-icons text-purple-600">medical_services</span>
                            Médico Solicitante
                        </h4>
                        <div class="flex gap-2">
                            <select id="codMed" required
                                class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Seleccione un médico</option>
                            </select>
                            <button type="button" onclick="mostrarVista('medico')"
                                class="px-4 py-3 bg-gradient-to-r from-purple-500 to-violet-600 text-white rounded-lg hover:from-purple-600 hover:to-violet-700 transition-all font-semibold shadow-md flex items-center gap-2">
                                <span class="material-icons text-sm">add</span>
                                Nuevo
                            </button>
                        </div>
                    </div>

                    <!-- Tipo de Estudio -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Tipo de Estudio <span class="text-red-500">*</span>
                        </label>
                        <select id="codTest" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                            <option value="">Seleccione un tipo de estudio</option>
                        </select>
                    </div>

                    <!-- Fecha Cronograma (oculto, se llena automáticamente) -->
                    <input type="hidden" id="fechaCrono-modal">
                    <input type="hidden" id="horaCrono">

                    <!-- Nro Ficha -->
                    <div class="bg-gray-50 rounded-lg p-3 flex items-center justify-between">
                        <span class="text-sm text-gray-600">
                            <span class="material-icons text-xs align-middle">confirmation_number</span>
                            Número de Ficha:
                        </span>
                        <span id="nroFicha-display" class="font-bold text-pink-600 text-lg">-</span>
                    </div>

                    <div class="flex justify-end space-x-4 pt-4 border-t-2 border-gray-200">
                        <button type="button" onclick="cerrarModalAsignarCita()"
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-pink-500 to-rose-600 text-white rounded-lg hover:from-pink-600 hover:to-rose-700 shadow-md font-semibold flex items-center gap-2">
                            <span class="material-icons text-sm">save</span>
                            Guardar Cita
                        </button>
                    </div>
                </form>
            </div>

            <!-- ========== VISTA: NUEVO PACIENTE ========== -->
            <div id="vista-paciente" class="hidden">
                <div class="mb-4">
                    <button type="button" onclick="mostrarVista('cita')"
                        class="flex items-center gap-2 text-gray-600 hover:text-pink-600 transition-colors">
                        <span class="material-icons">arrow_back</span>
                        <span class="font-medium">Volver al formulario de cita</span>
                    </button>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-lg">
                    <p class="text-sm text-blue-800 font-semibold flex items-center gap-2">
                        <span class="material-icons">info</span>
                        Complete los datos del nuevo paciente
                    </p>
                </div>

                <form id="form-nuevo-paciente" class="space-y-4">
                    <!-- Información Personal -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nombre <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="pa-nomPa" required placeholder="Ej: Juan"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Apellido Paterno</label>
                            <input type="text" id="pa-paternoPa" placeholder="Ej: Pérez"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Apellido Materno</label>
                            <input type="text" id="pa-maternoPa" placeholder="Ej: García"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Sexo <span
                                    class="text-red-500">*</span></label>
                            <select id="pa-sexo" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Fecha de Nacimiento</label>
                            <input type="date" id="pa-fechaNac" max="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Información Clínica -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nro. Historia Clínica (HCI)</label>
                            <input type="text" id="pa-nroHCI" placeholder="Ej: HCI-2024-001"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Debe ser único para cada paciente</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tipo de Paciente <span
                                    class="text-red-500">*</span></label>
                            <select id="pa-tipoPac" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="SUS">SUS (Seguro Universal de Salud)</option>
                                <option value="SINSUS">SIN SUS (Sin Seguro)</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 pt-4 border-t-2 border-gray-200">
                        <button type="button" onclick="mostrarVista('cita')"
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 shadow-md font-semibold flex items-center gap-2">
                            <span class="material-icons text-sm">save</span>
                            Guardar Paciente
                        </button>
                    </div>
                </form>
            </div>

            <!-- ========== VISTA: NUEVO MÉDICO ========== -->
            <div id="vista-medico" class="hidden">
                <div class="mb-4">
                    <button type="button" onclick="mostrarVista('cita')"
                        class="flex items-center gap-2 text-gray-600 hover:text-pink-600 transition-colors">
                        <span class="material-icons">arrow_back</span>
                        <span class="font-medium">Volver al formulario de cita</span>
                    </button>
                </div>

                <div class="bg-purple-50 border-l-4 border-purple-500 p-4 mb-6 rounded-lg">
                    <p class="text-sm text-purple-800 font-semibold flex items-center gap-2">
                        <span class="material-icons">info</span>
                        Complete los datos del nuevo médico
                    </p>
                </div>

                <form id="form-nuevo-medico" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nombre <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="med-nomMed" required placeholder="Ej: Carlos"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Apellido Paterno</label>
                            <input type="text" id="med-paternoMed" placeholder="Ej: López"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Apellido Materno</label>
                            <input type="text" id="med-maternoMed" placeholder="Ej: Rodríguez"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tipo de Médico <span
                                class="text-red-500">*</span></label>
                        <select id="med-tipoMed" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Seleccione...</option>
                            <option value="Interno">Interno</option>
                            <option value="Externo">Externo</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-4 pt-4 border-t-2 border-gray-200">
                        <button type="button" onclick="mostrarVista('cita')"
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-purple-500 to-violet-600 text-white rounded-lg hover:from-purple-600 hover:to-violet-700 shadow-md font-semibold flex items-center gap-2">
                            <span class="material-icons text-sm">save</span>
                            Guardar Médico
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <!-- Modal Detalle Servicio - Agregar después del modal-asignar-cita -->
    <div id="modal-detalle-servicio"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
        <div class="relative top-20 mx-auto p-6 border border-gray-200 w-full max-w-2xl shadow-2xl rounded-2xl bg-white">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="material-icons text-white text-2xl">medical_services</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Detalle del Servicio</h3>
                </div>
                <button onclick="cerrarModalDetalle()"
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-all">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div id="contenido-detalle-servicio" class="space-y-4">
                <!-- Se llenará dinámicamente -->
            </div>
        </div>
    </div>
    <div id="modal-confirmar-cambio"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
        <div class="relative top-1/3 mx-auto p-6 border border-gray-200 w-full max-w-md shadow-2xl rounded-2xl bg-white">
            <div class="text-center">
                <div
                    class="mx-auto flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-br from-pink-500 to-rose-600 mb-4 shadow-lg">
                    <span class="material-icons text-white text-4xl">schedule</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Confirmar Cambio de Horario</h3>
                <p class="text-sm text-gray-600 mb-4" id="mensaje-confirmacion"></p>
                <div class="flex gap-3">
                    <button onclick="cancelarCambioHorario()"
                        class="flex-1 px-5 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-semibold">
                        Cancelar
                    </button>
                    <button onclick="confirmarCambioHorario()"
                        class="flex-1 px-5 py-3 bg-gradient-to-r from-pink-500 to-rose-600 text-white rounded-xl hover:from-pink-600 hover:to-rose-700 transition-all font-semibold shadow-lg">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>

    </div>
    <div id="modal-confirmar-entrega"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-0 border w-full max-w-lg shadow-2xl rounded-xl bg-white">
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-t-xl p-6">
                <div class="flex items-center">
                    <div
                        class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4 backdrop-blur-sm">
                        <span class="material-icons text-white text-3xl">assignment_turned_in</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">Marcar como Entregado</h3>
                        <p class="text-sm text-purple-100">Confirme la entrega del servicio al paciente</p>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <div class="bg-purple-50 border-l-4 border-purple-500 p-4 rounded-lg">
                    <p class="text-gray-800 font-semibold mb-2">Está a punto de marcar este servicio como entregado:</p>
                    <div class="bg-white p-4 rounded-lg mt-3">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="material-icons text-purple-600">receipt</span>
                            <div>
                                <p class="text-xs text-gray-500">Nro. Servicio</p>
                                <p class="font-bold text-gray-900" id="entregar-nro-servicio">-</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-icons text-purple-600">person</span>
                            <div>
                                <p class="text-xs text-gray-500">Paciente</p>
                                <p class="font-bold text-gray-900" id="entregar-paciente">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800 font-medium flex items-start gap-2">
                        <span class="material-icons text-blue-600 text-lg">info</span>
                        <span>Al confirmar, el estado cambiará a <strong>"Entregado"</strong> y se registrará la fecha y
                            hora de entrega automáticamente.</span>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex gap-3">
                <button type="button" onclick="cerrarModal('modal-confirmar-entrega')"
                    class="flex-1 px-5 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-semibold">
                    Cancelar
                </button>
                <button type="button" id="btn-confirmar-entrega-calendario"
                    class="flex-1 px-5 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-lg hover:from-purple-600 hover:to-indigo-700 transition-all shadow-md font-semibold flex items-center justify-center gap-2">
                    <span class="material-icons">check_circle</span>
                    <span>Confirmar Entrega</span>
                </button>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            let cronogramas = [];
            let serviciosPorHorario = {};
            let cronogramaSeleccionado = null;
            let horarioSeleccionado = null;
            let pacientes = [];
            let medicos = [];
            let tiposEstudio = [];
            let servicioArrastrado = null;
            let horarioDestino = null;
            let servicioActual = null;

            document.addEventListener('DOMContentLoaded', function () {
                cargarCronogramas();
                cargarDatosFormulario();

                document.getElementById('select-cronograma').addEventListener('change', function () {
                    const fechaCrono = this.value;
                    if (fechaCrono) {
                        cargarServiciosPorFecha(fechaCrono);
                    } else {
                        ocultarCalendario();
                    }
                });

                document.getElementById('form-asignar-cita').addEventListener('submit', guardarCita);
                document.getElementById('form-nuevo-paciente').addEventListener('submit', guardarNuevoPaciente);
                document.getElementById('form-nuevo-medico').addEventListener('submit', guardarNuevoMedico);
                document.getElementById('btn-confirmar-entrega-calendario').addEventListener('click', async function () {
                    if (!servicioActual) return;

                    try {
                        const response = await fetch(`/api/personal/servicios/${servicioActual.codServ}/entregar`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            mostrarAlerta('✅ Servicio marcado como entregado exitosamente', 'success');
                            cerrarModal('modal-confirmar-entrega');

                            // Recargar servicios si estamos viendo el cronograma
                            if (cronogramaSeleccionado) {
                                await cargarServiciosPorFecha(cronogramaSeleccionado.fechaCrono);
                            }

                            servicioActual = null;
                        } else {
                            mostrarAlerta(data.message || 'Error al marcar como entregado', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        mostrarAlerta('Error al marcar como entregado', 'error');
                    }
                });
            });
            document.getElementById('fechaCrono-modal').addEventListener('change', async function () {
                await calcularNroFicha();
                // Solo cargar horarios si NO hay horario preseleccionado
                if (!horarioPreseleccionado) {
                    await cargarHorariosDisponiblesModal();
                }
            });
            async function guardarNuevoPaciente(e) {
                e.preventDefault();

                const datos = {
                    nomPa: document.getElementById('pa-nomPa').value.trim(),
                    paternoPa: document.getElementById('pa-paternoPa').value.trim() || null,
                    maternoPa: document.getElementById('pa-maternoPa').value.trim() || null,
                    sexo: document.getElementById('pa-sexo').value,
                    fechaNac: document.getElementById('pa-fechaNac').value || null,
                    nroHCI: document.getElementById('pa-nroHCI').value.trim() || null,
                    tipoPac: document.getElementById('pa-tipoPac').value
                };

                try {
                    const response = await fetch('/api/enfermera/pacientes', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(datos)
                    });

                    const data = await response.json();

                    if (data.success) {
                        mostrarAlerta('✅ Paciente creado exitosamente', 'success');

                        // Agregar a la lista y seleccionar
                        pacientes.push(data.data);
                        llenarSelectPacientes();
                        document.getElementById('codPa').value = data.data.codPa;

                        // Volver a vista cita
                        document.getElementById('form-nuevo-paciente').reset();
                        mostrarVista('cita');
                    } else {
                        mostrarAlerta(data.message || 'Error al crear paciente', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('Error al crear paciente', 'error');
                }
            }
            async function guardarNuevoMedico(e) {
                e.preventDefault();

                const datos = {
                    nomMed: document.getElementById('med-nomMed').value.trim(),
                    paternoMed: document.getElementById('med-paternoMed').value.trim() || null,
                    maternoMed: document.getElementById('med-maternoMed').value.trim() || null,
                    tipoMed: document.getElementById('med-tipoMed').value
                };

                try {
                    const response = await fetch('/api/enfermera/medicos', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(datos)
                    });

                    const data = await response.json();

                    if (data.success) {
                        mostrarAlerta('✅ Médico creado exitosamente', 'success');

                        // Agregar a la lista y seleccionar
                        medicos.push(data.data);
                        llenarSelectMedicos();
                        document.getElementById('codMed').value = data.data.codMed;

                        // Volver a vista cita
                        document.getElementById('form-nuevo-medico').reset();
                        mostrarVista('cita');
                    } else {
                        mostrarAlerta(data.message || 'Error al crear médico', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('Error al crear médico', 'error');
                }
            }

            function mostrarVista(vista) {
                document.getElementById('vista-cita').classList.add('hidden');
                document.getElementById('vista-paciente').classList.add('hidden');
                document.getElementById('vista-medico').classList.add('hidden');

                const tituloTexto = document.getElementById('modal-titulo-texto');
                const icono = document.getElementById('modal-icono');

                if (vista === 'cita') {
                    document.getElementById('vista-cita').classList.remove('hidden');
                    tituloTexto.textContent = 'Asignar Nueva Cita';
                    icono.textContent = 'add';
                } else if (vista === 'paciente') {
                    document.getElementById('vista-paciente').classList.remove('hidden');
                    tituloTexto.textContent = 'Nuevo Paciente';
                    icono.textContent = 'person_add';
                } else if (vista === 'medico') {
                    document.getElementById('vista-medico').classList.remove('hidden');
                    tituloTexto.textContent = 'Nuevo Médico';
                    icono.textContent = 'medical_services';
                }
            }

            async function cargarCronogramas() {
                try {
                    const response = await fetch('/api/enfermera/cronogramas');
                    const data = await response.json();

                    if (data.success) {
                        cronogramas = data.data.filter(c => c.estado === 'activo' || c.estado === 'inactivoFut');

                        const select = document.getElementById('select-cronograma');
                        const selectModal = document.getElementById('fechaCrono-modal');

                        select.innerHTML = '<option value="">Seleccione una fecha...</option>';
                        selectModal.innerHTML = '<option value="">Seleccione una fecha...</option>';

                        cronogramas.forEach(crono => {
                            const fecha = new Date(crono.fechaCrono + 'T00:00:00');
                            const fechaFormateada = fecha.toLocaleDateString('es-ES', {
                                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                            });
                            const textoFecha = fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);

                            const option1 = document.createElement('option');
                            option1.value = crono.fechaCrono;
                            option1.textContent = textoFecha;
                            select.appendChild(option1);

                            const option2 = document.createElement('option');
                            option2.value = crono.fechaCrono;
                            option2.textContent = textoFecha;
                            selectModal.appendChild(option2);
                        });
                    }
                } catch (error) {
                    console.error('Error al cargar cronogramas:', error);
                }
            }

            async function cargarDatosFormulario() {
                try {
                    const [resPacientes, resMedicos, resTipos] = await Promise.all([
                        fetch('/api/enfermera/pacientes'),
                        fetch('/api/enfermera/medicos'),
                        fetch('/api/enfermera/tipos-estudio')
                    ]);

                    const dataPacientes = await resPacientes.json();
                    const dataMedicos = await resMedicos.json();
                    const dataTipos = await resTipos.json();

                    if (dataPacientes.success) {
                        pacientes = dataPacientes.data;
                        llenarSelectPacientes();
                    }
                    if (dataMedicos.success) {
                        medicos = dataMedicos.data;
                        llenarSelectMedicos();
                    }
                    if (dataTipos.success) {
                        tiposEstudio = dataTipos.data;
                        llenarSelectTiposEstudio();
                    }
                } catch (error) {
                    console.error('Error al cargar datos del formulario:', error);
                }
            }

            function llenarSelectPacientes() {
                const select = document.getElementById('codPa');
                select.innerHTML = '<option value="">Seleccione un paciente</option>';
                pacientes.forEach(p => {
                    const option = document.createElement('option');
                    option.value = p.codPa;
                    option.textContent = `${p.nomPa} ${p.paternoPa || ''} - ${p.nroHCI || 'Sin HCI'}`;
                    select.appendChild(option);
                });
            }

            function llenarSelectMedicos() {
                const select = document.getElementById('codMed');
                select.innerHTML = '<option value="">Seleccione un médico</option>';
                medicos.forEach(m => {
                    const option = document.createElement('option');
                    option.value = m.codMed;
                    option.textContent = `${m.nomMed} ${m.paternoMed || ''}`;
                    select.appendChild(option);
                });
            }

            function llenarSelectTiposEstudio() {
                const select = document.getElementById('codTest');
                select.innerHTML = '<option value="">Seleccione un tipo de estudio</option>';
                tiposEstudio.forEach(t => {
                    const option = document.createElement('option');
                    option.value = t.codTest;
                    option.textContent = t.descripcion;
                    select.appendChild(option);
                });
            }

            async function cargarServiciosPorFecha(fechaCrono) {
                try {
                    cronogramaSeleccionado = cronogramas.find(c => c.fechaCrono === fechaCrono);
                    if (cronogramaSeleccionado) {
                        mostrarInfoCronograma(cronogramaSeleccionado);
                    }

                    const response = await fetch(`/api/enfermera/servicios/por-fecha-cronograma/${fechaCrono}`);
                    const data = await response.json();

                    if (data.success) {
                        serviciosPorHorario = data.data.por_horario || {};
                        renderizarCalendario();
                    }
                } catch (error) {
                    console.error('Error al cargar servicios:', error);
                }
            }

            function mostrarInfoCronograma(cronograma) {
                const infoDiv = document.getElementById('info-cronograma');
                const disponibles = cronograma.cantDispo || 0;
                const emergencia = cronograma.cantEmergencia || 0;
                const total = cronograma.cantFijo || 15;
                const atendidos = total - disponibles;

                infoDiv.innerHTML = `
                                                                                                                                                                                                                                                            <div class="flex items-center justify-between">
                                                                                                                                                                                                                                                                <div class="flex items-center gap-3">
                                                                                                                                                                                                                                                                    <div class="text-center">
                                                                                                                                                                                                                                                                        <p class="text-2xl font-bold text-pink-600">${disponibles}</p>
                                                                                                                                                                                                                                                                        <p class="text-xs text-gray-600 font-medium">Disponibles</p>
                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                    <div class="text-center">
                                                                                                                                                                                                                                                                        <p class="text-2xl font-bold text-blue-600">${atendidos}</p>
                                                                                                                                                                                                                                                                        <p class="text-xs text-gray-600 font-medium">Atendidos</p>
                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                    <div class="text-center">
                                                                                                                                                                                                                                                                        <p class="text-2xl font-bold text-red-600">${emergencia}</p>
                                                                                                                                                                                                                                                                        <p class="text-xs text-gray-600 font-medium">Emergencias</p>
                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                <div class="px-4 py-2 bg-gradient-to-r from-pink-500 to-rose-600 text-white rounded-lg font-bold text-sm shadow-md">
                                                                                                                                                                                                                                                                    ${cronograma.estado === 'activo' ? 'Activo' : 'Programado'}
                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                        `;
            }

            function renderizarCalendario() {
                const container = document.getElementById('horarios-dia');
                container.innerHTML = '';

                for (let hora = 8; hora <= 19; hora++) {
                    const horarioKey00 = `${String(hora).padStart(2, '0')}:00`;
                    container.appendChild(crearSlotHorario(hora, 0, horarioKey00));

                    const horarioKey30 = `${String(hora).padStart(2, '0')}:30`;
                    container.appendChild(crearSlotHorario(hora, 30, horarioKey30));
                }

                const horarioKey2000 = '20:00';
                container.appendChild(crearSlotHorario(20, 0, horarioKey2000));

                document.getElementById('calendario-container').classList.remove('hidden');
                document.getElementById('sin-seleccion').classList.add('hidden');
            }

            function crearSlotHorario(hora, minutos, horarioKey) {
                const div = document.createElement('div');
                const servicios = serviciosPorHorario[horarioKey] || [];
                const servicioMostrar = servicios.length > 0 ? servicios[0] : null;
                const estaDisponible = servicioMostrar === null;

                div.className = 'hora-slot';
                div.dataset.horario = horarioKey;

                div.addEventListener('dragover', handleDragOver);
                div.addEventListener('drop', handleDrop);
                div.addEventListener('dragleave', handleDragLeave);

                let contenidoHTML = `
                                                                                                                                                                                                                                                        <div class="mb-2 pb-2 border-b border-gray-100 text-center">
                                                                                                                                                                                                                                                            <p class="text-lg font-bold text-gray-800">${horarioKey}</p>
                                                                                                                                                                                                                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium mt-1
                                                                                                                                                                                                                                                                ${estaDisponible
                        ? 'bg-emerald-50 text-emerald-600'
                        : 'bg-amber-50 text-amber-600'}">
                                                                                                                                                                                                                                                                <span class="material-icons" style="font-size: 11px;">
                                                                                                                                                                                                                                                                    ${estaDisponible ? 'check_circle' : 'event_busy'}
                                                                                                                                                                                                                                                                </span>
                                                                                                                                                                                                                                                                ${estaDisponible ? 'Disponible' : 'Ocupado'}
                                                                                                                                                                                                                                                            </span>
                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                        <div class="slots-container">
                                                                                                                                                                                                                                                    `;

                if (servicioMostrar === null) {
                    contenidoHTML += `
                                                                                                                                                                                                                                                            <div class="slot-disponible" onclick="asignarCitaEnHorario('${horarioKey}')">
                                                                                                                                                                                                                                                                <span class="material-icons text-gray-400 text-xl">add</span>
                                                                                                                                                                                                                                                                <p class="text-xs text-gray-500 font-medium mt-1">Asignar Cita</p>
                                                                                                                                                                                                                                                                <p class="text-xs text-gray-400">o arrastra aquí</p>
                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                        `;
                } else {
                    contenidoHTML += crearTarjetaServicio(servicioMostrar);
                }

                contenidoHTML += `</div>`;
                div.innerHTML = contenidoHTML;

                return div;
            }
            function crearTarjetaServicio(servicio) {
                const paciente = `${servicio.paciente?.nomPa || ''} ${servicio.paciente?.paternoPa || ''}`.trim();
                const tipoEstudio = servicio.tipo_estudio?.descripcion || 'N/A';

                let colorClass = 'estado-programado';
                let iconoEstado = 'schedule';
                let textoEstado = 'Programado';
                let draggable = true;

                if (servicio.estado === 'EnProceso') {
                    colorClass = 'estado-en-proceso';
                    iconoEstado = 'autorenew';
                    textoEstado = 'En Proceso';
                } else if (servicio.estado === 'Atendido') {
                    colorClass = 'estado-atendido';
                    iconoEstado = 'check_circle';
                    textoEstado = 'Atendido';
                    draggable = false;
                } else if (servicio.estado === 'Entregado') {
                    colorClass = 'estado-entregado';
                    iconoEstado = 'inventory';
                    textoEstado = 'Entregado';
                    draggable = false;
                }

                if (servicio.tipoAseg && servicio.tipoAseg.includes('Emergencia')) {
                    colorClass = 'estado-emergencia';
                    iconoEstado = 'warning';
                    textoEstado = 'Emergencia';
                }

                const draggableAttr = draggable ? 'draggable="true"' : '';
                const cursorClass = draggable ? 'cursor-move' : 'cursor-pointer';

                return `
                                                                                                                                                                                                                                                <div class="servicio-card ${colorClass} ${cursorClass}"
                                                                                                                                                                                                                                                     ${draggableAttr}
                                                                                                                                                                                                                                                     data-servicio-id="${servicio.codServ}"
                                                                                                                                                                                                                                                     data-horario-actual="${servicio.horaCrono ? servicio.horaCrono.substring(0, 5) : ''}"
                                                                                                                                                                                                                                                     onclick="verDetalleServicio(${servicio.codServ})"
                                                                                                                                                                                                                                                     ondragstart="handleDragStart(event, ${servicio.codServ})"
                                                                                                                                                                                                                                                     ondragend="handleDragEnd(event)">

                                                                                                                                                                                                                                                    <!-- Header: Ficha e Icono -->
                                                                                                                                                                                                                                                    <div class="flex items-center justify-between mb-1">
                                                                                                                                                                                                                                                        <span class="text-xs font-semibold text-gray-500">#${servicio.nroFicha || 'N/A'}</span>
                                                                                                                                                                                                                                                        <span class="material-icons estado-texto" style="font-size: 16px;">${iconoEstado}</span>
                                                                                                                                                                                                                                                    </div>

                                                                                                                                                                                                                                                    <!-- Paciente -->
                                                                                                                                                                                                                                                    <p class="titulo-paciente font-semibold text-sm truncate leading-snug" title="${paciente}">
                                                                                                                                                                                                                                                        ${paciente}
                                                                                                                                                                                                                                                    </p>

                                                                                                                                                                                                                                                    <!-- Estudio -->
                                                                                                                                                                                                                                                    <p class="subtexto text-xs truncate opacity-75 mt-0.5" title="${tipoEstudio}">
                                                                                                                                                                                                                                                        ${tipoEstudio}
                                                                                                                                                                                                                                                    </p>

                                                                                                                                                                                                                                                    <!-- Footer: Código y Estado -->
                                                                                                                                                                                                                                                    <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-200/50">
                                                                                                                                                                                                                                                        <span class="text-xs text-gray-500 truncate max-w-[55%]" title="${servicio.nroServ || ''}">
                                                                                                                                                                                                                                                            ${servicio.nroServ || 'N/A'}
                                                                                                                                                                                                                                                        </span>
                                                                                                                                                                                                                                                        <span class="estado-texto text-xs font-medium">${textoEstado}</span>
                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                            `;
            }
            // ==========================================
            // FUNCIONES DE DRAG & DROP
            // ==========================================

            function handleDragStart(event, codServ) {
                event.stopPropagation();
                servicioArrastrado = codServ;
                event.currentTarget.classList.add('dragging');
            }

            function handleDragEnd(event) {
                event.currentTarget.classList.remove('dragging');
                document.querySelectorAll('.hora-slot').forEach(slot => {
                    slot.classList.remove('drag-over');
                });
            }

            function handleDragOver(event) {
                event.preventDefault();
                event.stopPropagation();
                event.currentTarget.classList.add('drag-over');
            }

            function handleDragLeave(event) {
                event.currentTarget.classList.remove('drag-over');
            }

            function handleDrop(event) {
                event.preventDefault();
                event.stopPropagation();

                const slot = event.currentTarget;
                slot.classList.remove('drag-over');

                if (!servicioArrastrado) return;

                horarioDestino = slot.dataset.horario;

                const serviciosEnDestino = serviciosPorHorario[horarioDestino] || [];

                if (serviciosEnDestino.length >= 1) {
                    mostrarAlerta('El horario seleccionado ya está ocupado (1 servicio por slot)', 'error');
                    servicioArrastrado = null;
                    horarioDestino = null;
                    return;
                }

                const servicio = encontrarServicio(servicioArrastrado);
                if (!servicio) {
                    servicioArrastrado = null;
                    horarioDestino = null;
                    return;
                }

                const horarioActual = servicio.horaCrono ? servicio.horaCrono.substring(0, 5) : '';

                if (horarioActual === horarioDestino) {
                    mostrarAlerta('El servicio ya está en ese horario', 'info');
                    servicioArrastrado = null;
                    horarioDestino = null;
                    return;
                }

                mostrarModalConfirmacion(servicio, horarioActual, horarioDestino);
            }

            function encontrarServicio(codServ) {
                for (let horario in serviciosPorHorario) {
                    const servicio = serviciosPorHorario[horario].find(s => s.codServ == codServ);
                    if (servicio) return servicio;
                }
                return null;
            }

            function mostrarModalConfirmacion(servicio, horarioActual, horarioNuevo) {
                const paciente = `${servicio.paciente?.nomPa || ''} ${servicio.paciente?.paternoPa || ''}`.trim();
                const mensaje = `¿Desea cambiar el horario del servicio de <strong>${paciente}</strong> de <strong>${horarioActual}</strong> a <strong>${horarioNuevo}</strong>?`;

                document.getElementById('mensaje-confirmacion').innerHTML = mensaje;
                document.getElementById('modal-confirmar-cambio').classList.remove('hidden');
            }

            async function confirmarCambioHorario() {
                document.getElementById('modal-confirmar-cambio').classList.add('hidden');

                if (!servicioArrastrado || !horarioDestino) return;

                try {
                    const response = await fetch(`/api/personal/servicios/${servicioArrastrado}/cambiar-horario`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            horaCrono: horarioDestino + ':00',
                            fechaCrono: cronogramaSeleccionado.fechaCrono
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        mostrarAlerta('✅ Horario cambiado exitosamente', 'success');
                        setTimeout(() => {
                            cargarServiciosPorFecha(cronogramaSeleccionado.fechaCrono);
                        }, 1000);
                    } else {
                        mostrarAlerta(data.message || 'Error al cambiar el horario', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('Error al cambiar el horario', 'error');
                } finally {
                    servicioArrastrado = null;
                    horarioDestino = null;
                }
            }

            function cancelarCambioHorario() {
                document.getElementById('modal-confirmar-cambio').classList.add('hidden');
                servicioArrastrado = null;
                horarioDestino = null;
            }

            // ==========================================
            // FUNCIONES DE MODAL Y CITAS
            // ==========================================

            async function verDetalleServicio(codServ) {
                if (servicioArrastrado) return;

                try {
                    const response = await fetch(`/api/enfermera/servicios/${codServ}`);
                    const data = await response.json();

                    if (data.success) {
                        const s = data.data;
                        const paciente = `${s.paciente?.nomPa || ''} ${s.paciente?.paternoPa || ''} ${s.paciente?.maternoPa || ''}`.trim();
                        const medico = `${s.medico?.nomMed || ''} ${s.medico?.paternoMed || ''}`.trim();

                        const estadoBadge = {
                            'Programado': 'bg-gradient-to-r from-orange-500 to-orange-600 text-white',
                            'EnProceso': 'bg-gradient-to-r from-blue-500 to-blue-600 text-white',
                            'Atendido': 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white',
                            'Entregado': 'bg-gradient-to-r from-purple-500 to-purple-600 text-white'
                        }[s.estado] || 'bg-gray-100 text-gray-700';

                        const horaCrono = s.horaCrono ? s.horaCrono.substring(0, 5) : 'Sin hora';

                        // Determinar si puede marcar como entregado
                        const puedeEntregar = s.estado === 'Atendido';
                        const estaEntregado = s.estado === 'Entregado';

                        document.getElementById('contenido-detalle-servicio').innerHTML = `
                        <div class="space-y-4">
                            <div class="bg-gradient-to-r from-pink-50 to-rose-50 rounded-xl p-5 border border-pink-200">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-600 font-semibold mb-1">Número de Servicio</p>
                                        <p class="text-lg font-bold text-pink-600">${s.nroServ}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600 font-semibold mb-1">Número de Ficha</p>
                                        <p class="text-lg font-bold text-gray-900">${s.nroFicha || 'N/A'}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600 font-semibold mb-1">Estado</p>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold ${estadoBadge}">${s.estado}</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600 font-semibold mb-1">Horario</p>
                                        <p class="text-lg font-bold text-pink-600">${horaCrono}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-blue-50 p-4 rounded-xl border border-blue-200">
                                    <p class="text-xs text-gray-600 font-semibold mb-2"><span class="material-icons text-xs text-blue-600">person</span> Paciente</p>
                                    <p class="font-bold text-gray-900">${paciente}</p>
                                    <p class="text-sm text-blue-700 mt-1">${s.paciente?.nroHCI || 'Sin HCI'}</p>
                                </div>
                                <div class="bg-purple-50 p-4 rounded-xl border border-purple-200">
                                    <p class="text-xs text-gray-600 font-semibold mb-2"><span class="material-icons text-xs text-purple-600">medical_services</span> Médico</p>
                                    <p class="font-bold text-gray-900">${medico}</p>
                                    <p class="text-sm text-purple-700 mt-1">${s.medico?.tipoMed || ''}</p>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-purple-50 to-indigo-50 p-5 rounded-xl border border-purple-200">
                                <p class="text-xs text-gray-600 font-semibold mb-2"><span class="material-icons text-xs text-purple-600">science</span> Tipo de Estudio</p>
                                <p class="font-bold text-gray-900 text-lg">${s.tipo_estudio?.descripcion || 'N/A'}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                <p class="text-xs text-gray-600 font-semibold mb-2"><span class="material-icons text-xs">security</span> Tipo de Seguro</p>
                                <p class="font-bold text-gray-900">${s.tipoAseg?.replace('Aseg', 'Aseg. ').replace('NoAseg', 'No Aseg. ')}</p>
                            </div>

                            ${estaEntregado && s.fechaEnt ? `
                                <div class="bg-purple-50 border-l-4 border-purple-500 p-4 rounded-lg">
                                    <p class="text-sm font-bold text-purple-900 mb-2"><span class="material-icons text-purple-600 text-sm">done_all</span> Entregado</p>
                                    <p class="text-xs text-purple-800">Fecha de entrega: ${formatearFecha(s.fechaEnt)}</p>
                                </div>
                            ` : ''}

                            ${(s.estado === 'Programado' || s.estado === 'EnProceso') ? `
                                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-lg">
                                    <p class="text-sm font-bold text-amber-900 mb-1"><span class="material-icons">info</span> Cambio de Horario</p>
                                    <p class="text-xs text-amber-800">Puedes arrastrar y soltar esta tarjeta para cambiar su horario</p>
                                </div>
                            ` : ''}

                            <div class="flex flex-col gap-3 mt-4">
                                <a href="/enfermera/calendario/servicio/${s.codServ}/pdf" target="_blank"
                                   class="w-full px-5 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all font-semibold shadow-lg flex items-center justify-center gap-2">
                                    <span class="material-icons">picture_as_pdf</span>
                                    Exportar Ficha de Cita (PDF)
                                </a>

                                ${puedeEntregar ? `
                                    <button onclick="confirmarEntregaDesdeDetalle(${s.codServ})"
                                            class="w-full px-5 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-xl hover:from-purple-600 hover:to-indigo-700 transition-all font-semibold shadow-lg flex items-center justify-center gap-2">
                                        <span class="material-icons">assignment_turned_in</span>
                                        Marcar como Entregado
                                    </button>
                                ` : ''}

                                <button onclick="cerrarModalDetalle()"
                                        class="w-full px-5 py-3 bg-gradient-to-r from-pink-500 to-rose-600 text-white rounded-xl hover:from-pink-600 hover:to-rose-700 transition-all font-semibold shadow-lg">
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    `;

                        document.getElementById('modal-detalle-servicio').classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error al obtener detalle:', error);
                }
            }

            function cerrarModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function confirmarEntregaDesdeDetalle(codServ) {
            // Cerrar modal de detalle
            cerrarModalDetalle();

            // Buscar el servicio
            const servicio = Object.values(serviciosPorHorario)
                .flat()
                .find(s => s.codServ === codServ);

            if (!servicio) {
                mostrarAlerta('No se encontró el servicio', 'error');
                return;
            }

            servicioActual = servicio;
            const paciente = `${servicio.paciente?.nomPa || ''} ${servicio.paciente?.paternoPa || ''} ${servicio.paciente?.maternoPa || ''}`.trim();

            document.getElementById('entregar-nro-servicio').textContent = servicio.nroServ;
            document.getElementById('entregar-paciente').textContent = paciente;
            document.getElementById('modal-confirmar-entrega').classList.remove('hidden');
        }
                    function cerrarModalDetalle() {
                        document.getElementById('modal-detalle-servicio').classList.add('hidden');
                    }

                    function ocultarCalendario() {
                        document.getElementById('calendario-container').classList.add('hidden');
                        document.getElementById('sin-seleccion').classList.remove('hidden');
                        document.getElementById('info-cronograma').innerHTML = 'Seleccione un cronograma para ver la información';
                    }

                    function abrirModalAsignarCita() {
                        document.getElementById('form-asignar-cita').reset();
                        horarioSeleccionado = null;
                        horarioPreseleccionado = null;

                        const ahora = new Date();
                        document.getElementById('fechaSol').valueAsDate = ahora;
                        document.getElementById('horaSol').value = ahora.toTimeString().slice(0, 5);

                        // Mostrar selector de horarios (viene del botón principal)
                        document.getElementById('horarios-container-modal').classList.remove('hidden');
                        document.getElementById('horario-preseleccionado-info').classList.add('hidden');

                        document.getElementById('modal-asignar-cita').classList.remove('hidden');
                    }


                    function asignarCitaEnHorario(horario) {
                        // Reset formularios
                        document.getElementById('form-asignar-cita').reset();
                        document.getElementById('form-nuevo-paciente').reset();
                        document.getElementById('form-nuevo-medico').reset();

                        const ahora = new Date();
                        document.getElementById('fechaSol').valueAsDate = ahora;
                        document.getElementById('horaSol').value = ahora.toTimeString().slice(0, 5);

                        // Guardar horario
                        horarioSeleccionado = horario.length === 5 ? horario + ':00' : horario;
                        document.getElementById('horaCrono').value = horarioSeleccionado;
                        document.getElementById('horario-preseleccionado-texto').textContent = horario.substring(0, 5);

                        // Fecha del cronograma
                        if (cronogramaSeleccionado) {
                            document.getElementById('fechaCrono-modal').value = cronogramaSeleccionado.fechaCrono;
                            calcularNroFicha();
                        }

                        // Mostrar vista de cita
                        mostrarVista('cita');
                        document.getElementById('modal-asignar-cita').classList.remove('hidden');
                    }
                    function cerrarModalAsignarCita() {
                        document.getElementById('modal-asignar-cita').classList.add('hidden');
                        horarioSeleccionado = null;
                    }

                    async function calcularNroFicha() {
                        const fechaCrono = document.getElementById('fechaCrono-modal').value;
                        const displayFicha = document.getElementById('nroFicha-display');

                        if (!fechaCrono) {
                            displayFicha.textContent = '-';
                            return;
                        }

                        try {
                            const response = await fetch(`/api/enfermera/servicios/calcular-ficha/${fechaCrono}`);
                            const data = await response.json();
                            if (data.success) {
                                displayFicha.textContent = data.data.nroFicha;
                            }
                        } catch (error) {
                            console.error('Error al calcular ficha:', error);
                            displayFicha.textContent = '-';
                        }
                    }
                    async function cargarHorariosDisponiblesModal() {
                        const fechaCrono = document.getElementById('fechaCrono-modal').value;
                        if (!fechaCrono) return;

                        try {
                            const response = await fetch(`/api/enfermera/servicios/horarios-disponibles/${fechaCrono}`);
                            const data = await response.json();

                            if (data.success) {
                                const horariosDisponibles = data.data.disponibles || [];
                                const espaciosPorHora = data.data.espacios_por_hora || {};

                                renderizarHorariosModal('horarios-manana-modal', horariosDisponibles, espaciosPorHora, 8, 13);
                                renderizarHorariosModal('horarios-tarde-modal', horariosDisponibles, espaciosPorHora, 14, 20);
                            }
                        } catch (error) {
                            console.error('Error:', error);
                        }
                    }

                    function renderizarHorariosModal(containerId, horariosDisponibles, espaciosPorHora, horaInicio, horaFin) {
                        const container = document.getElementById(containerId);
                        container.innerHTML = '';

                        for (let hora = horaInicio; hora <= horaFin; hora++) {
                            renderizarSlotHorarioModal(container, hora, 0, espaciosPorHora);
                            if (!(hora === horaFin && horaFin === 20)) {
                                renderizarSlotHorarioModal(container, hora, 30, espaciosPorHora);
                            }
                        }
                    }

                    function renderizarSlotHorarioModal(container, hora, minutos, espaciosPorHora) {
                        const horario = `${String(hora).padStart(2, '0')}:${String(minutos).padStart(2, '0')}:00`;
                        const horarioFormateado = horario.substring(0, 5);

                        const espacios = espaciosPorHora[horario];
                        const espaciosDisponibles = espacios ? espacios.disponibles : 1;
                        const estaDisponible = espaciosDisponibles > 0;

                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = `horario-btn px-3 py-2 rounded-lg border-2 font-semibold text-sm ${!estaDisponible
                            ? 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                            : 'bg-white text-gray-700 border-gray-300 hover:border-pink-500 hover:bg-pink-50'
                            }`;

                        button.innerHTML = `
                                                                                                                                                                                                                                                            <div class="font-bold text-base">${horarioFormateado}</div>
                                                                                                                                                                                                                                                            <div class="text-xs mt-1 ${estaDisponible ? 'text-emerald-600' : 'text-red-600'}">
                                                                                                                                                                                                                                                                ${estaDisponible ? '✓ Libre' : '✗ Ocupado'}
                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                        `;

                        button.disabled = !estaDisponible;

                        if (estaDisponible) {
                            button.onclick = () => seleccionarHorarioModal(horario, button);
                        }

                        container.appendChild(button);
                    }

                    function seleccionarHorarioModal(horario, botonElement) {
                        document.querySelectorAll('#horarios-manana-modal button, #horarios-tarde-modal button').forEach(btn => {
                            btn.classList.remove('selected', 'bg-pink-500', 'text-white', 'border-pink-600');
                            if (!btn.disabled) {
                                btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
                            }
                        });

                        if (botonElement) {
                            botonElement.classList.add('selected');
                        }

                        horarioSeleccionado = horario;
                        document.getElementById('horaCrono').value = horario;
                    }

                    async function guardarCita(e) {
                        e.preventDefault();

                        if (!horarioSeleccionado) {
                            mostrarAlerta('Error: No hay horario seleccionado', 'error');
                            return;
                        }

                        const fechaCronoModal = document.getElementById('fechaCrono-modal').value;
                        if (!fechaCronoModal) {
                            mostrarAlerta('Error: No hay fecha de cronograma', 'error');
                            return;
                        }

                        const datos = {
                            fechaSol: document.getElementById('fechaSol').value,
                            horaSol: document.getElementById('horaSol').value,
                            tipoAseg: document.getElementById('tipoAseg').value,
                            codPa: document.getElementById('codPa').value,
                            codMed: document.getElementById('codMed').value,
                            codTest: document.getElementById('codTest').value,
                            fechaCrono: fechaCronoModal,
                            horaCrono: horarioSeleccionado
                        };

                        try {
                            const response = await fetch('/api/enfermera/servicios', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify(datos)
                            });

                            const data = await response.json();

                            if (data.success) {
                                mostrarAlerta('✅ Cita asignada exitosamente', 'success');
                                cerrarModalAsignarCita();

                                if (cronogramaSeleccionado && datos.fechaCrono === cronogramaSeleccionado.fechaCrono) {
                                    cargarServiciosPorFecha(datos.fechaCrono);
                                }
                                cargarCronogramas();
                            } else {
                                mostrarAlerta(data.message || 'Error al asignar la cita', 'error');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            mostrarAlerta('Error al asignar la cita', 'error');
                        }
                    }


                    function abrirModalNuevoPaciente() {
                        window.open('{{ route("enfermera.calendario.agregar-paciente") }}', '_blank', 'width=900,height=700');
                    }

                    function abrirModalNuevoMedico() {
                        window.open('{{ route("enfermera.calendario.agregar-medico") }}', '_blank', 'width=900,height=600');
                    }

                    function formatearFecha(fecha) {
                        if (!fecha) return 'N/A';
                        const d = new Date(fecha);
                        return d.toLocaleDateString('es-ES', { day: '2-digit', month: 'long', year: 'numeric' });
                    }

                    function mostrarAlerta(mensaje, tipo = 'success') {
                        const alerta = document.getElementById('alerta');
                        const iconos = { success: 'check_circle', error: 'error', info: 'info' };
                        const colores = {
                            success: 'bg-emerald-50 border-emerald-300 text-emerald-800',
                            error: 'bg-red-50 border-red-300 text-red-800',
                            info: 'bg-blue-50 border-blue-300 text-blue-800'
                        };

                        alerta.className = `p-4 rounded-xl border-2 flex items-center ${colores[tipo]} mb-4 shadow-md`;
                        alerta.innerHTML = `
                                                                                                                                                                                                                                                            <span class="material-icons mr-2 text-xl">${iconos[tipo]}</span>
                                                                                                                                                                                                                                                            <span class="font-semibold">${mensaje}</span>
                                                                                                                                                                                                                                                        `;
                        alerta.classList.remove('hidden');

                        setTimeout(() => alerta.classList.add('hidden'), 5000);
                    }
                </script>
    @endpush

@endsection
