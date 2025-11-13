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
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        <span class="material-icons align-middle text-4xl text-blue-600">calendar_today</span>
                        Cronogramas de Atención
                    </h1>
                    <p class="text-gray-600">Gestiona los horarios semanales de atención</p>
                </div>
                <button onclick="abrirModalCrear()"
                    class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <span class="material-icons me-2">add</span>
                    Nuevo Cronograma
                </button>
            </div>
        </div>
        <!-- Selector de semana -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <button onclick="cambiarSemana(-1)" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                    <span class="material-icons">chevron_left</span>
                </button>
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900" id="tituloSemana">Cargando...</h3>
                    <p class="text-sm text-gray-600" id="rangoSemana"></p>
                </div>
                <button onclick="cambiarSemana(1)" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                    <span class="material-icons">chevron_right</span>
                </button>
            </div>
        </div>
        <!-- Calendario de cronogramas -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="grid grid-cols-7 gap-px bg-gray-200" id="calendarioCronogramas">
                <!-- Los días se generarán dinámicamente -->
            </div>
        </div>
        <!-- Leyenda -->
        <div class="bg-white rounded-lg shadow p-4">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Leyenda:</h4>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-500 rounded me-2"></div>
                    <span class="text-sm text-gray-700">Activo</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-yellow-500 rounded me-2"></div>
                    <span class="text-sm text-gray-700">Inactivo Futuro</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-gray-400 rounded me-2"></div>
                    <span class="text-sm text-gray-700">Inactivo Pasado</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 border-2 border-blue-500 rounded me-2"></div>
                    <span class="text-sm text-gray-700">Día actual</span>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para crear cronograma -->
    <div id="modalCronograma" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Crear Cronograma</h3>
                <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <form id="formCronograma" onsubmit="guardarCronograma(event)">
                <div class="space-y-4">
                    <!-- Fecha -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="material-icons text-sm align-middle">event</span>
                            Fecha del Cronograma
                        </label>
                        <input type="date" id="fechaCrono" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <!-- Información automática -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <div class="flex items-start">
                            <span class="material-icons text-blue-600 text-xl mr-2">info</span>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-blue-900 mb-2">Se creará con:</p>
                                <ul class="text-sm text-blue-800 space-y-1">
                                    <li class="flex items-center">
                                        <span class="material-icons text-xs mr-1">check_circle</span>
                                        <strong>15 turnos</strong> disponibles
                                    </li>
                                    <li class="flex items-center">
                                        <span class="material-icons text-xs mr-1">check_circle</span>
                                        Estado: <strong id="infoEstado" class="ml-1">Selecciona una fecha</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2 mt-6">
                    <button type="button" onclick="cerrarModal()"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        <span class="material-icons text-sm align-middle mr-1">save</span>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal de Confirmación -->
    <div id="modalConfirmacion" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-1/3 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <span class="material-icons text-green-600 text-3xl">check_circle</span>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">¡Cronograma Creado!</h3>
                <p class="text-sm text-gray-600 mb-4" id="mensajeConfirmacion"></p>
                <button onclick="cerrarConfirmacion()"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Entendido
                </button>
            </div>
        </div>
    </div>
    <!-- Modal de Error -->
    <div id="modalError" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-1/3 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <span class="material-icons text-red-600 text-3xl">error</span>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Error</h3>
                <p class="text-sm text-gray-600 mb-4" id="mensajeError"></p>
                <button onclick="cerrarError()"
                    class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
    <!-- Modal de Detalle de Cronograma -->
    <div id="modalDetalle" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <!-- Encabezado -->
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mr-3" id="detalleIconoContainer">
                        <span class="material-icons text-3xl" id="detalleIcono">calendar_today</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Detalles del Cronograma</h3>
                        <p class="text-sm text-gray-500" id="detalleFecha"></p>
                    </div>
                </div>
                <button onclick="cerrarDetalle()" class="text-gray-400 hover:text-gray-600">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <!-- Contenido -->
            <div class="space-y-4">
                <!-- Estado -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="material-icons text-gray-600 mr-2">info</span>
                            <span class="text-sm font-medium text-gray-700">Estado</span>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium" id="detalleEstadoBadge"></span>
                    </div>
                </div>
                <!-- Turnos -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <span class="material-icons text-gray-600 mr-2">event_available</span>
                            <span class="text-sm font-medium text-gray-700">Disponibilidad de Turnos</span>
                        </div>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-4">
                        <div class="text-center p-3 bg-white rounded border">
                            <p class="text-xs text-gray-500 mb-1">Disponibles</p>
                            <p class="text-2xl font-bold text-green-600" id="detalleDisponibles">0</p>
                        </div>
                        <div class="text-center p-3 bg-white rounded border">
                            <p class="text-xs text-gray-500 mb-1">Atendidos</p>
                            <p class="text-2xl font-bold text-blue-600" id="detalleAtendidos">0</p>
                        </div>
                    </div>
                    <div class="mt-3 text-center p-3 bg-white rounded border">
                        <p class="text-xs text-gray-500 mb-1">Total de Turnos</p>
                        <p class="text-2xl font-bold text-gray-600" id="detalleTotales">0</p>
                    </div>
                    <!-- Barra de progreso -->
                    <div class="mt-3">
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Progreso de atención</span>
                            <span id="detallePorcentaje">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" id="detalleBarraProgreso"
                                style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <!-- Información adicional -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <div class="flex items-start">
                        <span class="material-icons text-blue-600 text-xl mr-2">lightbulb</span>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-blue-900 mb-1">Información</p>
                            <p class="text-xs text-blue-800" id="detalleInfo"></p>
                        </div>
                    </div>
                </div>
                <!-- Creado por -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <span class="material-icons text-gray-600 mr-2">person</span>
                        <div>
                            <p class="text-xs text-gray-500">Creado por</p>
                            <p class="text-sm font-medium text-gray-900" id="detalleCreador">-</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Botones de acción -->
            <div class="mt-6">
                <button onclick="cerrarDetalle()"
                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    <span class="material-icons text-sm align-middle mr-1">close</span>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
    <style>
        .dia-calendario {
            min-height: 140px;
            background: white;
            transition: all 0.3s ease;
            position: relative;
        }

        .dia-calendario:hover {
            background: #f9fafb;
        }

        .dia-con-cronograma {
            cursor: pointer;
        }

        .dia-con-cronograma:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px -2px rgba(0, 0, 0, 0.15);
        }

        .cronograma-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .estado-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            box-shadow: 0 0 0 3px white;
        }

        .turnos-info {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-left: 3px solid #3b82f6;
        }

        .progreso-bar-container {
            background: #e5e7eb;
            height: 6px;
            border-radius: 3px;
            overflow: hidden;
        }

        .progreso-bar {
            height: 100%;
            transition: width 0.3s ease;
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        }

        .progreso-bar.medio {
            background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
        }

        .progreso-bar.alto {
            background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
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
                <div class="bg-gray-50 px-4 py-3 text-center border-b-2 border-gray-200">
                    <span class="text-sm font-semibold text-gray-700">${dia}</span>
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
                let bordeDia = esHoy ? 'ring-2 ring-blue-500 ring-inset' : '';

                if (cronograma) {
                    let colorEstado = '';
                    let iconoEstado = '';
                    let textoEstado = '';

                    if (cronograma.estado === 'activo') {
                        colorEstado = 'bg-green-500';
                        iconoEstado = 'check_circle';
                        textoEstado = 'Activo';
                    } else if (cronograma.estado === 'inactivoFut') {
                        colorEstado = 'bg-yellow-500';
                        iconoEstado = 'schedule';
                        textoEstado = 'Programado';
                    } else {
                        colorEstado = 'bg-gray-400';
                        iconoEstado = 'cancel';
                        textoEstado = 'Finalizado';
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
                        <div class="cronograma-card p-4">
                            <div class="estado-badge ${colorEstado}"></div>
                            <div class="flex justify-between items-start mb-3">
                                <span class="text-2xl font-bold text-gray-800">${fecha.getDate()}</span>
                                <span class="text-xs px-2 py-1 rounded-full ${colorEstado} text-white font-medium">
                                    ${textoEstado}
                                </span>
                            </div>
                            <div class="space-y-2 flex-grow">
                                <!-- Turnos Normales -->
                                <div class="turnos-info rounded-lg px-3 py-2">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-xs font-medium text-gray-700">Turnos Normales</span>
                                        <span class="material-icons text-sm text-blue-600">${iconoEstado}</span>
                                    </div>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-xl font-bold text-blue-700">${cronograma.cantDispo}</span>
                                        <span class="text-xs text-gray-600">disponibles</span>
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        ${atendidos} de ${totalFichas}
                                    </div>
                                </div>
                                <!-- Turnos Emergencia -->
                                <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-lg px-3 py-2 border border-red-200">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-xs font-medium text-red-700 flex items-center gap-1">
                                            <span class="material-icons text-xs">local_hospital</span>
                                            Emergencia
                                        </span>
                                    </div>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-xl font-bold text-red-700">${fichasEmergencia}</span>
                                        <span class="text-xs text-gray-600">disponibles</span>
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
                                <span class="text-2xl font-bold text-gray-700">${fecha.getDate()}</span>
                            </div>
                            <div class="flex-grow flex items-center justify-center">
                                <button onclick="crearCronogramaRapido('${fechaStr}')"
                                    class="w-full py-3 text-sm text-blue-600 hover:bg-blue-50 rounded-lg border-2 border-dashed border-blue-300 transition-all hover:border-blue-400 hover:shadow-sm">
                                    <span class="material-icons text-lg block mb-1">add_circle_outline</span>
                                    <span class="font-medium">Crear cronograma</span>
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
        iconoContainer.className = 'w-12 h-12 rounded-full flex items-center justify-center mr-3 bg-green-100';
        icono.className = 'material-icons text-3xl text-green-600';
        icono.textContent = 'check_circle';
        estadoBadge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
        estadoBadge.textContent = 'Activo';
    } else if (cronograma.estado === 'inactivoFut') {
        iconoContainer.className = 'w-12 h-12 rounded-full flex items-center justify-center mr-3 bg-yellow-100';
        icono.className = 'material-icons text-3xl text-yellow-600';
        icono.textContent = 'schedule';
        estadoBadge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800';
        estadoBadge.textContent = 'Programado';
    } else {
        iconoContainer.className = 'w-12 h-12 rounded-full flex items-center justify-center mr-3 bg-gray-100';
        icono.className = 'material-icons text-3xl text-gray-600';
        icono.textContent = 'cancel';
        estadoBadge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800';
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
        barra.className = 'bg-green-600 h-2 rounded-full transition-all duration-300';
    } else if (porcentajeOcupado < 80) {
        barra.className = 'bg-yellow-600 h-2 rounded-full transition-all duration-300';
    } else {
        barra.className = 'bg-red-600 h-2 rounded-full transition-all duration-300';
    }

    let infoTexto = '';
    if (cronograma.estado === 'activo') {
        if (disponibles > 0) {
            infoTexto = `Este cronograma está activo. Turnos normales: ${disponibles} disponibles, ${atendidos} atendidos. Turnos de emergencia: ${fichasEmergencia} disponibles.`;
        } else {
            infoTexto = `Este cronograma está activo pero no tiene turnos normales disponibles. Turnos de emergencia: ${fichasEmergencia}.`;
        }
    } else if (cronograma.estado === 'inactivoFut') {
        infoTexto = `Este cronograma está programado para una fecha futura. Tendrá ${totalFichas} turnos normales y ${fichasEmergencia} de emergencia.`;
    } else {
        infoTexto = `Este cronograma ya finalizó. Se atendieron ${atendidos} de ${totalFichas} turnos normales (${porcentajeOcupado}%).`;
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