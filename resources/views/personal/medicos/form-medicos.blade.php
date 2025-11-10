@extends('personal.layouts.personal')

@section('title', isset($medico) ? 'Editar Médico' : 'Agregar Médico')

@section('content')
@php
$breadcrumbs = [
    ['label' => 'Inicio', 'url' => route('personal.home')],
    ['label' => 'Médicos', 'url' => route('personal.medicos.medicos')],
    ['label' => isset($medico) ? 'Editar Médico' : 'Agregar Médico']
];
@endphp

@push('scripts')
<script>
    document.getElementById('formMedico').addEventListener('submit', async function(e) {
        e.preventDefault();

        // Deshabilitar botón de submit
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerText;
        submitBtn.disabled = true;
        submitBtn.innerText = 'Guardando...';

        // Recopilar datos del formulario
        let data = {
            nomMed: document.getElementById('nomMed').value.trim(),
            paternoMed: document.getElementById('paternoMed').value.trim(),
            tipoMed: document.getElementById('tipoMed').value,
        };

        const isEdit = @json(isset($medico) && $medico);
        const medicoId = @json(isset($medico) ? $medico->codMed : null);

        const url = isEdit ?
            `/api/personal/medicos/${medicoId}` :
            '/api/personal/medicos';

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
                window.location.href = "{{ route('personal.medicos.medicos') }}";
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
        alerta.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
</script>
@endpush

<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center">
                    <span class="material-icons text-4xl text-indigo-600 mr-3">
                        {{ isset($medico) ? 'edit' : 'person_add' }}
                    </span>
                    {{ isset($medico) ? 'Editar Médico' : 'Agregar Nuevo Médico' }}
                </h1>
                <p class="text-gray-600 ml-14">
                    {{ isset($medico) ? 'Actualiza la información del médico' : 'Completa el formulario para registrar un nuevo médico en el sistema' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    <div id="alerta" class="hidden"></div>
    <div id="errorContainer" class="hidden p-4 rounded-lg bg-red-50 border border-red-200 text-red-800"></div>

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form id="formMedico" class="space-y-6">
            @csrf

            <!-- Información Personal -->
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                    <span class="material-icons mr-2 text-indigo-600">badge</span>
                    Información del Médico
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Nombre -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Nombre <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="nomMed" id="nomMed"
                            value="{{ old('nomMed', $medico->nomMed ?? '') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5
                                   transition duration-150"
                            placeholder="Ej: Juan Carlos"
                            required>
                    </div>

                    <!-- Apellido -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Apellido <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="paternoMed" id="paternoMed"
                            value="{{ old('paternoMed', $medico->paternoMed ?? '') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5
                                   transition duration-150"
                            placeholder="Ej: Pérez García"
                            required>
                    </div>

                    <!-- Tipo de Médico -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Tipo de Médico <span class="text-red-600">*</span>
                        </label>
                        <select name="tipoMed" id="tipoMed"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5
                                   transition duration-150"
                            required>
                            <option value="">Seleccione...</option>
                            <option value="Interno" {{ old('tipoMed', $medico->tipoMed ?? '') === 'Interno' ? 'selected' : '' }}>
                                Interno
                            </option>
                            <option value="Externo" {{ old('tipoMed', $medico->tipoMed ?? '') === 'Externo' ? 'selected' : '' }}>
                                Externo
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('personal.medicos.medicos') }}"
                    class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300
                          rounded-lg hover:bg-gray-50 transition duration-150 flex items-center">
                    <span class="material-icons text-sm mr-1">close</span>
                    Cancelar
                </a>

                <button type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600
                           rounded-lg hover:bg-indigo-700 transition duration-150 flex items-center shadow-md">
                    <span class="material-icons text-sm mr-1">{{ isset($medico) ? 'save' : 'add' }}</span>
                    {{ isset($medico) ? 'Actualizar Médico' : 'Guardar Médico' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Información Adicional -->
    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
        <div class="flex items-start">
            <span class="material-icons text-indigo-600 mr-3">info</span>
            <div class="text-sm text-indigo-800">
                <p class="font-semibold mb-1">Información importante:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Los campos <strong>Nombre</strong>, <strong>Apellido</strong> y <strong>Tipo de Médico</strong> son obligatorios</li>
                    <li>El tipo de médico puede ser <strong>Interno</strong> (personal del hospital) o <strong>Externo</strong> (médico externo)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
