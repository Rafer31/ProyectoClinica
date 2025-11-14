@extends('supervisor.layouts.supervisor')
@section('title', 'Gestión de Personal de Salud')
@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('supervisor.home')],
            ['label' => 'Gestión de Personal']
        ];
    @endphp

    @push('scripts')
        <script>
            let personalData = [];
            let personalFiltrado = [];

            document.addEventListener("DOMContentLoaded", function () {
                cargarPersonal();

                // Event listeners para búsqueda y filtros
                document.getElementById('busqueda').addEventListener('input', filtrarPersonal);
                document.getElementById('filtroRol').addEventListener('change', filtrarPersonal);
                document.getElementById('filtroEstado').addEventListener('change', filtrarPersonal);
            });

            async function cargarPersonal() {
                mostrarLoader(true);

                try {
                    const response = await fetch('/api/personal-salud');
                    const data = await response.json();

                    if (data.success) {
                        personalData = data.data;
                        personalFiltrado = [...personalData];
                        renderPersonal(personalFiltrado);
                        actualizarEstadisticas();
                    } else {
                        mostrarAlerta('error', data.message);
                    }
                } catch (error) {
                    console.error('Error en la API:', error);
                    mostrarAlerta('error', 'Error al cargar el personal de salud');
                } finally {
                    mostrarLoader(false);
                }
            }

            function filtrarPersonal() {
                const busqueda = document.getElementById('busqueda').value.toLowerCase();
                const filtroRol = document.getElementById('filtroRol').value;
                const filtroEstado = document.getElementById('filtroEstado').value;

                personalFiltrado = personalData.filter(p => {
                    const nombreCompleto = `${p.nomPer || ''} ${p.paternoPer || ''} ${p.maternoPer || ''}`.toLowerCase();
                    const usuario = (p.usuarioPer || '').toLowerCase();
                    const cumpleBusqueda = nombreCompleto.includes(busqueda) || usuario.includes(busqueda);
                    const cumpleRol = !filtroRol || (p.rol && p.rol.codRol == filtroRol);
                    const cumpleEstado = !filtroEstado || p.estado === filtroEstado;

                    return cumpleBusqueda && cumpleRol && cumpleEstado;
                });

                renderPersonal(personalFiltrado);
            }

            function renderPersonal(personal) {
                const tbody = document.getElementById('tabla-personal');
                const tablaContainer = document.getElementById('tabla-container');
                const noData = document.getElementById('no-data');
                const resultadosCount = document.getElementById('resultados-count');

                tbody.innerHTML = "";

                if (personal.length > 0) {
                    tablaContainer.classList.remove('hidden');
                    noData.classList.add('hidden');
                    resultadosCount.textContent = `Mostrando ${personal.length} registro(s)`;

                    personal.forEach((p, index) => {
                        const nombreCompleto = `${p.nomPer || ''} ${p.paternoPer || ''} ${p.maternoPer || ''}`.trim();
                        const rolNombre = p.rol ? p.rol.nombreRol : 'Sin rol';
                        const estadoClass = p.estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                        const esSupervisor = p.rol && p.rol.nombreRol.toLowerCase().includes('supervisor');

                        const fila = `
                            <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full ${p.estado === 'activo' ? 'bg-blue-100' : 'bg-gray-100'} flex items-center justify-center">
                                                <span class="material-icons ${p.estado === 'activo' ? 'text-blue-600' : 'text-gray-400'}">person</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">${nombreCompleto}</div>
                                            <div class="text-sm text-gray-500">Usuario: ${p.usuarioPer || 'N/A'}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        ${rolNombre}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${estadoClass}">
                                        ${p.estado === 'activo' ? 'Activo' : 'Inactivo'}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <div class="flex items-center gap-2">
                                        <button onclick="verDetalle(${p.codPer})"
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200 border border-blue-200"
                                            title="Ver detalles">
                                            <span class="material-icons text-base mr-1">visibility</span>
                                            Ver
                                        </button>
                                        <a href="{{ route('supervisor.gestion-personal.gestion-personal') }}/editar/${p.codPer}"
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200 border border-green-200"
                                            title="Editar">
                                            <span class="material-icons text-base mr-1">edit</span>
                                            Editar
                                        </a>
                                        ${!esSupervisor ? `
                                        <button onclick="cambiarEstado(${p.codPer}, '${p.estado}')"
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium ${p.estado === 'activo' ? 'text-red-700 bg-red-50 hover:bg-red-100 border-red-200' : 'text-green-700 bg-green-50 hover:bg-green-100 border-green-200'} rounded-lg transition-colors duration-200 border"
                                            title="${p.estado === 'activo' ? 'Desactivar' : 'Activar'}">
                                            <span class="material-icons text-base mr-1">${p.estado === 'activo' ? 'block' : 'check_circle'}</span>
                                            ${p.estado === 'activo' ? 'Desactivar' : 'Activar'}
                                        </button>
                                        ` : ''}
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
                const total = personalData.length;
                const activos = personalData.filter(p => p.estado === 'activo').length;
                const inactivos = personalData.filter(p => p.estado === 'inactivo').length;

                document.getElementById('stat-total').textContent = total;
                document.getElementById('stat-activos').textContent = activos;
                document.getElementById('stat-inactivos').textContent = inactivos;
            }

            async function cambiarEstado(id, estadoActual) {
                const personal = personalData.find(p => p.codPer === id);
                if (!personal) return;

                const nombreCompleto = `${personal.nomPer || ''} ${personal.paternoPer || ''} ${personal.maternoPer || ''}`.trim();
                const nuevoEstado = estadoActual === 'activo' ? 'inactivo' : 'activo';

                // Mostrar modal de confirmación
                const modal = document.getElementById('modalCambiarEstado');
                document.getElementById('nombrePersonalEstado').textContent = nombreCompleto;
                document.getElementById('usuarioPersonalEstado').textContent = personal.usuarioPer || 'N/A';
                document.getElementById('estadoActualText').textContent = estadoActual === 'activo' ? 'activo' : 'inactivo';
                document.getElementById('estadoNuevoText').textContent = nuevoEstado === 'activo' ? 'activar' : 'desactivar';

                const accionTexto = document.getElementById('accionEstadoTexto');
                if (nuevoEstado === 'inactivo') {
                    accionTexto.className = 'text-sm text-red-600 font-medium';
                    accionTexto.textContent = 'Esta persona no podrá acceder al sistema hasta que sea reactivada';
                } else {
                    accionTexto.className = 'text-sm text-green-600 font-medium';
                    accionTexto.textContent = 'Esta persona podrá acceder nuevamente al sistema';
                }

                modal.classList.remove('hidden');
                modal.dataset.personalId = id;
            }

            function cerrarModalEstado() {
                const modal = document.getElementById('modalCambiarEstado');
                modal.classList.add('hidden');
                delete modal.dataset.personalId;
            }

            async function confirmarCambioEstado() {
                const modal = document.getElementById('modalCambiarEstado');
                const id = modal.dataset.personalId;

                if (!id) return;

                modal.classList.add('hidden');
                mostrarLoader(true);

                try {
                    const response = await fetch(`/api/personal-salud/${id}/cambiar-estado`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        mostrarAlerta('success', data.message);
                        cargarPersonal();
                    } else {
                        mostrarAlerta('error', data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('error', 'Error al cambiar el estado');
                } finally {
                    mostrarLoader(false);
                    delete modal.dataset.personalId;
                }
            }

            async function toggleAccesoSistema() {
                const modal = document.getElementById('modalToggleAcceso');
                modal.classList.remove('hidden');
            }

            function cerrarModalToggle() {
                const modal = document.getElementById('modalToggleAcceso');
                modal.classList.add('hidden');
            }

            async function confirmarToggleAcceso(accion) {
                const modal = document.getElementById('modalToggleAcceso');
                modal.classList.add('hidden');
                mostrarLoader(true);

                try {
                    // Obtener todos los usuarios no supervisores
                    const personalNoSupervisor = personalData.filter(p => {
                        return p.rol && !p.rol.nombreRol.toLowerCase().includes('supervisor');
                    });

                    let errores = 0;
                    let exitosos = 0;

                    for (const persona of personalNoSupervisor) {
                        // Solo cambiar si es necesario
                        const debeSerActivo = accion === 'activar';
                        const estadoActual = persona.estado === 'activo';

                        if (debeSerActivo !== estadoActual) {
                            try {
                                const response = await fetch(`/api/personal-salud/${persona.codPer}/cambiar-estado`, {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                });

                                const data = await response.json();
                                if (data.success) {
                                    exitosos++;
                                } else {
                                    errores++;
                                }
                            } catch (error) {
                                errores++;
                            }
                        }
                    }

                    if (errores === 0) {
                        mostrarAlerta('success', `Acceso al sistema ${accion === 'activar' ? 'habilitado' : 'bloqueado'} correctamente para ${exitosos} usuario(s)`);
                    } else {
                        mostrarAlerta('error', `Se procesaron ${exitosos} usuarios correctamente, pero ${errores} fallaron`);
                    }

                    cargarPersonal();
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('error', 'Error al cambiar el acceso del personal');
                } finally {
                    mostrarLoader(false);
                }
            }

            function verDetalle(id) {
                const personal = personalData.find(p => p.codPer === id);
                if (!personal) return;

                const nombreCompleto = `${personal.nomPer || ''} ${personal.paternoPer || ''} ${personal.maternoPer || ''}`.trim();
                const rolNombre = personal.rol ? personal.rol.nombreRol : 'Sin rol';

                const modal = document.getElementById('modalDetalle');
                const contenido = `
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="h-16 w-16 rounded-full ${personal.estado === 'activo' ? 'bg-blue-100' : 'bg-gray-100'} flex items-center justify-center">
                                <span class="material-icons ${personal.estado === 'activo' ? 'text-blue-600' : 'text-gray-400'} text-3xl">person</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">${nombreCompleto}</h3>
                                <p class="text-gray-500">ID: ${personal.codPer}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Usuario</p>
                                <p class="text-gray-900">${personal.usuarioPer || 'N/A'}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Rol</p>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                    ${rolNombre}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Nombre</p>
                                <p class="text-gray-900">${personal.nomPer || 'N/A'}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Apellido Paterno</p>
                                <p class="text-gray-900">${personal.paternoPer || 'N/A'}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Apellido Materno</p>
                                <p class="text-gray-900">${personal.maternoPer || 'N/A'}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Estado</p>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full ${personal.estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${personal.estado === 'activo' ? 'Activo' : 'Inactivo'}
                                </span>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <button onclick="cerrarModal()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                <span class="material-icons text-base mr-1">close</span>
                                Cerrar
                            </button>
                            <a href="/personal/gestion-personal/editar/${personal.codPer}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm">
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
                document.getElementById('filtroRol').value = '';
                document.getElementById('filtroEstado').value = '';
                filtrarPersonal();
            }
        </script>
    @endpush

    <div class="space-y-6">
        <!-- Alerta -->
        <div id="alerta" class="hidden"></div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                        <span class="material-icons text-indigo-600 text-3xl">people</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Personal</p>
                        <p class="text-2xl font-bold text-gray-900" id="stat-total">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                        <span class="material-icons text-green-600 text-3xl">check_circle</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Personal Activo</p>
                        <p class="text-2xl font-bold text-gray-900" id="stat-activos">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                        <span class="material-icons text-red-600 text-3xl">block</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Personal Inactivo</p>
                        <p class="text-2xl font-bold text-gray-900" id="stat-inactivos">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Encabezado y Filtros -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center">
                        <span class="material-icons text-4xl text-indigo-600 mr-3">admin_panel_settings</span>
                        Gestión de Personal de Salud
                    </h1>
                    <p class="text-gray-600" id="resultados-count">Cargando...</p>
                </div>
                <div class="mt-4 md:mt-0 flex gap-3">
                    <button onclick="toggleAccesoSistema()"
                        class="inline-flex items-center px-5 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors shadow-md hover:shadow-lg font-medium">
                        <span class="material-icons mr-2">lock</span>
                        Control de Acceso
                    </button>
                    <a href="{{ route('supervisor.gestion-personal.agregar') }}"
                        class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-md hover:shadow-lg font-medium">
                        <span class="material-icons mr-2">person_add</span>
                        Agregar Personal
                    </a>
                </div>
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <div class="relative">
                        <span class="material-icons absolute left-3 top-2.5 text-gray-400">search</span>
                        <input type="text" id="busqueda" placeholder="Buscar por nombre o usuario..."
                            class="pl-10 w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
                    <select id="filtroRol"
                        class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                        <option value="">Todos los roles</option>
                        <!-- Roles dinámicos se cargarían desde la API -->
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="filtroEstado"
                        class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                        <option value="">Todos los estados</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
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
                <span class="material-icons text-gray-300" style="font-size: 120px;">people</span>
                <p class="text-gray-600 text-lg mt-4 font-medium">No hay personal registrado</p>
                <p class="text-gray-500 text-sm mt-2">Comienza agregando personal de salud al sistema</p>
            </div>
        </div>

        <!-- Tabla de personal -->
        <div class="bg-white rounded-lg shadow overflow-hidden" id="tabla-container">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4">Personal</th>
                            <th scope="col" class="px-6 py-4">Rol</th>
                            <th scope="col" class="px-6 py-4">Estado</th>
                            <th scope="col" class="px-6 py-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-personal"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detalle -->
    <div id="modalDetalle" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-900">Detalle del Personal</h3>
                <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div id="modalContenido"></div>
        </div>
    </div>

    <!-- Modal Cambiar Estado -->
    <div id="modalCambiarEstado" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-6 border w-full max-w-md shadow-xl rounded-lg bg-white">
            <div class="flex justify-center mb-4">
                <div class="rounded-full bg-yellow-100 p-3">
                    <span class="material-icons text-yellow-600 text-4xl">warning</span>
                </div>
            </div>

            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">¿Cambiar estado?</h3>

            <div class="text-center mb-6">
                <p class="text-gray-600 mb-3">Estás a punto de <span id="estadoNuevoText" class="font-semibold"></span> a:</p>
                <div class="bg-gray-50 rounded-lg p-4 mb-3">
                    <p class="font-semibold text-gray-900" id="nombrePersonalEstado"></p>
                    <p class="text-sm text-gray-600">Usuario: <span id="usuarioPersonalEstado"></span></p>
                    <p class="text-sm text-gray-600">Estado actual: <span id="estadoActualText" class="font-semibold"></span></p>
                </div>
                <p id="accionEstadoTexto" class="text-sm text-yellow-600 font-medium"></p>
            </div>

            <div class="flex gap-3">
                <button onclick="cerrarModalEstado()"
                    class="flex-1 inline-flex justify-center items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    <span class="material-icons text-base mr-1">close</span>
                    Cancelar
                </button>
                <button onclick="confirmarCambioEstado()"
                    class="flex-1 inline-flex justify-center items-center px-4 py-2.5 text-sm font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700 transition-colors duration-200 shadow-sm">
                    <span class="material-icons text-base mr-1">swap_horiz</span>
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Toggle Acceso Sistema -->
    <div id="modalToggleAcceso" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-6 border w-full max-w-lg shadow-xl rounded-lg bg-white">
            <div class="flex justify-center mb-4">
                <div class="rounded-full bg-purple-100 p-3">
                    <span class="material-icons text-purple-600 text-4xl">lock</span>
                </div>
            </div>

            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Control de Acceso al Sistema</h3>

            <div class="text-center mb-6">
                <p class="text-gray-600 mb-4">Selecciona la acción que deseas realizar para todo el personal (excepto supervisores):</p>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <span class="material-icons text-blue-600 mr-2 mt-0.5">info</span>
                        <div class="text-sm text-blue-800 text-left">
                            <p class="font-semibold mb-1">Información importante:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Esta acción afectará a todo el personal no supervisor</li>
                                <li>Los supervisores mantienen siempre su acceso</li>
                                <li>Puedes revertir esta acción en cualquier momento</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button onclick="confirmarToggleAcceso('bloquear')"
                        class="flex flex-col items-center justify-center p-4 bg-red-50 border-2 border-red-200 rounded-lg hover:bg-red-100 transition-colors duration-200 group">
                        <span class="material-icons text-red-600 text-3xl mb-2 group-hover:scale-110 transition-transform">block</span>
                        <span class="font-semibold text-red-800">Bloquear Acceso</span>
                        <span class="text-xs text-red-600 mt-1">Desactivar a todos</span>
                    </button>

                    <button onclick="confirmarToggleAcceso('activar')"
                        class="flex flex-col items-center justify-center p-4 bg-green-50 border-2 border-green-200 rounded-lg hover:bg-green-100 transition-colors duration-200 group">
                        <span class="material-icons text-green-600 text-3xl mb-2 group-hover:scale-110 transition-transform">check_circle</span>
                        <span class="font-semibold text-green-800">Habilitar Acceso</span>
                        <span class="text-xs text-green-600 mt-1">Activar a todos</span>
                    </button>
                </div>
            </div>

            <div class="flex justify-center">
                <button onclick="cerrarModalToggle()"
                    class="inline-flex items-center px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    <span class="material-icons text-base mr-1">close</span>
                    Cancelar
                </button>
            </div>
        </div>
    </div>

@endsection
