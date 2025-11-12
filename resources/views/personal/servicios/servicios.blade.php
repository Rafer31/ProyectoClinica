@extends('personal.layouts.personal')

@section('title', 'Servicios')

@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('personal.home')],
            ['label' => 'Servicios']
        ];
    @endphp
    <style>
        /* Estilos para el Stepper - Agregar en tu archivo CSS principal o en el <style> del layout */

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

        /* Mejoras visuales para inputs del formulario */
        #modal-servicio input:focus,
        #modal-servicio select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        #modal-servicio input[required]:invalid:not(:placeholder-shown),
        #modal-servicio select[required]:invalid {
            border-color: #ef4444;
        }

        /* Animación para los botones */
        #btn-siguiente,
        #btn-anterior,
        #btn-guardar {
            transition: all 0.2s ease;
        }

        #btn-siguiente:hover:not(:disabled),
        #btn-guardar:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        #btn-anterior:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Estilo para diagnósticos */
        .diagnostico-item {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive para el stepper en móviles */
        @media (max-width: 640px) {
            .step-circle {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }


        .step-circle .material-icons {
                font-size: 16px;
            }


        .step-line {
                margin-left: 4px;
                margin-right: 4px;
            }
        }
    </style>
    <div class="space-y-6">
        <!-- Encabezado -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('personal.tipos-estudio.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all hover:shadow-lg">
                    <span class="material-icons">category</span>
                    <span class="font-medium">Tipos de Estudio</span>
                </a>
                <button id="btn-nuevo-servicio"
                    class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all hover:shadow-lg">
                    <span class="material-icons">add_circle</span>
                    <span class="font-medium">Nuevo Servicio</span>
                </button>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="material-icons text-4xl opacity-80">analytics</span>
                    <span class="bg-white bg-opacity-20 px-2 py-1 rounded text-xs">Hoy</span>
                </div>
                <p class="text-3xl font-bold" id="stat-hoy">0</p>
                <p class="text-sm opacity-90">Servicios realizados</p>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="material-icons text-4xl opacity-80">pending_actions</span>
                    <span class="bg-white bg-opacity-20 px-2 py-1 rounded text-xs">Ahora</span>
                </div>
                <p class="text-3xl font-bold" id="stat-proceso">0</p>
                <p class="text-sm opacity-90">En proceso</p>
            </div>

            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="material-icons text-4xl opacity-80">schedule</span>
                    <span class="bg-white bg-opacity-20 px-2 py-1 rounded text-xs">Hoy</span>
                </div>
                <p class="text-3xl font-bold" id="stat-programados">0</p>
                <p class="text-sm opacity-90">Programados</p>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="material-icons text-4xl opacity-80">category</span>
                    <span class="bg-white bg-opacity-20 px-2 py-1 rounded text-xs">Total</span>
                </div>
                <p class="text-3xl font-bold" id="stat-tipos">0</p>
                <p class="text-sm opacity-90">Tipos de servicios</p>
            </div>
        </div>

        <!-- Filtros y búsqueda -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="material-icons text-sm align-middle">search</span>
                        Buscar
                    </label>
                    <input type="text" id="buscar-servicio"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Buscar por nro servicio, paciente o HCI...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="material-icons text-sm align-middle">filter_alt</span>
                        Filtrar por estado
                    </label>
                    <select id="filtro-estado"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos los estados</option>
                        <option value="Programado">Programado</option>
                        <option value="EnProceso">En Proceso</option>
                        <option value="Atendido">Atendido</option>
                        <option value="Entregado">Entregado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="material-icons text-sm align-middle">security</span>
                        Tipo de Seguro
                    </label>
                    <select id="filtro-tipo-aseg"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos los tipos</option>
                        <option value="AsegEmergencia">Asegurado - Emergencia</option>
                        <option value="AsegRegular">Asegurado - Regular</option>
                        <option value="NoAsegEmergencia">No Asegurado - Emergencia</option>
                        <option value="NoAsegRegular">No Asegurado - Regular</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="cargarServicios()"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all hover:shadow-lg">
                        <span class="material-icons text-sm">refresh</span>
                        <span class="font-medium">Actualizar</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla de servicios -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Servicios Registrados</h2>
            </div>
            <div class="overflow-x-auto" id="tabla-servicios">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nro. Servicio</th>
                            <th scope="col" class="px-6 py-3">Paciente</th>
                            <th scope="col" class="px-6 py-3">Tipo Estudio</th>
                            <th scope="col" class="px-6 py-3">Médico</th>
                            <th scope="col" class="px-6 py-3">Tipo Seguro</th>
                            <th scope="col" class="px-6 py-3">Fecha Atención</th>
                            <th scope="col" class="px-6 py-3">Estado</th>
                            <th scope="col" class="px-6 py-3 text-center" style="min-width: 200px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600">
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Crear/Editar Servicio -->
        <div id="modal-servicio" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white mb-10">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900" id="titulo-modal">Nuevo Servicio</h3>
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
                                    class="step-circle active flex items-center justify-center w-10 h-10 rounded-full border-2 border-blue-600 bg-blue-600 text-white font-semibold transition-all">
                                    <span class="step-number">1</span>
                                    <span class="step-check material-icons hidden">check</span>
                                </div>
                                <div class="text-xs mt-2 font-medium text-blue-600 text-center">Información Básica</div>
                            </div>
                            <div class="step-line flex-1 h-1 bg-gray-300 mx-2"></div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex-1 flex items-center">
                            <div class="relative flex flex-col items-center flex-1">
                                <div
                                    class="step-circle flex items-center justify-center w-10 h-10 rounded-full border-2 border-gray-300 bg-white text-gray-500 font-semibold transition-all">
                                    <span class="step-number">2</span>
                                    <span class="step-check material-icons hidden">check</span>
                                </div>
                                <div class="text-xs mt-2 font-medium text-gray-500 text-center">Paciente y Médico</div>
                            </div>
                            <div class="step-line flex-1 h-1 bg-gray-300 mx-2"></div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex-1 flex items-center">
                            <div class="relative flex flex-col items-center flex-1">
                                <div
                                    class="step-circle flex items-center justify-center w-10 h-10 rounded-full border-2 border-gray-300 bg-white text-gray-500 font-semibold transition-all">
                                    <span class="step-number">3</span>
                                    <span class="step-check material-icons hidden">check</span>
                                </div>
                                <div class="text-xs mt-2 font-medium text-gray-500 text-center">Estudio</div>
                            </div>
                            <div class="step-line flex-1 h-1 bg-gray-300 mx-2"></div>
                        </div>

                        <!-- Step 4 -->
                        <div class="flex-1 flex items-center">
                            <div class="relative flex flex-col items-center flex-1">
                                <div
                                    class="step-circle flex items-center justify-center w-10 h-10 rounded-full border-2 border-gray-300 bg-white text-gray-500 font-semibold transition-all">
                                    <span class="step-number">4</span>
                                    <span class="step-check material-icons hidden">check</span>
                                </div>
                                <div class="text-xs mt-2 font-medium text-gray-500 text-center">Diagnósticos</div>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="form-servicio" class="space-y-6">
                    <!-- Step 1: Información Básica -->
                    <div class="form-step active" data-step="1">
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                            <div class="flex items-center">
                                <span class="material-icons text-blue-600 mr-2">info</span>
                                <p class="text-sm text-blue-800 font-medium">Complete la información básica del servicio</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha Solicitud <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="fechaSol" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Hora Solicitud <span class="text-red-500">*</span>
                                </label>
                                <input type="time" id="horaSol" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nro. Servicio
                                </label>
                                <input type="text" id="nroServ" placeholder="Se genera automático" readonly
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Seguro <span class="text-red-500">*</span>
                                </label>
                                <select id="tipoAseg" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Seleccione...</option>
                                    <option value="AsegEmergencia">Asegurado - Emergencia</option>
                                    <option value="AsegRegular">Asegurado - Regular</option>
                                    <option value="NoAsegEmergencia">No Asegurado - Emergencia</option>
                                    <option value="NoAsegRegular">No Asegurado - Regular</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Estado <span class="text-red-500">*</span>
                                </label>
                                <select id="estado" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="Programado">Programado</option>
                                    <option value="EnProceso">En Proceso</option>
                                    <option value="Atendido">Atendido</option>
                                    <option value="Entregado">Entregado</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nro. Ficha
                                </label>
                                <input type="text" id="nroFicha"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Paciente y Médico -->
                    <div class="form-step hidden" data-step="2">
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">
                            <div class="flex items-center">
                                <span class="material-icons text-green-600 mr-2">people</span>
                                <p class="text-sm text-green-800 font-medium">Seleccione el paciente y médico solicitante
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Paciente <span class="text-red-500">*</span>
                                </label>
                                <select id="codPa" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Seleccione un paciente</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Médico Solicitante <span class="text-red-500">*</span>
                                </label>
                                <select id="codMed" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Seleccione un médico</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Tipo de Estudio -->
                    <div class="form-step hidden" data-step="3">
                        <div class="bg-purple-50 border-l-4 border-purple-500 p-4 mb-4">
                            <div class="flex items-center">
                                <span class="material-icons text-purple-600 mr-2">science</span>
                                <p class="text-sm text-purple-800 font-medium">Configure el tipo de estudio y cronograma</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Estudio <span class="text-red-500">*</span>
                                </label>
                                <select id="codTest" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Seleccione un tipo de estudio</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha Cronograma <span class="text-red-500">*</span>
                                </label>
                                <select id="fechaCrono" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Seleccione una fecha disponible</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Diagnósticos -->
                    <div class="form-step hidden" data-step="4">
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                            <div class="flex items-center">
                                <span class="material-icons text-red-600 mr-2">medical_services</span>
                                <p class="text-sm text-red-800 font-medium">Agregue los diagnósticos relacionados (opcional)
                                </p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex gap-2">
                                <div class="flex-1">
                                    <select id="diagnostico-temp"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Seleccione un diagnóstico</option>
                                    </select>
                                </div>
                                <div class="w-40">
                                    <select id="tipo-diag-temp"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Tipo</option>
                                        <option value="sol">Solicitado</option>
                                        <option value="eco">Ecográfico</option>
                                    </select>
                                </div>
                                <button type="button" id="btn-agregar-diagnostico"
                                    class="flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all hover:shadow-lg">
                                    <span class="material-icons">add</span>
                                    <span>Agregar</span>
                                </button>
                            </div>
                            <div id="diagnosticos-seleccionados" class="space-y-2 min-h-[100px]">
                                <!-- Diagnósticos agregados dinámicamente -->
                                <div class="text-sm text-gray-500 italic text-center py-8" id="diagnosticos-vacio">
                                    No hay diagnósticos agregados
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de navegación -->
                    <div class="flex justify-between items-center pt-6 border-t">
                        <button type="button" id="btn-anterior"
                            class="flex items-center gap-2 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                            <span class="material-icons text-sm">arrow_back</span>
                            <span class="font-medium">Anterior</span>
                        </button>

                        <div class="flex gap-3">
                            <button type="button" onclick="cerrarModal('modal-servicio')"
                                class="flex items-center gap-2 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all">
                                <span class="material-icons text-sm">close</span>
                                <span class="font-medium">Cancelar</span>
                            </button>

                            <button type="button" id="btn-siguiente"
                                class="flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all hover:shadow-lg">
                                <span class="font-medium">Siguiente</span>
                                <span class="material-icons text-sm">arrow_forward</span>
                            </button>

                            <button type="submit" id="btn-guardar"
                                class="hidden flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all hover:shadow-lg">
                                <span class="material-icons text-sm">save</span>
                                <span class="font-medium">Guardar Servicio</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Detalle Servicio -->
        <div id="modal-detalle-servicio"
            class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="material-icons text-blue-600">visibility</span>
                        Detalle del Servicio
                    </h3>
                    <button onclick="cerrarModal('modal-detalle-servicio')"
                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-colors">
                        <span class="material-icons">close</span>
                    </button>
                </div>
                <div id="detalle-servicio-content">
                    <!-- Contenido dinámico -->
                </div>
                <div class="flex justify-end mt-6">
                    <button onclick="cerrarModal('modal-detalle-servicio')"
                        class="flex items-center gap-2 px-6 py-2.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all hover:shadow-lg">
                        <span class="material-icons text-sm">close</span>
                        <span class="font-medium">Cerrar</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Cambiar Estado -->
        <div id="modal-cambiar-estado"
            class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div
                class="relative top-1/2 -translate-y-1/2 mx-auto p-6 border w-full max-w-md shadow-2xl rounded-lg bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="material-icons text-blue-600">swap_horiz</span>
                        Cambiar Estado
                    </h3>
                    <button onclick="cerrarModal('modal-cambiar-estado')"
                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-colors">
                        <span class="material-icons">close</span>
                    </button>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Seleccione el nuevo estado:
                    </label>
                    <select id="nuevo-estado"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900 font-medium">
                        <option value="Programado" class="py-2">🗓️ Programado</option>
                        <option value="EnProceso" class="py-2">⚙️ En Proceso</option>
                        <option value="Atendido" class="py-2">✅ Atendido</option>
                        <option value="Entregado" class="py-2">📦 Entregado</option>
                    </select>
                </div>

                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="cerrarModal('modal-cambiar-estado')"
                        class="flex items-center gap-2 px-5 py-2.5 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-all shadow-md hover:shadow-lg">
                        <span class="material-icons text-sm">close</span>
                        <span>Cancelar</span>
                    </button>
                    <button type="button" id="btn-confirmar-cambio-estado"
                        class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-all shadow-md hover:shadow-lg">
                        <span class="material-icons text-sm">check_circle</span>
                        <span>Confirmar</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Eliminar -->
        <div id="modal-eliminar" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div
                class="relative top-1/2 -translate-y-1/2 mx-auto p-6 border w-full max-w-md shadow-2xl rounded-lg bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-red-600 flex items-center gap-2">
                        <span class="material-icons">warning</span>
                        Confirmar Eliminación
                    </h3>
                    <button onclick="cerrarModal('modal-eliminar')"
                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-colors">
                        <span class="material-icons">close</span>
                    </button>
                </div>

                <div class="mb-6">
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                        <p class="text-gray-800 font-medium">¿Está seguro que desea eliminar este servicio?</p>
                    </div>
                    <p class="text-gray-600 text-sm">
                        Esta acción no se puede deshacer. Todos los datos relacionados con este servicio serán eliminados
                        permanentemente.
                    </p>
                </div>

                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="cerrarModal('modal-eliminar')"
                        class="flex items-center gap-2 px-5 py-2.5 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-all shadow-md hover:shadow-lg">
                        <span class="material-icons text-sm">close</span>
                        <span>Cancelar</span>
                    </button>
                    <button type="button" id="btn-confirmar-eliminar"
                        class="flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-all shadow-md hover:shadow-lg">
                        <span class="material-icons text-sm">delete</span>
                        <span>Eliminar</span>
                    </button>
                </div>
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="{{ asset('js/servicios.js') }}"></script>
        @endpush
@endsection
