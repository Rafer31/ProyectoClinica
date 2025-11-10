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
                    resultadosCount.textContent = `Mostrando ${medicos.length} médico(s)`;

                    medicos.forEach((m, index) => {
                        const nombreCompleto = `${m.nomMed || ''} ${m.paternoMed || ''}`.trim();
                        const tipoClass = m.tipoMed === 'Interno' ? 'bg-green-100 text-green-800' :
                                         m.tipoMed === 'Externo' ? 'bg-orange-100 text-orange-800' :
                                         'bg-gray-100 text-gray-800';

                        const fila = `
                            <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="material-icons text-indigo-600">medical_services</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">${nombreCompleto}</div>
                                            <div class="text-sm text-gray-500">ID: ${m.codMed}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${tipoClass}">
                                        ${m.tipoMed || 'Sin especificar'}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <div class="flex items-center gap-2">
                                        <button onclick="verDetalle(${m.codMed})"
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200 border border-blue-200"
                                            title="Ver detalles">
                                            <span class="material-icons text-base mr-1">visibility</span>
                                            Ver
                                        </button>
                                        <a href="/personal/medicos/editar/${m.codMed}"
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200 border border-green-200"
                                            title="Editar">
                                            <span class="material-icons text-base mr-1">edit</span>
                                            Editar
                                        </a>
                                        <button onclick="eliminarMedico(${m.codMed})"
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors duration-200 border border-red-200"
                                            title="Eliminar">
                                            <span class="material-icons text-base mr-1">delete</span>
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tbody.innerHTML += fila;
                    });
                } else {
                    tablaContainer.classList.add('hidden');
                    noData.classList.remove('hidden');
                    resultadosCount.textContent = 'No se encontraron resultados';
                }
            }

            function actualizarEstadisticas() {
                const total = medicosData.length;
                const internos = medicosData.filter(m => m.tipoMed === 'Interno').length;

                document.getElementById('stat-total').textContent = total;
                document.getElementById('stat-tipos').textContent = internos;
            }

            async function eliminarMedico(id) {
                const medico = medicosData.find(m => m.codMed === id);
                if (!medico) return;

                const nombreCompleto = `${medico.nomMed || ''} ${medico.paternoMed || ''}`.trim();

                // Mostrar modal de confirmación
                const modal = document.getElementById('modalEliminar');
                document.getElementById('nombreMedicoEliminar').textContent = nombreCompleto;
                document.getElementById('tipoMedicoEliminar').textContent = medico.tipoMed || 'N/A';

                modal.classList.remove('hidden');

                // Guardar el ID en el modal para usarlo después
                modal.dataset.medicoId = id;
            }

            // Función para cerrar el modal de eliminación
            function cerrarModalEliminar() {
                const modal = document.getElementById('modalEliminar');
                modal.classList.add('hidden');
                delete modal.dataset.medicoId;
            }

            // Función para confirmar eliminación
            async function confirmarEliminacion() {
                const modal = document.getElementById('modalEliminar');
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
                    mostrarAlerta('error', 'Error al eliminar el médico');
                } finally {
                    mostrarLoader(false);
                    delete modal.dataset.medicoId;
                }
            }

            function verDetalle(id) {
                const medico = medicosData.find(m => m.codMed === id);
                if (!medico) return;

                const nombreCompleto = `${medico.nomMed || ''} ${medico.paternoMed || ''}`.trim();

                const modal = document.getElementById('modalDetalle');
                const contenido = `
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="material-icons text-indigo-600 text-3xl">medical_services</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">${nombreCompleto}</h3>
                                <p class="text-gray-500">ID: ${medico.codMed}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Nombre</p>
                                <p class="text-gray-900">${medico.nomMed || 'N/A'}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Apellido</p>
                                <p class="text-gray-900">${medico.paternoMed || 'N/A'}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm font-medium text-gray-500">Tipo de Médico</p>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full ${medico.tipoMed === 'Interno' ? 'bg-green-100 text-green-800' : medico.tipoMed === 'Externo' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800'}">
                                    ${medico.tipoMed || 'Sin especificar'}
                                </span>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <button onclick="cerrarModal()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                <span class="material-icons text-base mr-1">close</span>
                                Cerrar
                            </button>
                            <a href="/personal/medicos/editar/${medico.codMed}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm">
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
                    success: 'bg-green-100 border-green-400 text-green-800',
                    error: 'bg-red-100 border-red-400 text-red-800',
                    info: 'bg-blue-100 border-blue-400 text-blue-800'
                };

                alerta.className = `p-4 rounded-lg border flex items-center ${colores[tipo]} mb-4`;
                alerta.innerHTML = `
                    <span class="material-icons mr-2">${iconos[tipo]}</span>
                    <span>${mensaje}</span>
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
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                        <span class="material-icons text-indigo-600 text-3xl">medical_services</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Médicos</p>
                        <p class="text-2xl font-bold text-gray-900" id="stat-total">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                        <span class="material-icons text-purple-600 text-3xl">people</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Médicos Internos</p>
                        <p class="text-2xl font-bold text-gray-900" id="stat-tipos">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Encabezado y Filtros -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center">
                        <span class="material-icons text-4xl text-indigo-600 mr-3">medical_services</span>
                        Lista de Médicos
                    </h1>
                    <p class="text-gray-600" id="resultados-count">Cargando...</p>
                </div>
                <a href="{{ route('personal.medicos.agregar') }}"
                    class="mt-4 md:mt-0 inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-md hover:shadow-lg font-medium">
                    <span class="material-icons mr-2">add</span>
                    Agregar Médico
                </a>
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <div class="relative">
                        <span class="material-icons absolute left-3 top-2.5 text-gray-400">search</span>
                        <input type="text" id="busqueda" placeholder="Buscar por nombre..."
                            class="pl-10 w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Médico</label>
                    <select id="filtroTipo"
                        class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                        <option value="">Todos</option>
                        <option value="Interno">Interno</option>
                        <option value="Externo">Externo</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button onclick="limpiarFiltros()" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    <span class="material-icons text-sm mr-1">clear</span>
                    Limpiar filtros
                </button>
            </div>
        </div>

        <!-- Loader -->
        <div id="loader" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
        </div>

        <!-- Mensaje sin datos -->
        <div id="no-data" class="hidden bg-white rounded-lg shadow p-12">
            <div class="flex flex-col items-center justify-center">
                <span class="material-icons text-gray-300" style="font-size: 120px;">medical_services</span>
                <p class="text-gray-600 text-lg mt-4 font-medium">No hay médicos registrados</p>
                <p class="text-gray-500 text-sm mt-2">Comienza agregando tu primer médico</p>
            </div>
        </div>

        <!-- Tabla de médicos -->
        <div class="bg-white rounded-lg shadow overflow-hidden" id="tabla-container">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4">Médico</th>
                            <th scope="col" class="px-6 py-4">Tipo</th>
                            <th scope="col" class="px-6 py-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-medicos"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detalle -->
    <div id="modalDetalle" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-900">Detalle del Médico</h3>
                <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div id="modalContenido"></div>
        </div>
    </div>

    <!-- Modal Eliminar -->
    <div id="modalEliminar" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-6 border w-full max-w-md shadow-xl rounded-lg bg-white">
            <!-- Icono de advertencia -->
            <div class="flex justify-center mb-4">
                <div class="rounded-full bg-red-100 p-3">
                    <span class="material-icons text-red-600 text-4xl">warning</span>
                </div>
            </div>

            <!-- Título -->
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">¿Eliminar médico?</h3>

            <!-- Contenido -->
            <div class="text-center mb-6">
                <p class="text-gray-600 mb-3">Estás a punto de eliminar al médico:</p>
                <div class="bg-gray-50 rounded-lg p-4 mb-3">
                    <p class="font-semibold text-gray-900" id="nombreMedicoEliminar"></p>
                    <p class="text-sm text-gray-600">Tipo: <span id="tipoMedicoEliminar"></span></p>
                </div>
                <p class="text-sm text-red-600 font-medium">Esta acción no se puede deshacer</p>
            </div>

            <!-- Botones -->
            <div class="flex gap-3">
                <button onclick="cerrarModalEliminar()"
                    class="flex-1 inline-flex justify-center items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    <span class="material-icons text-base mr-1">close</span>
                    Cancelar
                </button>
                <button onclick="confirmarEliminacion()"
                    class="flex-1 inline-flex justify-center items-center px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors duration-200 shadow-sm">
                    <span class="material-icons text-base mr-1">delete</span>
                    Eliminar
                </button>
            </div>
        </div>
    </div>

@endsection
