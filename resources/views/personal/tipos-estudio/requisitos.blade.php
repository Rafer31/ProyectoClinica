@extends('personal.layouts.personal')

@section('title', 'Gestión de Requisitos')

@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('personal.home')],
            ['label' => 'Servicios', 'url' => route('personal.servicios.servicios')],
            ['label' => 'Tipos de Estudio', 'url' => route('personal.tipos-estudio.index')],
            ['label' => 'Requisitos']
        ];
    @endphp

    <div class="space-y-6">
        <!-- Encabezado -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-wrap gap-3 items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="material-icons text-white text-2xl">assignment</span>
                        </div>
                        Gestión de Requisitos
                    </h1>
                    <p class="text-blue-600 font-medium ml-15">Administra los requisitos disponibles para los tipos de estudio</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('personal.tipos-estudio.index') }}"
                        class="flex items-center gap-2 px-5 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all font-semibold">
                        <span class="material-icons">arrow_back</span>
                        <span>Volver</span>
                    </a>
                    <button onclick="abrirModalCrear()"
                        class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all shadow-md hover:shadow-lg font-semibold transform hover:scale-105">
                        <span class="material-icons">add_circle</span>
                        <span>Nuevo Requisito</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <span class="material-icons text-3xl">assignment</span>
                    </div>
                    <span class="text-xs font-bold bg-white bg-opacity-25 px-3 py-1 rounded-full">TOTAL</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="total-requisitos">0</p>
                <p class="text-sm opacity-90 font-medium">Requisitos Registrados</p>
            </div>

            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <span class="material-icons text-3xl">check_circle</span>
                    </div>
                    <span class="text-xs font-bold bg-white bg-opacity-25 px-3 py-1 rounded-full">ACTIVOS</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="requisitos-activos">0</p>
                <p class="text-sm opacity-90 font-medium">En Uso</p>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <span class="material-icons text-3xl">category</span>
                    </div>
                    <span class="text-xs font-bold bg-white bg-opacity-25 px-3 py-1 rounded-full">TOTAL</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="tipos-estudio">0</p>
                <p class="text-sm opacity-90 font-medium">Tipos de Estudio</p>
            </div>
        </div>

        <!-- Buscador -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex gap-4">
                <div class="flex-1">
                    <input type="text" id="buscar-requisito" placeholder="Buscar requisito..."
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
                <button onclick="cargarRequisitos()"
                    class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <span class="material-icons">refresh</span>
                </button>
            </div>
        </div>

        <!-- Tabla de Requisitos -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-200">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-white">list_alt</span>
                    </div>
                    Requisitos Registrados
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold">Código</th>
                            <th scope="col" class="px-6 py-4 font-bold">Descripción</th>
                            <th scope="col" class="px-6 py-4 font-bold text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-requisitos">
                        <!-- Cargando -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Estado de carga -->
        <div id="loading-state" class="hidden flex justify-center items-center py-12">
            <div class="flex flex-col items-center gap-3">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-200 border-t-blue-600"></div>
                <span class="text-sm font-medium text-gray-600">Cargando requisitos...</span>
            </div>
        </div>

        <!-- Estado vacío -->
        <div id="empty-state" class="hidden bg-white rounded-xl shadow-sm border border-gray-100 p-12">
            <div class="flex flex-col items-center justify-center">
                <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <span class="material-icons text-gray-300" style="font-size: 80px;">assignment</span>
                </div>
                <p class="text-gray-800 text-xl font-bold mb-2">No hay requisitos registrados</p>
                <p class="text-gray-500 text-sm mb-4">Comienza creando tu primer requisito</p>
                <button onclick="abrirModalCrear()"
                    class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all shadow-md font-semibold">
                    <span class="material-icons">add_circle</span>
                    Crear primer requisito
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Crear Requisito -->
    <div id="modal-crear" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-0 border w-full max-w-md shadow-2xl rounded-xl bg-white">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-t-xl p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <span class="material-icons text-white">add_circle</span>
                        </div>
                        <h3 class="text-xl font-bold text-white">Nuevo Requisito</h3>
                    </div>
                    <button onclick="cerrarModal('modal-crear')"
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                        <span class="material-icons">close</span>
                    </button>
                </div>
            </div>

            <form id="form-crear" onsubmit="guardarRequisito(event)">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Descripción del Requisito <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="descripcion-crear" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                            placeholder="Ej: Vejiga llena, Ayuno de 8 horas, etc.">
                        <p class="mt-1 text-xs text-gray-500">Ingrese una descripción clara y concisa del requisito</p>
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-3">
                        <p class="text-xs text-blue-800 flex items-center gap-2">
                            <span class="material-icons text-xs">info</span>
                            Este requisito estará disponible para todos los tipos de estudio
                        </p>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex gap-3">
                    <button type="button" onclick="cerrarModal('modal-crear')"
                        class="flex-1 px-5 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-semibold">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all shadow-md font-semibold flex items-center justify-center gap-2">
                        <span class="material-icons text-sm">save</span>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar Requisito -->
    <div id="modal-editar" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-0 border w-full max-w-md shadow-2xl rounded-xl bg-white">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-t-xl p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <span class="material-icons text-white">edit</span>
                        </div>
                        <h3 class="text-xl font-bold text-white">Editar Requisito</h3>
                    </div>
                    <button onclick="cerrarModal('modal-editar')"
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                        <span class="material-icons">close</span>
                    </button>
                </div>
            </div>

            <form id="form-editar" onsubmit="actualizarRequisito(event)">
                <div class="p-6 space-y-4">
                    <input type="hidden" id="codigo-editar">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Descripción del Requisito <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="descripcion-editar" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            placeholder="Ej: Vejiga llena, Ayuno de 8 horas, etc.">
                    </div>

                    <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-3">
                        <p class="text-xs text-amber-800 flex items-center gap-2">
                            <span class="material-icons text-xs">warning</span>
                            Los cambios afectarán a todos los tipos de estudio que usen este requisito
                        </p>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex gap-3">
                    <button type="button" onclick="cerrarModal('modal-editar')"
                        class="flex-1 px-5 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-semibold">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-5 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all shadow-md font-semibold flex items-center justify-center gap-2">
                        <span class="material-icons text-sm">save</span>
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Eliminar -->
    <div id="modal-eliminar" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-0 border w-full max-w-md shadow-2xl rounded-xl bg-white">
            <div class="bg-gradient-to-r from-red-500 to-rose-600 rounded-t-xl p-5">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-3">
                        <span class="material-icons text-white text-2xl">delete</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Eliminar Requisito</h3>
                        <p class="text-sm text-red-100">Esta acción no se puede deshacer</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <p class="text-gray-800 font-medium" id="mensaje-eliminar">
                    ¿Está seguro de que desea eliminar este requisito?
                </p>
            </div>

            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex gap-3">
                <button onclick="cerrarModal('modal-eliminar')"
                    class="flex-1 px-5 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-semibold">
                    Cancelar
                </button>
                <button onclick="confirmarEliminar()"
                    class="flex-1 px-5 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-lg hover:from-red-600 hover:to-rose-700 transition-all shadow-md font-semibold">
                    Eliminar
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let requisitosData = [];
        let requisitoEliminar = null;

        document.addEventListener('DOMContentLoaded', function () {
            cargarRequisitos();
            cargarEstadisticas();

            document.getElementById('buscar-requisito').addEventListener('input', filtrarRequisitos);
        });

        async function cargarRequisitos() {
            const tabla = document.getElementById('tabla-requisitos');
            const loading = document.getElementById('loading-state');
            const empty = document.getElementById('empty-state');

            loading.classList.remove('hidden');
            empty.classList.add('hidden');

            try {
                const response = await fetch('/api/personal/requisitos');
                const data = await response.json();

                loading.classList.add('hidden');

                if (data.success && data.data.length > 0) {
                    requisitosData = data.data;
                    renderizarRequisitos(data.data);
                    document.getElementById('total-requisitos').textContent = data.data.length;
                } else {
                    tabla.innerHTML = '';
                    empty.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error:', error);
                loading.classList.add('hidden');
                tabla.innerHTML = `
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <span class="material-icons text-red-500 text-6xl">error</span>
                                <p class="text-red-600 font-semibold">Error al cargar los requisitos</p>
                            </div>
                        </td>
                    </tr>
                `;
            }
        }

        function renderizarRequisitos(requisitos) {
            const tabla = document.getElementById('tabla-requisitos');

            if (requisitos.length === 0) {
                tabla.innerHTML = `
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <span class="material-icons text-gray-400 text-6xl">search_off</span>
                                <p class="text-gray-600 font-medium">No se encontraron requisitos</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            tabla.innerHTML = requisitos.map(req => `
                <tr class="border-b hover:bg-blue-50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-bold text-blue-600">${req.codRequisito}</span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-900">${req.descripRequisito}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 justify-center">
                            <button onclick="abrirModalEditar(${req.codRequisito}, '${req.descripRequisito.replace(/'/g, "\\'")}')"
                                class="inline-flex items-center px-3 py-2 text-sm font-semibold text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all border border-blue-200 hover:shadow-md"
                                title="Editar requisito">
                                <span class="material-icons text-base">edit</span>
                            </button>
                            <button onclick="abrirModalEliminar(${req.codRequisito}, '${req.descripRequisito.replace(/'/g, "\\'")}')"
                                class="inline-flex items-center px-3 py-2 text-sm font-semibold text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-all border border-red-200 hover:shadow-md"
                                title="Eliminar requisito">
                                <span class="material-icons text-base">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function filtrarRequisitos() {
            const busqueda = document.getElementById('buscar-requisito').value.toLowerCase();
            const filtrados = requisitosData.filter(req =>
                req.descripRequisito.toLowerCase().includes(busqueda) ||
                req.codRequisito.toString().includes(busqueda)
            );
            renderizarRequisitos(filtrados);
        }

        async function cargarEstadisticas() {
            try {
                const [reqRes, tiposRes] = await Promise.all([
                    fetch('/api/personal/requisitos'),
                    fetch('/api/personal/tipos-estudio')
                ]);

                const reqData = await reqRes.json();
                const tiposData = await tiposRes.json();

                if (reqData.success) {
                    document.getElementById('requisitos-activos').textContent = reqData.data.length;
                }

                if (tiposData.success) {
                    document.getElementById('tipos-estudio').textContent = tiposData.data.length;
                }
            } catch (error) {
                console.error('Error al cargar estadísticas:', error);
            }
        }

        function abrirModalCrear() {
            document.getElementById('descripcion-crear').value = '';
            document.getElementById('modal-crear').classList.remove('hidden');
        }

        function abrirModalEditar(codigo, descripcion) {
            document.getElementById('codigo-editar').value = codigo;
            document.getElementById('descripcion-editar').value = descripcion;
            document.getElementById('modal-editar').classList.remove('hidden');
        }

        function abrirModalEliminar(codigo, descripcion) {
            requisitoEliminar = codigo;
            document.getElementById('mensaje-eliminar').textContent =
                `¿Está seguro de que desea eliminar "${descripcion}"? Esta acción no se puede deshacer.`;
            document.getElementById('modal-eliminar').classList.remove('hidden');
        }

        function cerrarModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        async function guardarRequisito(event) {
            event.preventDefault();

            const descripcion = document.getElementById('descripcion-crear').value.trim();

            try {
                const response = await fetch('/api/personal/requisitos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ descripRequisito: descripcion })
                });

                const data = await response.json();

                if (data.success) {
                    mostrarNotificacion('Requisito creado exitosamente', 'success');
                    cerrarModal('modal-crear');
                    cargarRequisitos();
                    cargarEstadisticas();
                } else {
                    mostrarNotificacion(data.message || 'Error al crear el requisito', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarNotificacion('Error al crear el requisito', 'error');
            }
        }

        async function actualizarRequisito(event) {
            event.preventDefault();

            const codigo = document.getElementById('codigo-editar').value;
            const descripcion = document.getElementById('descripcion-editar').value.trim();

            try {
                const response = await fetch(`/api/personal/requisitos/${codigo}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ descripRequisito: descripcion })
                });

                const data = await response.json();

                if (data.success) {
                    mostrarNotificacion('Requisito actualizado exitosamente', 'success');
                    cerrarModal('modal-editar');
                    cargarRequisitos();
                } else {
                    mostrarNotificacion(data.message || 'Error al actualizar el requisito', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarNotificacion('Error al actualizar el requisito', 'error');
            }
        }

        async function confirmarEliminar() {
            if (!requisitoEliminar) return;

            try {
                const response = await fetch(`/api/personal/requisitos/${requisitoEliminar}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    mostrarNotificacion('Requisito eliminado exitosamente', 'success');
                    cerrarModal('modal-eliminar');
                    requisitoEliminar = null;
                    cargarRequisitos();
                    cargarEstadisticas();
                } else {
                    mostrarNotificacion(data.message || 'Error al eliminar el requisito', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarNotificacion('Error al eliminar el requisito', 'error');
            }
        }

        function mostrarNotificacion(mensaje, tipo = 'success') {
            const colores = {
                success: 'from-emerald-500 to-teal-600',
                error: 'from-red-500 to-rose-600',
                info: 'from-blue-500 to-indigo-600'
            };

            const iconos = {
                success: 'check_circle',
                error: 'error',
                info: 'info'
            };

            const notificacion = document.createElement('div');
            notificacion.className = `fixed top-4 right-4 bg-gradient-to-r ${colores[tipo]} text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2 transform transition-all`;
            notificacion.innerHTML = `
                <span class="material-icons">${iconos[tipo]}</span>
                <span class="font-semibold">${mensaje}</span>
            `;

            document.body.appendChild(notificacion);

            setTimeout(() => {
                notificacion.style.opacity = '0';
                notificacion.style.transform = 'translateY(-20px)';
                setTimeout(() => notificacion.remove(), 300);
            }, 3000);
        }
    </script>
@endpush
