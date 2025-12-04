@extends('personal.layouts.personal')
@section('title', 'Calendario de Horarios')
@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('personal.home')],
            ['label' => 'Cronogramas', 'url' => route('personal.cronogramas.cronogramas')],
            ['label' => 'Calendario de Horarios']
        ];
    @endphp
    <style>
        /* ===== SLOTS DE HORARIO ===== */
        /* ===== ESTILOS PROFESIONALES PARA CALENDARIO ===== */

        /* Slots de horario */
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
            background: #f0fdf4 !important;
            border: 2px dashed #22c55e !important;
        }

        /* Tarjetas de servicio - Diseño profesional */
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

        /* Estados con colores profesionales y sutiles */
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

        .estado-archivado {
            background: #f3f4f6;
            border: 1px solid #9ca3af;
            border-left: 4px solid #6b7280;
        }

        .estado-archivado .estado-texto {
            color: #374151;
        }

        .estado-archivado .titulo-paciente {
            color: #1f2937;
        }

        .estado-archivado .subtexto {
            color: #4b5563;
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

        /* Contenedor de slots */
        .slots-container {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
        }

        /* Slot disponible */
        .slot-disponible {
            background: #f9fafb;
            border: 1px dashed #d1d5db;
            border-radius: 8px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
        }

        .slot-disponible:hover {
            border-color: #9ca3af;
            background: #f3f4f6;
        }

        /* Animación drop */
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
    </style>
    <div class="space-y-6">
        <!-- Encabezado con botón de regreso -->
        <div class="bg-gradient-to-r from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="material-icons text-white text-2xl">event_note</span>
                        </div>
                        Calendario de Horarios
                    </h1>
                    <p class="text-gray-600 ml-15">
                        <span class="material-icons text-sm align-middle">info</span>
                        Arrastra y suelta para cambiar horarios • 8:00 AM - 8:00 PM • Intervalos de 30 minutos • 1 servicio
                        por slot
                    </p>
                </div>
                <a href="{{ route('personal.cronogramas.cronogramas') }}"
                    class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl hover:from-gray-600 hover:to-gray-700 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <span class="material-icons">arrow_back</span>
                    <span class="font-semibold">Volver a Cronogramas</span>
                </a>
            </div>
        </div>

        <!-- Alerta de cambio de horario -->
        <div id="alerta-cambio" class="hidden"></div>

        <!-- Selector de Cronograma -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                        <span class="material-icons text-teal-600">calendar_today</span>
                        Seleccionar Fecha de Cronograma
                    </label>
                    <select id="select-cronograma"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 transition-all font-medium">
                        <option value="">Seleccione una fecha...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                        <span class="material-icons text-teal-600">info</span>
                        Información del Cronograma
                    </label>
                    <div id="info-cronograma"
                        class="px-4 py-3 bg-gradient-to-r from-teal-50 to-cyan-50 rounded-lg border-2 border-teal-200 font-medium text-gray-700">
                        Seleccione un cronograma para ver la información
                    </div>
                </div>
            </div>
        </div>

        <!-- Leyenda -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="flex items-center gap-2 mb-4">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-lg flex items-center justify-center shadow-md">
                    <span class="material-icons text-white text-xl">palette</span>
                </div>
                <h4 class="text-lg font-bold text-gray-800">Leyenda de Estados</h4>
            </div>
            <div class="flex flex-wrap gap-6">
                <div class="flex items-center gap-2">
                    <div
                        class="w-6 h-6 bg-gradient-to-br from-emerald-100 to-emerald-200 border-2 border-emerald-400 rounded">
                    </div>
                    <span class="text-sm font-medium text-gray-700">Slot de 30 min Disponible</span>
                </div>

                <div class="flex items-center gap-2">
                    <div
                        class="w-6 h-6 bg-gradient-to-br from-emerald-100 to-emerald-200 border-2 border-emerald-400 rounded">
                    </div>
                    <span class="text-sm font-medium text-gray-700">Horario Disponible</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-gradient-to-br from-orange-500 to-orange-600 rounded shadow-sm"></div>
                    <span class="text-sm font-medium text-gray-700">Programado (Arrastrable)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded shadow-sm"></div>
                    <span class="text-sm font-medium text-gray-700">En Proceso</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded shadow-sm"></div>
                    <span class="text-sm font-medium text-gray-700">Atendido (No movible)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-gradient-to-br from-purple-500 to-purple-600 rounded shadow-sm"></div>
                    <span class="text-sm font-medium text-gray-700">Entregado (No movible)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-gradient-to-br from-gray-500 to-gray-600 rounded shadow-sm"></div>
                    <span class="text-sm font-medium text-gray-700">Archivado (No movible)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-gradient-to-br from-red-500 to-red-600 rounded shadow-sm"></div>
                    <span class="text-sm font-medium text-gray-700">Emergencia</span>
                </div>
            </div>
        </div>

        <!-- Calendario de Horarios (8:00 AM - 8:00 PM) -->
        <div id="calendario-container" class="hidden">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <span class="material-icons">schedule</span>
                        Horarios del Día (08:00 - 20:00) - Intervalos de 30 minutos, 1 servicio por slot
                    </h3>

                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-5"
                        id="horarios-dia">
                        <!-- Se llenará dinámicamente -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensaje sin selección -->
        <div id="sin-seleccion" class="bg-white rounded-xl shadow-lg border border-gray-200 p-12">
            <div class="flex flex-col items-center justify-center">
                <div
                    class="w-32 h-32 bg-gradient-to-br from-teal-100 to-cyan-100 rounded-full flex items-center justify-center mb-4">
                    <span class="material-icons text-teal-600" style="font-size: 80px;">event_available</span>
                </div>
                <p class="text-gray-800 text-xl font-bold mb-2">Selecciona un Cronograma</p>
                <p class="text-gray-500 text-sm">Elige una fecha para ver los horarios programados</p>
            </div>
        </div>
    </div>

    <!-- Modal Detalle de Servicio -->
    <div id="modal-detalle-servicio"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
        <div class="relative top-20 mx-auto p-6 border border-gray-200 w-full max-w-2xl shadow-2xl rounded-2xl bg-white">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg">
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

    <!-- Modal de Confirmación de Cambio de Horario -->
    <div id="modal-confirmar-cambio"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
        <div class="relative top-1/3 mx-auto p-6 border border-gray-200 w-full max-w-md shadow-2xl rounded-2xl bg-white">
            <div class="text-center">
                <div
                    class="mx-auto flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-br from-teal-500 to-cyan-600 mb-4 shadow-lg">
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
                        class="flex-1 px-5 py-3 bg-gradient-to-r from-teal-500 to-cyan-600 text-white rounded-xl hover:from-teal-600 hover:to-cyan-700 transition-all font-semibold shadow-lg">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let cronogramas = [];
            let serviciosPorHorario = {};
            let cronogramaSeleccionado = null;

            // Variables para drag & drop
            let servicioArrastrado = null;
            let horarioDestino = null;

            document.addEventListener('DOMContentLoaded', function () {
                cargarCronogramas();

                document.getElementById('select-cronograma').addEventListener('change', function () {
                    const fechaCrono = this.value;
                    if (fechaCrono) {
                        cargarServiciosPorFecha(fechaCrono);
                    } else {
                        ocultarCalendario();
                    }
                });
            });

            async function cargarCronogramas() {
                try {
                    const response = await fetch('/api/personal/cronogramas');
                    const data = await response.json();

                    if (data.success) {
                        cronogramas = data.data.filter(c => c.estado === 'activo' || c.estado === 'inactivoFut');

                        const select = document.getElementById('select-cronograma');
                        select.innerHTML = '<option value="">Seleccione una fecha...</option>';

                        cronogramas.forEach(crono => {
                            const option = document.createElement('option');
                            option.value = crono.fechaCrono;
                            const fecha = new Date(crono.fechaCrono + 'T00:00:00');
                            const fechaFormateada = fecha.toLocaleDateString('es-ES', {
                                weekday: 'long',
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });
                            option.textContent = fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);
                            select.appendChild(option);
                        });
                    }
                } catch (error) {
                    console.error('Error al cargar cronogramas:', error);
                }
            }

            async function cargarServiciosPorFecha(fechaCrono) {
                try {
                    cronogramaSeleccionado = cronogramas.find(c => c.fechaCrono === fechaCrono);

                    if (cronogramaSeleccionado) {
                        mostrarInfoCronograma(cronogramaSeleccionado);
                    }

                    const response = await fetch(`/api/personal/servicios/por-fecha-cronograma/${fechaCrono}`);
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
                const total = cronograma.cantFijo || 20;
                const atendidos = total - disponibles;

                infoDiv.innerHTML = `
                                                                                                                <div class="flex items-center justify-between">
                                                                                                                    <div class="flex items-center gap-3">
                                                                                                                        <div class="text-center">
                                                                                                                            <p class="text-2xl font-bold text-teal-600">${disponibles}</p>
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
                                                                                                                    <div class="px-4 py-2 bg-gradient-to-r from-teal-500 to-cyan-600 text-white rounded-lg font-bold text-sm shadow-md">
                                                                                                                        ${cronograma.estado === 'activo' ? 'Activo' : 'Programado'}
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            `;
            }

            function renderizarCalendario() {
                const container = document.getElementById('horarios-dia');
                container.innerHTML = '';

                // CAMBIO: Horarios de 8:00 AM a 8:00 PM en intervalos de 30 minutos
                for (let hora = 8; hora <= 19; hora++) {
                    // Hora en punto (XX:00)
                    const horarioKey00 = `${String(hora).padStart(2, '0')}:00`;
                    container.appendChild(crearSlotHorario(hora, 0, horarioKey00));

                    // Media hora (XX:30)
                    const horarioKey30 = `${String(hora).padStart(2, '0')}:30`;
                    container.appendChild(crearSlotHorario(hora, 30, horarioKey30));
                }

                // Agregar las 8:00 PM (20:00)
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
                        : (servicioMostrar?.estado === 'Atendido' ? 'bg-green-50 text-green-600' :
                           servicioMostrar?.estado === 'Entregado' ? 'bg-purple-50 text-purple-600' :
                           servicioMostrar?.estado === 'Archivado' ? 'bg-gray-50 text-gray-600' :
                           'bg-amber-50 text-amber-600')}">
                                <span class="material-icons" style="font-size: 11px;">
                                    ${estaDisponible ? 'check_circle' : 'event_busy'}
                                </span>
                                ${estaDisponible ? 'Disponible' : (servicioMostrar?.estado || 'Ocupado')}
                            </span>
                        </div>
                        <div class="slots-container">
                    `;

                if (servicioMostrar === null) {
                    contenidoHTML += `
                            <div class="slot-disponible">
                                <span class="material-icons text-gray-400 text-xl">add</span>
                                <p class="text-xs text-gray-500 mt-1">Arrastra aquí</p>
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
                } else if (servicio.estado === 'Archivado') {
                    colorClass = 'estado-archivado';
                    iconoEstado = 'archive';
                    textoEstado = 'Archivado';
                    draggable = false;
                }

                // Manejar emergencias conservando el texto pero usando el color del estado
                const esEmergencia = servicio.tipoAseg && servicio.tipoAseg.includes('Emergencia');
                if (esEmergencia) {
                    iconoEstado = 'warning';
                    textoEstado = textoEstado + ' (Emergencia)'; // Agregar indicador de emergencia al texto
                    // NO cambiar colorClass, mantener el color del estado
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
                        <span class="text-xs text-gray-500 truncate max-w-[60%]" title="${servicio.nroServ || ''}">
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

                // Limpiar todos los highlights
                document.querySelectorAll('.hora-slot').forEach(slot => {
                    slot.classList.remove('drag-over');
                });
            }

            function handleDragOver(event) {
                event.preventDefault();
                event.stopPropagation();

                const slot = event.currentTarget;
                slot.classList.add('drag-over');
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

                // CAMBIO: Verificar si el slot ya está ocupado (máximo 1 servicio)
                const serviciosEnDestino = serviciosPorHorario[horarioDestino] || [];

                if (serviciosEnDestino.length >= 1) {
                    mostrarAlerta('El horario seleccionado ya está ocupado (1 servicio por slot de 30 minutos)', 'error');
                    servicioArrastrado = null;
                    horarioDestino = null;
                    return;
                }

                // Buscar el servicio arrastrado
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

                // Mostrar modal de confirmación
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

                        // Recargar la vista
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

            async function verDetalleServicio(codServ) {
                // Evitar que se abra el modal si estamos arrastrando
                if (servicioArrastrado) return;

                try {
                    const response = await fetch(`/api/personal/servicios/${codServ}`);
                    const data = await response.json();

                    if (data.success) {
                        const s = data.data;
                        const paciente = `${s.paciente?.nomPa || ''} ${s.paciente?.paternoPa || ''} ${s.paciente?.maternoPa || ''}`.trim();
                        const medico = `${s.medico?.nomMed || ''} ${s.medico?.paternoMed || ''}`.trim();

                        // Determinar badge de estado con colores correctos
                        let estadoBadge = '';
                        if (s.estado === 'Programado') {
                            estadoBadge = 'bg-gradient-to-r from-orange-500 to-orange-600 text-white';
                        } else if (s.estado === 'EnProceso') {
                            estadoBadge = 'bg-gradient-to-r from-blue-500 to-blue-600 text-white';
                        } else if (s.estado === 'Atendido') {
                            estadoBadge = 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white';
                        } else if (s.estado === 'Entregado') {
                            estadoBadge = 'bg-gradient-to-r from-purple-500 to-purple-600 text-white';
                        } else {
                            estadoBadge = 'bg-gray-100 text-gray-700';
                        }

                        const contenido = document.getElementById('contenido-detalle-servicio');
                        contenido.innerHTML = `
                                                                                                                        <div class="bg-gradient-to-r from-teal-50 to-cyan-50 rounded-xl p-5 border border-teal-200">
                                                                                                                            <div class="grid grid-cols-2 gap-4">
                                                                                                                                <div>
                                                                                                                                    <p class="text-xs text-gray-600 font-semibold mb-1">Número de Servicio</p>
                                                                                                                                    <p class="text-lg font-bold text-teal-600">${s.nroServ}</p>
                                                                                                                                </div>
                                                                                                                                <div>
                                                                                                                                    <p class="text-xs text-gray-600 font-semibold mb-1">Número de Ficha</p>
                                                                                                                                    <p class="text-lg font-bold text-gray-900">${s.nroFicha || 'N/A'}</p>
                                                                                                                                </div>
                                                                                                                                <div>
                                                                                                                                    <p class="text-xs text-gray-600 font-semibold mb-1">Estado</p>
                                                                                                                                    <span class="px-3 py-1 rounded-full text-xs font-bold ${estadoBadge}">
                                                                                                                                        ${s.estado}
                                                                                                                                    </span>
                                                                                                                                </div>
                                                                                                                                <div>
                                                                                                                                    <p class="text-xs text-gray-600 font-semibold mb-1">Horario de Atención</p>
                                                                                                                                    <p class="text-lg font-bold text-teal-600">${s.horaCrono ? s.horaCrono.substring(0, 5) : 'N/A'}</p>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>

                                                                                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                                                                            <div class="bg-blue-50 p-4 rounded-xl border border-blue-200">
                                                                                                                                <p class="text-xs text-gray-600 font-semibold mb-2 flex items-center gap-1">
                                                                                                                                    <span class="material-icons text-xs text-blue-600">person</span>
                                                                                                                                    Paciente
                                                                                                                                </p>
                                                                                                                                <p class="font-bold text-gray-900">${paciente}</p>
                                                                                                                                <p class="text-sm text-blue-700 mt-1">${s.paciente?.nroHCI || 'Sin HCI'}</p>
                                                                                                                            </div>

                                                                                                                            <div class="bg-purple-50 p-4 rounded-xl border border-purple-200">
                                                                                                                                <p class="text-xs text-gray-600 font-semibold mb-2 flex items-center gap-1">
                                                                                                                                    <span class="material-icons text-xs text-purple-600">medical_services</span>
                                                                                                                                    Médico Solicitante
                                                                                                                                </p>
                                                                                                                                <p class="font-bold text-gray-900">${medico}</p>
                                                                                                                                <p class="text-sm text-purple-700 mt-1">${s.medico?.tipoMed || ''}</p>
                                                                                                                            </div>
                                                                                                                        </div>

                                                                                                                        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 p-5 rounded-xl border border-purple-200">
                                                                                                                            <p class="text-xs text-gray-600 font-semibold mb-2 flex items-center gap-1">
                                                                                                                                <span class="material-icons text-xs text-purple-600">science</span>
                                                                                                                                Tipo de Estudio
                                                                                                                            </p>
                                                                                                                            <p class="font-bold text-gray-900 text-lg">${s.tipo_estudio?.descripcion || 'N/A'}</p>
                                                                                                                        </div>

                                                                                                                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-4 rounded-xl border border-gray-200">
                                                                                                                            <p class="text-xs text-gray-600 font-semibold mb-2 flex items-center gap-1">
                                                                                                                                <span class="material-icons text-xs">security</span>
                                                                                                                                Tipo de Seguro
                                                                                                                            </p>
                                                                                                                            <p class="font-bold text-gray-900">${s.tipoAseg?.replace('Aseg', 'Aseg. ').replace('NoAseg', 'No Aseg. ')}</p>
                                                                                                                        </div>

                                                                                                                        ${s.estado === 'Programado' || s.estado === 'EnProceso' ? `
                                                                                                                        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-lg">
                                                                                                                            <div class="flex items-start gap-2">
                                                                                                                                <span class="material-icons text-amber-600">info</span>
                                                                                                                                <div>
                                                                                                                                    <p class="text-sm font-bold text-amber-900 mb-1">Cambio de Horario</p>
                                                                                                                                    <p class="text-xs text-amber-800">Puedes arrastrar y soltar esta tarjeta para cambiar su horario a otro slot disponible</p>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        ` : ''}

                                                                                                                        <div class="flex gap-3 mt-4">
                                                                                                                            <button onclick="cerrarModalDetalle()" class="flex-1 px-5 py-3 bg-gradient-to-r from-teal-500 to-cyan-600 text-white rounded-xl hover:from-teal-600 hover:to-cyan-700 transition-all font-semibold shadow-lg">
                                                                                                                                Cerrar
                                                                                                                            </button>
                                                                                                                        </div>
                                                                                                                    `;

                        document.getElementById('modal-detalle-servicio').classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error al obtener detalle:', error);
                }
            }

            function cerrarModalDetalle() {
                document.getElementById('modal-detalle-servicio').classList.add('hidden');
            }

            function ocultarCalendario() {
                document.getElementById('calendario-container').classList.add('hidden');
                document.getElementById('sin-seleccion').classList.remove('hidden');
                document.getElementById('info-cronograma').innerHTML = 'Seleccione un cronograma para ver la información';
            }

            function mostrarAlerta(mensaje, tipo = 'success') {
                const alerta = document.getElementById('alerta-cambio');
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

                alerta.className = `p-4 rounded-xl border-2 flex items-center ${colores[tipo]} shadow-md`;
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
