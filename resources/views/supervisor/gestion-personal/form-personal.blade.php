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
        submitBtn.innerHTML = '<span class="material-icons animate-spin mr-2">refresh</span>Guardando...';

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

        const clavePer = document.getElementById('clavePer').value.trim();
        const clavePerConfirm = document.getElementById('clavePer_confirmation').value.trim();

        if (!isEdit) {
            data.clavePer = clavePer;
        } else if (clavePer) {
            data.clavePer = clavePer;
        }

        if (clavePer && clavePer !== clavePerConfirm) {
            mostrarError('Las contraseñas no coinciden');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
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
            submitBtn.innerHTML = originalText;
        }
    });

    function manejarRespuesta(res) {
        const alerta = document.getElementById('alerta');
        const errorContainer = document.getElementById('errorContainer');

        document.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
        });
        document.querySelectorAll('.error-message').forEach(el => {
            el.remove();
        });

        if (res.success) {
            alerta.className = "p-6 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-400 text-green-800 flex items-center shadow-lg";
            alerta.innerHTML = `
                <div class="flex-shrink-0 w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mr-4">
                    <span class="material-icons text-white text-2xl">check_circle</span>
                </div>
                <div>
                    <p class="font-bold text-lg">${res.message}</p>
                    <p class="text-sm text-green-700 mt-1">Redirigiendo...</p>
                </div>
            `;
            alerta.classList.remove('hidden');
            errorContainer.classList.add('hidden');

            setTimeout(() => {
                window.location.href = "{{ route('supervisor.gestion-personal.gestion-personal') }}";
            }, 1500);

        } else {
            alerta.className = "p-6 rounded-xl bg-gradient-to-r from-red-50 to-pink-50 border-2 border-red-400 text-red-800 flex items-center shadow-lg";
            alerta.innerHTML = `
                <div class="flex-shrink-0 w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mr-4">
                    <span class="material-icons text-white text-2xl">error</span>
                </div>
                <div>
                    <p class="font-bold text-lg">${res.message ?? "Error al guardar"}</p>
                </div>
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
                        errorMsg.className = 'text-red-600 text-sm mt-1 error-message font-medium';
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
                        <span class="material-icons text-red-600 mr-3 mt-1">warning</span>
                        <div>
                            <p class="font-bold mb-2 text-lg">Por favor corrige los siguientes errores:</p>
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
        alerta.className = "p-6 rounded-xl bg-gradient-to-r from-red-50 to-pink-50 border-2 border-red-400 text-red-800 flex items-center shadow-lg";
        alerta.innerHTML = `
            <div class="flex-shrink-0 w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mr-4">
                <span class="material-icons text-white text-2xl">error</span>
            </div>
            <div>
                <p class="font-bold text-lg">${mensaje}</p>
            </div>
        `;
        alerta.classList.remove('hidden');
        alerta.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

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
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold mb-2 flex items-center gap-3">
                    <span class="material-icons text-5xl">
                        {{ isset($personal) ? 'edit' : 'person_add' }}
                    </span>
                    {{ isset($personal) ? 'Editar Personal de Salud' : 'Agregar Nuevo Personal' }}
                </h1>
                <p class="text-blue-100 text-lg">
                    {{ isset($personal) ? 'Actualiza la información del personal de salud' : 'Completa el formulario para registrar nuevo personal en el sistema' }}
                </p>
            </div>
            <div class="hidden lg:block">
                <div class="w-32 h-32 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <span class="material-icons" style="font-size: 80px; opacity: 0.5;">
                        {{ isset($personal) ? 'edit_note' : 'person_add_alt_1' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    <div id="alerta" class="hidden"></div>
    <div id="errorContainer" class="hidden p-6 rounded-xl bg-red-50 border-2 border-red-200 text-red-800"></div>

    <!-- Formulario -->
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <form id="formPersonal" class="space-y-8">
            @csrf

            <!-- Información de Cuenta -->
            <div>
                <div class="flex items-center gap-3 mb-6 pb-4 border-b-2 border-gray-100">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="material-icons text-white text-2xl">account_circle</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Información de Cuenta</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Usuario -->
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Usuario <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <span class="material-icons absolute left-3 top-3.5 text-gray-400">person</span>
                            <input type="text" name="usuarioPer" id="usuarioPer"
                                value="{{ old('usuarioPer', $personal->usuarioPer ?? '') }}"
                                class="pl-11 bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-xl
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3
                                       transition duration-150"
                                placeholder="Ej: jperez"
                                {{ isset($personal) ? 'readonly' : '' }}
                                required>
                        </div>
                        @if(isset($personal))
                            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                <span class="material-icons text-xs">lock</span>
                                El usuario no puede ser modificado
                            </p>
                        @endif
                    </div>

                    <!-- Rol -->
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Rol <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <span class="material-icons absolute left-3 top-3.5 text-gray-400">work</span>
                            <select name="codRol" id="codRol"
                                class="pl-11 bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-xl
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3
                                       transition duration-150"
                                required>
                                <option value="">Seleccione un rol...</option>
                            </select>
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Contraseña
                            @if(!isset($personal))
                                <span class="text-red-600">*</span>
                            @else
                                <span class="text-gray-500 text-xs font-normal">(dejar en blanco para mantener la actual)</span>
                            @endif
                        </label>
                        <div class="relative">
                            <span class="material-icons absolute left-3 top-3.5 text-gray-400">lock</span>
                            <input type="password" name="clavePer" id="clavePer"
                                class="pl-11 pr-11 bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-xl
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3
                                       transition duration-150"
                                placeholder="Mínimo 6 caracteres"
                                {{ isset($personal) ? '' : 'required' }}>
                            <button type="button"
                                onclick="togglePassword('clavePer', 'iconClavePer')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                                <span class="material-icons" id="iconClavePer">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Confirmar Contraseña
                            @if(!isset($personal))
                                <span class="text-red-600">*</span>
                            @endif
                        </label>
                        <div class="relative">
                            <span class="material-icons absolute left-3 top-3.5 text-gray-400">lock_outline</span>
                            <input type="password" name="clavePer_confirmation" id="clavePer_confirmation"
                                class="pl-11 pr-11 bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-xl
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3
                                       transition duration-150"
                                placeholder="Repite la contraseña"
                                {{ isset($personal) ? '' : 'required' }}>
                            <button type="button"
                                onclick="togglePassword('clavePer_confirmation', 'iconClavePerConf')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                                <span class="material-icons" id="iconClavePerConf">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Personal -->
            <div>
                <div class="flex items-center gap-3 mb-6 pb-4 border-b-2 border-gray-100">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="material-icons text-white text-2xl">badge</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Información Personal</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Nombre -->
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Nombre <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <span class="material-icons absolute left-3 top-3.5 text-gray-400">badge</span>
                            <input type="text" name="nomPer" id="nomPer"
                                value="{{ old('nomPer', $personal->nomPer ?? '') }}"
                                class="pl-11 bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-xl
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3
                                       transition duration-150"
                                placeholder="Ej: Juan Carlos"
                                required>
                        </div>
                    </div>

                    <!-- Apellido Paterno -->
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Apellido Paterno <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="paternoPer" id="paternoPer"
                            value="{{ old('paternoPer', $personal->paternoPer ?? '') }}"
                            class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-xl
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3
                                   transition duration-150"
                            placeholder="Ej: Pérez"
                            required>
                    </div>

                    <!-- Apellido Materno -->
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Apellido Materno
                        </label>
                        <input type="text" name="maternoPer" id="maternoPer"
                            value="{{ old('maternoPer', $personal->maternoPer ?? '') }}"
                            class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-xl
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3
                                   transition duration-150"
                            placeholder="Ej: García">
                    </div>
                </div>
            </div>

            <!-- Estado -->
            <div>
                <div class="flex items-center gap-3 mb-6 pb-4 border-b-2 border-gray-100">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="material-icons text-white text-2xl">toggle_on</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Estado de Acceso</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Estado <span class="text-red-600">*</span>
                        </label>
                        <select name="estado" id="estado"
                            class="bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-xl
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3
                                   transition duration-150"
                            required>
                            <option value="activo" {{ old('estado', $personal->estado ?? 'activo') === 'activo' ? 'selected' : '' }}>
                                ✅ Activo - Puede acceder al sistema
                            </option>
                            <option value="inactivo" {{ old('estado', $personal->estado ?? '') === 'inactivo' ? 'selected' : '' }}>
                                ❌ Inactivo - No puede acceder al sistema
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 pt-6 border-t-2 border-gray-100">
                <a href="{{ route('supervisor.gestion-personal.gestion-personal') }}"
                    class="px-8 py-3 text-sm font-bold text-gray-700 bg-white border-2 border-gray-300
                          rounded-xl hover:bg-gray-50 hover:shadow-lg transition-all duration-200 flex items-center">
                    <span class="material-icons mr-2">close</span>
                    Cancelar
                </a>

                <button type="submit"
                    class="px-8 py-3 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600
                           rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 flex items-center shadow-lg hover:shadow-xl">
                    <span class="material-icons mr-2">{{ isset($personal) ? 'save' : 'add' }}</span>
                    {{ isset($personal) ? 'Actualizar Personal' : 'Guardar Personal' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Información Adicional -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-6 shadow-lg">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                <span class="material-icons text-white text-2xl">info</span>
            </div>
            <div class="text-sm text-blue-900">
                <p class="font-bold text-lg mb-3">💡 Información importante:</p>
                <ul class="list-disc list-inside space-y-2">
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