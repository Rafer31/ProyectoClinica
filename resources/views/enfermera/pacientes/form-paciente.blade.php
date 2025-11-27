@extends('enfermera.layouts.enfermera')

@section('title', isset($paciente) ? 'Editar Paciente' : 'Agregar Paciente')

@section('content')
@php
$breadcrumbs = [
['label' => 'Inicio', 'url' => route('enfermera.home')],
['label' => 'Pacientes', 'url' => route('enfermera.pacientes.pacientes')],
['label' => isset($paciente) ? 'Editar Paciente' : 'Agregar Paciente']
];
@endphp

@push('scripts')
<script>
    document.getElementById('formPaciente').addEventListener('submit', async function(e) {
        e.preventDefault();

        // Deshabilitar botón de submit
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <div class="flex items-center">
                <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Guardando...
            </div>
        `;

        // Recopilar datos del formulario
        let data = {
            nomPa: document.getElementById('nomPa').value.trim(),
            paternoPa: document.getElementById('paternoPa').value.trim() || null,
            maternoPa: document.getElementById('maternoPa').value.trim() || null,
            sexo: document.getElementById('sexo').value,
            fechaNac: document.getElementById('fechaNac').value || null,
            nroHCI: document.getElementById('nroHCI').value.trim() || null,
            tipoPac: document.getElementById('tipoPac').value,
        };

        const isEdit = @json(isset($paciente) && $paciente);
        const pacienteId = @json(isset($paciente) ? $paciente -> codPa : null);

        const url = isEdit ?
            `/api/personal/pacientes/${pacienteId}` :
            '/api/personal/pacientes';

        const method = isEdit ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify(data)
            });

            const res = await response.json();
            manejarRespuesta(res);
        } catch (error) {
            manejarRespuesta({
                success: false,
                message: 'Error de conexión. Por favor, intenta nuevamente.'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    function manejarRespuesta(res) {
        const alerta = document.getElementById('alerta');
        const errorContainer = document.getElementById('errorContainer');

        // Limpiar errores previos
        document.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
        });
        document.querySelectorAll('.error-message').forEach(el => {
            el.remove();
        });

        if (res.success) {
            alerta.className = "p-4 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-300 text-emerald-800 flex items-center shadow-md";
            alerta.innerHTML = `
                <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center mr-3">
                    <span class="material-icons text-white">check_circle</span>
                </div>
                <div>
                    <p class="font-bold">¡Éxito!</p>
                    <p class="text-sm">${res.message}</p>
                </div>
            `;
            alerta.classList.remove('hidden');
            errorContainer.classList.add('hidden');

            // Redirigir después de 1.5s
            setTimeout(() => {
                window.location.href = "{{ route('enfermera.pacientes.pacientes') }}";
            }, 1500);

        } else {
            alerta.className = "p-4 rounded-xl bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-300 text-red-800 flex items-center shadow-md";
            alerta.innerHTML = `
                <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                    <span class="material-icons text-white">error</span>
                </div>
                <div>
                    <p class="font-bold">Error al guardar</p>
                    <p class="text-sm">${res.message ?? "Verifica los datos ingresados"}</p>
                </div>
            `;
            alerta.classList.remove('hidden');

            // Mostrar errores de validación en cada campo
            if (res.errors) {
                errorContainer.classList.remove('hidden');
                let erroresHtml = '<ul class="list-disc list-inside space-y-1">';

                for (let campo in res.errors) {
                    const inputElement = document.getElementById(campo);
                    if (inputElement) {
                        inputElement.classList.add('border-red-500');

                        // Agregar mensaje de error debajo del input
                        const errorMsg = document.createElement('p');
                        errorMsg.className = 'text-red-600 text-sm mt-1 error-message font-semibold';
                        errorMsg.innerText = res.errors[campo][0];
                        inputElement.parentElement.appendChild(errorMsg);
                    }

                    res.errors[campo].forEach(error => {
                        erroresHtml += `<li>${error}</li>`;
                    });
                }

                erroresHtml += '</ul>';
                errorContainer.innerHTML = `
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                            <span class="material-icons text-white">warning</span>
                        </div>
                        <div>
                            <p class="font-bold mb-2">Por favor corrige los siguientes errores:</p>
                            ${erroresHtml}
                        </div>
                    </div>
                `;
            }
        }

        // Auto-scroll a la alerta
        alerta.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }

    // Calcular edad automáticamente
    document.getElementById('fechaNac').addEventListener('change', function() {
        const fechaNac = new Date(this.value);
        const hoy = new Date();
        let edad = hoy.getFullYear() - fechaNac.getFullYear();
        const mes = hoy.getMonth() - fechaNac.getMonth();

        if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
            edad--;
        }

        const edadSpan = document.getElementById('edadCalculada');
        if (edad >= 0) {
            edadSpan.innerText = `✓ ${edad} años`;
            edadSpan.className = 'text-sm text-emerald-600 font-semibold mt-1 flex items-center gap-1';
            edadSpan.classList.remove('hidden');
        } else {
            edadSpan.classList.add('hidden');
        }
    });

    // Validación en tiempo real
    document.getElementById('nroHCI').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Trigger cálculo de edad si ya hay fecha
    window.addEventListener('DOMContentLoaded', function() {
        const fechaNacInput = document.getElementById('fechaNac');
        if (fechaNacInput.value) {
            fechaNacInput.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush

<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="material-icons text-white text-3xl">
                            {{ isset($paciente) ? 'edit' : 'person_add' }}
                        </span>
                    </div>
                    {{ isset($paciente) ? 'Editar Paciente' : 'Agregar Nuevo Paciente' }}
                </h1>
                <p class="text-gray-600 ml-17 font-medium">
                    {{ isset($paciente) ? 'Actualiza la información del paciente' : 'Completa el formulario para registrar un nuevo paciente en el sistema' }}
                </p>
            </div>
            @if(isset($paciente))
            <div class="text-right">
                <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold border-2 shadow-sm
                    {{ $paciente->estado === 'activo' ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-red-50 text-red-700 border-red-300' }}">
                    <span class="w-2.5 h-2.5 rounded-full mr-2 {{ $paciente->estado === 'activo' ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                    {{ ucfirst($paciente->estado) }}
                </span>
            </div>
            @endif
        </div>
    </div>

    <!-- Alertas -->
    <div id="alerta" class="hidden"></div>
    <div id="errorContainer" class="hidden p-5 rounded-xl bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-300 text-red-800 shadow-md"></div>

    <!-- Formulario -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form id="formPaciente" class="space-y-8">
            @csrf

            <!-- Información Personal -->
            <div>
                <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-gray-200">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-white">person</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Información Personal</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Nombre -->
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Nombre <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="nomPa" id="nomPa"
                            value="{{ old('nomPa', $paciente->nomPa ?? '') }}"
                            class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3
                                   transition-all font-medium"
                            placeholder="Ej: Juan"
                            required>
                    </div>

                    <!-- Apellido Paterno -->
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Apellido Paterno
                        </label>
                        <input type="text" name="paternoPa" id="paternoPa"
                            value="{{ old('paternoPa', $paciente->paternoPa ?? '') }}"
                            class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3
                                   transition-all font-medium"
                            placeholder="Ej: Pérez">
                    </div>

                    <!-- Apellido Materno -->
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Apellido Materno
                        </label>
                        <input type="text" name="maternoPa" id="maternoPa"
                            value="{{ old('maternoPa', $paciente->maternoPa ?? '') }}"
                            class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3
                                   transition-all font-medium"
                            placeholder="Ej: García">
                    </div>

                    <!-- Sexo -->
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Sexo <span class="text-red-600">*</span>
                        </label>
                        <select name="sexo" id="sexo"
                            class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3
                                   transition-all font-medium"
                            required>
                            <option value="M" {{ old('sexo', $paciente->sexo ?? '') === 'M' ? 'selected' : '' }}>
                                Masculino
                            </option>
                            <option value="F" {{ old('sexo', $paciente->sexo ?? '') === 'F' ? 'selected' : '' }}>
                                Femenino
                            </option>
                        </select>
                    </div>

                    <!-- Fecha de Nacimiento -->
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Fecha de Nacimiento
                        </label>
                        <input type="date" name="fechaNac" id="fechaNac"
                            value="{{ old('fechaNac', isset($paciente->fechaNac) ? \Carbon\Carbon::parse($paciente->fechaNac)->format('Y-m-d') : '') }}"
                            max="{{ date('Y-m-d') }}"
                            class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3
                                   transition-all font-medium">
                        <span id="edadCalculada" class="hidden"></span>
                    </div>
                </div>
            </div>

            <!-- Información Clínica -->
            <div>
                <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-gray-200">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-white">medical_services</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Información Clínica</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Número HCI -->
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Número de Historia Clínica (HCI)
                        </label>
                        <input type="text" name="nroHCI" id="nroHCI"
                            value="{{ old('nroHCI', $paciente->nroHCI ?? '') }}"
                            class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3
                                   transition-all font-medium"
                            placeholder="Ej: HCI-2024-001">
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                            <span class="material-icons text-xs">info</span>
                            Debe ser único para cada paciente
                        </p>
                    </div>

                    <!-- Tipo de Paciente -->
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Tipo de Paciente <span class="text-red-600">*</span>
                        </label>
                        <select name="tipoPac" id="tipoPac"
                            class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3
                                   transition-all font-medium"
                            required>

                            <option value="SUS" {{ old('tipoPac', $paciente->tipoPac ?? '') === 'SUS' ? 'selected' : '' }}>
                                SUS (Seguro Universal de Salud)
                            </option>
                            <option value="SINSUS" {{ old('tipoPac', $paciente->tipoPac ?? '') === 'SINSUS' ? 'selected' : '' }}>
                                SIN SUS (Sin Seguro)
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 pt-6 border-t-2 border-gray-200">
                <a href="{{ route('enfermera.pacientes.pacientes') }}"
                    class="px-6 py-3 text-sm font-bold text-gray-700 bg-white border-2 border-gray-300
                          rounded-lg hover:bg-gray-50 transition-all flex items-center gap-2 hover:shadow-md">
                    <span class="material-icons text-lg">close</span>
                    Cancelar
                </a>

                <button type="submit"
                    class="px-6 py-3 text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-600
                           rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all flex items-center gap-2 shadow-md hover:shadow-lg transform hover:scale-105">
                    <span class="material-icons text-lg">{{ isset($paciente) ? 'save' : 'add' }}</span>
                    {{ isset($paciente) ? 'Actualizar Paciente' : 'Guardar Paciente' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Información Adicional -->
    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-300 rounded-xl p-5 shadow-sm">
        <div class="flex items-start">
            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                <span class="material-icons text-white">info</span>
            </div>
            <div class="text-sm text-blue-800">
                <p class="font-bold mb-2 text-base">Información importante:</p>
                <ul class="list-disc list-inside space-y-1 font-medium">
                    <li>Los campos marcados con <span class="text-red-600 font-bold">*</span> son obligatorios</li>
                    <li>El número de Historia Clínica (HCI) debe ser único</li>
                    <li>La fecha de nacimiento no puede ser posterior a hoy</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
