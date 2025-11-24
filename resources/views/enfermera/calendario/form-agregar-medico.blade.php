@extends('enfermera.layouts.enfermera')
@section('title', 'Agregar Médico')
@section('content')

<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-gradient-to-r from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="material-icons text-white text-2xl">person_add</span>
                    </div>
                    Agregar Nuevo Médico
                </h1>
                <p class="text-gray-600 ml-15 font-medium">Complete el formulario para registrar un nuevo médico</p>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    <div id="alerta" class="hidden"></div>
    <div id="errorContainer" class="hidden p-5 rounded-xl bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-200 text-red-800 shadow-lg"></div>

    <!-- Formulario -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8">
        <form id="formMedico" class="space-y-8">
            @csrf

            <!-- Información del Médico -->
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2 border-b-2 border-purple-200 pb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-violet-600 rounded-lg flex items-center justify-center shadow-md">
                        <span class="material-icons text-white text-lg">badge</span>
                    </div>
                    Información del Médico
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Nombre -->
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900 flex items-center gap-1">
                            Nombre <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <span class="material-icons absolute left-3 top-3 text-purple-600">person</span>
                            <input type="text" id="nomMed" required
                                class="pl-11 bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-xl
                                       focus:ring-2 focus:ring-purple-500 focus:border-purple-500 block w-full p-3
                                       transition-all shadow-sm font-medium"
                                placeholder="Ej: Juan Carlos">
                        </div>
                    </div>

                    <!-- Apellido -->
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900 flex items-center gap-1">
                            Apellido <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <span class="material-icons absolute left-3 top-3 text-purple-600">badge</span>
                            <input type="text" id="paternoMed" required
                                class="pl-11 bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-xl
                                       focus:ring-2 focus:ring-purple-500 focus:border-purple-500 block w-full p-3
                                       transition-all shadow-sm font-medium"
                                placeholder="Ej: Pérez García">
                        </div>
                    </div>

                    <!-- Tipo de Médico -->
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900 flex items-center gap-1">
                            Tipo de Médico <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <span class="material-icons absolute left-3 top-3 text-purple-600">category</span>
                            <select id="tipoMed" required
                                class="pl-11 bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-xl
                                       focus:ring-2 focus:ring-purple-500 focus:border-purple-500 block w-full p-3
                                       transition-all shadow-sm font-medium appearance-none">
                                <option value="">Seleccione un tipo...</option>
                                <option value="Interno">Interno</option>
                                <option value="Externo">Externo</option>
                            </select>
                            <span class="material-icons absolute right-3 top-3 text-gray-400 pointer-events-none">arrow_drop_down</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 pt-8 border-t-2 border-gray-200">
                <button type="button" onclick="window.close()"
                    class="px-6 py-3 text-sm font-bold text-gray-700 bg-white border-2 border-gray-300
                          rounded-xl hover:bg-gray-50 transition-all shadow-sm hover:shadow-md flex items-center gap-2">
                    <span class="material-icons text-sm">close</span>
                    Cancelar
                </button>

                <button type="submit"
                    class="px-6 py-3 text-sm font-bold text-white bg-gradient-to-r from-purple-500 to-violet-600
                           rounded-xl hover:from-purple-600 hover:to-violet-700 transition-all flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <span class="material-icons text-sm">add_circle</span>
                    Guardar Médico
                </button>
            </div>
        </form>
    </div>

    <!-- Información Adicional -->
    <div class="bg-gradient-to-br from-purple-50 to-violet-50 border-l-4 border-purple-500 rounded-xl p-6 shadow-lg">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                <span class="material-icons text-white text-xl">info</span>
            </div>
            <div class="text-sm text-purple-900">
                <p class="font-bold text-base mb-3">Información importante:</p>
                <ul class="list-disc list-inside space-y-2 font-medium">
                    <li>Los campos <strong>Nombre</strong>, <strong>Apellido</strong> y <strong>Tipo de Médico</strong> son obligatorios</li>
                    <li>El tipo de médico puede ser:
                        <ul class="ml-6 mt-2 space-y-1">
                            <li><strong class="text-emerald-700">Interno:</strong> Personal médico de la clínica</li>
                            <li><strong class="text-orange-700">Externo:</strong> Médico visitante o colaborador externo</li>
                        </ul>
                    </li>
                    <li>Una vez guardado, cierre esta ventana y actualice los datos en la ventana principal</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('formMedico').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalHTML = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <div class="flex items-center justify-center gap-2">
                <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
                <span>Guardando...</span>
            </div>
        `;

        let data = {
            nomMed: document.getElementById('nomMed').value.trim(),
            paternoMed: document.getElementById('paternoMed').value.trim(),
            tipoMed: document.getElementById('tipoMed').value,
        };

        try {
            const response = await fetch('/api/enfermera/medicos', {
                method: 'POST',
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
            submitBtn.innerHTML = originalHTML;
        }
    });

    function manejarRespuesta(res) {
        const alerta = document.getElementById('alerta');
        const errorContainer = document.getElementById('errorContainer');

        document.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
            el.classList.add('border-gray-300');
        });
        document.querySelectorAll('.error-message').forEach(el => el.remove());

        if (res.success) {
            alerta.className = "p-5 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-400 text-emerald-800 flex items-center shadow-lg";
            alerta.innerHTML = `
                <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center mr-3 shadow-md">
                    <span class="material-icons text-white">check_circle</span>
                </div>
                <div>
                    <span class="font-bold">${res.message}</span>
                    <p class="text-sm mt-1">Cierre esta ventana y actualice la página principal</p>
                </div>
            `;
            alerta.classList.remove('hidden');
            errorContainer.classList.add('hidden');

            setTimeout(() => {
                if (window.opener && !window.opener.closed) {
                    window.opener.cargarDatosFormulario();
                }
            }, 1000);

        } else {
            alerta.className = "p-5 rounded-xl bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-400 text-red-800 flex items-center shadow-lg";
            alerta.innerHTML = `
                <div class="w-10 h-10 bg-red-500 rounded-xl flex items-center justify-center mr-3 shadow-md">
                    <span class="material-icons text-white">error</span>
                </div>
                <span class="font-bold">${res.message ?? "Error al guardar"}</span>
            `;
            alerta.classList.remove('hidden');

            if (res.errors) {
                errorContainer.classList.remove('hidden');
                let erroresHtml = '<ul class="list-disc list-inside space-y-2">';

                for (let campo in res.errors) {
                    const inputElement = document.getElementById(campo);
                    if (inputElement) {
                        inputElement.classList.remove('border-gray-300');
                        inputElement.classList.add('border-red-500');

                        const errorMsg = document.createElement('p');
                        errorMsg.className = 'text-red-600 text-sm mt-2 font-semibold error-message flex items-center gap-1';
                        errorMsg.innerHTML = `
                            <span class="material-icons text-sm">error</span>
                            ${res.errors[campo][0]}
                        `;
                        inputElement.parentElement.appendChild(errorMsg);
                    }

                    res.errors[campo].forEach(error => {
                        erroresHtml += `<li class="font-medium">${error}</li>`;
                    });
                }

                erroresHtml += '</ul>';
                errorContainer.innerHTML = `
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-red-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                            <span class="material-icons text-white">warning</span>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-lg mb-3">Por favor corrige los siguientes errores:</p>
                            ${erroresHtml}
                        </div>
                    </div>
                `;
            }
        }

        alerta.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
</script>
@endpush

@endsection
