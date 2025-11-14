@extends('supervisor.layouts.supervisor')

@section('title', isset($personal) ? 'Editar Personal' : 'Agregar Personal')

@section('content')
@php
$breadcrumbs = [
    ['label' => 'Inicio', 'url' => route('supervisor.home')],
    ['label' => 'Gestión Personal', 'url' => route('supervisor.gestion-personal.gestion-personal')],
    ['label' => isset($personal) ? 'Editar Personal' : 'Agregar Personal']
];
@endphp

@push('scripts')
<script>
    let rolesDisponibles = [];

    document.addEventListener("DOMContentLoaded", function () {
        cargarRoles();
    });

    async function cargarRoles() {
        try {
            const response = await fetch('/api/roles');
            const data = await response.json();

            if (data.success) {
                rolesDisponibles = data.data;
                const select = document.getElementById('codRol');

                rolesDisponibles.forEach(rol => {
                    const option = document.createElement('option');
                    option.value = rol.codRol;
                    option.textContent = rol.nombreRol;

                    const isEdit = @json(isset($personal) && $personal);
                    const personalRol = @json(isset($personal) ? $personal->codRol : null);

                    if (isEdit && personalRol == rol.codRol) {
                        option.selected = true;
                    }

                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error al cargar roles:', error);
        }
    }

    document.getElementById('formPersonal').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerText;
        submitBtn.disabled = true;
        submitBtn.innerText = 'Guardando...';

        let data = {
            usuarioPer: document.getElementById('usuarioPer').value.trim(),
            nomPer: document.getElementById('nomPer').value.trim(),
            paternoPer: document.getElementById('paternoPer').value.trim(),
            maternoPer: document.getElementById('maternoPer').value.trim(),
            codRol: document.getElementById('codRol').value,
            estado: document.getElementById('estado').value
        };

        const isEdit = @json(isset($personal) && $personal);
        const personalId = @json(isset($personal) ? $personal->codPer : null);

        // Solo agregar clavePer si no es edición o si se proporcionó una nueva contraseña
        const clavePer = document.getElementById('clavePer').value.trim();
        const clavePerConfirm = document.getElementById('clavePer_confirmation').value.trim();

        if (!isEdit) {
            // En creación, la contraseña es obligatoria
            data.clavePer = clavePer;
        } else if (clavePer) {
            // En edición, solo si se proporciona
            data.clavePer = clavePer;
        }

        // Validar que las contraseñas coincidan si se proporcionaron
        if (clavePer && clavePer !== clavePerConfirm) {
            mostrarError('Las contraseñas no coinciden');
            submitBtn.disabled = false;
            submitBtn.innerText = originalText;
            return;
        }

        const url = isEdit ?
            `/api/personal-salud/${personalId}` :
            '/api/personal-salud';

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

            setTimeout(() => {
                window.location.href = "{{ route('supervisor.gestion-personal.gestion-personal') }}";
            }, 1500);

        } else {
            alerta.className = "p-4 mt-4 rounded-lg bg-red-100 border border-red-400 text-red-800 flex items-center";
            alerta.innerHTML = `
                <span class="material-icons mr-2">error</span>
                <span>${res.message ?? "Error al guardar"}</span>
            `;
            alerta.classList.remove('hidden');

            if (res.errors) {
                errorContainer.classList.remove('hidden');
                let erroresHtml = '<ul class="list-disc list-inside space-y-1">';

                for (let campo in res.errors) {
                    const inputElement = document.getElementById(campo);
                    if (inputElement) {
                        inputElement.classList.add('border-red-500');

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

        alerta.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function mostrarError(mensaje) {
        const alerta = document.getElementById('alerta');
        alerta.className = "p-4 mt-4 rounded-lg bg-red-100 border border-red-400 text-red-800 flex items-center";
        alerta.innerHTML = `
            <span class="material-icons mr-2">error</span>
            <span>${mensaje}</span>
        `;
        alerta.classList.remove('hidden');
        alerta.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Mostrar/ocultar campos de contraseña
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
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
                        {{ isset($personal) ? 'edit' : 'person_add' }}
                    </span>
                    {{ isset($personal) ? 'Editar Personal de Salud' : 'Agregar Nuevo Personal' }}
                </h1>
                <p class="text-gray-600 ml-14">
                    {{ isset($personal) ? 'Actualiza la información del personal de salud' : 'Completa el formulario para registrar nuevo personal en el sistema' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    <div id="alerta" class="hidden"></div>
    <div id="errorContainer" class="hidden p-4 rounded-lg bg-red-50 border border-red-200 text-red-800"></div>

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form id="formPersonal" class="space-y-6">
            @csrf

            <!-- Información de Cuenta -->
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                    <span class="material-icons mr-2 text-indigo-600">account_circle</span>
                    Información de Cuenta
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Usuario -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Usuario <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="usuarioPer" id="usuarioPer"
                            value="{{ old('usuarioPer', $personal->usuarioPer ?? '') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5
                                   transition duration-150"
                            placeholder="Ej: jperez"
                            {{ isset($personal) ? 'readonly' : '' }}
                            required>
                        @if(isset($personal))
                            <p class="text-xs text-gray-500 mt-1">El usuario no puede ser modificado</p>
                        @endif
                    </div>

                    <!-- Rol -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Rol <span class="text-red-600">*</span>
                        </label>
                        <select name="codRol" id="codRol"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5
                                   transition duration-150"
                            required>
                            <option value="">Seleccione un rol...</option>
                        </select>
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Contraseña
                            @if(!isset($personal))
                                <span class="text-red-600">*</span>
                            @else
                                <span class="text-gray-500">(dejar en blanco para mantener la actual)</span>
                            @endif
                        </label>
                        <div class="relative">
                            <input type="password" name="clavePer" id="clavePer"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                       focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 pr-10
                                       transition duration-150"
                                placeholder="Mínimo 6 caracteres"
                                {{ isset($personal) ? '' : 'required' }}>
                            <button type="button"
                                onclick="togglePassword('clavePer', 'iconClavePer')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                                <span class="material-icons text-sm" id="iconClavePer">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Confirmar Contraseña
                            @if(!isset($personal))
                                <span class="text-red-600">*</span>
                            @endif
                        </label>
                        <div class="relative">
                            <input type="password" name="clavePer_confirmation" id="clavePer_confirmation"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                       focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 pr-10
                                       transition duration-150"
                                placeholder="Repite la contraseña"
                                {{ isset($personal) ? '' : 'required' }}>
                            <button type="button"
                                onclick="togglePassword('clavePer_confirmation', 'iconClavePerConf')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                                <span class="material-icons text-sm" id="iconClavePerConf">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Personal -->
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                    <span class="material-icons mr-2 text-indigo-600">badge</span>
                    Información Personal
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Nombre -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Nombre <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="nomPer" id="nomPer"
                            value="{{ old('nomPer', $personal->nomPer ?? '') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5
                                   transition duration-150"
                            placeholder="Ej: Juan Carlos"
                            required>
                    </div>

                    <!-- Apellido Paterno -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Apellido Paterno <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="paternoPer" id="paternoPer"
                            value="{{ old('paternoPer', $personal->paternoPer ?? '') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5
                                   transition duration-150"
                            placeholder="Ej: Pérez"
                            required>
                    </div>

                    <!-- Apellido Materno -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Apellido Materno
                        </label>
                        <input type="text" name="maternoPer" id="maternoPer"
                            value="{{ old('maternoPer', $personal->maternoPer ?? '') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5
                                   transition duration-150"
                            placeholder="Ej: García">
                    </div>
                </div>
            </div>

            <!-- Estado -->
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                    <span class="material-icons mr-2 text-indigo-600">toggle_on</span>
                    Estado de Acceso
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Estado <span class="text-red-600">*</span>
                        </label>
                        <select name="estado" id="estado"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                   focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5
                                   transition duration-150"
                            required>
                            <option value="activo" {{ old('estado', $personal->estado ?? 'activo') === 'activo' ? 'selected' : '' }}>
                                Activo - Puede acceder al sistema
                            </option>
                            <option value="inactivo" {{ old('estado', $personal->estado ?? '') === 'inactivo' ? 'selected' : '' }}>
                                Inactivo - No puede acceder al sistema
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('supervisor.gestion-personal.gestion-personal') }}"
                    class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300
                          rounded-lg hover:bg-gray-50 transition duration-150 flex items-center">
                    <span class="material-icons text-sm mr-1">close</span>
                    Cancelar
                </a>

                <button type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600
                           rounded-lg hover:bg-indigo-700 transition duration-150 flex items-center shadow-md">
                    <span class="material-icons text-sm mr-1">{{ isset($personal) ? 'save' : 'add' }}</span>
                    {{ isset($personal) ? 'Actualizar Personal' : 'Guardar Personal' }}
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
                    <li>Todos los campos marcados con <strong class="text-red-600">*</strong> son obligatorios</li>
                    <li>El usuario debe ser único en el sistema</li>
                    <li>La contraseña debe tener al menos 6 caracteres</li>
                    @if(isset($personal))
                        <li>Si no deseas cambiar la contraseña, deja los campos en blanco</li>
                        <li>El usuario no puede ser modificado una vez creado</li>
                    @endif
                    <li>El personal con estado "Inactivo" no podrá acceder al sistema</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
