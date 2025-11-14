@extends('personal.layouts.personal')

@section('title', 'Tipos de Estudio')

@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('personal.home')],
            ['label' => 'Servicios', 'url' => route('personal.servicios.servicios')],
            ['label' => 'Tipos de Estudio']
        ];
    @endphp

    <div class="space-y-6">
        <!-- Encabezado -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        <span class="material-icons align-middle text-4xl text-purple-600">category</span>
                        Tipos de Estudio
                    </h1>
                    <p class="text-gray-600">Gestiona los tipos de estudios ecográficos y sus requisitos</p>
                </div>
                <a href="{{ route('personal.tipos-estudio.crear') }}"
                    class="flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <span class="material-icons me-2">add</span>
                    Nuevo Tipo de Estudio
                </a>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="material-icons text-4xl opacity-80">category</span>
                </div>
                <p class="text-3xl font-bold" id="total-tipos">0</p>
                <p class="text-sm opacity-90">Total de Tipos de Estudio</p>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="material-icons text-4xl opacity-80">check_circle</span>
                </div>
                <p class="text-3xl font-bold" id="total-requisitos">0</p>
                <p class="text-sm opacity-90">Requisitos Disponibles</p>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="material-icons text-4xl opacity-80">medical_services</span>
                </div>
                <p class="text-3xl font-bold" id="estudios-hoy">0</p>
                <p class="text-sm opacity-90">Estudios Realizados Hoy</p>
            </div>
        </div>

        <!-- Buscador -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex gap-4">
                <div class="flex-1">
                    <input type="text" id="buscar-tipo" placeholder="Buscar tipo de estudio..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                <button onclick="cargarTiposEstudio()"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <span class="material-icons">refresh</span>
                </button>
            </div>
        </div>

        <!-- Contenedor de Cards -->
        <div id="tipos-estudio-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Las cards se cargarán dinámicamente aquí -->
        </div>

        <!-- Estado de carga -->
        <div id="loading-state" class="text-center py-12 hidden">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
            <p class="mt-4 text-gray-600">Cargando tipos de estudio...</p>
        </div>

        <!-- Estado vacío -->
        <div id="empty-state" class="text-center py-12 hidden">
            <span class="material-icons text-gray-400" style="font-size: 72px;">category</span>
            <p class="mt-4 text-gray-600 text-lg">No hay tipos de estudio registrados</p>
            <a href="{{ route('personal.tipos-estudio.crear') }}"
                class="mt-4 inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                <span class="material-icons me-2">add</span>
                Crear primer tipo de estudio
            </a>
        </div>
    </div>

    <!-- Modal de Confirmación de Eliminación -->
    <div id="modal-eliminar" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <span class="material-icons text-red-600">delete</span>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4 text-center">Eliminar Tipo de Estudio</h3>
                <p class="text-sm text-gray-500 mt-2 text-center" id="mensaje-eliminar">
                    ¿Está seguro de que desea eliminar este tipo de estudio?
                </p>
                <div class="mt-6 flex gap-3">
                    <button onclick="cerrarModalEliminar()"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </button>
                    <button onclick="confirmarEliminar()"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalle -->
    <div id="modal-detalle" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4 border-b pb-4">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                    <span class="material-icons text-purple-600 mr-2">medical_services</span>
                    <span id="detalle-titulo">Detalle del Tipo de Estudio</span>
                </h3>
                <button onclick="cerrarModalDetalle()" class="text-gray-400 hover:text-gray-600">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <div id="detalle-contenido" class="space-y-6">
                <!-- El contenido se cargará dinámicamente -->
            </div>

            <div class="mt-6 flex gap-3 border-t pt-4">
                <button onclick="cerrarModalDetalle()"
                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let tipoEstudioEliminar = null;

        document.addEventListener('DOMContentLoaded', function () {
            cargarTiposEstudio();
            cargarEstadisticas();

            // Buscador en tiempo real
            document.getElementById('buscar-tipo').addEventListener('input', function (e) {
                const busqueda = e.target.value.toLowerCase();
                const cards = document.querySelectorAll('.tipo-estudio-card');

                cards.forEach(card => {
                    const titulo = card.querySelector('.titulo-tipo').textContent.toLowerCase();
                    card.style.display = titulo.includes(busqueda) ? 'block' : 'none';
                });
            });
        });

        async function cargarTiposEstudio() {
            const container = document.getElementById('tipos-estudio-container');
            const loadingState = document.getElementById('loading-state');
            const emptyState = document.getElementById('empty-state');

            container.innerHTML = '';
            loadingState.classList.remove('hidden');
            emptyState.classList.add('hidden');

            try {
                const response = await fetch('/api/personal/tipos-estudio');
                const data = await response.json();

                loadingState.classList.add('hidden');

                if (data.success && data.data.length > 0) {
                    data.data.forEach(tipo => {
                        container.innerHTML += crearCardTipoEstudio(tipo);
                    });
                    document.getElementById('total-tipos').textContent = data.data.length;
                } else {
                    emptyState.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error:', error);
                loadingState.classList.add('hidden');
                container.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <span class="material-icons text-red-500 text-6xl">error</span>
                    <p class="mt-4 text-red-600">Error al cargar los tipos de estudio</p>
                </div>
            `;
            }
        }

        async function cargarEstadisticas() {
            try {
                const response = await fetch('/api/personal/requisitos');
                const data = await response.json();

                if (data.success) {
                    document.getElementById('total-requisitos').textContent = data.data.length;
                }
            } catch (error) {
                console.error('Error al cargar requisitos:', error);
            }
        }

        function crearCardTipoEstudio(tipo) {
            const colores = [
                'from-blue-500 to-blue-600',
                'from-purple-500 to-purple-600',
                'from-green-500 to-green-600',
                'from-pink-500 to-pink-600',
                'from-indigo-500 to-indigo-600',
                'from-orange-500 to-orange-600'
            ];
            const color = colores[tipo.codTest % colores.length];

            const requisitosHTML = tipo.requisitos && tipo.requisitos.length > 0
                ? tipo.requisitos.map(req => `
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mr-1 mb-1">
                    <span class="material-icons text-xs mr-1">check_circle</span>
                    ${req.descripRequisito}
                </span>
            `).join('')
                : '<span class="text-sm text-gray-500 italic">Sin requisitos específicos</span>';

            // Obtener la observación principal (la primera con texto)
            const observacion = tipo.requisitos && tipo.requisitos.length > 0 && tipo.requisitos[0].pivot.observacion
                ? tipo.requisitos[0].pivot.observacion
                : 'Sin observaciones adicionales';

            return `
            <div class="tipo-estudio-card bg-white rounded-lg shadow hover:shadow-xl transition-all">
                <div class="bg-gradient-to-br ${color} p-4 rounded-t-lg">
                    <div class="flex items-center justify-between text-white">
                        <span class="material-icons text-3xl opacity-90">medical_services</span>
                        <span class="bg-white bg-opacity-20 px-2 py-1 rounded text-xs font-medium">
                            ID: ${tipo.codTest}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="titulo-tipo text-xl font-bold text-gray-900 mb-3">${tipo.descripcion}</h3>

                    <div class="mb-4">
                        <p class="text-xs font-semibold text-gray-600 mb-2">OBSERVACIONES:</p>
                        <p class="text-sm text-gray-700 leading-relaxed line-clamp-3">${observacion}</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-xs font-semibold text-gray-600 mb-2">REQUISITOS:</p>
                        <div class="flex flex-wrap gap-1">
                            ${requisitosHTML}
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4 pt-4 border-t">
                        <span class="flex items-center">
                            <span class="material-icons text-sm mr-1">assignment</span>
                            ${tipo.requisitos ? tipo.requisitos.length : 0} requisitos
                        </span>
                    </div>

                    <div class="flex gap-2">
                        <button onclick="verDetalle(${tipo.codTest})"
                            class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium flex items-center justify-center">
                            <span class="material-icons text-sm mr-1">visibility</span>
                            Ver Detalle
                        </button>
                        <a href="/personal/tipos-estudio/editar/${tipo.codTest}"
                            class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center">
                            <span class="material-icons text-sm">edit</span>
                        </a>
                        <button onclick="abrirModalEliminar(${tipo.codTest}, '${tipo.descripcion}')"
                            class="px-3 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 flex items-center">
                            <span class="material-icons text-sm">delete</span>
                        </button>
                    </div>
                </div>
            </div>
        `;
        }

        async function verDetalle(codTest) {
            const modal = document.getElementById('modal-detalle');
            const contenido = document.getElementById('detalle-contenido');

            try {
                // Mostrar modal con loading
                modal.classList.remove('hidden');
                contenido.innerHTML = `
                <div class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
                    <p class="mt-4 text-gray-600">Cargando información...</p>
                </div>
            `;

                // Cargar datos
                const response = await fetch(`/api/personal/tipos-estudio/${codTest}`);
                const data = await response.json();

                if (data.success) {
                    const tipo = data.data;
                    document.getElementById('detalle-titulo').textContent = tipo.descripcion;

                    // Obtener la observación principal
                    let observacionPrincipal = 'Sin observaciones adicionales';
                    if (tipo.requisitos && tipo.requisitos.length > 0 && tipo.requisitos[0].pivot.observacion) {
                        observacionPrincipal = tipo.requisitos[0].pivot.observacion;
                    }

                    const requisitosHTML = tipo.requisitos && tipo.requisitos.length > 0
                        ? tipo.requisitos.map(req => `
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 hover:border-purple-400 transition-colors">
                            <div class="flex items-start gap-3">
                                <span class="material-icons text-purple-600 mt-1 text-xl">check_circle</span>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900 text-base">${req.descripRequisito}</p>
                                </div>
                            </div>
                        </div>
                    `).join('')
                        : '<p class="text-gray-500 italic text-center py-4">Sin requisitos específicos</p>';

                    contenido.innerHTML = `
                    <div class="space-y-6">
                        <!-- Información General -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-5 rounded-lg border border-blue-200">
                            <h4 class="font-bold text-gray-900 mb-4 flex items-center text-lg">
                                <span class="material-icons text-blue-600 mr-2">info</span>
                                Información General
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white p-3 rounded-lg">
                                    <p class="text-sm text-gray-600 mb-1">Código:</p>
                                    <p class="font-bold text-gray-900 text-lg">${tipo.codTest}</p>
                                </div>
                                <div class="bg-white p-3 rounded-lg">
                                    <p class="text-sm text-gray-600 mb-1">Total de Requisitos:</p>
                                    <p class="font-bold text-gray-900 text-lg">${tipo.requisitos ? tipo.requisitos.length : 0}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Observación Principal -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-5 rounded-lg border border-green-200">
                            <h4 class="font-bold text-gray-900 mb-4 flex items-center text-lg">
                                <span class="material-icons text-green-600 mr-2">description</span>
                                Instrucciones para el Paciente
                            </h4>
                            <div class="bg-white p-4 rounded-lg border-l-4 border-green-500">
                                <p class="text-gray-800 text-base leading-relaxed whitespace-pre-line">${observacionPrincipal}</p>
                            </div>
                        </div>

                        <!-- Requisitos -->
                        <div>
                            <h4 class="font-bold text-gray-900 mb-4 flex items-center text-lg">
                                <span class="material-icons text-purple-600 mr-2">assignment</span>
                                Requisitos Específicos (${tipo.requisitos ? tipo.requisitos.length : 0})
                            </h4>
                            <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                                ${requisitosHTML}
                            </div>
                        </div>
                    </div>
                `;

                    // Guardar el codTest actual para el PDF
                    modal.setAttribute('data-codtest', codTest);
                } else {
                    contenido.innerHTML = `
                    <div class="text-center py-8">
                        <span class="material-icons text-red-500 text-6xl">error</span>
                        <p class="mt-4 text-red-600 text-lg">Error al cargar la información</p>
                    </div>
                `;
                }
            } catch (error) {
                console.error('Error:', error);
                contenido.innerHTML = `
                <div class="text-center py-8">
                    <span class="material-icons text-red-500 text-6xl">error</span>
                    <p class="mt-4 text-red-600 text-lg">Error al cargar la información</p>
                </div>
            `;
            }
        }

        function cerrarModalDetalle() {
            document.getElementById('modal-detalle').classList.add('hidden');
        }

        function abrirModalEliminar(codTest, descripcion) {
            tipoEstudioEliminar = codTest;
            document.getElementById('mensaje-eliminar').textContent =
                `¿Está seguro de que desea eliminar "${descripcion}"? Esta acción no se puede deshacer.`;
            document.getElementById('modal-eliminar').classList.remove('hidden');
        }

        function cerrarModalEliminar() {
            tipoEstudioEliminar = null;
            document.getElementById('modal-eliminar').classList.add('hidden');
        }

        async function confirmarEliminar() {
            if (!tipoEstudioEliminar) return;

            try {
                const response = await fetch(`/api/personal/tipos-estudio/${tipoEstudioEliminar}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    cerrarModalEliminar();
                    cargarTiposEstudio();
                    mostrarNotificacion('Tipo de estudio eliminado exitosamente', 'success');
                } else {
                    mostrarNotificacion(data.message || 'Error al eliminar', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarNotificacion('Error al eliminar el tipo de estudio', 'error');
            }
        }

        function mostrarNotificacion(mensaje, tipo = 'success') {
            const colores = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                info: 'bg-blue-500'
            };

            const notificacion = document.createElement('div');
            notificacion.className = `fixed top-4 right-4 ${colores[tipo]} text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2`;
            notificacion.innerHTML = `
            <span class="material-icons">${tipo === 'success' ? 'check_circle' : 'error'}</span>
            <span>${mensaje}</span>
        `;

            document.body.appendChild(notificacion);

            setTimeout(() => {
                notificacion.remove();
            }, 3000);
        }
    </script>

    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endpush
