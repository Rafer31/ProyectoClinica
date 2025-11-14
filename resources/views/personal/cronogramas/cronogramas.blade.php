@extends('personal.layouts.personal')
@section('title', 'Cronogramas')
@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('personal.home')],
            ['label' => 'Cronogramas']
        ];
    @endphp
    <div class="space-y-6" id="cronogramasApp">
        <!-- Encabezado -->
        <div class="bg-gradient-to-r from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="material-icons text-white text-2xl">calendar_today</span>
                        </div>
                        Cronogramas de Atención
                    </h1>
                    <p class="text-gray-600 ml-15">Gestiona los horarios semanales de atención médica</p>
                </div>
                <button onclick="abrirModalCrear()"
                    class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <span class="material-icons">add_circle</span>
                    <span class="font-semibold">Nuevo Cronograma</span>
                </button>
            </div>
        </div>

        <!-- Selector de semana -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <button onclick="cambiarSemana(-1)" 
                    class="p-3 text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all hover:shadow-md border border-transparent hover:border-emerald-200">
                    <span class="material-icons">chevron_left</span>
                </button>
                <div class="text-center">
                    <h3 class="text-xl font-bold text-gray-900 mb-1" id="tituloSemana">Cargando...</h3>
                    <p class="text-sm text-gray-600 font-medium" id="rangoSemana"></p>
                </div>
                <button onclick="cambiarSemana(1)" 
                    class="p-3 text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all hover:shadow-md border border-transparent hover:border-emerald-200">
                    <span class="material-icons">chevron_right</span>
                </button>
            </div>
        </div>

        <!-- Calendario de cronogramas -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="grid grid-cols-7 gap-px bg-gray-200" id="calendarioCronogramas">
                <!-- Los días se generarán dinámicamente -->
            </div>
        </div>

        <!-- Leyenda -->
        <div class="bg-gradient-to-r from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center shadow-md">
                    <span class="material-icons text-white text-xl">info</span>
                </div>
                <h4 class="text-lg font-bold text-gray-800">Leyenda de Estados</h4>
            </div>
            <div class="flex flex-wrap gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg shadow-sm"></div>
                    <span class="text-sm font-medium text-gray-700">Activo</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 bg-gradient-to-br from-amber-400 to-yellow-500 rounded-lg shadow-sm"></div>
                    <span class="text-sm font-medium text-gray-700">Programado</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 bg-gradient-to-br from-gray-400 to-gray-500 rounded-lg shadow-sm"></div>
                    <span class="text-sm font-medium text-gray-700">Finalizado</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 border-3 border-teal-500 rounded-lg bg-teal-50"></div>
                    <span class="text-sm font-medium text-gray-700">Día actual</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear cronograma -->
    <div id="modalCronograma" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
        <div class="relative top-20 mx-auto p-6 border border-gray-200 w-full max-w-md shadow-2xl rounded-2xl bg-white">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="material-icons text-white text-xl">add_circle</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Crear Cronograma</h3>
                </div>
                <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-all">
                    <span class="material-icons">close</span>
                </button>
            </div>
            
            <form id="formCronograma" onsubmit="guardarCronograma(event)">
                <div class="space-y-5">
                    <!-- Fecha -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <span class="material-icons text-emerald-600 text-lg">event</span>
                            Fecha del Cronograma
                        </label>
                        <input type="date" id="fechaCrono" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                    </div>

                    <!-- Información automática -->
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border-l-4 border-emerald-500 p-5 rounded-xl shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center flex-shrink-0 shadow-md">
                                <span class="material-icons text-white text-lg">info</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-emerald-900 mb-3">Se creará automáticamente con:</p>
                                <ul class="space-y-2">
                                    <li class="flex items-center gap-2 text-sm text-emerald-800">
                                        <span class="material-icons text-emerald-600 text-base">check_circle</span>
                                        <span><strong class="font-semibold">15 turnos</strong> disponibles</span>
                                    </li>
                                    <li class="flex items-center gap-2 text-sm text-emerald-800">
                                        <span class="material-icons text-emerald-600 text-base">schedule</span>
                                        <span>Estado: <strong id="infoEstado" class="font-semibold">Selecciona una fecha</strong></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="cerrarModal()"
                        class="flex-1 px-5 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-semibold border border-gray-200">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center gap-2">
                        <span class="material-icons text-lg">save</span>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div id="modalConfirmacion" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
        <div class="relative top-1/3 mx-auto p-6 border border-gray-200 w-96 shadow-2xl rounded-2xl bg-white">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 mb-4 shadow-lg">
                    <span class="material-icons text-white text-4xl">check_circle</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">¡Cronograma Creado!</h3>
                <p class="text-sm text-gray-600 mb-6" id="mensajeConfirmacion"></p>
                <button onclick="cerrarConfirmacion()"
                    class="px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                    Entendido
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Error -->
    <div id="modalError" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
        <div class="relative top-1/3 mx-auto p-6 border border-gray-200 w-96 shadow-2xl rounded-2xl bg-white">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-br from-rose-500 to-red-600 mb-4 shadow-lg">
                    <span class="material-icons text-white text-4xl">error</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Error</h3>
                <p class="text-sm text-gray-600 mb-6" id="mensajeError"></p>
                <button onclick="cerrarError()"
                    class="px-8 py-3 bg-gradient-to-r from-rose-500 to-red-600 text-white rounded-xl hover:from-rose-600 hover:to-red-700 transition-all font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Detalle de Cronograma -->
    <div id="modalDetalle" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
        <div class="relative top-20 mx-auto p-6 border border-gray-200 w-full max-w-lg shadow-2xl rounded-2xl bg-white">
            <!-- Encabezado -->
            <div class="flex justify-between items-start mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg" id="detalleIconoContainer">
                        <span class="material-icons text-4xl" id="detalleIcono">calendar_today</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Detalles del Cronograma</h3>
                        <p class="text-sm text-gray-500 font-medium" id="detalleFecha"></p>
                    </div>
                </div>
                <button onclick="cerrarDetalle()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-all">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <!-- Contenido -->
            <div class="space-y-4">
                <!-- Estado -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-icons text-gray-600">info</span>
                            <span class="text-sm font-semibold text-gray-700">Estado</span>
                        </div>
                        <span class="px-4 py-2 rounded-xl text-sm font-bold shadow-sm" id="detalleEstadoBadge"></span>
                    </div>
                </div>

                <!-- Turnos -->
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-5 border border-emerald-200 shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-icons text-emerald-600">event_available</span>
                        <span class="text-sm font-bold text-emerald-900">Disponibilidad de Turnos</span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="text-center p-4 bg-white rounded-xl border-2 border-emerald-200 shadow-sm">
                            <p class="text-xs text-gray-600 mb-1 font-semibold">Disponibles</p>
                            <p class="text-3xl font-bold text-emerald-600" id="detalleDisponibles">0</p>
                        </div>
                        <div class="text-center p-4 bg-white rounded-xl border-2 border-teal-200 shadow-sm">
                            <p class="text-xs text-gray-600 mb-1 font-semibold">Atendidos</p>
                            <p class="text-3xl font-bold text-teal-600" id="detalleAtendidos">0</p>
                        </div>
                    </div>
                    
                    <div class="text-center p-4 bg-white rounded-xl border-2 border-gray-200 shadow-sm mb-4">
                        <p class="text-xs text-gray-600 mb-1 font-semibold">Total de Turnos</p>
                        <p class="text-3xl font-bold text-gray-700" id="detalleTotales">0</p>
                    </div>

                    <!-- Barra de progreso -->
                    <div>
                        <div class="flex justify-between text-xs text-gray-700 mb-2 font-semibold">
                            <span>Progreso de atención</span>
                            <span id="detallePorcentaje">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 shadow-inner">
                            <div class="h-3 rounded-full transition-all duration-500 shadow-sm" id="detalleBarraProgreso"
                                style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="bg-gradient-to-br from-teal-50 to-emerald-50 border-l-4 border-teal-500 p-4 rounded-xl shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-teal-500 rounded-lg flex items-center justify-center flex-shrink-0 shadow-md">
                            <span class="material-icons text-white text-lg">lightbulb</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-teal-900 mb-1">Información</p>
                            <p class="text-xs text-teal-800" id="detalleInfo"></p>
                        </div>
                    </div>
                </div>

                <!-- Creado por -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                            <span class="material-icons text-white">person</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold">Creado por</p>
                            <p class="text-sm font-bold text-gray-900" id="detalleCreador">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-6">
                <button onclick="cerrarDetalle()"
                    class="w-full px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center gap-2">
                    <span class="material-icons">close</span>
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <style>
        .dia-calendario {
            min-height: 160px;
            background: white;
            transition: all 0.3s ease;
            position: relative;
            border-radius: 0.75rem;
        }

        .dia-calendario:hover {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        }

        .dia-con-cronograma {
            cursor: pointer;
        }

        .dia-con-cronograma:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -4px rgba(5, 150, 105, 0.2);
        }

        .cronograma-card {
            height: 100%;
            display: flex;
            flex-direction: column;
            padding: 1rem;
        }

        .estado-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            box-shadow: 0 0 0 4px white, 0 2px 8px rgba(0,0,0,0.15);
        }

        .turnos-info {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-left: 4px solid #10b981;
            border-radius: 0.75rem;
        }

        .emergencia-info {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-left: 4px solid #ef4444;
            border-radius: 0.75rem;
        }

        .progreso-bar-container {
            background: #e5e7eb;
            height: 8px;
            border-radius: 999px;
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }

        .progreso-bar {
            height: 100%;
            transition: width 0.5s ease;
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.4);
        }

        .progreso-bar.medio {
            background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.4);
        }

        .progreso-bar.alto {
            background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.4);
        }

        #calendarioCronogramas > div {
            background: white;
        }
    </style>

    <script>
        let cronogramas = [];
        let fechaInicio = null;
        let fechaFin = null;
        let cronogramaActual = null;

        // Inicializar
        document.addEventListener('DOMContentLoaded', function () {
            calcularSemanaActual();
            cargarCronogramas();

            const fechaInput = document.getElementById('fechaCrono');
            if (fechaInput) {
                fechaInput.addEventListener('change', actualizarInfoEstado);
            }
        });

        // Calcular semana actual
        function calcularSemanaActual() {
            const hoy = new Date();
            const diaSemana = hoy.getDay();
            const diff = diaSemana === 0 ? -6 : 1 - diaSemana;
            fechaInicio = new Date(hoy);
            fechaInicio.setDate(hoy.getDate() + diff);
            fechaFin = new Date(fechaInicio);
            fechaFin.setDate(fechaInicio.getDate() + 6);
            actualizarEncabezadoSemana();
        }

        // Cambiar semana
        function cambiarSemana(direccion) {
            fechaInicio.setDate(fechaInicio.getDate() + (direccion * 7));
            fechaFin.setDate(fechaFin.getDate() + (direccion * 7));
            actualizarEncabezadoSemana();
            cargarCronogramas();
        }

        // Actualizar encabezado de semana
        function actualizarEncabezadoSemana() {
            const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            const mesInicio = meses[fechaInicio.getMonth()];
            const mesFin = meses[fechaFin.getMonth()];
            const anoInicio = fechaInicio.getFullYear();
            const anoFin = fechaFin.getFullYear();

            let titulo = '';
            if (mesInicio === mesFin && anoInicio === anoFin) {
                titulo = `${mesInicio} ${anoInicio}`;
            } else if (anoInicio === anoFin) {
                titulo = `${mesInicio} - ${mesFin} ${anoInicio}`;
            } else {
                titulo = `${mesInicio} ${anoInicio} - ${mesFin} ${anoFin}`;
            }

            document.getElementById('tituloSemana').textContent = titulo;
            document.getElementById('rangoSemana').textContent =
                `${fechaInicio.getDate()} - ${fechaFin.getDate()}`;
        }

        // Cargar cronogramas
        async function cargarCronogramas() {
            try {
                const inicio = formatearFecha(fechaInicio);
                const fin = formatearFecha(fechaFin);
                const response = await fetch(`/api/personal/cronogramas/entre-fechas?fechaInicio=${inicio}&fechaFin=${fin}`);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                if (data.success) {
                    cronogramas = data.data;
                    renderizarCalendario();
                } else {
                    mostrarError(data.message || 'Error al cargar cronogramas');
                }
            } catch (error) {
                console.error('Error al cargar cronogramas:', error);
                mostrarError('Error al cargar los cronogramas: ' + error.message);
            }
        }

        // Renderizar calendario
        function renderizarCalendario() {
            const calendario = document.getElementById('calendarioCronogramas');
            const dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            const hoy = formatearFecha(new Date());
            let html = '';

            // Encabezados de días
            dias.forEach(dia => {
                html += `
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 px-4 py-4 text-center border-b-2 border-teal-700 shadow-sm">
                    <span class="text-sm font-bold text-white">${dia}</span>
                </div>
            `;
            });

            // Días de la semana con cronogramas
            for (let i = 0; i < 7; i++) {
                const fecha = new Date(fechaInicio.getTime());
                fecha.setDate(fecha.getDate() + i);
                const fechaStr = formatearFecha(fecha);
                const esHoy = fechaStr === hoy;
                const cronograma = cronogramas.find(c => c.fechaCrono === fechaStr);

                let contenido = '';
                let bordeDia = esHoy ? 'ring-4 ring-teal-400 ring-offset-2' : '';

                if (cronograma) {
                    let colorEstado = '';
                    let iconoEstado = '';
                    let textoEstado = '';
                    let badgeColor = '';

                    if (cronograma.estado === 'activo') {
                        colorEstado = 'bg-gradient-to-br from-emerald-500 to-emerald-600';
                        iconoEstado = 'check_circle';
                        textoEstado = 'Activo';
                        badgeColor = 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white';
                    } else if (cronograma.estado === 'inactivoFut') {
                        colorEstado = 'bg-gradient-to-br from-amber-400 to-yellow-500';
                        iconoEstado = 'schedule';
                        textoEstado = 'Programado';
                        badgeColor = 'bg-gradient-to-r from-amber-400 to-yellow-500 text-white';
                    } else {
                        colorEstado = 'bg-gradient-to-br from-gray-400 to-gray-500';
                        iconoEstado = 'cancel';
                        textoEstado = 'Finalizado';
                        badgeColor = 'bg-gradient-to-r from-gray-400 to-gray-500 text-white';
                    }

                    const totalFichas = cronograma.cantFijo || 15;
                    const atendidos = totalFichas - cronograma.cantDispo;
                    const porcentaje = Math.round((atendidos / totalFichas) * 100);
                    const fichasEmergencia = cronograma.cantEmergencia || 0;

                    let claseProgreso = '';
                    if (porcentaje < 50) claseProgreso = '';
                    else if (porcentaje < 80) claseProgreso = 'medio';
                    else claseProgreso = 'alto';

                    contenido = `
                    <div class="dia-calendario dia-con-cronograma ${bordeDia}" onclick="verDetalleCronograma('${fechaStr}', ${JSON.stringify(cronograma).replace(/"/g, '&quot;')})">
                        <div class="cronograma-card">
                            <div class="estado-badge ${colorEstado}"></div>
                            <div class="flex justify-between items-start mb-3">
                                <span class="text-3xl font-bold text-gray-800">${fecha.getDate()}</span>
                                <span class="text-xs px-3 py-1 rounded-lg ${badgeColor} font-bold shadow-md">
                                    ${textoEstado}
                                </span>
                            </div>
                            <div class="space-y-3 flex-grow">
                                <!-- Turnos Normales -->
                                <div class="turnos-info px-3 py-3 shadow-sm">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-bold text-emerald-900">Turnos Normales</span>
                                        <span class="material-icons text-sm text-emerald-600">${iconoEstado}</span>
                                    </div>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-2xl font-bold text-emerald-700">${cronograma.cantDispo}</span>
                                        <span class="text-xs text-gray-700 font-medium">disponibles</span>
                                    </div>
                                    <div class="text-xs text-gray-700 font-medium mt-1">
                                        ${atendidos} de ${totalFichas} atendidos
                                    </div>
                                </div>
                                <!-- Turnos Emergencia -->
                                <div class="emergencia-info px-3 py-3 shadow-sm">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-bold text-red-900 flex items-center gap-1">
                                            <span class="material-icons text-xs">local_hospital</span>
                                            Emergencia
                                        </span>
                                    </div>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-2xl font-bold text-red-700">${fichasEmergencia}</span>
                                        <span class="text-xs text-gray-700 font-medium">atendidos</span>
                                    </div>
                                </div>
                                <!-- Barra de progreso -->
                                <div class="progreso-bar-container">
                                    <div class="progreso-bar ${claseProgreso}" style="width: ${porcentaje}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                } else {
                    contenido = `
                    <div class="dia-calendario ${bordeDia}">
                        <div class="p-4 h-full flex flex-col">
                            <div class="flex justify-between items-start mb-3">
                                <span class="text-3xl font-bold text-gray-700">${fecha.getDate()}</span>
                            </div>
                            <div class="flex-grow flex items-center justify-center">
                                <button onclick="crearCronogramaRapido('${fechaStr}')"
                                    class="w-full py-4 text-sm text-teal-600 hover:bg-gradient-to-br hover:from-emerald-50 hover:to-teal-50 rounded-xl border-2 border-dashed border-teal-300 transition-all hover:border-teal-500 hover:shadow-md font-semibold">
                                    <span class="material-icons text-2xl block mb-2">add_circle_outline</span>
                                    <span>Crear cronograma</span>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                }

                html += contenido;
            }

            calendario.innerHTML = html;
        }

        // Abrir modal para crear
        function abrirModalCrear() {
            document.getElementById('formCronograma').reset();
            const hoy = formatearFecha(new Date());
            document.getElementById('fechaCrono').min = hoy;
            document.getElementById('modalCronograma').classList.remove('hidden');
            document.getElementById('infoEstado').textContent = 'Selecciona una fecha';
        }

        // Crear cronograma rápido
        function crearCronogramaRapido(fecha) {
            document.getElementById('fechaCrono').value = fecha;
            document.getElementById('modalCronograma').classList.remove('hidden');
            actualizarInfoEstado();
        }

        // Ver detalle de cronograma
        function verDetalleCronograma(fecha, cronograma) {
            cronogramaActual = cronograma;

            const fechaObj = new Date(fecha + 'T00:00:00');
            const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const fechaFormateada = fechaObj.toLocaleDateString('es-ES', opciones);

            document.getElementById('detalleFecha').textContent = fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);

            let iconoContainer = document.getElementById('detalleIconoContainer');
            let icono = document.getElementById('detalleIcono');
            let estadoBadge = document.getElementById('detalleEstadoBadge');

            if (cronograma.estado === 'activo') {
                iconoContainer.className = 'w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg bg-gradient-to-br from-emerald-500 to-emerald-600';
                icono.className = 'material-icons text-4xl text-white';
                icono.textContent = 'check_circle';
                estadoBadge.className = 'px-4 py-2 rounded-xl text-sm font-bold shadow-sm bg-gradient-to-r from-emerald-500 to-emerald-600 text-white';
                estadoBadge.textContent = 'Activo';
            } else if (cronograma.estado === 'inactivoFut') {
                iconoContainer.className = 'w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg bg-gradient-to-br from-amber-400 to-yellow-500';
                icono.className = 'material-icons text-4xl text-white';
                icono.textContent = 'schedule';
                estadoBadge.className = 'px-4 py-2 rounded-xl text-sm font-bold shadow-sm bg-gradient-to-r from-amber-400 to-yellow-500 text-white';
                estadoBadge.textContent = 'Programado';
            } else {
                iconoContainer.className = 'w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg bg-gradient-to-br from-gray-400 to-gray-500';
                icono.className = 'material-icons text-4xl text-white';
                icono.textContent = 'cancel';
                estadoBadge.className = 'px-4 py-2 rounded-xl text-sm font-bold shadow-sm bg-gradient-to-r from-gray-400 to-gray-500 text-white';
                estadoBadge.textContent = 'Finalizado';
            }

            const totalFichas = cronograma.cantFijo || 15;
            const disponibles = cronograma.cantDispo;
            const atendidos = totalFichas - disponibles;
            const porcentajeOcupado = Math.round((atendidos / totalFichas) * 100);
            const fichasEmergencia = cronograma.cantEmergencia || 0;

            document.getElementById('detalleDisponibles').textContent = disponibles;
            document.getElementById('detalleAtendidos').textContent = atendidos;
            document.getElementById('detalleTotales').textContent = totalFichas;
            document.getElementById('detallePorcentaje').textContent = porcentajeOcupado + '%';
            document.getElementById('detalleBarraProgreso').style.width = porcentajeOcupado + '%';

            const barra = document.getElementById('detalleBarraProgreso');
            if (porcentajeOcupado < 50) {
                barra.className = 'h-3 rounded-full transition-all duration-500 shadow-sm bg-gradient-to-r from-emerald-500 to-emerald-600';
            } else if (porcentajeOcupado < 80) {
                barra.className = 'h-3 rounded-full transition-all duration-500 shadow-sm bg-gradient-to-r from-amber-500 to-yellow-600';
            } else {
                barra.className = 'h-3 rounded-full transition-all duration-500 shadow-sm bg-gradient-to-r from-rose-500 to-red-600';
            }

            let infoTexto = '';
            if (cronograma.estado === 'activo') {
                if (disponibles > 0) {
                    infoTexto = `Este cronograma está activo. Turnos normales: ${disponibles} disponibles, ${atendidos} atendidos. Turnos de emergencia: ${fichasEmergencia} atendidos.`;
                } else {
                    infoTexto = `Este cronograma está activo pero no tiene turnos normales disponibles. Turnos de emergencia: ${fichasEmergencia}.`;
                }
            } else if (cronograma.estado === 'inactivoFut') {
                infoTexto = `Este cronograma está programado para una fecha futura. Tendrá ${totalFichas} turnos normales disponibles.`;
            } else {
                infoTexto = `Este cronograma ya finalizó. Se atendieron ${atendidos} de ${totalFichas} turnos normales (${porcentajeOcupado}%) y ${fichasEmergencia} turnos de emergencia.`;
            }
            document.getElementById('detalleInfo').textContent = infoTexto;

            if (cronograma.personal_salud) {
                const nombreCompleto = `${cronograma.personal_salud.nomPer || ''} ${cronograma.personal_salud.paternoPer || ''}`.trim();
                document.getElementById('detalleCreador').textContent = nombreCompleto || 'No disponible';
            } else {
                document.getElementById('detalleCreador').textContent = 'No disponible';
            }

            document.getElementById('modalDetalle').classList.remove('hidden');
        }

        function cerrarDetalle() {
            document.getElementById('modalDetalle').classList.add('hidden');
            cronogramaActual = null;
        }

        function actualizarInfoEstado() {
            const fechaInput = document.getElementById('fechaCrono').value;
            if (!fechaInput) {
                document.getElementById('infoEstado').textContent = 'Selecciona una fecha';
                return;
            }

            const fechaSeleccionada = new Date(fechaInput + 'T00:00:00');
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);

            let estadoTexto = '';
            if (fechaSeleccionada.getTime() === hoy.getTime()) {
                estadoTexto = 'Activo';
            } else if (fechaSeleccionada > hoy) {
                estadoTexto = 'Inactivo Futuro';
            } else {
                estadoTexto = 'Inactivo Pasado';
            }

            document.getElementById('infoEstado').textContent = estadoTexto;
        }

        async function guardarCronograma(event) {
            event.preventDefault();
            const fechaCrono = document.getElementById('fechaCrono').value;

            if (!fechaCrono) {
                mostrarError('Por favor selecciona una fecha');
                return;
            }

            const datos = {
                fechaCrono: fechaCrono,
                cantDispo: 15,
                cantFijo: 15
            };

            console.log('Enviando datos:', datos);

            try {
                const response = await fetch('/api/personal/cronogramas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(datos)
                });

                const text = await response.text();
                console.log('Respuesta del servidor:', text);

                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Error al parsear JSON:', e);
                    throw new Error('Respuesta inválida del servidor');
                }

                if (data.success) {
                    cerrarModal();
                    mostrarConfirmacion(data.message);
                    cargarCronogramas();
                } else {
                    if (data.errors) {
                        const errores = Object.values(data.errors).flat().join('\n');
                        mostrarError(errores);
                    } else {
                        mostrarError(data.message || 'Error desconocido');
                    }
                }
            } catch (error) {
                console.error('Error al guardar:', error);
                mostrarError('Error al guardar el cronograma: ' + error.message);
            }
        }

        function cerrarModal() {
            document.getElementById('modalCronograma').classList.add('hidden');
            document.getElementById('formCronograma').reset();
        }

        function mostrarConfirmacion(mensaje) {
            document.getElementById('mensajeConfirmacion').textContent = mensaje;
            document.getElementById('modalConfirmacion').classList.remove('hidden');
        }

        function cerrarConfirmacion() {
            document.getElementById('modalConfirmacion').classList.add('hidden');
        }

        function mostrarError(mensaje) {
            document.getElementById('mensajeError').textContent = mensaje;
            document.getElementById('modalError').classList.remove('hidden');
        }

        function cerrarError() {
            document.getElementById('modalError').classList.add('hidden');
        }

        function formatearFecha(fecha) {
            const year = fecha.getFullYear();
            const month = String(fecha.getMonth() + 1).padStart(2, '0');
            const day = String(fecha.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
    </script>
@endsection