@extends('personal.layouts.personal')

@section('title', 'Servicios')

@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('personal.home')],
            ['label' => 'Servicios', 'url' => route('personal.servicios.servicios')],
            ['label' => 'Todos los Servicios']
        ];
    @endphp

    <style>
        .step-circle {
            transition: all 0.3s ease;
            position: relative;
            z-index: 10;
        }

        .step-line {
            transition: all 0.3s ease;
        }

        .form-step {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .horario-btn {
            transition: all 0.2s ease;
        }

        .horario-btn:hover:not(:disabled) {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .horario-btn.selected {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-weight: bold;
            border-color: #059669;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-wrap gap-3 items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="material-icons text-white text-2xl">medical_services</span>
                        </div>
                        Gestión de Servicios
                    </h1>
                    <p class="text-emerald-600 font-medium ml-15">Todos los servicios registrados en el sistema</p>
                </div>
                <button id="btn-nuevo-servicio"
                    class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all shadow-md hover:shadow-lg font-semibold transform hover:scale-105">
                    <span class="material-icons">add_circle</span>
                    <span>Nuevo Servicio</span>
                </button>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div
                class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <span class="material-icons text-3xl">schedule</span>
                    </div>
                    <span class="text-xs font-bold bg-white bg-opacity-25 px-3 py-1 rounded-full">HOY</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="stat-hoy">0</p>
                <p class="text-sm opacity-90 font-medium">Servicios Hoy</p>
            </div>

            <div
                class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <span class="material-icons text-3xl">pending_actions</span>
                    </div>
                    <span class="text-xs font-bold bg-white bg-opacity-25 px-3 py-1 rounded-full">AHORA</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="stat-proceso">0</p>
                <p class="text-sm opacity-90 font-medium">En Proceso</p>
            </div>

            <div
                class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <span class="material-icons text-3xl">event</span>
                    </div>
                    <span class="text-xs font-bold bg-white bg-opacity-25 px-3 py-1 rounded-full">HOY</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="stat-programados">0</p>
                <p class="text-sm opacity-90 font-medium">Programados</p>
            </div>

            <div
                class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <span class="material-icons text-3xl">check_circle</span>
                    </div>
                    <span class="text-xs font-bold bg-white bg-opacity-25 px-3 py-1 rounded-full">HOY</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="stat-atendidos">0</p>
                <p class="text-sm opacity-90 font-medium">Atendidos</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <span class="material-icons text-sm text-emerald-600">search</span>
                        Buscar
                    </label>
                    <input type="text" id="buscar-servicio"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                        placeholder="Buscar por nro servicio, paciente o HCI...">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <span class="material-icons text-sm text-emerald-600">filter_alt</span>
                        Estado
                    </label>
                    <select id="filtro-estado"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 transition-all font-medium">
                        <option value="">Todos</option>
                        <option value="Programado">Programado</option>
                        <option value="EnProceso">En Proceso</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <span class="material-icons text-sm text-emerald-600">security</span>
                        Tipo Seguro
                    </label>
                    <select id="filtro-tipo-aseg"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 transition-all font-medium">
                        <option value="">Todos</option>
                        <option value="AsegEmergencia">Aseg. Emergencia</option>
                        <option value="AsegRegular">Aseg. Regular</option>
                        <option value="NoAsegEmergencia">No Aseg. Emergencia</option>
                        <option value="NoAsegRegular">No Aseg. Regular</option>
                    </select>
                </div>
            </div>

        </div>

        <!-- Loader -->
        <div id="loader" class="hidden flex justify-center items-center py-12">
            <div class="flex flex-col items-center gap-3">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-emerald-200 border-t-emerald-600"></div>
                <span class="text-sm font-medium text-gray-600">Cargando servicios...</span>
            </div>
        </div>

        <!-- Mensaje sin datos -->
        <div id="no-data" class="hidden bg-white rounded-xl shadow-sm border border-gray-100 p-12">
            <div class="flex flex-col items-center justify-center">
                <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <span class="material-icons text-gray-300" style="font-size: 80px;">inbox</span>
                </div>
                <p class="text-gray-800 text-xl font-bold mb-2">No hay servicios registrados</p>
                <p class="text-gray-500 text-sm">Comienza creando un nuevo servicio</p>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" id="tabla-container">
            <div class="p-6 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-emerald-200">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-white">list_alt</span>
                    </div>
                    Servicios Registrados
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead
                        class="text-xs text-gray-700 uppercase bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold">Nro. Servicio</th>
                            <th scope="col" class="px-6 py-4 font-bold">Paciente</th>
                            <th scope="col" class="px-6 py-4 font-bold">Tipo Estudio</th>
                            <th scope="col" class="px-6 py-4 font-bold">Médico</th>
                            <th scope="col" class="px-6 py-4 font-bold">Tipo Seguro</th>
                            <th scope="col" class="px-6 py-4 font-bold">Fecha/Hora Crono</th>
                            <th scope="col" class="px-6 py-4 font-bold">Estado</th>
                            <th scope="col" class="px-6 py-4 font-bold text-center" style="min-width: 300px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-servicios">
                        <!-- Cargando -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Crear Servicio (Multi-paso) -->
    <div id="modal-servicio" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-6 border w-full max-w-4xl shadow-2xl rounded-xl bg-white mb-10">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                        <span class="material-icons text-white">add</span>
                    </div>
                    <span id="titulo-modal">Nuevo Servicio</span>
                </h3>
                <button onclick="cerrarModal('modal-servicio')"
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-colors">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <!-- Stepper -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <!-- Step 1 -->
                    <div class="flex-1 flex items-center">
                        <div class="relative flex flex-col items-center flex-1">
                            <div
                                class="step-circle step-1 flex items-center justify-center w-12 h-12 rounded-full border-2 border-emerald-600 bg-emerald-600 text-white font-bold transition-all">
                                <span class="step-number">1</span>
                            </div>
                            <div class="text-xs mt-2 font-semibold text-emerald-600 text-center step-label-1">Información
                                Básica</div>
                        </div>
                        <div class="step-line step-line-1 flex-1 h-1 bg-gray-300 mx-2"></div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex-1 flex items-center">
                        <div class="relative flex flex-col items-center flex-1">
                            <div
                                class="step-circle step-2 flex items-center justify-center w-12 h-12 rounded-full border-2 border-gray-300 bg-white text-gray-500 font-bold transition-all">
                                <span class="step-number">2</span>
                            </div>
                            <div class="text-xs mt-2 font-medium text-gray-500 text-center step-label-2">Paciente y Médico
                            </div>
                        </div>
                        <div class="step-line step-line-2 flex-1 h-1 bg-gray-300 mx-2"></div>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex-1 flex items-center">
                        <div class="relative flex flex-col items-center flex-1">
                            <div
                                class="step-circle step-3 flex items-center justify-center w-12 h-12 rounded-full border-2 border-gray-300 bg-white text-gray-500 font-bold transition-all">
                                <span class="step-number">3</span>
                            </div>
                            <div class="text-xs mt-2 font-medium text-gray-500 text-center step-label-3">Estudio</div>
                        </div>
                        <div class="step-line step-line-3 flex-1 h-1 bg-gray-300 mx-2"></div>
                    </div>

                    <!-- Step 4: NUEVO PASO PARA HORARIOS -->
                    <div class="flex-1 flex items-center">
                        <div class="relative flex flex-col items-center flex-1">
                            <div
                                class="step-circle step-4 flex items-center justify-center w-12 h-12 rounded-full border-2 border-gray-300 bg-white text-gray-500 font-bold transition-all">
                                <span class="step-number">4</span>
                            </div>
                            <div class="text-xs mt-2 font-medium text-gray-500 text-center step-label-4">Horario</div>
                        </div>
                    </div>
                </div>
            </div>

            <form id="form-servicio" class="space-y-6">
                <!-- Paso 1: Información Básica -->
                <div class="form-step active" data-step="1">
                    <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-6 rounded-lg">
                        <div class="flex items-center">
                            <span class="material-icons text-emerald-600 mr-2">info</span>
                            <p class="text-sm text-emerald-800 font-semibold">Complete la información básica del servicio
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Fecha Solicitud <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="fechaSol" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Hora Solicitud <span class="text-red-500">*</span>
                            </label>
                            <input type="time" id="horaSol" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Nro. Servicio
                            </label>
                            <input type="text" id="nroServ" placeholder="Se genera automático" readonly
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg bg-gray-50 text-gray-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Tipo de Seguro <span class="text-red-500">*</span>
                            </label>
                            <select id="tipoAseg" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 transition-all font-medium">
                                <option value="">Seleccione...</option>
                                <option value="AsegEmergencia">Asegurado - Emergencia</option>
                                <option value="AsegRegular">Asegurado - Regular</option>
                                <option value="NoAsegEmergencia">No Asegurado - Emergencia</option>
                                <option value="NoAsegRegular">No Asegurado - Regular</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Nro. Ficha
                            </label>
                            <input type="text" id="nroFicha" readonly
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed"
                                placeholder="Se asigna automáticamente">
                            <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                <span class="material-icons text-xs">info</span>
                                Se asigna según el cronograma seleccionado
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Paso 2: Paciente y Médico -->
                <div class="form-step hidden" data-step="2">
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-lg">
                        <div class="flex items-center">
                            <span class="material-icons text-blue-600 mr-2">people</span>
                            <p class="text-sm text-blue-800 font-semibold">Seleccione el paciente y médico solicitante</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Paciente <span class="text-red-500">*</span>
                            </label>
                            <select id="codPa" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 transition-all font-medium">
                                <option value="">Seleccione un paciente</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Médico Solicitante <span class="text-red-500">*</span>
                            </label>
                            <select id="codMed" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 transition-all font-medium">
                                <option value="">Seleccione un médico</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Paso 3: Tipo de Estudio -->
                <div class="form-step hidden" data-step="3">
                    <div class="bg-purple-50 border-l-4 border-purple-500 p-4 mb-6 rounded-lg">
                        <div class="flex items-center">
                            <span class="material-icons text-purple-600 mr-2">science</span>
                            <p class="text-sm text-purple-800 font-semibold">Configure el tipo de estudio y cronograma</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Tipo de Estudio <span class="text-red-500">*</span>
                            </label>
                            <select id="codTest" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 transition-all font-medium">
                                <option value="">Seleccione un tipo de estudio</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Fecha Cronograma <span class="text-red-500">*</span>
                            </label>
                            <select id="fechaCrono" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 transition-all font-medium">
                                <option value="">Seleccione una fecha disponible</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Paso 4: NUEVO - Selección de Horario -->
                <div class="form-step hidden" data-step="4">
                    <div class="bg-teal-50 border-l-4 border-teal-500 p-4 mb-6 rounded-lg">
                        <div class="flex items-center">
                            <span class="material-icons text-teal-600 mr-2">schedule</span>
                            <p class="text-sm text-teal-800 font-semibold">Seleccione el horario de atención (intervalos de
                                30 minutos)</p>
                        </div>
                    </div>

                    <div id="horarios-container" class="space-y-6">
                        <!-- Horarios de Mañana -->
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="material-icons text-amber-600">wb_sunny</span>
                                Horarios de Mañana (08:00 - 13:30) - cada 30 minutos
                            </h4>
                            <div class="grid grid-cols-5 gap-3" id="horarios-manana">
                                <!-- Se llenarán dinámicamente -->
                            </div>
                        </div>

                        <!-- Horarios de Tarde -->
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="material-icons text-blue-600">wb_twilight</span>
                                Horarios de Tarde (14:00 - 20:00) - cada 30 minutos
                            </h4>
                            <div class="grid grid-cols-6 gap-3" id="horarios-tarde">
                                <!-- Se llenarán dinámicamente -->
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="horaCrono" name="horaCrono" required>
                </div>

                <!-- Botones de navegación -->
                <div class="flex justify-between items-center pt-6 border-t-2 border-gray-200">
                    <button type="button" id="btn-anterior"
                        class="flex items-center gap-2 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                        <span class="material-icons text-sm">arrow_back</span>
                        <span>Anterior</span>
                    </button>

                    <div class="flex gap-3">
                        <button type="button" onclick="cerrarModal('modal-servicio')"
                            class="flex items-center gap-2 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-semibold">
                            <span class="material-icons text-sm">close</span>
                            <span>Cancelar</span>
                        </button>

                        <button type="button" id="btn-siguiente"
                            class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all shadow-md font-semibold">
                            <span>Siguiente</span>
                            <span class="material-icons text-sm">arrow_forward</span>
                        </button>

                        <button type="submit" id="btn-guardar"
                            class="hidden flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all shadow-md font-semibold">
                            <span class="material-icons text-sm">save</span>
                            <span>Guardar Servicio</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Gestionar Diagnóstico -->
    <div id="modal-diagnostico"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-0 border w-full max-w-2xl shadow-2xl rounded-xl bg-white">
            <!-- Header -->
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-t-xl p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <span class="material-icons text-white text-2xl">medical_services</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Gestionar Diagnóstico</h3>
                            <p class="text-sm text-emerald-100">Complete o actualice el diagnóstico del servicio</p>
                        </div>
                    </div>
                    <button onclick="cerrarModalDiagnostico()"
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                        <span class="material-icons">close</span>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-lg">
                    <p class="text-sm text-emerald-800 font-medium flex items-center gap-2">
                        <span class="material-icons text-emerald-600">info</span>
                        Al guardar el diagnóstico, el servicio pasará automáticamente a estado <strong>"Atendido"</strong>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">
                        Diagnóstico <span class="text-red-600">*</span>
                    </label>
                    <textarea id="diagnostico-texto" rows="6" maxlength="500" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 transition-all resize-none font-medium"
                        placeholder="Ingrese el diagnóstico del paciente..."></textarea>
                    <div class="flex items-center justify-between mt-2">
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            <span class="material-icons text-xs">info</span>
                            Máximo 500 caracteres
                        </p>
                        <p class="text-xs font-semibold text-gray-600">
                            <span id="caracteres-actuales">0</span>/500
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">
                        Tipo de Diagnóstico <span class="text-red-600">*</span>
                    </label>
                    <select id="tipo-diagnostico" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 transition-all font-medium">
                        <option value="">Seleccione el tipo</option>
                        <option value="sol">Solicitado</option>
                        <option value="eco">Ecográfico</option>
                    </select>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex gap-3">
                <button type="button" onclick="cerrarModalDiagnostico()"
                    class="flex-1 px-5 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-semibold">
                    Cancelar
                </button>
                <button type="button" onclick="guardarDiagnostico()"
                    class="flex-1 px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all shadow-md font-semibold">
                    Guardar y Marcar como Atendido
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Ver Detalle -->
    <div id="modal-detalle"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div
            class="relative mx-auto p-0 border w-full max-w-3xl shadow-2xl rounded-xl bg-white max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 p-6 rounded-t-xl z-10">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="material-icons text-emerald-600">visibility</span>
                        Detalle del Servicio
                    </h3>
                    <button onclick="cerrarModal('modal-detalle')"
                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-colors">
                        <span class="material-icons">close</span>
                    </button>
                </div>
            </div>
            <div id="detalle-servicio-content" class="p-6">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>

    <!-- Modal Cancelar -->
    <div id="modal-cancelar"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-0 border w-full max-w-md shadow-2xl rounded-xl bg-white">
            <div class="bg-gradient-to-r from-red-500 to-rose-600 rounded-t-xl p-5">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-3">
                        <span class="material-icons text-white text-2xl">cancel</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Cancelar Servicio</h3>
                        <p class="text-sm text-red-100">Esta acción cambiará el estado del servicio</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-4">
                    <p class="text-gray-800 font-medium">¿Está seguro que desea cancelar este servicio?</p>
                    <p class="text-sm text-gray-600 mt-2">El servicio pasará a estado <strong>"Cancelado"</strong> y no
                        podrá ser procesado</p>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex gap-3">
                <button type="button" onclick="cerrarModal('modal-cancelar')"
                    class="flex-1 px-5 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-semibold">
                    No, mantener
                </button>
                <button type="button" id="btn-confirmar-cancelar"
                    class="flex-1 px-5 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-lg hover:from-red-600 hover:to-rose-700 transition-all shadow-md font-semibold">
                    Sí, cancelar servicio
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Variables globales
            let serviciosData = [];
            let servicioActual = null;
            let pasoActual = 1;
            const totalPasos = 4;  // Ahora son 4 pasos
            let datosFormulario = {};
            let horarioSeleccionado = null;
            document.addEventListener('DOMContentLoaded', function () {
                cargarServicios();
                cargarEstadisticas();

                // Event listeners
                document.getElementById('buscar-servicio').addEventListener('input', filtrarServicios);
                document.getElementById('filtro-estado').addEventListener('change', filtrarServicios);
                document.getElementById('filtro-tipo-aseg').addEventListener('change', filtrarServicios);
                document.getElementById('btn-nuevo-servicio').addEventListener('click', abrirModalNuevoServicio);
                document.getElementById('btn-siguiente').addEventListener('click', siguientePaso);
                document.getElementById('btn-anterior').addEventListener('click', anteriorPaso);
                document.getElementById('form-servicio').addEventListener('submit', guardarServicio);
                document.getElementById('btn-confirmar-cancelar').addEventListener('click', confirmarCancelarServicio);
                document.getElementById('fechaCrono').addEventListener('change', async function () {
                    await calcularNroFicha.call(this);
                    await cargarHorariosDisponibles();
                });
            });

            // ==========================================
            // FUNCIONES DE CARGA DE DATOS
            // ==========================================

            async function cargarServicios() {
                mostrarLoader(true);
                const tablaContainer = document.getElementById('tabla-container');
                const noData = document.getElementById('no-data');

                try {
                    const response = await fetch('/api/personal/servicios');
                    const data = await response.json();

                    if (data.success) {
                        serviciosData = data.data.filter(s =>
                            s.estado !== 'Atendido' && s.estado !== 'Entregado'
                        );

                        renderServicios(serviciosData);

                        if (serviciosData.length > 0) {
                            tablaContainer.classList.remove('hidden');
                            noData.classList.add('hidden');
                        } else {
                            tablaContainer.classList.add('hidden');
                            noData.classList.remove('hidden');
                        }
                    } else {
                        tablaContainer.classList.add('hidden');
                        noData.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('Error al cargar los servicios', 'error');
                } finally {
                    mostrarLoader(false);
                }
            }


            async function cargarEstadisticas() {
                try {
                    const response = await fetch('/api/personal/servicios/estadisticas');
                    const data = await response.json();

                    if (data.success) {
                        document.getElementById('stat-hoy').textContent = data.data.hoy || 0;
                        document.getElementById('stat-proceso').textContent = data.data.enProceso || 0;
                        document.getElementById('stat-programados').textContent = data.data.programados || 0;
                        document.getElementById('stat-atendidos').textContent = data.data.atendidos || 0;
                    }
                } catch (error) {
                    console.error('Error al cargar estadísticas:', error);
                }
            }

            // ==========================================
            // FUNCIONES DE RENDERIZADO
            // ==========================================

            function renderServicios(servicios) {
                const tbody = document.getElementById('tabla-servicios');
                tbody.innerHTML = '';

                if (servicios.length === 0) {
                    tbody.innerHTML = `
                                                                                                                                                                                    <tr>
                                                                                                                                                                                        <td colspan="8" class="px-6 py-12 text-center">
                                                                                                                                                                                            <div class="flex flex-col items-center gap-3">
                                                                                                                                                                                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                                                                                                                                                                                    <span class="material-icons text-gray-400 text-3xl">inbox</span>
                                                                                                                                                                                                </div>
                                                                                                                                                                                                <p class="text-gray-500 font-medium">No se encontraron servicios</p>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </td>
                                                                                                                                                                                    </tr>
                                                                                                                                                                                `;
                    return;
                }


                servicios.forEach(servicio => {
                    const paciente = `${servicio.paciente?.nomPa || ''} ${servicio.paciente?.paternoPa || ''}`.trim();
                    const medico = `${servicio.medico?.nomMed || ''} ${servicio.medico?.paternoMed || ''}`.trim();

                    const estadoConfig = {
                        'Programado': { class: 'bg-orange-100 text-orange-700 border-orange-300', icon: 'schedule' },
                        'EnProceso': { class: 'bg-blue-100 text-blue-700 border-blue-300', icon: 'pending_actions' },
                        'Atendido': { class: 'bg-emerald-100 text-emerald-700 border-emerald-300', icon: 'check_circle' },
                        'Entregado': { class: 'bg-purple-100 text-purple-700 border-purple-300', icon: 'done_all' },
                        'Cancelado': { class: 'bg-red-100 text-red-700 border-red-300', icon: 'cancel' }
                    };

                    const estado = estadoConfig[servicio.estado] || estadoConfig['Programado'];

                    const tipoAsegConfig = {
                        'AsegEmergencia': 'bg-red-100 text-red-700 border-red-300',
                        'AsegRegular': 'bg-emerald-100 text-emerald-700 border-emerald-300',
                        'NoAsegEmergencia': 'bg-orange-100 text-orange-700 border-orange-300',
                        'NoAsegRegular': 'bg-blue-100 text-blue-700 border-blue-300'
                    };

                    const tipoAsegClass = tipoAsegConfig[servicio.tipoAseg] || tipoAsegConfig['NoAsegRegular'];
                    const tipoAsegTexto = servicio.tipoAseg?.replace('Aseg', 'Aseg. ').replace('NoAseg', 'No Aseg. ') || 'N/A';

                    const puedeEditar = servicio.estado !== 'Cancelado' && servicio.estado !== 'Entregado' && servicio.estado !== 'Atendido';

                    // Formatear horaCrono
                    const horaCrono = servicio.horaCrono ? servicio.horaCrono.substring(0, 5) : 'Sin hora';

                    const fila = `
                                                                                                                                                                            <tr class="border-b hover:bg-emerald-50 transition-colors">
                                                                                                                                                                                <td class="px-6 py-4">
                                                                                                                                                                                    <span class="font-bold text-emerald-600">${servicio.nroServ || 'N/A'}</span>
                                                                                                                                                                                </td>
                                                                                                                                                                                <td class="px-6 py-4">
                                                                                                                                                                                    <div class="font-semibold text-gray-900">${paciente}</div>
                                                                                                                                                                                    <div class="text-xs text-gray-500">${servicio.paciente?.nroHCI || 'Sin HCI'}</div>
                                                                                                                                                                                </td>
                                                                                                                                                                                <td class="px-6 py-4 text-gray-700">${servicio.tipo_estudio?.descripcion || 'N/A'}</td>
                                                                                                                                                                                <td class="px-6 py-4 text-gray-700">${medico}</td>
                                                                                                                                                                                <td class="px-6 py-4">
                                                                                                                                                                                    <span class="px-3 py-1.5 text-xs font-bold rounded-full border ${tipoAsegClass}">
                                                                                                                                                                                        ${tipoAsegTexto}
                                                                                                                                                                                    </span>
                                                                                                                                                                                </td>
                                                                                                                                                                                <td class="px-6 py-4">
                                                                                                                                                                                    <div class="font-semibold text-gray-900">${formatearFecha(servicio.fechaCrono)}</div>
                                                                                                                                                                                    <div class="text-xs text-teal-600 font-bold flex items-center gap-1">
                                                                                                                                                                                        <span class="material-icons text-xs">schedule</span>
                                                                                                                                                                                        ${horaCrono}
                                                                                                                                                                                    </div>
                                                                                                                                                                                </td>
                                                                                                                                                                                <td class="px-6 py-4">
                                                                                                                                                                                    <span class="px-3 py-1.5 text-xs font-bold rounded-full border ${estado.class} flex items-center gap-1 w-fit">
                                                                                                                                                                                        <span class="material-icons text-xs">${estado.icon}</span>
                                                                                                                                                                                        ${servicio.estado}
                                                                                                                                                                                    </span>
                                                                                                                                                                                </td>
                                                                                                                                                                                <td class="px-6 py-4">
                                                                                                                                                                                    <div class="flex items-center gap-2 justify-center">
                                                                                                                                                                                        <button onclick="verDetalle(${servicio.codServ})"
                                                                                                                                                                                            class="inline-flex items-center px-3 py-2 text-sm font-semibold text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all border border-blue-200 hover:shadow-md"
                                                                                                                                                                                            title="Ver detalles">
                                                                                                                                                                                            <span class="material-icons text-base">visibility</span>
                                                                                                                                                                                        </button>
                                                                                                                                                                                        ${puedeEditar ? `
                                                                                                                                                                                            <button onclick="abrirModalDiagnostico(${servicio.codServ})"
                                                                                                                                                                                                class="inline-flex items-center px-3 py-2 text-sm font-semibold text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-all border border-emerald-200 hover:shadow-md"
                                                                                                                                                                                                title="Gestionar diagnóstico">
                                                                                                                                                                                                <span class="material-icons text-base">medical_services</span>
                                                                                                                                                                                            </button>
                                                                                                                                                                                            <button onclick="confirmarCancelar(${servicio.codServ})"
                                                                                                                                                                                                class="inline-flex items-center px-3 py-2 text-sm font-semibold text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-all border border-red-200 hover:shadow-md"
                                                                                                                                                                                                title="Cancelar servicio">
                                                                                                                                                                                                <span class="material-icons text-base">cancel</span>
                                                                                                                                                                                            </button>
                                                                                                                                                                                        ` : ''}
                                                                                                                                                                                    </div>
                                                                                                                                                                                </td>
                                                                                                                                                                            </tr>
                                                                                                                                                                        `;
                    tbody.innerHTML += fila;
                });
            }
            function filtrarServicios() {
                const busqueda = document.getElementById('buscar-servicio').value.toLowerCase();
                const filtroEstado = document.getElementById('filtro-estado').value;
                const filtroTipoAseg = document.getElementById('filtro-tipo-aseg').value;

                const serviciosFiltrados = serviciosData.filter(s => {
                    const paciente = `${s.paciente?.nomPa || ''} ${s.paciente?.paternoPa || ''}`.toLowerCase();
                    const cumpleBusqueda =
                        s.nroServ?.toLowerCase().includes(busqueda) ||
                        paciente.includes(busqueda) ||
                        s.paciente?.nroHCI?.toLowerCase().includes(busqueda);

                    const cumpleEstado = !filtroEstado || s.estado === filtroEstado;
                    const cumpleTipoAseg = !filtroTipoAseg || s.tipoAseg === filtroTipoAseg;

                    return cumpleBusqueda && cumpleEstado && cumpleTipoAseg;
                });

                renderServicios(serviciosFiltrados);
            }

            // ==========================================
            // MODAL NUEVO SERVICIO
            // ==========================================

            async function abrirModalNuevoServicio() {
                try {
                    mostrarLoader(true);

                    const response = await fetch('/api/personal/servicios/datos-formulario');
                    const data = await response.json();

                    if (data.success) {
                        datosFormulario = data.data;

                        llenarSelect('codPa', datosFormulario.pacientes, 'codPa', (p) => `${p.nomPa} ${p.paternoPa || ''} - ${p.nroHCI || 'Sin HCI'}`);
                        llenarSelect('codMed', datosFormulario.medicos, 'codMed', (m) => `${m.nomMed} ${m.paternoMed || ''}`);
                        llenarSelect('codTest', datosFormulario.tiposEstudio, 'codTest', 'descripcion');
                        llenarSelect('fechaCrono', datosFormulario.cronogramas, 'fechaCrono');

                        if (!datosFormulario.cronogramas || datosFormulario.cronogramas.length === 0) {
                            mostrarAlerta('⚠️ No hay cronogramas disponibles. Por favor, cree un cronograma primero.', 'error');
                            mostrarLoader(false);
                            return;
                        }

                        document.getElementById('form-servicio').reset();
                        pasoActual = 1;
                        horarioSeleccionado = null;
                        mostrarPaso(1);

                        const ahora = new Date();
                        document.getElementById('fechaSol').valueAsDate = ahora;
                        document.getElementById('horaSol').value = ahora.toTimeString().slice(0, 5);

                        document.getElementById('modal-servicio').classList.remove('hidden');
                    } else {
                        mostrarAlerta(data.message || 'Error al cargar datos del formulario', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('Error al cargar datos del formulario', 'error');
                } finally {
                    mostrarLoader(false);
                }
            }


            function llenarSelect(selectId, datos, valueKey, textKey) {
                const select = document.getElementById(selectId);
                const optionInicial = select.querySelector('option[value=""]');
                select.innerHTML = '';

                if (optionInicial) {
                    select.appendChild(optionInicial.cloneNode(true));
                }

                if (!datos || datos.length === 0) {
                    if (selectId === 'fechaCrono') {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'No hay cronogramas disponibles';
                        option.disabled = true;
                        select.appendChild(option);
                    }
                    return;
                }

                datos.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item[valueKey];

                    if (selectId === 'fechaCrono') {
                        const fecha = formatearFechaCorta(item.fechaCrono);
                        const fichasDisponibles = item.cantDispo || 0;
                        const fichasTexto = ` (${fichasDisponibles} ${fichasDisponibles === 1 ? 'ficha' : 'fichas'} disponible${fichasDisponibles === 1 ? '' : 's'})`;

                        option.textContent = `${fecha}${fichasTexto}`;

                        if (fichasDisponibles <= 0) {
                            option.disabled = true;
                            option.style.color = '#999';
                            option.style.fontStyle = 'italic';
                            option.textContent = `${fecha} - ❌ SIN CUPO`;
                        } else if (fichasDisponibles <= 3) {
                            option.style.color = '#f59e0b';
                            option.style.fontWeight = 'bold';
                        }
                    } else {
                        option.textContent = typeof textKey === 'function' ? textKey(item) : item[textKey];
                    }

                    select.appendChild(option);
                });
            }
            // ==========================================
            // FUNCIONES DE HORARIOS
            // ==========================================


            async function cargarHorariosDisponibles() {
                const fechaCrono = document.getElementById('fechaCrono').value;

                if (!fechaCrono) {
                    return;
                }

                try {
                    const response = await fetch(`/api/personal/servicios/horarios-disponibles/${fechaCrono}`);
                    const data = await response.json();

                    if (data.success) {
                        const horariosDisponibles = data.data.disponibles || [];
                        const espaciosPorHora = data.data.espacios_por_hora || {};

                        // CAMBIO: Renderizar horarios de mañana (8:00 - 13:30)
                        renderizarHorariosActualizado('horarios-manana', horariosDisponibles, espaciosPorHora, 8, 13, true);

                        // CAMBIO: Renderizar horarios de tarde (14:00 - 20:00)
                        renderizarHorariosActualizado('horarios-tarde', horariosDisponibles, espaciosPorHora, 14, 20, true);
                    } else {
                        mostrarAlerta('Error al cargar horarios disponibles', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('Error al cargar horarios disponibles', 'error');
                }
            }
            function renderizarHorariosActualizado(containerId, horariosDisponibles, espaciosPorHora, horaInicio, horaFin, incluirMedias) {
                const container = document.getElementById(containerId);
                container.innerHTML = '';

                let hayHorarios = false;

                for (let hora = horaInicio; hora <= horaFin; hora++) {
                    // Renderizar hora en punto (XX:00)
                    renderizarSlotHorario(container, hora, 0, horariosDisponibles, espaciosPorHora);
                    hayHorarios = true;

                    // Renderizar media hora (XX:30) solo si no es la última hora
                    if (incluirMedias && !(hora === horaFin && horaFin === 20)) {
                        renderizarSlotHorario(container, hora, 30, horariosDisponibles, espaciosPorHora);
                    }
                }

                if (!hayHorarios) {
                    container.innerHTML = `
                                                                    <div class="col-span-full text-center py-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                                                        <span class="material-icons text-gray-400 text-3xl block mb-2">event_busy</span>
                                                                        <p class="text-sm text-gray-500 font-medium">No hay horarios en este rango</p>
                                                                    </div>
                                                                `;
                }
            }
            function renderizarSlotHorario(container, hora, minutos, horariosDisponibles, espaciosPorHora) {
                const horario = sprintf('%02d:%02d:00', hora, minutos);
                const horarioFormateado = horario.substring(0, 5); // HH:MM

                const espacios = espaciosPorHora[horario];
                const espaciosDisponibles = espacios ? espacios.disponibles : 1;
                const totalOcupados = espacios ? espacios.total : 0;

                // CAMBIO: Ahora solo 1 espacio por slot
                const estaDisponible = espaciosDisponibles > 0;

                const button = document.createElement('button');
                button.type = 'button';
                button.className = `horario-btn px-4 py-3 rounded-lg border-2 font-semibold text-sm relative ${!estaDisponible
                    ? 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                    : 'bg-white text-gray-700 border-gray-300 hover:border-emerald-500'
                    }`;

                button.innerHTML = `
                                        <div class="font-bold text-base">${horarioFormateado}</div>
                                        <div class="text-xs mt-1 ${estaDisponible ? 'text-emerald-600' : 'text-red-600'}">
                                            ${estaDisponible ? '✓ Disponible' : '✗ Ocupado'}
                                        </div>
                                    `;

                button.disabled = !estaDisponible;

                if (estaDisponible) {
                    button.onclick = () => seleccionarHorario(horario, button);
                }

                container.appendChild(button);
            }
            function sprintf(format, ...args) {
                let formatted = format;
                args.forEach((arg, index) => {
                    formatted = formatted.replace('%02d', String(arg).padStart(2, '0'));
                });
                return formatted;
            }

            function seleccionarHorario(horario, botonElement) {
                // Remover selección previa
                document.querySelectorAll('.horario-btn.selected').forEach(btn => {
                    btn.classList.remove('selected');
                });

                // Seleccionar nuevo horario
                botonElement.classList.add('selected');
                horarioSeleccionado = horario;
                document.getElementById('horaCrono').value = horario;
            }

            async function calcularNroFicha() {
                const fechaCrono = this.value;
                const nroFichaInput = document.getElementById('nroFicha');

                if (!fechaCrono) {
                    nroFichaInput.value = '';
                    return;
                }

                try {
                    const response = await fetch(`/api/personal/servicios/calcular-ficha/${fechaCrono}`);
                    const data = await response.json();

                    if (data.success) {
                        nroFichaInput.value = data.data.nroFicha;

                        const cantDispo = data.data.cantDispo;
                        const infoDiv = nroFichaInput.nextElementSibling;

                        if (infoDiv && infoDiv.classList.contains('text-xs')) {
                            infoDiv.innerHTML = `
                                                                                                                            <span class="material-icons text-xs">info</span>
                                                                                                                            Fichas disponibles: ${cantDispo}
                                                                                                                        `;

                            if (cantDispo <= 0) {
                                infoDiv.classList.add('text-red-600');
                                infoDiv.classList.remove('text-gray-500', 'text-orange-600');
                                mostrarAlerta('⚠️ No hay fichas disponibles en este cronograma', 'error');
                            } else if (cantDispo <= 3) {
                                infoDiv.classList.add('text-orange-600');
                                infoDiv.classList.remove('text-gray-500', 'text-red-600');
                            } else {
                                infoDiv.classList.add('text-gray-500');
                                infoDiv.classList.remove('text-red-600', 'text-orange-600');
                            }
                        }
                    } else {
                        mostrarAlerta(data.message || 'No se pudo calcular el número de ficha', 'error');
                        nroFichaInput.value = '';
                    }
                } catch (error) {
                    console.error('Error al calcular ficha:', error);
                    mostrarAlerta('Error al calcular el número de ficha', 'error');
                    nroFichaInput.value = '';
                }
            }

            async function guardarServicio(e) {
                e.preventDefault();

                if (!horarioSeleccionado) {
                    mostrarAlerta('Por favor seleccione un horario', 'error');
                    return;
                }

                const datos = {
                    fechaSol: document.getElementById('fechaSol').value,
                    horaSol: document.getElementById('horaSol').value,
                    tipoAseg: document.getElementById('tipoAseg').value,
                    codPa: document.getElementById('codPa').value,
                    codMed: document.getElementById('codMed').value,
                    codTest: document.getElementById('codTest').value,
                    fechaCrono: document.getElementById('fechaCrono').value,
                    horaCrono: horarioSeleccionado
                };

                try {
                    const response = await fetch('/api/personal/servicios', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(datos)
                    });

                    const data = await response.json();

                    if (data.success) {
                        mostrarAlerta('✅ Servicio creado exitosamente', 'success');
                        cerrarModal('modal-servicio');
                        cargarServicios();
                        cargarEstadisticas();
                        horarioSeleccionado = null;
                    } else {
                        mostrarAlerta(data.message || 'Error al crear el servicio', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('Error al crear el servicio', 'error');
                }
            }

            // ==========================================
            // STEPPER - NAVEGACIÓN ENTRE PASOS
            // ==========================================

            function siguientePaso() {
                if (validarPasoActual()) {
                    pasoActual++;
                    mostrarPaso(pasoActual);
                }
            }

            function anteriorPaso() {
                pasoActual--;
                mostrarPaso(pasoActual);
            }

            function validarPasoActual() {
                if (pasoActual === 1) {
                    const fechaSol = document.getElementById('fechaSol').value;
                    const horaSol = document.getElementById('horaSol').value;
                    const tipoAseg = document.getElementById('tipoAseg').value;

                    if (!fechaSol || !horaSol || !tipoAseg) {
                        mostrarAlerta('Complete todos los campos obligatorios', 'error');
                        return false;
                    }
                } else if (pasoActual === 2) {
                    const codPa = document.getElementById('codPa').value;
                    const codMed = document.getElementById('codMed').value;

                    if (!codPa || !codMed) {
                        mostrarAlerta('Seleccione el paciente y médico', 'error');
                        return false;
                    }
                } else if (pasoActual === 3) {
                    const codTest = document.getElementById('codTest').value;
                    const fechaCrono = document.getElementById('fechaCrono').value;

                    if (!codTest || !fechaCrono) {
                        mostrarAlerta('Seleccione el tipo de estudio y cronograma', 'error');
                        return false;
                    }
                } else if (pasoActual === 4) {
                    if (!horarioSeleccionado) {
                        mostrarAlerta('Seleccione un horario', 'error');
                        return false;
                    }
                }

                return true;
            }

            function mostrarPaso(paso) {
                document.querySelectorAll('.form-step').forEach(step => {
                    step.classList.add('hidden');
                    step.classList.remove('active');
                });

                const pasoElement = document.querySelector(`.form-step[data-step="${paso}"]`);
                if (pasoElement) {
                    pasoElement.classList.remove('hidden');
                    pasoElement.classList.add('active');
                }

                actualizarStepper(paso);

                document.getElementById('btn-anterior').disabled = paso === 1;
                document.getElementById('btn-siguiente').classList.toggle('hidden', paso === totalPasos);
                document.getElementById('btn-guardar').classList.toggle('hidden', paso !== totalPasos);
            }

            function actualizarStepper(pasoActivo) {
                for (let i = 1; i <= totalPasos; i++) {
                    const circle = document.querySelector(`.step-${i}`);
                    const label = document.querySelector(`.step-label-${i}`);
                    const line = document.querySelector(`.step-line-${i}`);

                    if (!circle || !label) continue;

                    if (i < pasoActivo) {
                        circle.className = `step-circle step-${i} flex items-center justify-center w-12 h-12 rounded-full border-2 border-green-600 bg-green-600 text-white font-bold transition-all`;
                        circle.innerHTML = '<span class="material-icons text-xl">check</span>';
                        label.className = `text-xs mt-2 font-bold text-green-600 text-center step-label-${i}`;

                        if (line) {
                            line.className = `step-line step-line-${i} flex-1 h-1 bg-green-600 mx-2 transition-all`;
                        }
                    } else if (i === pasoActivo) {
                        circle.className = `step-circle step-${i} flex items-center justify-center w-12 h-12 rounded-full border-2 border-emerald-600 bg-emerald-600 text-white font-bold transition-all shadow-lg`;
                        circle.innerHTML = `<span class="step-number text-xl">${i}</span>`;
                        label.className = `text-xs mt-2 font-bold text-emerald-600 text-center step-label-${i}`;

                        if (line) {
                            line.className = `step-line step-line-${i} flex-1 h-1 bg-gray-300 mx-2 transition-all`;
                        }
                    } else {
                        circle.className = `step-circle step-${i} flex items-center justify-center w-12 h-12 rounded-full border-2 border-gray-300 bg-white text-gray-500 font-bold transition-all`;
                        circle.innerHTML = `<span class="step-number text-xl">${i}</span>`;
                        label.className = `text-xs mt-2 font-medium text-gray-500 text-center step-label-${i}`;

                        if (line) {
                            line.className = `step-line step-line-${i} flex-1 h-1 bg-gray-300 mx-2 transition-all`;
                        }
                    }
                }
            }
            // ==========================================
            // MODAL DIAGNÓSTICO
            // ==========================================

            async function abrirModalDiagnostico(codServ) {
                const servicio = serviciosData.find(s => s.codServ === codServ);
                if (!servicio) return;

                servicioActual = servicio;
                const modal = document.getElementById('modal-diagnostico');
                const textarea = document.getElementById('diagnostico-texto');
                const select = document.getElementById('tipo-diagnostico');

                // Pre-llenar si ya tiene diagnóstico
                if (servicio.diagnosticos && servicio.diagnosticos.length > 0) {
                    textarea.value = servicio.diagnosticos[0].descripDiag || '';
                    select.value = servicio.diagnosticos[0].pivot?.tipo || '';
                    document.getElementById('caracteres-actuales').textContent = textarea.value.length;
                } else {
                    textarea.value = '';
                    select.value = '';
                    document.getElementById('caracteres-actuales').textContent = '0';
                }

                modal.classList.remove('hidden');
            }

            function cerrarModalDiagnostico() {
                document.getElementById('modal-diagnostico').classList.add('hidden');
                servicioActual = null;
            }

            async function guardarDiagnostico() {
                const diagnosticoTexto = document.getElementById('diagnostico-texto').value.trim();
                const tipoDiagnostico = document.getElementById('tipo-diagnostico').value;

                if (!diagnosticoTexto) {
                    mostrarAlerta('Ingrese el diagnóstico', 'error');
                    return;
                }

                if (!tipoDiagnostico) {
                    mostrarAlerta('Seleccione el tipo de diagnóstico', 'error');
                    return;
                }

                if (!servicioActual) return;

                try {
                    const response = await fetch(`/api/personal/servicios/${servicioActual.codServ}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            diagnosticoTexto: diagnosticoTexto,
                            tipoDiagnostico: tipoDiagnostico
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        mostrarAlerta('✓ Diagnóstico guardado. El servicio ha pasado a estado "Atendido"', 'success');
                        cerrarModalDiagnostico();

                        setTimeout(() => {
                            cargarServicios();
                            cargarEstadisticas();
                        }, 2000);
                    } else {
                        mostrarAlerta(data.message || 'Error al guardar el diagnóstico', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('Error al guardar el diagnóstico', 'error');
                }
            }

            // ==========================================
            // CANCELAR SERVICIO
            // ==========================================

            function confirmarCancelar(codServ) {
                servicioActual = serviciosData.find(s => s.codServ === codServ);
                if (!servicioActual) return;

                document.getElementById('modal-cancelar').classList.remove('hidden');
            }

            async function confirmarCancelarServicio() {
                if (!servicioActual) return;

                try {
                    const response = await fetch(`/api/personal/servicios/${servicioActual.codServ}/cancelar`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        mostrarAlerta('Servicio cancelado exitosamente', 'success');
                        cerrarModal('modal-cancelar');
                        cargarServicios();
                        cargarEstadisticas();
                        servicioActual = null;
                    } else {
                        mostrarAlerta(data.message || 'Error al cancelar el servicio', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('Error al cancelar el servicio', 'error');
                }
            }

            // ==========================================
            // VER DETALLE
            // ==========================================

            async function verDetalle(codServ) {
                const modal = document.getElementById('modal-detalle');
                const contenido = document.getElementById('detalle-servicio-content');

                try {
                    modal.classList.remove('hidden');
                    contenido.innerHTML = `
                                                                                                                                                                                                                    <div class="text-center py-8">
                                                                                                                                                                                                                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-emerald-200 border-t-emerald-600"></div>
                                                                                                                                                                                                                        <p class="mt-4 text-gray-600">Cargando información...</p>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                `;

                    const response = await fetch(`/api/personal/servicios/${codServ}`);
                    const data = await response.json();

                    if (data.success) {
                        const s = data.data;
                        const paciente = `${s.paciente?.nomPa || ''} ${s.paciente?.paternoPa || ''} ${s.paciente?.maternoPa || ''}`.trim();
                        const medico = `${s.medico?.nomMed || ''} ${s.medico?.paternoMed || ''}`.trim();

                        const estadoConfig = {
                            'Programado': { class: 'bg-orange-100 text-orange-700 border-orange-300', icon: 'schedule' },
                            'EnProceso': { class: 'bg-blue-100 text-blue-700 border-blue-300', icon: 'pending_actions' },
                            'Atendido': { class: 'bg-emerald-100 text-emerald-700 border-emerald-300', icon: 'check_circle' },
                            'Entregado': { class: 'bg-purple-100 text-purple-700 border-purple-300', icon: 'done_all' },
                            'Cancelado': { class: 'bg-red-100 text-red-700 border-red-300', icon: 'cancel' }
                        };

                        const estado = estadoConfig[s.estado] || estadoConfig['Programado'];

                        const diagnosticos = s.diagnosticos && s.diagnosticos.length > 0
                            ? s.diagnosticos.map(d => `
                                                                                                                                                                                                                            <div class="bg-emerald-50 p-4 rounded-lg border-l-4 border-emerald-500">
                                                                                                                                                                                                                                <p class="text-sm font-medium text-gray-800 leading-relaxed">${d.descripDiag}</p>
                                                                                                                                                                                                                                <span class="text-xs text-emerald-700 font-bold mt-2 inline-block">Tipo: ${d.pivot?.tipo === 'sol' ? 'Solicitado' : 'Ecográfico'}</span>
                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                        `).join('')
                            : '<p class="text-gray-500 italic text-center py-4">Sin diagnósticos registrados</p>';

                        contenido.innerHTML = `
                                                                                                                                                                                                                        <div class="space-y-6">
                                                                                                                                                                                                                            <!-- Info General -->
                                                                                                                                                                                                                            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 p-6 rounded-xl border border-emerald-200">
                                                                                                                                                                                                                                <div class="flex items-center justify-between mb-4">
                                                                                                                                                                                                                                    <h4 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                                                                                                                                                                                                                                        <span class="material-icons text-emerald-600">info</span>
                                                                                                                                                                                                                                        Información General
                                                                                                                                                                                                                                    </h4>
                                                                                                                                                                                                                                    <span class="px-4 py-2 text-sm font-bold rounded-full border ${estado.class} flex items-center gap-1">
                                                                                                                                                                                                                                        <span class="material-icons text-xs">${estado.icon}</span>
                                                                                                                                                                                                                                        ${s.estado}
                                                                                                                                                                                                                                    </span>
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                <div class="grid grid-cols-2 gap-4">
                                                                                                                                                                                                                                    <div class="bg-white p-4 rounded-lg shadow-sm">
                                                                                                                                                                                                                                        <p class="text-xs text-gray-600 mb-1 font-semibold uppercase">Nro. Servicio</p>
                                                                                                                                                                                                                                        <p class="font-bold text-emerald-600 text-xl">${s.nroServ}</p>
                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                    <div class="bg-white p-4 rounded-lg shadow-sm">
                                                                                                                                                                                                                                        <p class="text-xs text-gray-600 mb-1 font-semibold uppercase">Nro. Ficha</p>
                                                                                                                                                                                                                                        <p class="font-bold text-gray-900 text-xl">${s.nroFicha || 'N/A'}</p>
                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                    <div class="bg-white p-4 rounded-lg shadow-sm">
                                                                                                                                                                                                                                        <p class="text-xs text-gray-600 mb-1 font-semibold uppercase">Tipo de Seguro</p>
                                                                                                                                                                                                                                        <p class="font-bold text-gray-900">${s.tipoAseg?.replace('Aseg', 'Aseg. ').replace('NoAseg', 'No Aseg. ')}</p>
                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                    <div class="bg-white p-4 rounded-lg shadow-sm">
                                                                                                                                                                                                                                        <p class="text-xs text-gray-600 mb-1 font-semibold uppercase">Fecha Solicitud</p>
                                                                                                                                                                                                                                        <p class="font-bold text-gray-900">${formatearFecha(s.fechaSol)}</p>
                                                                                                                                                                                                                                        <p class="text-xs text-emerald-600 font-semibold mt-1">${s.horaSol || 'Sin hora'}</p>
                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                            </div>

                                                                                                                                                                                                                            <!-- Paciente y Médico -->
                                                                                                                                                                                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                                                                                                                                                                                <div class="bg-blue-50 p-5 rounded-xl border border-blue-200">
                                                                                                                                                                                                                                    <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                                                                                                                                                                                                                                        <span class="material-icons text-blue-600">person</span>
                                                                                                                                                                                                                                        Paciente
                                                                                                                                                                                                                                    </h4>
                                                                                                                                                                                                                                    <p class="text-gray-900 font-bold text-lg">${paciente}</p>
                                                                                                                                                                                                                                    <p class="text-sm text-blue-700 font-semibold mt-1">${s.paciente?.nroHCI || 'Sin HCI'}</p>
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                <div class="bg-purple-50 p-5 rounded-xl border border-purple-200">
                                                                                                                                                                                                                                    <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                                                                                                                                                                                                                                        <span class="material-icons text-purple-600">medical_services</span>
                                                                                                                                                                                                                                        Médico Solicitante
                                                                                                                                                                                                                                    </h4>
                                                                                                                                                                                                                                    <p class="text-gray-900 font-bold text-lg">${medico}</p>
                                                                                                                                                                                                                                    <p class="text-sm text-purple-700 font-semibold mt-1">${s.medico?.tipoMed || ''}</p>
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                            </div>

                                                                                                                                                                                                                            <!-- Tipo de Estudio -->
                                                                                                                                                                                                                            <div class="bg-gradient-to-r from-purple-50 to-indigo-50 p-5 rounded-xl border border-purple-200">
                                                                                                                                                                                                                                <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                                                                                                                                                                                                                                    <span class="material-icons text-purple-600">science</span>
                                                                                                                                                                                                                                    Tipo de Estudio
                                                                                                                                                                                                                                </h4>
                                                                                                                                                                                                                                <p class="text-gray-900 font-bold text-xl">${s.tipo_estudio?.descripcion || 'N/A'}</p>
                                                                                                                                                                                                                            </div>

                                                                                                                                                                                                                            <!-- Diagnósticos -->
                                                                                                                                                                                                                            <div>
                                                                                                                                                                                                                                <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2 text-lg">
                                                                                                                                                                                                                                    <span class="material-icons text-emerald-600">assignment</span>
                                                                                                                                                                                                                                    Diagnósticos
                                                                                                                                                                                                                                </h4>
                                                                                                                                                                                                                                <div class="space-y-3">
                                                                                                                                                                                                                                    ${diagnosticos}
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                            </div>

                                                                                                                                                                                                                            <!-- Historial de Fechas -->
                                                                                                                                                                                                                            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                                                                                                                                                                                                                                <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                                                                                                                                                                                                                                    <span class="material-icons text-gray-600">schedule</span>
                                                                                                                                                                                                                                    Historial del Proceso
                                                                                                                                                                                                                                </h4>
                                                                                                                                                                                                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                                                                                                                                                                                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
                                                                                                                                                                                                                                        <div class="flex items-center gap-2 mb-2">
                                                                                                                                                                                                                                            <span class="material-icons text-blue-600 text-sm">event</span>
                                                                                                                                                                                                                                            <p class="text-xs text-gray-600 font-semibold uppercase">Fecha Solicitud</p>
                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                        <p class="font-bold text-gray-900 text-lg">${formatearFecha(s.fechaSol)}</p>
                                                                                                                                                                                                                                        <div class="flex items-center gap-1 mt-2">
                                                                                                                                                                                                                                            <span class="material-icons text-blue-600 text-xs">schedule</span>
                                                                                                                                                                                                                                            <p class="text-sm text-blue-700 font-semibold">${s.horaSol || 'Sin hora'}</p>
                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                    </div>

                                                                                                                                                                                                                                    ${s.fechaAten ? `
                                                                                                                                                                                                                                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-emerald-500">
                                                                                                                                                                                                                                        <div class="flex items-center gap-2 mb-2">
                                                                                                                                                                                                                                            <span class="material-icons text-emerald-600 text-sm">check_circle</span>
                                                                                                                                                                                                                                            <p class="text-xs text-gray-600 font-semibold uppercase">Fecha Atención</p>
                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                        <p class="font-bold text-gray-900 text-lg">${formatearFecha(s.fechaAten)}</p>
                                                                                                                                                                                                                                        ${s.horaAten ? `
                                                                                                                                                                                                                                        <div class="flex items-center gap-1 mt-2">
                                                                                                                                                                                                                                            <span class="material-icons text-emerald-600 text-xs">schedule</span>
                                                                                                                                                                                                                                            <p class="text-sm text-emerald-700 font-semibold">${s.horaAten}</p>
                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                        ` : ''}
                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                    ` : ''}

                                                                                                                                                                                                                                    ${s.fechaEnt ? `
                                                                                                                                                                                                                                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-purple-500">
                                                                                                                                                                                                                                        <div class="flex items-center gap-2 mb-2">
                                                                                                                                                                                                                                            <span class="material-icons text-purple-600 text-sm">done_all</span>
                                                                                                                                                                                                                                            <p class="text-xs text-gray-600 font-semibold uppercase">Fecha Entrega</p>
                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                        <p class="font-bold text-gray-900 text-lg">${formatearFecha(s.fechaEnt)}</p>
                                                                                                                                                                                                                                        ${s.horaEnt ? `
                                                                                                                                                                                                                                        <div class="flex items-center gap-1 mt-2">
                                                                                                                                                                                                                                            <span class="material-icons text-purple-600 text-xs">schedule</span>
                                                                                                                                                                                                                                            <p class="text-sm text-purple-700 font-semibold">${s.horaEnt}</p>
                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                        ` : ''}
                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                    ` : ''}
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                    `;
                    }
                } catch (error) {
                    console.error('Error:', error);
                    contenido.innerHTML = `
                                                                                                                                                                                                                    <div class="text-center py-8">
                                                                                                                                                                                                                        <span class="material-icons text-red-500 text-6xl">error</span>
                                                                                                                                                                                                                        <p class="mt-4 text-red-600 font-semibold">Error al cargar la información</p>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                `;
                }
            }

            // ==========================================
            // UTILIDADES
            // ==========================================

            function cerrarModal(modalId) {
                document.getElementById(modalId).classList.add('hidden');
                if (modalId === 'modal-servicio') {
                    horarioSeleccionado = null;
                }
            }

            function mostrarLoader(mostrar) {
                document.getElementById('loader').classList.toggle('hidden', !mostrar);
            }

            function mostrarAlerta(mensaje, tipo = 'success') {
                const alerta = document.getElementById('alerta');
                const iconos = {
                    success: 'check_circle',
                    error: 'error',
                    info: 'info'
                };
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

            function formatearFecha(fecha) {
                if (!fecha) return 'N/A';
                const d = new Date(fecha);
                return d.toLocaleDateString('es-ES', { day: '2-digit', month: 'long', year: 'numeric' });
            }

            function formatearFechaCorta(fecha) {
                if (!fecha) return 'N/A';

                try {
                    if (typeof fecha === 'string' && fecha.includes('-')) {
                        const fechaParts = fecha.split('-');
                        const año = parseInt(fechaParts[0]);
                        const mes = parseInt(fechaParts[1]) - 1;
                        const dia = parseInt(fechaParts[2]);

                        const d = new Date(año, mes, dia);

                        const opciones = {
                            weekday: 'short',
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        };
                        return d.toLocaleDateString('es-ES', opciones);
                    }

                    const d = new Date(fecha);
                    if (isNaN(d.getTime())) return 'N/A';

                    const opciones = {
                        weekday: 'short',
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    };
                    return d.toLocaleDateString('es-ES', opciones);
                } catch (error) {
                    console.error('Error al formatear fecha:', fecha, error);
                    return 'N/A';
                }
            }
        </script>
    @endpush
@endsection