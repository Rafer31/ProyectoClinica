@extends('personal.layouts.personal')
@section('title', 'Lista de Médicos')
@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('personal.home')],
            ['label' => 'Médicos']
        ];
    @endphp

    @push('scripts')
        <script>
            let medicosData = [];
            let medicosFiltrados = [];
            let paginaActual = 1;
            const registrosPorPagina = 10;

            document.addEventListener("DOMContentLoaded", function () {
                cargarMedicos();

                // Event listeners para búsqueda y filtros
                document.getElementById('busqueda').addEventListener('input', filtrarMedicos);
                document.getElementById('filtroTipo').addEventListener('change', filtrarMedicos);
            });

            async function cargarMedicos() {
                mostrarLoader(true);

                try {
                    const response = await fetch('/api/personal/medicos');
                    const data = await response.json();

                    if (data.success) {
                        medicosData = data.data;
                        medicosFiltrados = [...medicosData];
                        paginaActual = 1;
                        renderMedicos(medicosFiltrados);
                        actualizarEstadisticas();
                    } else {
                        mostrarAlerta('error', data.message);
                    }
                } catch (error) {
                    console.error('Error en la API:', error);
                    mostrarAlerta('error', 'Error al cargar los médicos');
                } finally {
                    mostrarLoader(false);
                }
            }

            function filtrarMedicos() {
                const busqueda = document.getElementById('busqueda').value.toLowerCase();
                const filtroTipo = document.getElementById('filtroTipo').value;

                medicosFiltrados = medicosData.filter(m => {
                    const nombreCompleto = `${m.nomMed || ''} ${m.paternoMed || ''}`.toLowerCase();
                    const cumpleBusqueda = nombreCompleto.includes(busqueda);
                    const cumpleTipo = !filtroTipo || m.tipoMed === filtroTipo;

                    return cumpleBusqueda && cumpleTipo;
                });

                paginaActual = 1;
                renderMedicos(medicosFiltrados);
            }

            function renderMedicos(medicos) {
                const tbody = document.getElementById('tabla-medicos');
                const tablaContainer = document.getElementById('tabla-container');
                const noData = document.getElementById('no-data');
                const resultadosCount = document.getElementById('resultados-count');

                tbody.innerHTML = "";

                if (medicos.length > 0) {
                    tablaContainer.classList.remove('hidden');
                    noData.classList.add('hidden');

                    // Calcular paginación
                    const totalPaginas = Math.ceil(medicos.length / registrosPorPagina);
                    const inicio = (paginaActual - 1) * registrosPorPagina;
                    const fin = inicio + registrosPorPagina;
                    const medicosPaginados = medicos.slice(inicio, fin);

                    resultadosCount.textContent = `Mostrando ${inicio + 1}-${Math.min(fin, medicos.length)} de ${medicos.length} médico(s)`;

                    medicosPaginados.forEach((m, index) => {
                        const nombreCompleto = `${m.nomMed || ''} ${m.paternoMed || ''}`.trim();
                        const tipoClass = m.tipoMed === 'Interno'
                            ? 'bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-800 border border-emerald-200'
                            : m.tipoMed === 'Externo'
                            ? 'bg-gradient-to-r from-amber-100 to-orange-100 text-orange-800 border border-orange-200'
                            : 'bg-gray-100 text-gray-800 border border-gray-200';

                        const estadoClass = m.estado === 'Activo'
                            ? 'bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-800 border border-emerald-300'
                            : 'bg-gradient-to-r from-red-100 to-rose-100 text-red-800 border border-red-300';

                        const estadoIcon = m.estado === 'Activo' ? 'check_circle' : 'cancel';

                        const btnEstadoClass = m.estado === 'Activo'
                            ? 'text-red-700 bg-gradient-to-r from-red-50 to-rose-50 hover:from-red-100 hover:to-rose-100 border-red-200'
                            : 'text-emerald-700 bg-gradient-to-r from-emerald-50 to-teal-50 hover:from-emerald-100 hover:to-teal-100 border-emerald-200';

                        const btnEstadoIcon = m.estado === 'Activo' ? 'block' : 'check_circle';
                        const btnEstadoText = m.estado === 'Activo' ? 'Desactivar' : 'Activar';

                        const fila = `
                            <tr class="bg-white border-b hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 transition-all duration-200">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                                                <span class="material-icons text-white text-xl">medical_services</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">${nombreCompleto}</div>
                                            <div class="text-xs text-gray-500 font-medium">ID: ${m.codMed}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1.5 inline-flex text-xs leading-5 font-bold rounded-lg ${tipoClass} shadow-sm">
                                        ${m.tipoMed || 'Sin especificar'}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1.5 inline-flex items-center gap-1 text-xs leading-5 font-bold rounded-lg ${estadoClass} shadow-sm">
                                        <span class="material-icons text-sm">${estadoIcon}</span>
                                        ${m.estado || 'Sin especificar'}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                   <div class="flex items-center gap-2">

    <!-- Botón VER -->
    <button onclick="verDetalle(${m.codMed})"
        class="flex items-center gap-1 px-4 py-2 text-sm font-semibold
               text-teal-700 bg-teal-50 border border-teal-200 rounded-lg
               hover:bg-teal-100 transition shadow-sm"
        title="Ver detalles">
        <span class="material-icons text-base">visibility</span>
        Ver
    </button>

    <!-- Botón EDITAR -->
    <a href="/personal/medicos/editar/${m.codMed}"
        class="flex items-center gap-1 px-4 py-2 text-sm font-semibold
               text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg
               hover:bg-emerald-100 transition shadow-sm"
        title="Editar">
        <span class="material-icons text-base">edit</span>
        Editar
    </a>

    <!-- Botón ACTIVAR/DESACTIVAR -->
    <button onclick="cambiarEstadoMedico(${m.codMed}, '${m.estado}')"
        class="flex items-center gap-1 px-4 py-2 text-sm font-semibold
               ${btnEstadoClass} border rounded-lg transition shadow-sm"
        title="${btnEstadoText}">
        <span class="material-icons text-base">${btnEstadoIcon}</span>
        ${btnEstadoText}
    </button>

</div>

                                </td>
                            </tr>
                        `;
                        tbody.innerHTML += fila;
                    });

                    // Renderizar paginador
                    renderPaginador(totalPaginas);
                } else {
                    tablaContainer.classList.add('hidden');
                    noData.classList.remove('hidden');
                    resultadosCount.textContent = 'No se encontraron resultados';
                    document.getElementById('paginador-container').innerHTML = '';
                }
            }

            function renderPaginador(totalPaginas) {
                const container = document.getElementById('paginador-container');
                if (totalPaginas <= 1) {
                    container.innerHTML = '';
                    return;
                }

                let html = '<div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200">';

                // Información de página
                html += `
                    <div class="text-sm text-gray-700 font-medium">
                        Página <span class="font-bold text-emerald-600">${paginaActual}</span> de <span class="font-bold">${totalPaginas}</span>
                    </div>
                `;

                // Botones de navegación
                html += '<div class="flex gap-2">';

                // Botón anterior
                html += `
                    <button onclick="cambiarPagina(${paginaActual - 1})"
                        ${paginaActual === 1 ? 'disabled' : ''}
                        class="px-4 py-2 text-sm font-bold rounded-lg transition-all duration-200 flex items-center gap-1
                        ${paginaActual === 1
                            ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                            : 'bg-white text-emerald-600 border border-emerald-200 hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 shadow-sm hover:shadow-md'}">
                        <span class="material-icons text-sm">chevron_left</span>
                        Anterior
                    </button>
                `;

                // Números de página
                const maxBotones = 5;
                let inicio = Math.max(1, paginaActual - Math.floor(maxBotones / 2));
                let fin = Math.min(totalPaginas, inicio + maxBotones - 1);

                if (fin - inicio < maxBotones - 1) {
                    inicio = Math.max(1, fin - maxBotones + 1);
                }

                if (inicio > 1) {
                    html += `<button onclick="cambiarPagina(1)" class="px-3 py-2 text-sm font-bold rounded-lg bg-white text-gray-700 border border-gray-200 hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 hover:text-emerald-600 hover:border-emerald-200 transition-all duration-200 shadow-sm">1</button>`;
                    if (inicio > 2) {
                        html += '<span class="px-2 text-gray-500">...</span>';
                    }
                }

                for (let i = inicio; i <= fin; i++) {
                    html += `
                        <button onclick="cambiarPagina(${i})"
                            class="px-3 py-2 text-sm font-bold rounded-lg transition-all duration-200 shadow-sm
                            ${i === paginaActual
                                ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-md'
                                : 'bg-white text-gray-700 border border-gray-200 hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 hover:text-emerald-600 hover:border-emerald-200'}">
                            ${i}
                        </button>
                    `;
                }

                if (fin < totalPaginas) {
                    if (fin < totalPaginas - 1) {
                        html += '<span class="px-2 text-gray-500">...</span>';
                    }
                    html += `<button onclick="cambiarPagina(${totalPaginas})" class="px-3 py-2 text-sm font-bold rounded-lg bg-white text-gray-700 border border-gray-200 hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 hover:text-emerald-600 hover:border-emerald-200 transition-all duration-200 shadow-sm">${totalPaginas}</button>`;
                }

                // Botón siguiente
                html += `
                    <button onclick="cambiarPagina(${paginaActual + 1})"
                        ${paginaActual === totalPaginas ? 'disabled' : ''}
                        class="px-4 py-2 text-sm font-bold rounded-lg transition-all duration-200 flex items-center gap-1
                        ${paginaActual === totalPaginas
                            ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                            : 'bg-white text-emerald-600 border border-emerald-200 hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 shadow-sm hover:shadow-md'}">
                        Siguiente
                        <span class="material-icons text-sm">chevron_right</span>
                    </button>
                `;

                html += '</div></div>';
                container.innerHTML = html;
            }

            function cambiarPagina(nuevaPagina) {
                const totalPaginas = Math.ceil(medicosFiltrados.length / registrosPorPagina);
                if (nuevaPagina >= 1 && nuevaPagina <= totalPaginas) {
                    paginaActual = nuevaPagina;
                    renderMedicos(medicosFiltrados);

                    // Scroll suave a la tabla
                    document.getElementById('tabla-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            function actualizarEstadisticas() {
                const total = medicosData.length;
                const internos = medicosData.filter(m => m.tipoMed === 'Interno').length;

                document.getElementById('stat-total').textContent = total;
                document.getElementById('stat-tipos').textContent = internos;
            }

            async function cambiarEstadoMedico(id, estadoActual) {
                const medico = medicosData.find(m => m.codMed === id);
                if (!medico) return;

                const nombreCompleto = `${medico.nomMed || ''} ${medico.paternoMed || ''}`.trim();
                const nuevoEstado = estadoActual === 'Activo' ? 'Inactivo' : 'Activo';
                const accion = estadoActual === 'Activo' ? 'desactivar' : 'activar';

                const modal = document.getElementById('modalCambiarEstado');
                document.getElementById('nombreMedicoCambiarEstado').textContent = nombreCompleto;
                document.getElementById('tipoMedicoCambiarEstado').textContent = medico.tipoMed || 'N/A';
                document.getElementById('estadoActualMedico').textContent = estadoActual;
                document.getElementById('nuevoEstadoMedico').textContent = nuevoEstado;
                document.getElementById('accionEstado').textContent = accion;

                // Actualizar estilos del modal según la acción
                const iconoEstado = document.getElementById('iconoModalEstado');
                const tituloModal = document.getElementById('tituloModalEstado');
                const btnConfirmar = document.getElementById('btnConfirmarEstado');

                if (estadoActual === 'Activo') {
                    // Desactivar
                    iconoEstado.className = 'rounded-2xl bg-gradient-to-br from-red-500 to-rose-600 p-4 shadow-lg';
                    iconoEstado.innerHTML = '<span class="material-icons text-white text-5xl">block</span>';
                    tituloModal.textContent = '¿Desactivar médico?';
                    btnConfirmar.className = 'flex-1 inline-flex justify-center items-center px-5 py-3 text-sm font-bold text-white bg-gradient-to-r from-red-500 to-rose-600 rounded-xl hover:from-red-600 hover:to-rose-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105';
                    btnConfirmar.innerHTML = '<span class="material-icons text-base mr-1">block</span> Desactivar';
                } else {
                    // Activar
                    iconoEstado.className = 'rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 p-4 shadow-lg';
                    iconoEstado.innerHTML = '<span class="material-icons text-white text-5xl">check_circle</span>';
                    tituloModal.textContent = '¿Activar médico?';
                    btnConfirmar.className = 'flex-1 inline-flex justify-center items-center px-5 py-3 text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105';
                    btnConfirmar.innerHTML = '<span class="material-icons text-base mr-1">check_circle</span> Activar';
                }

                modal.classList.remove('hidden');
                modal.dataset.medicoId = id;
            }

            function cerrarModalCambiarEstado() {
                const modal = document.getElementById('modalCambiarEstado');
                modal.classList.add('hidden');
                delete modal.dataset.medicoId;
            }

            async function confirmarCambioEstado() {
                const modal = document.getElementById('modalCambiarEstado');
                const id = modal.dataset.medicoId;

                if (!id) return;

                modal.classList.add('hidden');
                mostrarLoader(true);

                try {
                    const response = await fetch(`/api/personal/medicos/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        mostrarAlerta('success', data.message);
                        cargarMedicos();
                    } else {
                        mostrarAlerta('error', data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('error', 'Error al cambiar el estado del médico');
                } finally {
                    mostrarLoader(false);
                    delete modal.dataset.medicoId;
                }
            }

            function verDetalle(id) {
                const medico = medicosData.find(m => m.codMed === id);
                if (!medico) return;

                const nombreCompleto = `${medico.nomMed || ''} ${medico.paternoMed || ''}`.trim();
                const tipoClass = medico.tipoMed === 'Interno'
                    ? 'bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-800 border border-emerald-200'
                    : medico.tipoMed === 'Externo'
                    ? 'bg-gradient-to-r from-amber-100 to-orange-100 text-orange-800 border border-orange-200'
                    : 'bg-gray-100 text-gray-800 border border-gray-200';

                const estadoClass = medico.estado === 'Activo'
                    ? 'bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-800 border border-emerald-300'
                    : 'bg-gradient-to-r from-red-100 to-rose-100 text-red-800 border border-red-300';

                const estadoIcon = medico.estado === 'Activo' ? 'check_circle' : 'cancel';

                const modal = document.getElementById('modalDetalle');
                const contenido = `
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4">
                            <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg">
                                <span class="material-icons text-white text-4xl">medical_services</span>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">${nombreCompleto}</h3>
                                <p class="text-gray-500 font-medium">ID: ${medico.codMed}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-4 rounded-xl border border-gray-200">
                                <p class="text-xs font-semibold text-gray-600 mb-1">Nombre</p>
                                <p class="text-gray-900 font-bold">${medico.nomMed || 'N/A'}</p>
                            </div>
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-4 rounded-xl border border-gray-200">
                                <p class="text-xs font-semibold text-gray-600 mb-1">Apellido</p>
                                <p class="text-gray-900 font-bold">${medico.paternoMed || 'N/A'}</p>
                            </div>
                            <div class="col-span-2 bg-gradient-to-br from-gray-50 to-gray-100 p-4 rounded-xl border border-gray-200">
                                <p class="text-xs font-semibold text-gray-600 mb-2">Tipo de Médico</p>
                                <span class="px-4 py-2 text-sm font-bold rounded-xl ${tipoClass} shadow-sm inline-block">
                                    ${medico.tipoMed || 'Sin especificar'}
                                </span>
                            </div>
                            <div class="col-span-2 bg-gradient-to-br from-gray-50 to-gray-100 p-4 rounded-xl border border-gray-200">
                                <p class="text-xs font-semibold text-gray-600 mb-2">Estado</p>
                                <span class="px-4 py-2 text-sm font-bold rounded-xl ${estadoClass} shadow-sm inline-flex items-center gap-2">
                                    <span class="material-icons text-base">${estadoIcon}</span>
                                    ${medico.estado || 'Sin especificar'}
                                </span>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <button onclick="cerrarModal()" class="inline-flex items-center px-5 py-2.5 text-sm font-bold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-sm">
                                <span class="material-icons text-base mr-1">close</span>
                                Cerrar
                            </button>
                            <a href="/personal/medicos/editar/${medico.codMed}" class="inline-flex items-center px-5 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                                <span class="material-icons text-base mr-1">edit</span>
                                Editar
                            </a>
                        </div>
                    </div>
                `;

                document.getElementById('modalContenido').innerHTML = contenido;
                modal.classList.remove('hidden');
            }

            function cerrarModal() {
                document.getElementById('modalDetalle').classList.add('hidden');
            }

            function mostrarAlerta(tipo, mensaje) {
                const alerta = document.getElementById('alerta');
                const iconos = {
                    success: 'check_circle',
                    error: 'error',
                    info: 'info'
                };
                const colores = {
                    success: 'bg-gradient-to-r from-emerald-50 to-teal-50 border-emerald-400 text-emerald-800',
                    error: 'bg-gradient-to-r from-red-50 to-rose-50 border-red-400 text-red-800',
                    info: 'bg-gradient-to-r from-blue-50 to-cyan-50 border-blue-400 text-blue-800'
                };

                alerta.className = `p-4 rounded-xl border-2 flex items-center ${colores[tipo]} mb-4 shadow-lg`;
                alerta.innerHTML = `
                    <span class="material-icons mr-3 text-2xl">${iconos[tipo]}</span>
                    <span class="font-semibold">${mensaje}</span>
                `;
                alerta.classList.remove('hidden');

                setTimeout(() => {
                    alerta.classList.add('hidden');
                }, 5000);
            }

            function mostrarLoader(mostrar) {
                const loader = document.getElementById('loader');
                if (mostrar) {
                    loader.classList.remove('hidden');
                } else {
                    loader.classList.add('hidden');
                }
            }

            function limpiarFiltros() {
                document.getElementById('busqueda').value = '';
                document.getElementById('filtroTipo').value = '';
                filtrarMedicos();
            }
        </script>
    @endpush

    <div class="space-y-6">
        <!-- Alerta -->
        <div id="alerta" class="hidden"></div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl p-4 shadow-lg">
                        <span class="material-icons text-white text-3xl">medical_services</span>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-semibold text-gray-600">Total Médicos</p>
                        <p class="text-3xl font-bold text-gray-900" id="stat-total">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-xl p-4 shadow-lg">
                        <span class="material-icons text-white text-3xl">people</span>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-semibold text-gray-600">Médicos Internos</p>
                        <p class="text-3xl font-bold text-gray-900" id="stat-tipos">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Encabezado y Filtros -->
        <div class="bg-gradient-to-r from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="material-icons text-white text-2xl">medical_services</span>
                        </div>
                        Lista de Médicos
                    </h1>
                    <p class="text-gray-600 ml-15 font-medium" id="resultados-count">Cargando...</p>
                </div>
                <a href="{{ route('personal.medicos.agregar') }}"
                    class="mt-4 md:mt-0 inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all shadow-lg hover:shadow-xl font-bold transform hover:scale-105">
                    <span class="material-icons">add_circle</span>
                    Agregar Médico
                </a>
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Buscar Médico</label>
                    <div class="relative">
                        <span class="material-icons absolute left-3 top-3 text-emerald-600">search</span>
                        <input type="text" id="busqueda" placeholder="Buscar por nombre o apellido..."
                            class="pl-11 w-full bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 p-3 transition-all shadow-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tipo de Médico</label>
                    <select id="filtroTipo"
                        class="w-full bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 p-3 transition-all shadow-sm font-medium">
                        <option value="">Todos los tipos</option>
                        <option value="Interno">Interno</option>
                        <option value="Externo">Externo</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button onclick="limpiarFiltros()" class="inline-flex items-center text-sm text-emerald-600 hover:text-emerald-700 font-bold hover:underline">
                    <span class="material-icons text-sm mr-1">clear</span>
                    Limpiar filtros
                </button>
            </div>
        </div>

        <!-- Loader -->
        <div id="loader" class="flex justify-center items-center py-16">
            <div class="relative">
                <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200"></div>
                <div class="animate-spin rounded-full h-16 w-16 border-4 border-emerald-500 border-t-transparent absolute top-0 left-0"></div>
            </div>
        </div>

        <!-- Mensaje sin datos -->
        <div id="no-data" class="hidden bg-white rounded-xl shadow-lg border border-gray-200 p-16">
            <div class="flex flex-col items-center justify-center">
                <div class="w-32 h-32 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <span class="material-icons text-emerald-600" style="font-size: 80px;">medical_services</span>
                </div>
                <p class="text-gray-900 text-xl font-bold mb-2">No hay médicos registrados</p>
                <p class="text-gray-500 text-sm">Comienza agregando tu primer médico al sistema</p>
            </div>
        </div>

        <!-- Tabla de médicos -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden" id="tabla-container">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gradient-to-r from-emerald-500 to-teal-600 border-b-2 border-teal-700">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-white font-bold">Médico</th>
                            <th scope="col" class="px-6 py-4 text-white font-bold">Tipo</th>
                            <th scope="col" class="px-6 py-4 text-white font-bold">Estado</th>
                            <th scope="col" class="px-6 py-4 text-white font-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-medicos"></tbody>
                </table>
            </div>

            <!-- Paginador -->
            <div id="paginador-container"></div>
        </div>
    </div>

    <!-- Modal Detalle -->
    <div id="modalDetalle" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
        <div class="relative top-20 mx-auto p-6 border border-gray-200 w-full max-w-2xl shadow-2xl rounded-2xl bg-white">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="material-icons text-white text-xl">info</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Detalle del Médico</h3>
                </div>
                <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-all">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div id="modalContenido"></div>
        </div>
    </div>

    <!-- Modal Cambiar Estado -->
    <div id="modalCambiarEstado" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 backdrop-blur-sm flex items-center justify-center">
        <div class="relative mx-auto p-6 border border-gray-200 w-full max-w-md shadow-2xl rounded-2xl bg-white">
            <!-- Icono dinámico -->
            <div class="flex justify-center mb-6">
                <div id="iconoModalEstado" class="rounded-2xl bg-gradient-to-br from-red-500 to-rose-600 p-4 shadow-lg">
                    <span class="material-icons text-white text-5xl">block</span>
                </div>
            </div>

            <!-- Título dinámico -->
            <h3 id="tituloModalEstado" class="text-2xl font-bold text-gray-900 text-center mb-3">¿Cambiar estado del médico?</h3>

            <!-- Contenido -->
            <div class="text-center mb-6">
                <p class="text-gray-600 mb-4 font-medium">Estás a punto de <span id="accionEstado" class="font-bold text-gray-900">cambiar</span> al médico:</p>
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-5 mb-4 border border-gray-200 shadow-sm">
                    <p class="font-bold text-gray-900 text-lg" id="nombreMedicoCambiarEstado"></p>
                    <p class="text-sm text-gray-600 mt-1">Tipo: <span class="font-semibold" id="tipoMedicoCambiarEstado"></span></p>
                </div>

                <!-- Info del cambio de estado -->
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 border-l-4 border-blue-500 p-4 rounded-lg">
                    <div class="flex items-center justify-center gap-3 text-sm">
                        <div class="text-center">
                            <p class="text-xs text-gray-600 mb-1">Estado actual</p>
                            <p class="font-bold text-gray-900" id="estadoActualMedico">Activo</p>
                        </div>
                        <span class="material-icons text-blue-600">arrow_forward</span>
                        <div class="text-center">
                            <p class="text-xs text-gray-600 mb-1">Nuevo estado</p>
                            <p class="font-bold text-blue-700" id="nuevoEstadoMedico">Inactivo</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex gap-3">
                <button onclick="cerrarModalCambiarEstado()"
                    class="flex-1 inline-flex justify-center items-center px-5 py-3 text-sm font-bold text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-sm">
                    <span class="material-icons text-base mr-1">close</span>
                    Cancelar
                </button>
                <button id="btnConfirmarEstado" onclick="confirmarCambioEstado()"
                    class="flex-1 inline-flex justify-center items-center px-5 py-3 text-sm font-bold text-white bg-gradient-to-r from-red-500 to-rose-600 rounded-xl hover:from-red-600 hover:to-rose-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <span class="material-icons text-base mr-1">block</span>
                    Desactivar
                </button>
            </div>
        </div>
    </div>

@endsection
