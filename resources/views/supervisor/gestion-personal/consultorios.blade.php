@extends('supervisor.layouts.supervisor')
@section('title', 'Gestión de Consultorios')
@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('supervisor.home')],
            ['label' => 'Gestión Personal', 'url' => route('supervisor.gestion-personal.gestion-personal')],
            ['label' => 'Consultorios']
        ];
    @endphp

    @push('scripts')
        <script>
            let consultorios = [];

            document.addEventListener("DOMContentLoaded", function () {
                cargarConsultorios();
            });

            async function cargarConsultorios() {
                mostrarLoader(true);

                try {
                    const response = await fetch('/api/consultorios');
                    const data = await response.json();

                    if (data.success) {
                        consultorios = data.data;
                        renderConsultorios();
                    } else {
                        mostrarAlerta('error', data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('error', 'Error al cargar consultorios');
                } finally {
                    mostrarLoader(false);
                }
            }

            function renderConsultorios() {
                const container = document.getElementById('consultoriosContainer');
                const countElement = document.getElementById('totalConsultorios');
                
                countElement.textContent = consultorios.length;
                container.innerHTML = '';

                if (consultorios.length === 0) {
                    container.innerHTML = `
                        <div class="col-span-full flex flex-col items-center justify-center py-12">
                            <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mb-4">
                                <span class="material-icons text-blue-400 text-5xl">meeting_room</span>
                            </div>
                            <p class="text-gray-600 text-lg font-medium">No hay consultorios registrados</p>
                            <p class="text-gray-500 text-sm mt-2">Comienza agregando un consultorio</p>
                        </div>
                    `;
                    return;
                }

                consultorios.forEach(cons => {
                    const card = `
                        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-gray-100 hover:border-blue-300 hover:shadow-xl transition-all duration-200">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <span class="material-icons text-white text-2xl">meeting_room</span>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">Consultorio ${cons.numCons}</h3>
                                        <p class="text-xs text-gray-500">ID: ${cons.codCons}</p>
                                    </div>
                                </div>
                                <button onclick="eliminarConsultorio(${cons.codCons})" 
                                    class="text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg p-2 transition-all"
                                    title="Eliminar">
                                    <span class="material-icons">delete</span>
                                </button>
                            </div>
                            <div class="pt-4 border-t border-gray-100">
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <span class="flex items-center gap-1">
                                        <span class="material-icons text-xs">info</span>
                                        Código: ${cons.codCons}
                                    </span>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                        Disponible
                                    </span>
                                </div>
                            </div>
                        </div>
                    `;
                    container.innerHTML += card;
                });
            }

            function abrirModalAgregar() {
                document.getElementById('modalAgregar').classList.remove('hidden');
            }

            function cerrarModalAgregar() {
                document.getElementById('modalAgregar').classList.add('hidden');
                document.getElementById('formConsultorio').reset();
            }

            async function guardarConsultorio() {
                const numCons = document.getElementById('numCons').value;

                if (!numCons) {
                    mostrarAlerta('error', 'Por favor ingrese el número del consultorio');
                    return;
                }

                mostrarLoader(true);
                cerrarModalAgregar();

                try {
                    const response = await fetch('/api/consultorios', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ numCons: numCons })
                    });

                    const data = await response.json();

                    if (data.success) {
                        mostrarAlerta('success', 'Consultorio agregado exitosamente');
                        cargarConsultorios();
                    } else {
                        mostrarAlerta('error', data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('error', 'Error al guardar el consultorio');
                } finally {
                    mostrarLoader(false);
                }
            }

            async function eliminarConsultorio(id) {
                const consultorio = consultorios.find(c => c.codCons === id);
                if (!consultorio) return;

                const modal = document.getElementById('modalEliminar');
                document.getElementById('numConsEliminar').textContent = consultorio.numCons;
                modal.classList.remove('hidden');
                modal.dataset.consultorioId = id;
            }

            function cerrarModalEliminar() {
                const modal = document.getElementById('modalEliminar');
                modal.classList.add('hidden');
                delete modal.dataset.consultorioId;
            }

            async function confirmarEliminar() {
                const modal = document.getElementById('modalEliminar');
                const id = modal.dataset.consultorioId;

                if (!id) return;

                cerrarModalEliminar();
                mostrarLoader(true);

                try {
                    const response = await fetch(`/api/consultorios/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        mostrarAlerta('success', 'Consultorio eliminado exitosamente');
                        cargarConsultorios();
                    } else {
                        mostrarAlerta('error', data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('error', 'Error al eliminar el consultorio');
                } finally {
                    mostrarLoader(false);
                }
            }

            function mostrarAlerta(tipo, mensaje) {
                const alerta = document.getElementById('alerta');
                const iconos = {
                    success: 'check_circle',
                    error: 'error'
                };
                const colores = {
                    success: 'bg-green-100 border-green-400 text-green-800',
                    error: 'bg-red-100 border-red-400 text-red-800'
                };

                alerta.className = `p-6 rounded-xl border-2 flex items-center shadow-lg ${colores[tipo]} mb-6`;
                alerta.innerHTML = `
                    <div class="flex-shrink-0 w-12 h-12 ${tipo === 'success' ? 'bg-green-500' : 'bg-red-500'} rounded-full flex items-center justify-center mr-4">
                        <span class="material-icons text-white text-2xl">${iconos[tipo]}</span>
                    </div>
                    <p class="font-bold text-lg">${mensaje}</p>
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
        </script>
    @endpush

    <div class="space-y-6">
        <!-- Alerta -->
        <div id="alerta" class="hidden"></div>

        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-2xl p-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2 flex items-center gap-3">
                        <span class="material-icons text-5xl">meeting_room</span>
                        Gestión de Consultorios
                    </h1>
                    <p class="text-blue-100 text-lg">Administra los consultorios disponibles en la clínica</p>
                </div>
                <div class="hidden lg:flex gap-3">
                    <a href="{{ route('supervisor.gestion-personal.gestion-personal') }}"
                        class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm text-white rounded-xl hover:bg-opacity-30 transition-all shadow-lg hover:shadow-xl font-medium">
                        <span class="material-icons mr-2">arrow_back</span>
                        Volver
                    </a>
                    <button onclick="abrirModalAgregar()"
                        class="inline-flex items-center px-6 py-3 bg-white text-blue-600 rounded-xl hover:shadow-xl transition-all font-medium">
                        <span class="material-icons mr-2">add</span>
                        Agregar Consultorio
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadística -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-4 shadow-lg">
                    <span class="material-icons text-white text-3xl">meeting_room</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 uppercase">Total Consultorios</p>
                    <p class="text-3xl font-bold text-gray-900" id="totalConsultorios">0</p>
                </div>
            </div>
        </div>

        <!-- Loader -->
        <div id="loader" class="hidden flex justify-center items-center py-12">
            <div class="relative">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-600"></div>
                <div class="absolute top-0 left-0 h-16 w-16 rounded-full border-4 border-blue-200"></div>
            </div>
        </div>

        <!-- Grid de Consultorios -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="consultoriosContainer">
            <!-- Los consultorios se cargarán dinámicamente aquí -->
        </div>
    </div>

    <!-- Modal Agregar Consultorio -->
    <div id="modalAgregar" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full z-50 flex items-center justify-center backdrop-blur-sm">
        <div class="relative mx-auto p-6 border-0 w-full max-w-md shadow-2xl rounded-2xl bg-white">
            <div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-gray-100">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="material-icons text-blue-600">add_circle</span>
                    Nuevo Consultorio
                </h3>
                <button onclick="cerrarModalAgregar()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-2 transition">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <form id="formConsultorio" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">
                        Número de Consultorio <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-icons absolute left-3 top-3.5 text-gray-400">numbers</span>
                        <input type="number" id="numCons" required min="1"
                            class="pl-11 w-full bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3 transition"
                            placeholder="Ej: 101">
                    </div>
                    <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                        <span class="material-icons text-xs">info</span>
                        Ingrese el número identificador del consultorio
                    </p>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="cerrarModalAgregar()"
                        class="flex-1 inline-flex justify-center items-center px-4 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:shadow-md transition-all duration-200">
                        <span class="material-icons text-base mr-1">close</span>
                        Cancelar
                    </button>
                    <button type="button" onclick="guardarConsultorio()"
                        class="flex-1 inline-flex justify-center items-center px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <span class="material-icons text-base mr-1">save</span>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Eliminar -->
    <div id="modalEliminar" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full z-50 flex items-center justify-center backdrop-blur-sm">
        <div class="relative mx-auto p-6 border-0 w-full max-w-md shadow-2xl rounded-2xl bg-white">
            <div class="flex justify-center mb-4">
                <div class="rounded-full bg-gradient-to-br from-red-100 to-pink-100 p-4 shadow-lg">
                    <span class="material-icons text-red-600 text-5xl">warning</span>
                </div>
            </div>

            <h3 class="text-2xl font-bold text-gray-900 text-center mb-2">¿Eliminar Consultorio?</h3>

            <div class="text-center mb-6">
                <p class="text-gray-600 mb-3">Estás a punto de eliminar el:</p>
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 mb-3 border border-gray-200">
                    <p class="font-bold text-gray-900 text-lg">Consultorio <span id="numConsEliminar"></span></p>
                </div>
                <p class="text-sm text-red-600 font-medium">Esta acción no se puede deshacer</p>
            </div>

            <div class="flex gap-3">
                <button onclick="cerrarModalEliminar()"
                    class="flex-1 inline-flex justify-center items-center px-4 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:shadow-md transition-all duration-200">
                    <span class="material-icons text-base mr-1">close</span>
                    Cancelar
                </button>
                <button onclick="confirmarEliminar()"
                    class="flex-1 inline-flex justify-center items-center px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-red-500 to-pink-500 rounded-xl hover:from-red-600 hover:to-pink-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <span class="material-icons text-base mr-1">delete</span>
                    Eliminar
                </button>
            </div>
        </div>
    </div>

@endsection