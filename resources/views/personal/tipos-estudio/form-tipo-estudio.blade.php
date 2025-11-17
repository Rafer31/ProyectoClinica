@extends('personal.layouts.personal')

@section('title', isset($tipoEstudio) ? 'Editar Tipo de Estudio' : 'Nuevo Tipo de Estudio')

@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('personal.home')],
            ['label' => 'Servicios', 'url' => route('personal.servicios.servicios')],
            ['label' => 'Tipos de Estudio', 'url' => route('personal.tipos-estudio.index')],
            ['label' => isset($tipoEstudio) ? 'Editar' : 'Nuevo']
        ];
        $esEdicion = isset($tipoEstudio);
    @endphp

    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Encabezado -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                    <span class="material-icons text-white text-2xl">{{ $esEdicion ? 'edit' : 'add_circle' }}</span>
                </div>
                {{ $esEdicion ? 'Editar Tipo de Estudio' : 'Nuevo Tipo de Estudio' }}
            </h1>
            <p class="text-emerald-600 font-medium ml-15">
                {{ $esEdicion ? 'Modifica los datos del tipo de estudio' : 'Completa los pasos para crear un nuevo tipo de estudio' }}
            </p>
        </div>

        <!-- Indicador de pasos -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div id="paso-1-indicator"
                            class="paso-indicator active flex items-center justify-center w-12 h-12 rounded-full bg-emerald-600 text-white font-bold shadow-lg">
                            1
                        </div>
                        <div class="flex-1 h-2 bg-gray-200 mx-2 rounded-full overflow-hidden">
                            <div id="progreso-1" class="h-full bg-emerald-600 transition-all duration-300" style="width: 0%">
                            </div>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-gray-700 mt-2">Información Básica</p>
                </div>

                <div class="flex-1">
                    <div class="flex items-center">
                        <div id="paso-2-indicator"
                            class="paso-indicator flex items-center justify-center w-12 h-12 rounded-full bg-gray-300 text-gray-600 font-bold">
                            2
                        </div>
                        <div class="flex-1 h-2 bg-gray-200 mx-2 rounded-full overflow-hidden">
                            <div id="progreso-2" class="h-full bg-emerald-600 transition-all duration-300" style="width: 0%">
                            </div>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-gray-700 mt-2">Observaciones</p>
                </div>

                <div class="flex-1">
                    <div class="flex items-center">
                        <div id="paso-3-indicator"
                            class="paso-indicator flex items-center justify-center w-12 h-12 rounded-full bg-gray-300 text-gray-600 font-bold">
                            3
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-gray-700 mt-2">Requisitos</p>
                </div>
            </div>
        </div>

        <!-- Formulario Multi-paso -->
        <form id="form-tipo-estudio" class="space-y-6">
            @csrf

            <!-- Paso 1: Información Básica -->
            <div id="paso-1" class="paso bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center mr-3">
                        <span class="material-icons text-white">info</span>
                    </div>
                    Paso 1: Información Básica
                </h2>

                <div class="space-y-4">
                    <div>
                        <label for="descripcion" class="block text-sm font-bold text-gray-700 mb-2">
                            Título del Tipo de Estudio <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="descripcion" name="descripcion" required
                            value="{{ $esEdicion ? $tipoEstudio->descripcion : '' }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                            placeholder="Ej: Estudio Obstétrico 1, Estudio Morfológico, etc.">
                        <p class="mt-1 text-sm text-gray-500">Este será el nombre del tipo de estudio</p>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="siguientePaso(2)"
                            class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all shadow-md font-semibold flex items-center">
                            Siguiente
                            <span class="material-icons ml-2">arrow_forward</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Paso 2: Observaciones -->
            <div id="paso-2" class="paso hidden bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center mr-3">
                        <span class="material-icons text-white">description</span>
                    </div>
                    Paso 2: Observaciones e Instrucciones
                </h2>

                <div class="space-y-4">
                    <div>
                        <label for="observacion-principal" class="block text-sm font-bold text-gray-700 mb-2">
                            Instrucciones para el Paciente <span class="text-red-500">*</span>
                        </label>
                        <textarea id="observacion-principal" name="observacion-principal" rows="6" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                            placeholder="Escriba las instrucciones completas para el paciente. Ej: Estimado paciente debe acudir para el estudio...">{{ $esEdicion && $tipoEstudio->requisitos->isNotEmpty() && $tipoEstudio->requisitos[0]->pivot->observacion ? $tipoEstudio->requisitos[0]->pivot->observacion : '' }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Escriba todas las indicaciones que debe seguir el paciente</p>
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
                        <p class="text-sm text-blue-800 flex items-center">
                            <span class="material-icons text-blue-600 mr-2">info</span>
                            En el siguiente paso podrá agregar los requisitos específicos (vejiga llena, ayuno, etc.)
                        </p>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" onclick="anteriorPaso(1)"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all font-semibold flex items-center">
                            <span class="material-icons mr-2">arrow_back</span>
                            Anterior
                        </button>
                        <button type="button" onclick="siguientePaso(3)"
                            class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all shadow-md font-semibold flex items-center">
                            Siguiente
                            <span class="material-icons ml-2">arrow_forward</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Paso 3: Requisitos -->
            <div id="paso-3" class="paso hidden bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center mr-3">
                        <span class="material-icons text-white">fact_check</span>
                    </div>
                    Paso 3: Requisitos del Estudio
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Seleccionar Requisitos
                        </label>
                        <div class="flex gap-2">
                            <select id="select-requisito"
                                class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                                <option value="">-- Seleccione un requisito --</option>
                            </select>
                            <button type="button" onclick="agregarRequisitoSeleccionado()"
                                class="px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all shadow-md flex items-center"
                                title="Agregar requisito seleccionado">
                                <span class="material-icons">add</span>
                            </button>
                            <button type="button" onclick="abrirModalRequisito()"
                                class="px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all shadow-md flex items-center gap-1"
                                title="Crear nuevo requisito">
                                <span class="material-icons text-sm">add_circle</span>
                                <span class="text-sm font-semibold">Nuevo</span>
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Seleccione requisitos existentes o cree uno nuevo con el botón "Nuevo"
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Requisitos Agregados
                        </label>
                        <div id="requisitos-agregados"
                            class="space-y-2 min-h-[100px] border-2 border-dashed border-gray-300 rounded-lg p-4">
                            <p class="text-sm text-gray-500 text-center" id="mensaje-sin-requisitos">
                                No hay requisitos agregados. Seleccione requisitos de la lista superior.
                            </p>
                        </div>
                    </div>

                    <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
                        <h3 class="text-sm font-bold text-green-800 mb-2 flex items-center">
                            <span class="material-icons text-green-600 mr-2">visibility</span>
                            Vista Previa
                        </h3>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-700"><strong>Título:</strong> <span id="preview-titulo">-</span></p>
                            <p class="text-sm text-gray-700"><strong>Observaciones:</strong> <span
                                    id="preview-observacion">-</span></p>
                            <p class="text-sm text-gray-700"><strong>Requisitos:</strong> <span
                                    id="preview-requisitos">-</span></p>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" onclick="anteriorPaso(2)"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all font-semibold flex items-center">
                            <span class="material-icons mr-2">arrow_back</span>
                            Anterior
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all shadow-md font-semibold flex items-center">
                            <span class="material-icons mr-2">save</span>
                            {{ $esEdicion ? 'Actualizar' : 'Guardar' }} Tipo de Estudio
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Modal Nuevo Requisito -->
        <div id="modal-nuevo-requisito"
                class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
                <div class="relative mx-auto p-0 border w-full max-w-md shadow-2xl rounded-xl bg-white">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-t-xl p-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <span class="material-icons text-white">add_circle</span>
                                </div>
                                <h3 class="text-xl font-bold text-white">Crear Nuevo Requisito</h3>
                            </div>
                            <button onclick="cerrarModalRequisito()"
                                class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                                <span class="material-icons">close</span>
                            </button>
                        </div>
                    </div>

                    <form onsubmit="guardarNuevoRequisito(event); return false;">
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Descripción del Requisito <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="descripcion-requisito" required
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                    placeholder="Ej: Vejiga llena, Ayuno de 8 horas, etc.">
                                <p class="mt-1 text-xs text-gray-500">Ingrese una descripción clara del requisito</p>
                            </div>

                            <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-3">
                                <p class="text-xs text-blue-800 flex items-center gap-2">
                                    <span class="material-icons text-xs">info</span>
                                    El requisito se agregará a la lista y podrá usarlo inmediatamente
                                </p>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex gap-3">
                            <button type="button" onclick="cerrarModalRequisito()"
                                class="flex-1 px-4 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-semibold">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all shadow-md font-semibold flex items-center justify-center gap-2">
                                <span class="material-icons text-sm">save</span>
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
    </div>

@endsection

@push('scripts')
    <script>
        let requisitosDisponibles = [];
        let requisitosAgregados = [];
        const esEdicion = {{ $esEdicion ? 'true' : 'false' }};
        const tipoEstudioId = {{ $esEdicion ? $tipoEstudio->codTest : 'null' }};

        document.addEventListener('DOMContentLoaded', async function () {
            await cargarRequisitos();

            if (esEdicion) {
                cargarDatosEdicion();
            }

            document.getElementById('descripcion').addEventListener('input', actualizarVistaPrevia);
            document.getElementById('observacion-principal').addEventListener('input', actualizarVistaPrevia);
        });

        async function cargarRequisitos() {
            try {
                const response = await fetch('/api/personal/requisitos');
                const data = await response.json();

                if (data.success) {
                    requisitosDisponibles = data.data;
                    const select = document.getElementById('select-requisito');

                    data.data.forEach(req => {
                        const option = document.createElement('option');
                        option.value = req.codRequisito;
                        option.textContent = req.descripRequisito;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error al cargar requisitos:', error);
            }
        }

        async function cargarDatosEdicion() {
            try {
                const response = await fetch(`/api/personal/tipos-estudio/${tipoEstudioId}`);
                const data = await response.json();

                if (data.success && data.data.requisitos) {
                    requisitosAgregados = data.data.requisitos.map(req => ({
                        codRequisito: req.codRequisito,
                        descripRequisito: req.descripRequisito
                    }));

                    renderizarRequisitos();
                    actualizarVistaPrevia();
                }
            } catch (error) {
                console.error('Error al cargar datos:', error);
            }
        }

        function siguientePaso(paso) {
            const pasoActual = paso - 1;
            if (pasoActual === 1) {
                const descripcion = document.getElementById('descripcion').value.trim();
                if (!descripcion) {
                    mostrarNotificacion('Por favor ingrese el título del tipo de estudio', 'error');
                    return;
                }
            } else if (pasoActual === 2) {
                const observacion = document.getElementById('observacion-principal').value.trim();
                if (!observacion) {
                    mostrarNotificacion('Por favor ingrese las observaciones', 'error');
                    return;
                }
            }

            document.getElementById(`paso-${pasoActual}`).classList.add('hidden');
            document.getElementById(`paso-${paso}`).classList.remove('hidden');
            actualizarIndicadores(paso);
            actualizarVistaPrevia();
        }

        function anteriorPaso(paso) {
            const pasoActual = paso + 1;
            document.getElementById(`paso-${pasoActual}`).classList.add('hidden');
            document.getElementById(`paso-${paso}`).classList.remove('hidden');
            actualizarIndicadores(paso);
        }

        function actualizarIndicadores(pasoActivo) {
            for (let i = 1; i <= 3; i++) {
                const indicator = document.getElementById(`paso-${i}-indicator`);
                if (i < pasoActivo) {
                    indicator.className = 'paso-indicator flex items-center justify-center w-12 h-12 rounded-full bg-green-500 text-white font-bold shadow-lg';
                    indicator.innerHTML = '<span class="material-icons">check</span>';
                } else if (i === pasoActivo) {
                    indicator.className = 'paso-indicator flex items-center justify-center w-12 h-12 rounded-full bg-emerald-600 text-white font-bold shadow-lg';
                    indicator.textContent = i;
                } else {
                    indicator.className = 'paso-indicator flex items-center justify-center w-12 h-12 rounded-full bg-gray-300 text-gray-600 font-bold';
                    indicator.textContent = i;
                }
            }

            document.getElementById('progreso-1').style.width = pasoActivo >= 2 ? '100%' : '0%';
            document.getElementById('progreso-2').style.width = pasoActivo >= 3 ? '100%' : '0%';
        }

        function agregarRequisitoSeleccionado() {
            const select = document.getElementById('select-requisito');
            const codRequisito = parseInt(select.value);

            if (!codRequisito) {
                mostrarNotificacion('Seleccione un requisito', 'error');
                return;
            }

            if (requisitosAgregados.some(r => r.codRequisito === codRequisito)) {
                mostrarNotificacion('Este requisito ya fue agregado', 'error');
                return;
            }

            const requisito = requisitosDisponibles.find(r => r.codRequisito === codRequisito);
            requisitosAgregados.push({
                codRequisito: requisito.codRequisito,
                descripRequisito: requisito.descripRequisito
            });

            renderizarRequisitos();
            actualizarVistaPrevia();
            select.value = '';
        }

        function eliminarRequisito(codRequisito) {
            console.log('Eliminando requisito:', codRequisito);
            requisitosAgregados = requisitosAgregados.filter(r => r.codRequisito !== codRequisito);
            console.log('Requisitos después de eliminar:', requisitosAgregados);
            renderizarRequisitos();
            actualizarVistaPrevia();
        }

        function renderizarRequisitos() {
            const container = document.getElementById('requisitos-agregados');
            const mensaje = document.getElementById('mensaje-sin-requisitos');

            if (requisitosAgregados.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-500 text-center" id="mensaje-sin-requisitos">No hay requisitos agregados. Seleccione requisitos de la lista superior.</p>';
                return;
            }

            container.innerHTML = requisitosAgregados.map(req => `
                <div class="flex items-center justify-between p-3 bg-emerald-50 border-2 border-emerald-200 rounded-lg hover:border-emerald-400 transition-all">
                    <div class="flex items-center">
                        <span class="material-icons text-emerald-600 mr-2">check_circle</span>
                        <span class="text-sm font-semibold text-gray-800">${req.descripRequisito}</span>
                    </div>
                    <button type="button" onclick="eliminarRequisito(${req.codRequisito})"
                        class="text-red-600 hover:text-red-800 hover:bg-red-100 p-1 rounded transition-all">
                        <span class="material-icons">close</span>
                    </button>
                </div>
            `).join('');
        }

        function actualizarVistaPrevia() {
            const titulo = document.getElementById('descripcion').value || '-';
            const observacion = document.getElementById('observacion-principal').value || '-';
            const requisitos = requisitosAgregados.length > 0
                ? requisitosAgregados.map(r => r.descripRequisito).join(', ')
                : 'Sin requisitos';

            document.getElementById('preview-titulo').textContent = titulo;
            document.getElementById('preview-observacion').textContent = observacion.substring(0, 150) + (observacion.length > 150 ? '...' : '');
            document.getElementById('preview-requisitos').textContent = requisitos;
        }

        document.getElementById('form-tipo-estudio').addEventListener('submit', async function (e) {
            e.preventDefault();

            const descripcion = document.getElementById('descripcion').value.trim();
            const observacionPrincipal = document.getElementById('observacion-principal').value.trim();

            if (!descripcion || !observacionPrincipal) {
                mostrarNotificacion('Complete todos los campos requeridos', 'error');
                return;
            }

            if (requisitosAgregados.length === 0) {
                mostrarNotificacion('Debe agregar al menos un requisito', 'error');
                return;
            }

            try {
                if (esEdicion) {
                    const responseUpdate = await fetch(`/api/personal/tipos-estudio/${tipoEstudioId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ descripcion: descripcion })
                    });

                    if (!responseUpdate.ok) {
                        mostrarNotificacion('Error al actualizar el tipo de estudio', 'error');
                        return;
                    }

                    const datosRequisitos = {
                        requisitos: requisitosAgregados.map((req, index) => ({
                            codRequisito: req.codRequisito,
                            observacion: index === 0 ? observacionPrincipal : null
                        }))
                    };

                    const responseRequisitos = await fetch(`/api/personal/tipos-estudio/${tipoEstudioId}/requisitos/asignar`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(datosRequisitos)
                    });

                    const dataRequisitos = await responseRequisitos.json();

                    if (dataRequisitos.success) {
                        mostrarNotificacion('Tipo de estudio actualizado exitosamente', 'success');
                        setTimeout(() => {
                            window.location.href = '/personal/tipos-estudio';
                        }, 1500);
                    }
                } else {
                    const datos = {
                        descripcion: descripcion,
                        requisitos: requisitosAgregados.map((req, index) => ({
                            codRequisito: req.codRequisito,
                            observacion: index === 0 ? observacionPrincipal : null
                        }))
                    };

                    const response = await fetch('/api/personal/tipos-estudio/con-requisitos', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(datos)
                    });

                    const data = await response.json();

                    if (data.success) {
                        mostrarNotificacion('Tipo de estudio creado exitosamente', 'success');
                        setTimeout(() => {
                            window.location.href = '/personal/tipos-estudio';
                        }, 1500);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarNotificacion('Error al procesar la solicitud', 'error');
            }
        });

        function abrirModalRequisito() {
            const modal = document.getElementById('modal-nuevo-requisito');
            const input = document.getElementById('descripcion-requisito');
            if (modal) modal.classList.remove('hidden');
            if (input) {
                input.value = '';
                setTimeout(() => input.focus(), 100);
            }
        }

        function cerrarModalRequisito() {
            const modal = document.getElementById('modal-nuevo-requisito');
            const input = document.getElementById('descripcion-requisito');
            if (modal) modal.classList.add('hidden');
            if (input) input.value = '';
        }

        async function guardarNuevoRequisito(event) {
            event.preventDefault();

            const descripcion = document.getElementById('descripcion-requisito').value.trim();

            if (!descripcion) {
                mostrarNotificacion('Ingrese una descripción para el requisito', 'error');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            if (!csrfToken) {
                mostrarNotificacion('Error: Token CSRF no encontrado', 'error');
                return;
            }

            try {
                const response = await fetch('/api/personal/requisitos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        descripRequisito: descripcion
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    requisitosDisponibles.push(data.data);

                    const select = document.getElementById('select-requisito');
                    if (select) {
                        const option = document.createElement('option');
                        option.value = data.data.codRequisito;
                        option.textContent = data.data.descripRequisito;
                        select.appendChild(option);
                        select.value = data.data.codRequisito;
                    }

                    cerrarModalRequisito();
                    mostrarNotificacion('✓ Requisito creado exitosamente. Presione el botón "+" para agregarlo.', 'success');

                    setTimeout(() => {
                        const botonAgregar = document.querySelector('button[onclick="agregarRequisitoSeleccionado()"]');
                        if (botonAgregar) botonAgregar.focus();
                    }, 100);
                } else {
                    mostrarNotificacion(data.message || 'Error al crear el requisito', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarNotificacion('Error al crear el requisito: ' + error.message, 'error');
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

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('modal-nuevo-requisito');
                if (modal && !modal.classList.contains('hidden')) {
                    cerrarModalRequisito();
                }
            }
        });
    </script>

    <style>
        .paso {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush
