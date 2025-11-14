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
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <span class="material-icons align-middle text-4xl text-purple-600">
                    {{ $esEdicion ? 'edit' : 'add_circle' }}
                </span>
                {{ $esEdicion ? 'Editar Tipo de Estudio' : 'Nuevo Tipo de Estudio' }}
            </h1>
            <p class="text-gray-600">
                {{ $esEdicion ? 'Modifica los datos del tipo de estudio' : 'Completa los pasos para crear un nuevo tipo de estudio' }}
            </p>
        </div>

        <!-- Indicador de pasos -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div id="paso-1-indicator"
                            class="paso-indicator active flex items-center justify-center w-10 h-10 rounded-full bg-purple-600 text-white font-bold">
                            1
                        </div>
                        <div class="flex-1 h-1 bg-gray-300 mx-2">
                            <div id="progreso-1" class="h-full bg-purple-600 transition-all duration-300" style="width: 0%">
                            </div>
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-700 mt-2">Información Básica</p>
                </div>

                <div class="flex-1">
                    <div class="flex items-center">
                        <div id="paso-2-indicator"
                            class="paso-indicator flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600 font-bold">
                            2
                        </div>
                        <div class="flex-1 h-1 bg-gray-300 mx-2">
                            <div id="progreso-2" class="h-full bg-purple-600 transition-all duration-300" style="width: 0%">
                            </div>
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-700 mt-2">Observaciones</p>
                </div>

                <div class="flex-1">
                    <div class="flex items-center">
                        <div id="paso-3-indicator"
                            class="paso-indicator flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600 font-bold">
                            3
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-700 mt-2">Requisitos</p>
                </div>
            </div>
        </div>

        <!-- Formulario Multi-paso -->
        <form id="form-tipo-estudio" class="space-y-6">
            @csrf

            <!-- Paso 1: Información Básica -->
            <div id="paso-1" class="paso bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <span class="material-icons mr-2 text-purple-600">info</span>
                    Paso 1: Información Básica
                </h2>

                <div class="space-y-4">
                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                            Título del Tipo de Estudio <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="descripcion" name="descripcion" required
                            value="{{ $esEdicion ? $tipoEstudio->descripcion : '' }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Ej: Estudio Obstétrico 1, Estudio Morfológico, etc.">
                        <p class="mt-1 text-sm text-gray-500">Este será el nombre del tipo de estudio</p>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="siguientePaso(2)"
                            class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center">
                            Siguiente
                            <span class="material-icons ml-2">arrow_forward</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Paso 2: Observaciones -->
            <div id="paso-2" class="paso hidden bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <span class="material-icons mr-2 text-purple-600">description</span>
                    Paso 2: Observaciones e Instrucciones
                </h2>

                <div class="space-y-4">
                    <div>
                        <label for="observacion-principal" class="block text-sm font-medium text-gray-700 mb-2">
                            Instrucciones para el Paciente <span class="text-red-500">*</span>
                        </label>
                        <textarea id="observacion-principal" name="observacion-principal" rows="6" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Escriba las instrucciones completas para el paciente. Ej: Estimado paciente debe acudir para el estudio...">{{ $esEdicion && $tipoEstudio->requisitos->isNotEmpty() && $tipoEstudio->requisitos[0]->pivot->observacion ? $tipoEstudio->requisitos[0]->pivot->observacion : '' }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Escriba todas las indicaciones que debe seguir el paciente</p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800 flex items-center">
                            <span class="material-icons text-blue-600 mr-2">info</span>
                            En el siguiente paso podrá agregar los requisitos específicos (vejiga llena, ayuno, etc.)
                        </p>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" onclick="anteriorPaso(1)"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 flex items-center">
                            <span class="material-icons mr-2">arrow_back</span>
                            Anterior
                        </button>
                        <button type="button" onclick="siguientePaso(3)"
                            class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center">
                            Siguiente
                            <span class="material-icons ml-2">arrow_forward</span>
                        </button>
                    </div>
                </div>
            </div>

            <div id="paso-3" class="paso hidden bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <span class="material-icons mr-2 text-purple-600">fact_check</span>
                    Paso 3: Requisitos del Estudio
                </h2>

                <div class="space-y-4">
                    <!-- Selector de requisitos CON BOTÓN PARA CREAR -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Seleccionar Requisitos
                        </label>
                        <div class="flex gap-2">
                            <select id="select-requisito"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">-- Seleccione un requisito --</option>
                            </select>
                            <button type="button" onclick="agregarRequisitoSeleccionado()"
                                class="px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center"
                                title="Agregar requisito seleccionado">
                                <span class="material-icons">add</span>
                            </button>
                            <!-- NUEVO BOTÓN: Crear requisito -->
                            <button type="button" onclick="abrirModalRequisito()"
                                class="px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-1"
                                title="Crear nuevo requisito">
                                <span class="material-icons text-sm">add_circle</span>
                                <span class="text-sm font-medium">Nuevo</span>
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Seleccione requisitos existentes o cree uno nuevo con el botón "Nuevo"
                        </p>
                    </div>

                    <!-- Lista de requisitos agregados -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Requisitos Agregados
                        </label>
                        <div id="requisitos-agregados"
                            class="space-y-2 min-h-[100px] border-2 border-dashed border-gray-300 rounded-lg p-4">
                            <p class="text-sm text-gray-500 text-center" id="mensaje-sin-requisitos">
                                No hay requisitos agregados. Seleccione requisitos de la lista superior.
                            </p>
                        </div>
                    </div>

                    <!-- Vista previa -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
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
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 flex items-center">
                            <span class="material-icons mr-2">arrow_back</span>
                            Anterior
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center">
                            <span class="material-icons mr-2">save</span>
                            {{ $esEdicion ? 'Actualizar' : 'Guardar' }} Tipo de Estudio
                        </button>
                    </div>
                </div>
            </div>
        </form>
          <div id="modal-nuevo-requisito"
                class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-lg rounded-md bg-white">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="material-icons text-purple-600">add_circle</span>
                            Crear Nuevo Requisito
                        </h3>
                        <button onclick="cerrarModalRequisito()"
                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-colors">
                            <span class="material-icons">close</span>
                        </button>
                    </div>

                    <!-- CORREGIDO: Eliminado el id="form-nuevo-requisito" y usando solo onsubmit -->
                    <form onsubmit="guardarNuevoRequisito(event); return false;">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Descripción del Requisito <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="descripcion-requisito" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="Ej: Vejiga llena, Ayuno de 8 horas, etc.">
                                <p class="mt-1 text-xs text-gray-500">Ingrese una descripción clara del requisito</p>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-xs text-blue-800 flex items-center gap-2">
                                    <span class="material-icons text-xs">info</span>
                                    El requisito se agregará a la lista y podrá usarlo inmediatamente
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-3 mt-6">
                            <button type="button" onclick="cerrarModalRequisito()"
                                class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all flex items-center justify-center gap-2">
                                <span class="material-icons text-sm">save</span>
                                Guardar
                            </button>
                        </div>
                    </form>
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

                    // Actualizar vista previa en tiempo real
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
                    // Validar paso actual
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

                    // Ocultar paso actual y mostrar siguiente
                    document.getElementById(`paso-${pasoActual}`).classList.add('hidden');
                    document.getElementById(`paso-${paso}`).classList.remove('hidden');

                    // Actualizar indicadores
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
                    // Resetear todos
                    for (let i = 1; i <= 3; i++) {
                        const indicator = document.getElementById(`paso-${i}-indicator`);
                        if (i < pasoActivo) {
                            indicator.classList.remove('bg-gray-300', 'text-gray-600', 'bg-purple-600', 'text-white');
                            indicator.classList.add('bg-green-500', 'text-white');
                            indicator.innerHTML = '<span class="material-icons">check</span>';
                        } else if (i === pasoActivo) {
                            indicator.classList.remove('bg-gray-300', 'text-gray-600', 'bg-green-500');
                            indicator.classList.add('bg-purple-600', 'text-white');
                            indicator.textContent = i;
                        } else {
                            indicator.classList.remove('bg-purple-600', 'text-white', 'bg-green-500');
                            indicator.classList.add('bg-gray-300', 'text-gray-600');
                            indicator.textContent = i;
                        }
                    }

                    // Actualizar barras de progreso
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

                    // Verificar si ya está agregado
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
                    requisitosAgregados = requisitosAgregados.filter(r => r.codRequisito !== codRequisito);
                    renderizarRequisitos();
                    actualizarVistaPrevia();
                }

                function renderizarRequisitos() {
                    const container = document.getElementById('requisitos-agregados');
                    const mensaje = document.getElementById('mensaje-sin-requisitos');

                    if (requisitosAgregados.length === 0) {
                        mensaje.classList.remove('hidden');
                        container.innerHTML = '<p class="text-sm text-gray-500 text-center" id="mensaje-sin-requisitos">No hay requisitos agregados. Seleccione requisitos de la lista superior.</p>';
                        return;
                    }

                    container.innerHTML = requisitosAgregados.map(req => `
                                            <div class="flex items-center justify-between p-3 bg-purple-50 border border-purple-200 rounded-lg">
                                                <div class="flex items-center">
                                                    <span class="material-icons text-purple-600 mr-2">check_circle</span>
                                                    <span class="text-sm font-medium text-gray-800">${req.descripRequisito}</span>
                                                </div>
                                                <button type="button" onclick="eliminarRequisito(${req.codRequisito})"
                                                    class="text-red-600 hover:text-red-800">
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
                            // EDICIÓN: Dos pasos separados

                            // 1. Actualizar el tipo de estudio
                            const responseUpdate = await fetch(`/api/personal/tipos-estudio/${tipoEstudioId}`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({ descripcion: descripcion })
                            });

                            if (!responseUpdate.ok) {
                                const errorText = await responseUpdate.text();
                                console.error('Error response:', errorText);
                                mostrarNotificacion('Error al actualizar el tipo de estudio', 'error');
                                return;
                            }

                            const dataUpdate = await responseUpdate.json();

                            if (!dataUpdate.success) {
                                mostrarNotificacion(dataUpdate.message || 'Error al actualizar', 'error');
                                return;
                            }

                            // 2. Asignar los requisitos
                            const datosRequisitos = {
                                requisitos: requisitosAgregados.map((req, index) => ({
                                    codRequisito: req.codRequisito,
                                    observacion: index === 0 ? observacionPrincipal : null
                                }))
                            };

                            console.log('Enviando requisitos:', JSON.stringify(datosRequisitos, null, 2));

                            const responseRequisitos = await fetch(`/api/personal/tipos-estudio/${tipoEstudioId}/requisitos/asignar`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify(datosRequisitos)
                            });

                            if (!responseRequisitos.ok) {
                                const errorText = await responseRequisitos.text();
                                console.error('Error al asignar requisitos:', errorText);
                                console.error('Status:', responseRequisitos.status);
                                mostrarNotificacion('Error al asignar requisitos. Revisa la consola para más detalles.', 'error');
                                return;
                            }

                            const dataRequisitos = await responseRequisitos.json();

                            if (dataRequisitos.success) {
                                mostrarNotificacion('Tipo de estudio actualizado exitosamente', 'success');
                                setTimeout(() => {
                                    window.location.href = '/personal/tipos-estudio';
                                }, 1500);
                            } else {
                                mostrarNotificacion(dataRequisitos.message || 'Error al asignar requisitos', 'error');
                            }

                        } else {
                            // CREACIÓN: Un solo paso
                            const datos = {
                                descripcion: descripcion,
                                requisitos: requisitosAgregados.map((req, index) => ({
                                    codRequisito: req.codRequisito,
                                    observacion: index === 0 ? observacionPrincipal : null
                                }))
                            };

                            console.log('Creando tipo de estudio:', JSON.stringify(datos, null, 2));

                            const response = await fetch('/api/personal/tipos-estudio/con-requisitos', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify(datos)
                            });

                            if (!response.ok) {
                                const errorText = await response.text();
                                console.error('Error response:', errorText);
                                console.error('Status:', response.status);
                                mostrarNotificacion('Error al crear. Revisa la consola para más detalles.', 'error');
                                return;
                            }

                            const data = await response.json();

                            if (data.success) {
                                mostrarNotificacion('Tipo de estudio creado exitosamente', 'success');
                                setTimeout(() => {
                                    window.location.href = '/personal/tipos-estudio';
                                }, 1500);
                            } else {
                                mostrarNotificacion(data.message || 'Error al guardar', 'error');
                            }
                        }
                    } catch (error) {
                        console.error('Error completo:', error);
                        console.error('Error stack:', error.stack);
                        mostrarNotificacion('Error al procesar la solicitud: ' + error.message, 'error');
                    }
                });

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
                function abrirModalRequisito() {
                    const modal = document.getElementById('modal-nuevo-requisito');
                    const input = document.getElementById('descripcion-requisito');

                    if (modal) {
                        modal.classList.remove('hidden');
                    }

                    if (input) {
                        input.value = '';
                        setTimeout(() => input.focus(), 100);
                    }
                }

                function cerrarModalRequisito() {
                    const modal = document.getElementById('modal-nuevo-requisito');
                    const input = document.getElementById('descripcion-requisito');

                    if (modal) {
                        modal.classList.add('hidden');
                    }

                    // Limpiar el input manualmente (NO usar reset())
                    if (input) {
                        input.value = '';
                    }
                }

                async function guardarNuevoRequisito(event) {
                    event.preventDefault();

                    const descripcion = document.getElementById('descripcion-requisito').value.trim();

                    if (!descripcion) {
                        mostrarNotificacion('Ingrese una descripción para el requisito', 'error');
                        return;
                    }

                    // Obtener el token CSRF
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                    if (!csrfToken) {
                        mostrarNotificacion('Error: Token CSRF no encontrado', 'error');
                        console.error('No se encontró el meta tag csrf-token en el HTML');
                        return;
                    }

                    console.log('=== GUARDANDO REQUISITO ===');
                    console.log('Descripción:', descripcion);
                    console.log('URL:', '/api/personal/requisitos');

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

                        console.log('Response status:', response.status);
                        console.log('Response OK:', response.ok);

                        // Leer el texto de la respuesta
                        const responseText = await response.text();
                        console.log('Response text:', responseText);

                        // Intentar parsear como JSON
                        let data;
                        try {
                            data = JSON.parse(responseText);
                            console.log('Response data:', data);
                        } catch (e) {
                            console.error('Error al parsear JSON:', e);
                            console.error('Respuesta recibida:', responseText);
                            throw new Error('La respuesta del servidor no es JSON válido');
                        }

                        if (response.ok && data.success) {
                            console.log('✓ Requisito creado exitosamente:', data.data);

                            // Agregar el nuevo requisito a la lista disponible
                            requisitosDisponibles.push(data.data);

                            // Agregar la opción al select
                            const select = document.getElementById('select-requisito');
                            if (select) {
                                const option = document.createElement('option');
                                option.value = data.data.codRequisito;
                                option.textContent = data.data.descripRequisito;
                                select.appendChild(option);

                                // Seleccionar automáticamente el nuevo requisito
                                select.value = data.data.codRequisito;

                                console.log('✓ Requisito agregado al select con ID:', data.data.codRequisito);
                            }

                            // Cerrar modal
                            cerrarModalRequisito();

                            // Mostrar notificación de éxito
                            mostrarNotificacion('✓ Requisito creado exitosamente. Presione el botón "+" para agregarlo.', 'success');

                            // Hacer foco en el botón de agregar
                            setTimeout(() => {
                                const botonAgregar = document.querySelector('button[onclick="agregarRequisitoSeleccionado()"]');
                                if (botonAgregar) {
                                    botonAgregar.focus();
                                }
                            }, 100);
                        } else {
                            console.error('✗ Error en la respuesta:', data);
                            mostrarNotificacion(data.message || 'Error al crear el requisito', 'error');
                        }
                    } catch (error) {
                        console.error('✗ ERROR COMPLETO:', error);
                        console.error('Stack trace:', error.stack);
                        mostrarNotificacion('Error al crear el requisito: ' + error.message, 'error');
                    }
                }

                // Cerrar modal al presionar ESC
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        const modal = document.getElementById('modal-nuevo-requisito');
                        if (modal && !modal.classList.contains('hidden')) {
                            cerrarModalRequisito();
                        }
                    }
                });

                // Cerrar modal al hacer clic fuera del contenido
                document.addEventListener('DOMContentLoaded', function () {
                    const modal = document.getElementById('modal-nuevo-requisito');
                    if (modal) {
                        modal.addEventListener('click', function (e) {
                            if (e.target === this) {
                                cerrarModalRequisito();
                            }
                        });
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
