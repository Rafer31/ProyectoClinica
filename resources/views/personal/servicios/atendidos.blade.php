@extends('personal.layouts.personal')

@section('title', 'Servicios Atendidos')

@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('personal.home')],
            ['label' => 'Servicios', 'url' => route('personal.servicios.servicios')],
            ['label' => 'Servicios Atendidos']
        ];
    @endphp

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
                            <span class="material-icons text-white text-2xl">check_circle</span>
                        </div>
                        Servicios Atendidos
                    </h1>
                    <p class="text-emerald-600 font-medium ml-15">Servicios listos para entrega al paciente</p>
                </div>
                <a href="{{ route('personal.servicios.servicios') }}"
                    class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl hover:from-gray-600 hover:to-gray-700 transition-all shadow-md hover:shadow-lg font-semibold">
                    <span class="material-icons">arrow_back</span>
                    <span>Ver Todos los Servicios</span>
                </a>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <span class="material-icons text-3xl">check_circle</span>
                    </div>
                    <span class="text-xs font-bold bg-white bg-opacity-25 px-3 py-1 rounded-full">TOTAL</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="stat-total-atendidos">0</p>
                <p class="text-sm opacity-90 font-medium">Total Atendidos</p>
            </div>

            <div
                class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <span class="material-icons text-3xl">today</span>
                    </div>
                    <span class="text-xs font-bold bg-white bg-opacity-25 px-3 py-1 rounded-full">HOY</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="stat-atendidos-hoy">0</p>
                <p class="text-sm opacity-90 font-medium">Atendidos Hoy</p>
            </div>

            <div
                class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <span class="material-icons text-3xl">pending</span>
                    </div>
                    <span class="text-xs font-bold bg-white bg-opacity-25 px-3 py-1 rounded-full">PENDIENTES</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="stat-pendientes-entrega">0</p>
                <p class="text-sm opacity-90 font-medium">Pendientes de Entrega</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                        <span class="material-icons text-sm text-emerald-600">date_range</span>
                        Fecha de Atención
                    </label>
                    <input type="date" id="filtro-fecha"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 transition-all font-medium">
                </div>
            </div>

            <div class="flex gap-3 mt-4">

            </div>
        </div>

        <!-- Loader -->
        <div id="loader" class="hidden flex justify-center items-center py-12">
            <div class="flex flex-col items-center gap-3">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-emerald-200 border-t-emerald-600"></div>
                <span class="text-sm font-medium text-gray-600">Cargando servicios atendidos...</span>
            </div>
        </div>

        <!-- Mensaje sin datos -->
        <div id="no-data" class="hidden bg-white rounded-xl shadow-sm border border-gray-100 p-12">
            <div class="flex flex-col items-center justify-center">
                <div class="w-32 h-32 bg-emerald-50 rounded-full flex items-center justify-center mb-4">
                    <span class="material-icons text-emerald-300" style="font-size: 80px;">check_circle</span>
                </div>
                <p class="text-gray-800 text-xl font-bold mb-2">No hay servicios atendidos</p>
                <p class="text-gray-500 text-sm">Los servicios marcados como atendidos aparecerán aquí</p>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" id="tabla-container">
            <div class="p-6 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-emerald-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
                            <span class="material-icons text-white">list_alt</span>
                        </div>
                        Servicios Atendidos
                    </h2>
                    <span class="text-sm font-semibold text-emerald-700" id="contador-servicios">0 servicios</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead
                        class="text-xs text-gray-700 uppercase bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold">Nro. Servicio</th>
                            <th scope="col" class="px-6 py-4 font-bold">Paciente</th>
                            <th scope="col" class="px-6 py-4 font-bold">Tipo Estudio</th>
                            <th scope="col" class="px-6 py-4 font-bold">Diagnóstico</th>
                            <th scope="col" class="px-6 py-4 font-bold">Fecha Atención</th>
                            <th scope="col" class="px-6 py-4 font-bold text-center" style="min-width: 250px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-servicios">
                        <!-- Cargando -->
                    </tbody>
                </table>
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

    <!-- Modal Confirmar Entrega -->
    <div id="modal-entregar"
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
                    <h3 class="text-2xl font-bold text-white" id="modal-title">Marcar como Entregado</h3>
                        <p class="text-sm text-purple-100" id="modal-subtitle">Confirme la entrega del servicio al paciente</p>
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
                        <div class="flex items-center gap-3 mb-3">
                            <span class="material-icons text-purple-600">person</span>
                            <div>
                                <p class="text-xs text-gray-500">Paciente</p>
                                <p class="font-bold text-gray-900" id="entregar-paciente">-</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-icons text-purple-600">shield</span>
                            <div>
                                <p class="text-xs text-gray-500">Tipo de Seguro</p>
                                <p class="font-bold text-gray-900" id="entregar-tipo-seguro">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800 font-medium flex items-start gap-2">
                        <span class="material-icons text-blue-600 text-lg">info</span>
                        <span>El paciente <strong>NO cuenta con seguro</strong>. Al confirmar, el estado cambiará a <strong>"Entregado"</strong> y se registrará la fecha y hora de entrega automáticamente.</span>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex gap-3">
                <button type="button" onclick="cerrarModal('modal-entregar')"
                    class="flex-1 px-5 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-semibold">
                    Cancelar
                </button>
                <button type="button" id="btn-confirmar-entrega"
                    class="flex-1 px-5 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-lg hover:from-purple-600 hover:to-indigo-700 transition-all shadow-md font-semibold flex items-center justify-center gap-2">
                    <span class="material-icons">check_circle</span>
                    <span>Confirmar Entrega</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Archivado -->
    <div id="modal-archivar"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-0 border w-full max-w-lg shadow-2xl rounded-xl bg-white">
            <!-- Header -->
            <div class="bg-gradient-to-r from-orange-500 to-amber-600 rounded-t-xl p-6">
                <div class="flex items-center">
                    <div
                        class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4 backdrop-blur-sm">
                        <span class="material-icons text-white text-3xl">archive</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">Archivar Servicio</h3>
                        <p class="text-sm text-orange-100">El reporte se archivará y no se entregará al paciente</p>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-lg">
                    <p class="text-gray-800 font-semibold mb-2">Está a punto de archivar este servicio:</p>
                    <div class="bg-white p-4 rounded-lg mt-3">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="material-icons text-orange-600">receipt</span>
                            <div>
                                <p class="text-xs text-gray-500">Nro. Servicio</p>
                                <p class="font-bold text-gray-900" id="archivar-nro-servicio">-</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 mb-3">
                            <span class="material-icons text-orange-600">person</span>
                            <div>
                                <p class="text-xs text-gray-500">Paciente</p>
                                <p class="font-bold text-gray-900" id="archivar-paciente">-</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-icons text-orange-600">shield</span>
                            <div>
                                <p class="text-xs text-gray-500">Tipo de Seguro</p>
                                <p class="font-bold text-gray-900" id="archivar-tipo-seguro">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border border-orange-200 rounded-lg p-4">
                    <p class="text-sm text-orange-800 font-medium flex items-start gap-2">
                        <span class="material-icons text-orange-600 text-lg">info</span>
                        <span>El paciente <strong>SÍ cuenta con seguro</strong>. Al confirmar, el estado cambiará a <strong>"Archivado"</strong> y el reporte se guardará sin entregar al paciente.</span>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex gap-3">
                <button type="button" onclick="cerrarModal('modal-archivar')"
                    class="flex-1 px-5 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-semibold">
                    Cancelar
                </button>
                <button type="button" id="btn-confirmar-archivado"
                    class="flex-1 px-5 py-3 bg-gradient-to-r from-orange-500 to-amber-600 text-white rounded-lg hover:from-orange-600 hover:to-amber-700 transition-all shadow-md font-semibold flex items-center justify-center gap-2">
                    <span class="material-icons">archive</span>
                    <span>Confirmar Archivado</span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let serviciosData = [];
            let servicioActual = null;

            document.addEventListener('DOMContentLoaded', function () {
                cargarServiciosAtendidos();
                cargarEstadisticas();

                // Event listeners
                document.getElementById('buscar-servicio').addEventListener('input', filtrarServicios);
                document.getElementById('filtro-fecha').addEventListener('change', filtrarServicios);
            });

            async function cargarServiciosAtendidos() {
                mostrarLoader(true);
                const tablaContainer = document.getElementById('tabla-container');
                const noData = document.getElementById('no-data');

                try {
                    const response = await fetch('/api/personal/servicios');
                    const data = await response.json();

                    if (data.success) {
                        // FILTRAR: Incluir servicios con estado "Atendido", "Entregado" o "Archivado"
                        serviciosData = data.data.filter(s =>
                            s.estado === 'Atendido' || s.estado === 'Entregado' || s.estado === 'Archivado'
                        );

                        if (serviciosData.length > 0) {
                            renderServicios(serviciosData);
                            tablaContainer.classList.remove('hidden');
                            noData.classList.add('hidden');
                        } else {
                            serviciosData = [];
                            tablaContainer.classList.add('hidden');
                            noData.classList.remove('hidden');
                        }
                    } else {
                        serviciosData = [];
                        tablaContainer.classList.add('hidden');
                        noData.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('Error al cargar los servicios atendidos', 'error');
                } finally {
                    mostrarLoader(false);
                }
            }

            function renderServicios(servicios) {
                const tbody = document.getElementById('tabla-servicios');
                const contador = document.getElementById('contador-servicios');
                tbody.innerHTML = '';

                contador.textContent = `${servicios.length} servicio${servicios.length !== 1 ? 's' : ''}`;

                if (servicios.length === 0) {
                    tbody.innerHTML = `
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center">
                                            <span class="material-icons text-emerald-300 text-3xl">check_circle</span>
                                        </div>
                                        <p class="text-gray-500 font-medium">No se encontraron servicios atendidos</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                    return;
                }

                servicios.forEach(servicio => {
                    const paciente = `${servicio.paciente?.nomPa || ''} ${servicio.paciente?.paternoPa || ''}`.trim();

                    // Obtener diagnóstico
                    const diagnostico = servicio.diagnosticos && servicio.diagnosticos.length > 0
                        ? servicio.diagnosticos[0].descripDiag.substring(0, 50) + (servicio.diagnosticos[0].descripDiag.length > 50 ? '...' : '')
                        : 'Sin diagnóstico';

                    const tipoDiagnostico = servicio.diagnosticos && servicio.diagnosticos.length > 0
                        ? (servicio.diagnosticos[0].pivot?.tipo === 'sol' ? 'Solicitado' : 'Ecográfico')
                        : '';

                    // Badge de estado
                    const estaEntregado = servicio.estado === 'Entregado';
                    const estaArchivado = servicio.estado === 'Archivado';

                    let badgeEstado = '';
                    if (estaEntregado) {
                        badgeEstado = `<span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold rounded-full border bg-purple-100 text-purple-700 border-purple-300">
                                <span class="material-icons text-xs">done_all</span>
                                Entregado
                              </span>`;
                    } else if (estaArchivado) {
                        badgeEstado = `<span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold rounded-full border bg-gray-100 text-gray-700 border-gray-300">
                                <span class="material-icons text-xs">archive</span>
                                Archivado
                              </span>`;
                    }

                    const fila = `
                            <tr class="border-b hover:bg-emerald-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-emerald-600">${servicio.nroServ || 'N/A'}</span>
                                    <div class="text-xs text-gray-500 mt-1">Ficha: ${servicio.nroFicha || 'N/A'}</div>
                                    ${badgeEstado ? `<div class="mt-2">${badgeEstado}</div>` : ''}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">${paciente}</div>
                                    <div class="text-xs text-emerald-600 font-medium">${servicio.paciente?.nroHCI || 'Sin HCI'}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-900 font-medium">${servicio.tipo_estudio?.descripcion || 'N/A'}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-700 text-sm leading-relaxed">${diagnostico}</div>
                                    ${tipoDiagnostico ? `<span class="text-xs font-semibold text-emerald-700 mt-1 inline-block">${tipoDiagnostico}</span>` : ''}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">${formatearFecha(servicio.fechaAten)}</div>
                                    <div class="text-xs text-gray-500">${servicio.horaAten || ''}</div>
                                    ${estaEntregado && servicio.fechaEnt ? `
                                        <div class="mt-2 pt-2 border-t border-gray-200">
                                            <p class="text-xs text-purple-600 font-semibold">Entregado:</p>
                                            <p class="text-xs text-gray-700">${formatearFecha(servicio.fechaEnt)}</p>
                                        </div>
                                    ` : ''}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 justify-center">
                                        <button onclick="verDetalle(${servicio.codServ})"
                                            class="inline-flex items-center px-3 py-2 text-sm font-semibold text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all border border-blue-200 hover:shadow-md"
                                            title="Ver detalles">
                                            <span class="material-icons text-base mr-1">visibility</span>
                                            Ver
                                        </button>

                                        <a href="/personal/servicios/${servicio.nroServ}/pdf/ver" target="_blank"
                                            class="inline-flex items-center px-3 py-2 text-sm font-semibold text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-all border border-red-200 hover:shadow-md"
                                            title="Ver PDF">
                                            <span class="material-icons text-base mr-1">picture_as_pdf</span>
                                            PDF
                                        </a>

                                        ${!estaEntregado && !estaArchivado ? (
                                            servicio.tipoAseg?.startsWith('Aseg') ? `
                                                <button onclick="confirmarArchivado(${servicio.codServ})" 
                                                    class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-gradient-to-r from-orange-500 to-amber-600 rounded-lg hover:from-orange-600 hover:to-amber-700 transition-all shadow-md hover:shadow-lg"
                                                    title="Marcar como archivado">
                                                    <span class="material-icons text-base mr-1">archive</span>
                                                    Archivar
                                                </button>
                                            ` : `
                                                <button onclick="confirmarEntrega(${servicio.codServ})" 
                                                    class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg hover:from-purple-600 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg"
                                                    title="Marcar como entregado">
                                                    <span class="material-icons text-base mr-1">description</span>
                                                    Entregar
                                                </button>
                                            `
                                        ) : `
                                            <span class="inline-flex items-center px-3 py-2 text-sm font-semibold ${estaArchivado ? 'text-gray-700 bg-gray-50 border-gray-200' : 'text-purple-700 bg-purple-50 border-purple-200'} rounded-lg border"
                                                title="${estaArchivado ? 'Ya fue archivado' : 'Ya fue entregado'}">
                                                <span class="material-icons text-base mr-1">${estaArchivado ? 'archive' : 'check'}</span>
                                                ${estaArchivado ? 'Archivado' : 'Entregado'}
                                            </span>
                                        `}
                                    </div>
                                </td>
                            </tr>
                        `;
                    tbody.innerHTML += fila;
                });
            }

            function filtrarServicios() {
                const busqueda = document.getElementById('buscar-servicio').value.toLowerCase();
                const filtroFecha = document.getElementById('filtro-fecha').value;

                // DEBUG: Ver qué fechas vienen del servidor (TEMPORAL - remover después)
                if (filtroFecha && serviciosData.length > 0) {
                    console.log('Fecha del filtro:', filtroFecha);
                    console.log('Ejemplo de fechaAten del servidor:', serviciosData[0].fechaAten);
                }

                const serviciosFiltrados = serviciosData.filter(s => {
                    const paciente = `${s.paciente?.nomPa || ''} ${s.paciente?.paternoPa || ''}`.toLowerCase();
                    const cumpleBusqueda =
                        s.nroServ?.toLowerCase().includes(busqueda) ||
                        paciente.includes(busqueda) ||
                        s.paciente?.nroHCI?.toLowerCase().includes(busqueda);

                    // Normalizar fechas para comparación
                    let cumpleFecha = true;
                    if (filtroFecha) {
                        if (s.fechaAten) {
                            // Intentar varios formatos
                            let fechaAtenNormalizada = s.fechaAten;

                            // Si viene con timestamp (2025-11-16T00:00:00.000Z)
                            if (fechaAtenNormalizada.includes('T')) {
                                fechaAtenNormalizada = fechaAtenNormalizada.split('T')[0];
                            }

                            // Si viene con espacios (2025-11-16 00:00:00)
                            if (fechaAtenNormalizada.includes(' ')) {
                                fechaAtenNormalizada = fechaAtenNormalizada.split(' ')[0];
                            }

                            // DEBUG: Ver comparación (TEMPORAL)
                            if (filtroFecha) {
                                console.log(`Comparando: "${fechaAtenNormalizada}" === "${filtroFecha}"`, fechaAtenNormalizada === filtroFecha);
                            }

                            cumpleFecha = fechaAtenNormalizada === filtroFecha;
                        } else {
                            cumpleFecha = false;
                        }
                    }

                    return cumpleBusqueda && cumpleFecha;
                });

                console.log(`Total servicios: ${serviciosData.length}, Filtrados: ${serviciosFiltrados.length}`);
                renderServicios(serviciosFiltrados);
            }

            async function cargarEstadisticas() {
                try {
                    const response = await fetch('/api/personal/servicios');
                    const data = await response.json();

                    if (data.success) {
                        const serviciosAtendidos = data.data.filter(s =>
                            s.estado === 'Atendido' || s.estado === 'Entregado' || s.estado === 'Archivado'
                        );

                        const hoy = new Date().toISOString().split('T')[0];
                        const atendidosHoy = serviciosAtendidos.filter(s => s.fechaAten === hoy).length;

                        // Contar solo los que están en estado "Atendido" (pendientes de entrega)
                        const pendientesEntrega = data.data.filter(s => s.estado === 'Atendido').length;

                        document.getElementById('stat-total-atendidos').textContent = serviciosAtendidos.length;
                        document.getElementById('stat-atendidos-hoy').textContent = atendidosHoy;
                        document.getElementById('stat-pendientes-entrega').textContent = pendientesEntrega;
                    }
                } catch (error) {
                    console.error('Error al cargar estadísticas:', error);
                }
            }

            function confirmarEntrega(codServ) {
                const servicio = serviciosData.find(s => s.codServ === codServ);
                if (!servicio) return;

                servicioActual = servicio;
                const paciente = `${servicio.paciente?.nomPa || ''} ${servicio.paciente?.paternoPa || ''} ${servicio.paciente?.maternoPa || ''}`.trim();
                const tipoSeguro = servicio.tipoAseg?.replace('Aseg', 'Con Seguro - ').replace('NoAseg', 'Sin Seguro - ') || 'No especificado';

                document.getElementById('entregar-nro-servicio').textContent = servicio.nroServ;
                document.getElementById('entregar-paciente').textContent = paciente;
                document.getElementById('entregar-tipo-seguro').textContent = tipoSeguro;
                document.getElementById('modal-entregar').classList.remove('hidden');
            }

            function confirmarArchivado(codServ) {
                const servicio = serviciosData.find(s => s.codServ === codServ);
                if (!servicio) return;

                servicioActual = servicio;
                const paciente = `${servicio.paciente?.nomPa || ''} ${servicio.paciente?.paternoPa || ''} ${servicio.paciente?.maternoPa || ''}`.trim();
                const tipoSeguro = servicio.tipoAseg?.replace('Aseg', 'Con Seguro - ').replace('NoAseg', 'Sin Seguro - ') || 'No especificado';

                document.getElementById('archivar-nro-servicio').textContent = servicio.nroServ;
                document.getElementById('archivar-paciente').textContent = paciente;
                document.getElementById('archivar-tipo-seguro').textContent = tipoSeguro;
                document.getElementById('modal-archivar').classList.remove('hidden');
            }

            document.getElementById('btn-confirmar-entrega').addEventListener('click', async function () {
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
                        mostrarAlerta(data.message, 'success');
                        cerrarModal('modal-entregar');
                        cargarServiciosAtendidos();
                        cargarEstadisticas();
                        servicioActual = null;
                    } else {
                        mostrarAlerta(data.message || 'Error al marcar como entregado', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('Error al marcar como entregado', 'error');
                }
            });

            document.getElementById('btn-confirmar-archivado').addEventListener('click', async function () {
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
                        mostrarAlerta(data.message, 'success');
                        cerrarModal('modal-archivar');
                        cargarServiciosAtendidos();
                        cargarEstadisticas();
                        servicioActual = null;
                    } else {
                        mostrarAlerta(data.message || 'Error al archivar', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('Error al archivar el servicio', 'error');
                }
            });

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
                                                                                <span class="px-4 py-2 text-sm font-bold rounded-full border bg-emerald-100 text-emerald-700 border-emerald-300">
                                                                                    Atendido
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
                                                                                    <p class="text-xs text-gray-600 mb-1 font-semibold uppercase">Fecha Atención</p>
                                                                                    <p class="font-bold text-gray-900">${formatearFecha(s.fechaAten)}</p>
                                                                                    <p class="text-xs text-gray-500 mt-1">${s.horaAten || ''}</p>
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

                                                                        <!-- Fechas del Proceso -->
                                                                        <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                                                                            <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                                                                                <span class="material-icons text-gray-600">schedule</span>
                                                                                Historial del Proceso
                                                                            </h4>
                                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                                                                    <div class="flex items-center gap-2 mb-2">
                                                                                        <span class="material-icons text-blue-600 text-sm">event</span>
                                                                                        <p class="text-xs text-gray-600 font-semibold uppercase">Fecha Solicitud</p>
                                                                                    </div>
                                                                                    <p class="font-bold text-gray-900">${formatearFecha(s.fechaSol)}</p>
                                                                                    <p class="text-xs text-gray-500 mt-1">${s.horaSol || ''}</p>
                                                                                </div>
                                                                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                                                                    <div class="flex items-center gap-2 mb-2">
                                                                                        <span class="material-icons text-emerald-600 text-sm">check_circle</span>
                                                                                        <p class="text-xs text-gray-600 font-semibold uppercase">Fecha Atención</p>
                                                                                    </div>
                                                                                    <p class="font-bold text-gray-900">${formatearFecha(s.fechaAten)}</p>
                                                                                    <p class="text-xs text-gray-500 mt-1">${s.horaAten || ''}</p>
                                                                                </div>
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

            function cerrarModal(modalId) {
                document.getElementById(modalId).classList.add('hidden');
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
                                                            <div class="w-10 h-10 ${tipo === 'success' ? 'bg-emerald-500' : tipo === 'error' ? 'bg-red-500' : 'bg-blue-500'} rounded-lg flex items-center justify-center mr-3">
                                                                <span class="material-icons text-white">${iconos[tipo]}</span>
                                                            </div>
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
        </script>
    @endpush
@endsection
