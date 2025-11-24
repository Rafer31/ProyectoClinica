@extends('enfermera.layouts.enfermera')
@section('title', 'Agregar Paciente')
@section('content')

<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-gradient-to-r from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="material-icons text-white text-2xl">person_add</span>
                    </div>
                    Agregar Nuevo Paciente
                </h1>
                <p class="text-gray-600 ml-15 font-medium">Complete el formulario para registrar un nuevo paciente</p>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    <div id="alerta" class="hidden"></div>
    <div id="errorContainer" class="hidden p-5 rounded-xl bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-200 text-red-800 shadow-lg"></div>

    <!-- Formulario -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8">
        <form id="formPaciente" class="space-y-8">
            @csrf

            <!-- Información Personal -->
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2 border-b-2 border-blue-200 pb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-md">
                        <span class="material-icons text-white text-lg">person</span>
                    </div>
                    Información Personal
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Nombre <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="nomPa" required
                            class="bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition-all font-medium"
                            placeholder="Ej: Juan">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Apellido Paterno
                        </label>
                        <input type="text" id="paternoPa"
                            class="bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition-all font-medium"
                            placeholder="Ej: Pérez">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Apellido Materno
                        </label>
                        <input type="text" id="maternoPa"
                            class="bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition-all font-medium"
                            placeholder="Ej: García">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Sexo <span class="text-red-600">*</span>
                        </label>
                        <select id="sexo" required
                            class="bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition-all font-medium">
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Fecha de Nacimiento
                        </label>
                        <input type="date" id="fechaNac"
                            class="bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition-all font-medium">
                        <span id="edadCalculada" class="hidden"></span>
                    </div>
                </div>
            </div>

            <!-- Información Clínica -->
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2 border-b-2 border-blue-200 pb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-md">
                        <span class="material-icons text-white text-lg">medical_services</span>
                    </div>
                    Información Clínica
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Número de HCI
                        </label>
                        <input type="text" id="nroHCI"
                            class="bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition-all font-medium"
                            placeholder="Ej: HCI-2024-001">
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                            <span class="material-icons text-xs">info</span>
                            Debe ser único para cada paciente
                        </p>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">
                            Tipo de Paciente <span class="text-red-600">*</span>
                        </label>
                        <select id="tipoPac" required
                            class="bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition-all font-medium">
                            <option value="SUS">SUS (Seguro Universal de Salud)</option>
                            <option value="SINSUS">SIN SUS (Sin Seguro)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 pt-8 border-t-2 border-gray-200">
                <button type="button" onclick="window.close()"
                    class="px-6 py-3 text-sm font-bold text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 transition-all shadow-sm hover:shadow-md flex items-center gap-2">
                    <span class="material-icons text-sm">close</span>
                    Cancelar
                </button>

                <button type="submit"
                    class="px-6 py-3 text-sm font-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <span class="material-icons text-sm">add_circle</span>
                    Guardar Paciente
                </button>
            </div>
        </form>
    </div>

    <!-- Información Adicional -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-xl p-6 shadow-lg">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                <span class="material-icons text-white text-xl">info</span>
            </div>
            <div class="text-sm text-blue-900">
                <p class="font-bold text-base mb-3">Información importante:</p>
                <ul class="list-disc list-inside space-y-2 font-medium">
                    <li>Los campos marcados con <span class="text-red-600 font-bold">*</span> son obligatorios</li>
                    <li>El número de Historia Clínica (HCI) debe ser único</li>
                    <li>Una vez guardado, cierre esta ventana y actualice los datos en la ventana principal</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('formPaciente').addEventListener('submit', async function(e) {
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
            nomPa: document.getElementById('nomPa').value.trim(),
            paternoPa: document.getElementById('paternoPa').value.trim() || null,
            maternoPa: document.getElementById('maternoPa').value.trim() || null,
            sexo: document.getElementById('sexo').value,
            fechaNac: document.getElementById('fechaNac').value || null,
            nroHCI: document.getElementById('nroHCI').value.trim() || null,
            tipoPac: document.getElementById('tipoPac').value,
        };

        try {
            const response = await fetch('/api/enfermera/pacientes', {
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

    document.getElementById('nroHCI').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
</script>
@endpush

@endsection
