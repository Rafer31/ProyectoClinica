@extends('personal.layouts.personal')
@section('title', 'Lista de Pacientes')
@section('content')
@php
$breadcrumbs = [
['label' => 'Inicio', 'url' => route('personal.home')],
['label' => 'Pacientes']
];
@endphp

@push('scripts')
<script>
    let pacientesData = [];
    let pacientesFiltrados = [];

    document.addEventListener("DOMContentLoaded", function() {
        cargarPacientes();

        // Event listeners para búsqueda y filtros
        document.getElementById('busqueda').addEventListener('input', filtrarPacientes);
        document.getElementById('filtroEstado').addEventListener('change', filtrarPacientes);
        document.getElementById('filtroTipo').addEventListener('change', filtrarPacientes);
    });

    async function cargarPacientes() {
        mostrarLoader(true);

        try {
            const response = await fetch('/api/personal/pacientes');
            const data = await response.json();

            if (data.success) {
                pacientesData = data.data;
                pacientesFiltrados = [...pacientesData];
                renderPacientes(pacientesFiltrados);
                actualizarEstadisticas();
            } else {
                mostrarAlerta('error', data.message);
            }
        } catch (error) {
            console.error('Error en la API:', error);
            mostrarAlerta('error', 'Error al cargar los pacientes');
        } finally {
            mostrarLoader(false);
        }
    }

    function filtrarPacientes() {
        const busqueda = document.getElementById('busqueda').value.toLowerCase();
        const filtroEstado = document.getElementById('filtroEstado').value;
        const filtroTipo = document.getElementById('filtroTipo').value;

        pacientesFiltrados = pacientesData.filter(p => {
            const nombreCompleto = `${p.nomPa || ''} ${p.paternoPa || ''} ${p.maternoPa || ''}`.toLowerCase();
            const cumpleBusqueda = nombreCompleto.includes(busqueda) ||
                (p.nroHCI || '').toLowerCase().includes(busqueda);

            const cumpleEstado = !filtroEstado || p.estado === filtroEstado;
            const cumpleTipo = !filtroTipo || p.tipoPac === filtroTipo;

            return cumpleBusqueda && cumpleEstado && cumpleTipo;
        });

        renderPacientes(pacientesFiltrados);
    }

    function renderPacientes(pacientes) {
        const tbody = document.getElementById('tabla-pacientes');
        const tablaContainer = document.getElementById('tabla-container');
        const noData = document.getElementById('no-data');
        const resultadosCount = document.getElementById('resultados-count');

        tbody.innerHTML = "";

        if (pacientes.length > 0) {
            tablaContainer.classList.remove('hidden');
            noData.classList.add('hidden');
            resultadosCount.textContent = `Mostrando ${pacientes.length} paciente(s)`;

            pacientes.forEach((p, index) => {
                const nombreCompleto = `${p.nomPa || ''} ${p.paternoPa || ''} ${p.maternoPa || ''}`.trim();
                const estadoClass = p.estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                const tipoClass = p.tipoPac === 'SUS' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800';

                const edad = p.fechaNac ? calcularEdad(p.fechaNac) : '-';
                const sexoIcon = p.sexo === 'M' ? 'male' : p.sexo === 'F' ? 'female' : 'person';

                const fila = `
                    <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="material-icons text-blue-600">${sexoIcon}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${nombreCompleto}</div>
                                    <div class="text-sm text-gray-500">${p.nroHCI || 'Sin HCI'}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">${edad} años</div>
                            <div class="text-sm text-gray-500">${p.fechaNac ? new Date(p.fechaNac).toLocaleDateString('es-ES') : 'Sin fecha'}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${tipoClass}">
                                ${p.tipoPac || 'N/A'}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${estadoClass}">
                                ${p.estado || 'N/A'}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div class="flex items-center space-x-3">
                                <button onclick="verDetalle(${p.codPa})" 
                                    class="text-blue-600 hover:text-blue-900 flex items-center"
                                    title="Ver detalles">
                                    <span class="material-icons text-sm">visibility</span>
                                </button>
                                <a href="/personal/pacientes/editar/${p.codPa}" 
                                    class="text-green-600 hover:text-green-900 flex items-center"
                                    title="Editar">
                                    <span class="material-icons text-sm">edit</span>
                                </a>
                                <button onclick="cambiarEstado(${p.codPa}, '${p.estado}')" 
                                    class="text-orange-600 hover:text-orange-900 flex items-center"
                                    title="${p.estado === 'activo' ? 'Desactivar' : 'Activar'}">
                                    <span class="material-icons text-sm">${p.estado === 'activo' ? 'block' : 'check_circle'}</span>
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

    function calcularEdad(fechaNac) {
        const hoy = new Date();
        const nacimiento = new Date(fechaNac);
        let edad = hoy.getFullYear() - nacimiento.getFullYear();
        const mes = hoy.getMonth() - nacimiento.getMonth();

        if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
            edad--;
        }

        return edad >= 0 ? edad : 0;
    }

    function actualizarEstadisticas() {
        const total = pacientesData.length;
        const activos = pacientesData.filter(p => p.estado === 'activo').length;
        const sus = pacientesData.filter(p => p.tipoPac === 'SUS').length;

        document.getElementById('stat-total').textContent = total;
        document.getElementById('stat-activos').textContent = activos;
        document.getElementById('stat-sus').textContent = sus;
    }

    async function cambiarEstado(id, estadoActual) {
        const nuevoEstado = estadoActual === 'activo' ? 'inactivo' : 'activo';
        const accion = nuevoEstado === 'activo' ? 'activar' : 'desactivar';

        if (!confirm(`¿Estás seguro de ${accion} este paciente?`)) {
            return;
        }

        try {
            const response = await fetch(`/api/personal/pacientes/${id}`, {
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
                cargarPacientes();
            } else {
                mostrarAlerta('error', data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarAlerta('error', 'Error al cambiar el estado del paciente');
        }
    }

    function verDetalle(id) {
        const paciente = pacientesData.find(p => p.codPa === id);
        if (!paciente) return;

        const nombreCompleto = `${paciente.nomPa || ''} ${paciente.paternoPa || ''} ${paciente.maternoPa || ''}`.trim();
        const edad = paciente.fechaNac ? calcularEdad(paciente.fechaNac) : 'N/A';

        const modal = document.getElementById('modalDetalle');
        const contenido = `
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="material-icons text-blue-600 text-3xl">${paciente.sexo === 'M' ? 'male' : 'female'}</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">${nombreCompleto}</h3>
                        <p class="text-gray-500">${paciente.nroHCI || 'Sin HCI'}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Edad</p>
                        <p class="text-gray-900">${edad} años</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Sexo</p>
                        <p class="text-gray-900">${paciente.sexo === 'M' ? 'Masculino' : 'Femenino'}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Fecha de Nacimiento</p>
                        <p class="text-gray-900">${paciente.fechaNac ? new Date(paciente.fechaNac).toLocaleDateString('es-ES') : 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Tipo de Paciente</p>
                        <p class="text-gray-900">${paciente.tipoPac || 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Estado</p>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full ${paciente.estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${paciente.estado}
                        </span>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button onclick="cerrarModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                        Cerrar
                    </button>
                    <a href="/personal/pacientes/${id}/editar" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
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
        document.getElementById('filtroEstado').value = '';
        document.getElementById('filtroTipo').value = '';
        filtrarPacientes();
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
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <span class="material-icons text-blue-600 text-3xl">people</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Pacientes</p>
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
                    <p class="text-sm font-medium text-gray-500">Pacientes Activos</p>
                    <p class="text-2xl font-bold text-gray-900" id="stat-activos">0</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <span class="material-icons text-purple-600 text-3xl">medical_services</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Con SUS</p>
                    <p class="text-2xl font-bold text-gray-900" id="stat-sus">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Encabezado y Filtros -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center">
                    <span class="material-icons text-4xl text-blue-600 mr-3">people</span>
                    Lista de Pacientes
                </h1>
                <p class="text-gray-600" id="resultados-count">Cargando...</p>
            </div>
            <a href="{{ route('personal.pacientes.agregar') }}"
                class="mt-4 md:mt-0 flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                <span class="material-icons mr-2">add</span>
                Agregar Paciente
            </a>
        </div>

        <!-- Filtros -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <div class="relative">
                    <span class="material-icons absolute left-3 top-2.5 text-gray-400">search</span>
                    <input type="text" id="busqueda"
                        placeholder="Buscar por nombre o HCI..."
                        class="pl-10 w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select id="filtroEstado"
                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                    <option value="">Todos</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                <select id="filtroTipo"
                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                    <option value="">Todos</option>
                    <option value="SUS">SUS</option>
                    <option value="SINSUS">SIN SUS</option>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <button onclick="limpiarFiltros()"
                class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                <span class="material-icons text-sm mr-1">clear</span>
                Limpiar filtros
            </button>
        </div>
    </div>

    <!-- Loader -->
    <div id="loader" class="hidden flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Mensaje sin datos -->
    <div id="no-data" class="hidden bg-white rounded-lg shadow p-12">
        <div class="flex flex-col items-center justify-center">
            <span class="material-icons text-gray-300" style="font-size: 120px;">folder_open</span>
            <p class="text-gray-600 text-lg mt-4 font-medium">No hay pacientes registrados</p>
            <p class="text-gray-500 text-sm mt-2">Comienza agregando tu primer paciente</p>
            <a href="{{ route('personal.pacientes.agregar') }}"
                class="mt-6 flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <span class="material-icons mr-2">add</span>
                Agregar Paciente
            </a>
        </div>
    </div>

    <!-- Tabla de pacientes -->
    <div class="bg-white rounded-lg shadow overflow-hidden" id="tabla-container">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4">Paciente</th>
                        <th scope="col" class="px-6 py-4">Edad</th>
                        <th scope="col" class="px-6 py-4">Tipo</th>
                        <th scope="col" class="px-6 py-4">Estado</th>
                        <th scope="col" class="px-6 py-4">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-pacientes"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detalle -->
<div id="modalDetalle" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-2xl font-bold text-gray-900">Detalle del Paciente</h3>
            <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <div id="modalContenido"></div>
    </div>
</div>

@endsection