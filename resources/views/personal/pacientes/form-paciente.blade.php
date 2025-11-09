@extends('personal.layouts.personal')

@section('title', isset($paciente) ? 'Editar Paciente' : 'Agregar Paciente')

@section('content')
@php
$breadcrumbs = [
['label' => 'Inicio', 'url' => route('personal.home')],
['label' => 'Pacientes', 'url' => route('personal.pacientes.pacientes')],
['label' => isset($paciente) ? 'Editar Paciente' : 'Agregar Paciente']
];
@endphp

@push('scripts')
<script>
    document.getElementById('formPaciente').addEventListener('submit', async function(e) {
        e.preventDefault();

        // Deshabilitar botón de submit
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerText;
        submitBtn.disabled = true;
        submitBtn.innerText = 'Guardando...';

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
            submitBtn.innerText = originalText;
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
            alerta.className = "p-4 mt-4 rounded-lg bg-green-100 border border-green-400 text-green-800 flex items-center";
            alerta.innerHTML = `
                <span class="material-icons mr-2">check_circle</span>
                <span>${res.message}</span>
            `;
            alerta.classList.remove('hidden');
            errorContainer.classList.add('hidden');

            // Redirigir después de 1.5s
            setTimeout(() => {
                window.location.href = "{{ route('personal.pacientes.pacientes') }}";
            }, 1500);

        } else {
            alerta.className = "p-4 mt-4 rounded-lg bg-red-100 border border-red-400 text-red-800 flex items-center";
            alerta.innerHTML = `
                <span class="material-icons mr-2">error</span>
                <span>${res.message ?? "Error al guardar"}</span>
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
                        errorMsg.className = 'text-red-600 text-sm mt-1 error-message';
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
                        <span class="material-icons text-red-600 mr-2">warning</span>
                        <div>
                            <p class="font-semibold mb-2">Por favor corrige los siguientes errores:</p>
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
            edadSpan.innerText = `(${edad} años)`;
            edadSpan.classList.remove('hidden');
        } else {
            edadSpan.classList.add('hidden');
        }
    });

    // Validación en tiempo real
    document.getElementById('nroHCI').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
</script>
@endpush

<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center">
                    <span class="material-icons text-4xl text-blue-600 mr-3">
                        {{ isset($paciente) ? 'edit' : 'person_add' }}
                    </span>
                    {{ isset($paciente) ? 'Editar Paciente' : 'Agregar Nuevo Paciente' }}
                </h1>
                <p class="text-gray-600 ml-14">
                    {{ isset($paciente) ? 'Actualiza la información del paciente' : 'Completa el formulario para registrar un nuevo paciente en el sistema' }}
                </p>
            </div>
            @if(isset($paciente))
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                    {{ $paciente->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    <span class="w-2 h-2 rounded-full mr-2 {{ $paciente->estado === 'activo' ? 'bg-green-600' : 'bg-red-600' }}"></span>
                    {{ ucfirst($paciente->estado) }}
                </span>
            </div>
            @endif
        </div>
    </div>

    <!-- Alertas -->
    <div id="alerta" class="hidden"></div>
    <div id="errorContainer" class="hidden p-4 rounded-lg bg-red-50 border border-red-200 text-red-800"></div>

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form id="formPaciente" class="space-y-6">
            @csrf

            <!-- Información Personal -->
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                    <span class="material-icons mr-2 text-blue-600">person</span>
                    Información Personal
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Nombre -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Nombre <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="nomPa" id="nomPa"
                            value="{{ old('nomPa', $paciente->nomPa ?? '') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                   transition duration-150"
                            placeholder="Ej: Juan"
                            required>
                    </div>

                    <!-- Apellido Paterno -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Apellido Paterno
                        </label>
                        <input type="text" name="paternoPa" id="paternoPa"
                            value="{{ old('paternoPa', $paciente->paternoPa ?? '') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                   transition duration-150"
                            placeholder="Ej: Pérez">
                    </div>

                    <!-- Apellido Materno -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Apellido Materno
                        </label>
                        <input type="text" name="maternoPa" id="maternoPa"
                            value="{{ old('maternoPa', $paciente->maternoPa ?? '') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                   transition duration-150"
                            placeholder="Ej: García">
                    </div>

                    <!-- Sexo -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Sexo <span class="text-red-600">*</span>
                        </label>
                        <select name="sexo" id="sexo"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                   transition duration-150"
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
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Fecha de Nacimiento
                        </label>
                        <input type="date" name="fechaNac" id="fechaNac"
                            value="{{ old('fechaNac', isset($paciente->fechaNac) ? \Carbon\Carbon::parse($paciente->fechaNac)->format('Y-m-d') : '') }}"
                            max="{{ date('Y-m-d') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                   transition duration-150">
                        <span id="edadCalculada" class="text-xs text-gray-600 mt-1 hidden"></span>
                    </div>
                </div>
            </div>

            <!-- Información Clínica -->
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                    <span class="material-icons mr-2 text-blue-600">medical_services</span>
                    Información Clínica
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Número HCI -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Número de Historia Clínica (HCI)
                        </label>
                        <input type="text" name="nroHCI" id="nroHCI"
                            value="{{ old('nroHCI', $paciente->nroHCI ?? '') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                   transition duration-150"
                            placeholder="Ej: HCI-2024-001">
                        <p class="text-xs text-gray-500 mt-1">Debe ser único para cada paciente</p>
                    </div>

                    <!-- Tipo de Paciente -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Tipo de Paciente <span class="text-red-600">*</span>
                        </label>
                        <select name="tipoPac" id="tipoPac"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                   transition duration-150"
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
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('personal.pacientes.pacientes') }}"
                    class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300
                          rounded-lg hover:bg-gray-50 transition duration-150 flex items-center">
                    <span class="material-icons text-sm mr-1">close</span>
                    Cancelar
                </a>

                <button type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600
                           rounded-lg hover:bg-blue-700 transition duration-150 flex items-center shadow-md">
                    <span class="material-icons text-sm mr-1">{{ isset($paciente) ? 'save' : 'add' }}</span>
                    {{ isset($paciente) ? 'Actualizar Paciente' : 'Guardar Paciente' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Información Adicional -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <span class="material-icons text-blue-600 mr-3">info</span>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Información importante:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Los campos marcados con <span class="text-red-600">*</span> son obligatorios</li>
                    <li>El número de Historia Clínica (HCI) debe ser único</li>
                    <li>La fecha de nacimiento no puede ser posterior a hoy</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
