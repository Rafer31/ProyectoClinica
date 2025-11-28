@extends('enfermera.layouts.enfermera')
@section('title', 'Lista de Pacientes')
@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('enfermera.home')],
            ['label' => 'Pacientes']
        ];
    @endphp

    @push('scripts')
        <script>
            let pacientesData = [];
            let pacientesFiltrados = [];
            let paginaActual = 1;
            const itemsPorPagina = 10;

            document.addEventListener("DOMContentLoaded", function () {
                cargarPacientes();

                // Event listeners para búsqueda y filtros
                document.getElementById('busqueda').addEventListener('input', filtrarPacientes);
                document.getElementById('filtroEstado').addEventListener('change', filtrarPacientes);
                document.getElementById('filtroTipo').addEventListener('change', filtrarPacientes);
            });

            async function cargarPacientes() {
                mostrarLoader(true);

                try {
                    const response = await fetch('/api/personal/pacientes');
                    const data = await response.json();

                    if (data.success) {
                        pacientesData = data.data;
                        pacientesFiltrados = [...pacientesData];
                        paginaActual = 1;
                        renderPacientes();
                        actualizarEstadisticas();
                    } else {
                        mostrarAlerta('error', data.message);
                    }
                } catch (error) {
                    console.error('Error en la API:', error);
                    mostrarAlerta('error', 'Error al cargar los pacientes');
                } finally {
                    mostrarLoader(false);
                }
            }

            function filtrarPacientes() {
                const busqueda = document.getElementById('busqueda').value.toLowerCase();
                const filtroEstado = document.getElementById('filtroEstado').value;
                const filtroTipo = document.getElementById('filtroTipo').value;

                pacientesFiltrados = pacientesData.filter(p => {
                    const nombreCompleto = `${p.nomPa || ''} ${p.paternoPa || ''} ${p.maternoPa || ''}`.toLowerCase();
                    const cumpleBusqueda = nombreCompleto.includes(busqueda) ||
                        (p.nroHCI || '').toLowerCase().includes(busqueda);

                    const cumpleEstado = !filtroEstado || p.estado === filtroEstado;
                    const cumpleTipo = !filtroTipo || p.tipoPac === filtroTipo;

                    return cumpleBusqueda && cumpleEstado && cumpleTipo;
                });

                paginaActual = 1;
                renderPacientes();
            }

            function renderPacientes() {
                const tbody = document.getElementById('tabla-pacientes');
                const tablaContainer = document.getElementById('tabla-container');
                const noData = document.getElementById('no-data');
                const resultadosCount = document.getElementById('resultados-count');

                tbody.innerHTML = "";

                if (pacientesFiltrados.length > 0) {
                    tablaContainer.classList.remove('hidden');
                    noData.classList.add('hidden');

                    // Calcular paginación
                    const totalPaginas = Math.ceil(pacientesFiltrados.length / itemsPorPagina);
                    const inicio = (paginaActual - 1) * itemsPorPagina;
                    const fin = inicio + itemsPorPagina;
                    const pacientesPagina = pacientesFiltrados.slice(inicio, fin);

                    resultadosCount.textContent = `Mostrando ${inicio + 1}-${Math.min(fin, pacientesFiltrados.length)} de ${pacientesFiltrados.length} paciente(s)`;

                    pacientesPagina.forEach((p) => {
                        const nombreCompleto = `${p.nomPa || ''} ${p.paternoPa || ''} ${p.maternoPa || ''}`.trim();
                        const estadoClass = p.estado === 'activo' ? 'bg-pink-100 text-pink-700 border-pink-300' : 'bg-red-100 text-red-700 border-red-300';
                        const tipoClass = p.tipoPac === 'SUS' ? 'bg-blue-100 text-blue-700 border-blue-300' : 'bg-gray-100 text-gray-700 border-gray-300';

                        const edad = p.fechaNac ? calcularEdad(p.fechaNac) : '-';
                        const sexoIcon = p.sexo === 'M' ? 'male' : p.sexo === 'F' ? 'female' : 'person';
                        const sexoBg = p.sexo === 'M' ? 'bg-blue-100' : 'bg-pink-100';
                        const sexoColor = p.sexo === 'M' ? 'text-blue-600' : 'text-pink-600';

                        const fila = `
                            <tr class="bg-white border-b hover:bg-pink-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            <div class="h-12 w-12 rounded-xl ${sexoBg} flex items-center justify-center shadow-sm">
                                                <span class="material-icons ${sexoColor}">${sexoIcon}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">${nombreCompleto}</div>
                                            <div class="text-xs text-pink-600 font-medium">${p.nroHCI || 'Sin HCI'}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">${edad} años</div>
                                    <div class="text-xs text-gray-500">${p.fechaNac ? new Date(p.fechaNac).toLocaleDateString('es-ES') : 'Sin fecha'}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full ${tipoClass} border">
                                        ${p.tipoPac || 'N/A'}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full ${estadoClass} border">
                                        ${p.estado || 'N/A'}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <div class="flex items-center gap-2">
                                        <button onclick="verHistorial(${p.codPa})"
                                            class="inline-flex items-center px-3 py-2 text-sm font-semibold text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100 transition-all border border-purple-200 hover:shadow-md"
                                            title="Ver Historial">
                                            <span class="material-icons text-base mr-1">history</span>
                                            Historial
                                        </button>
                                        <button onclick="verDetalle(${p.codPa})"
                                            class="inline-flex items-center px-3 py-2 text-sm font-semibold text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all border border-blue-200 hover:shadow-md"
                                            title="Ver detalles">
                                            <span class="material-icons text-base mr-1">visibility</span>
                                            Ver
                                        </button>
                                        <a href="/enfermera/pacientes/editar/${p.codPa}"
                                            class="inline-flex items-center px-3 py-2 text-sm font-semibold text-pink-700 bg-pink-50 rounded-lg hover:bg-pink-100 transition-all border border-pink-200 hover:shadow-md"
                                            title="Editar">
                                            <span class="material-icons text-base mr-1">edit</span>
                                            Editar
                                        </a>
                                        <button onclick="cambiarEstado(${p.codPa}, '${p.estado}')"
                                            class="inline-flex items-center px-3 py-2 text-sm font-semibold ${p.estado === 'activo' ? 'text-orange-700 bg-orange-50 hover:bg-orange-100 border-orange-200' : 'text-pink-700 bg-pink-50 hover:bg-pink-100 border-pink-200'} rounded-lg transition-all border hover:shadow-md"
                                            title="${p.estado === 'activo' ? 'Desactivar' : 'Activar'}">
                                            <span class="material-icons text-base mr-1">${p.estado === 'activo' ? 'block' : 'check_circle'}</span>
                                            ${p.estado === 'activo' ? 'Desactivar' : 'Activar'}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tbody.innerHTML += fila;
                    });

                    // Renderizar paginación
                    renderPaginacion(totalPaginas);
                } else {
                    tablaContainer.classList.add('hidden');
                    noData.classList.remove('hidden');
                    resultadosCount.textContent = 'No se encontraron resultados';
                }
            }

            function renderPaginacion(totalPaginas) {
                const paginacionContainer = document.getElementById('paginacion');
                paginacionContainer.innerHTML = '';

                if (totalPaginas <= 1) return;

                // Botón anterior
                const btnAnterior = `
                    <button onclick="cambiarPagina(${paginaActual - 1})"
                        ${paginaActual === 1 ? 'disabled' : ''}
                        class="px-4 py-2 text-sm font-semibold rounded-lg border transition-all
                            ${paginaActual === 1
                                ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                                : 'bg-white text-pink-700 border-pink-300 hover:bg-pink-50 hover:shadow-md'}">
                        <span class="material-icons text-sm">chevron_left</span>
                    </button>
                `;

                // Números de página
                let numeros = '';
                const rango = 2;
                for (let i = 1; i <= totalPaginas; i++) {
                    if (i === 1 || i === totalPaginas || (i >= paginaActual - rango && i <= paginaActual + rango)) {
                        numeros += `
                            <button onclick="cambiarPagina(${i})"
                                class="px-4 py-2 text-sm font-bold rounded-lg border transition-all
                                    ${i === paginaActual
                                        ? 'bg-gradient-to-r from-pink-500 to-rose-600 text-white border-pink-600 shadow-md'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-pink-50 hover:border-pink-300 hover:shadow-md'}">
                                ${i}
                            </button>
                        `;
                    } else if (i === paginaActual - rango - 1 || i === paginaActual + rango + 1) {
                        numeros += '<span class="px-2 text-gray-500">...</span>';
                    }
                }

                // Botón siguiente
                const btnSiguiente = `
                    <button onclick="cambiarPagina(${paginaActual + 1})"
                        ${paginaActual === totalPaginas ? 'disabled' : ''}
                        class="px-4 py-2 text-sm font-semibold rounded-lg border transition-all
                            ${paginaActual === totalPaginas
                                ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                                : 'bg-white text-pink-700 border-pink-300 hover:bg-pink-50 hover:shadow-md'}">
                        <span class="material-icons text-sm">chevron_right</span>
                    </button>
                `;

                paginacionContainer.innerHTML = `
                    <div class="flex items-center justify-center gap-2">
                        ${btnAnterior}
                        ${numeros}
                        ${btnSiguiente}
                    </div>
                `;
            }

            function cambiarPagina(nuevaPagina) {
                const totalPaginas = Math.ceil(pacientesFiltrados.length / itemsPorPagina);
                if (nuevaPagina >= 1 && nuevaPagina <= totalPaginas) {
                    paginaActual = nuevaPagina;
                    renderPacientes();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }

            function calcularEdad(fechaNac) {
                const hoy = new Date();
                const nacimiento = new Date(fechaNac);
                let edad = hoy.getFullYear() - nacimiento.getFullYear();
                const mes = hoy.getMonth() - nacimiento.getMonth();

                if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
                    edad--;
                }

                return edad >= 0 ? edad : 0;
            }

            function actualizarEstadisticas() {
                const total = pacientesData.length;
                const activos = pacientesData.filter(p => p.estado === 'activo').length;
                const sus = pacientesData.filter(p => p.tipoPac === 'SUS').length;

                document.getElementById('stat-total').textContent = total;
                document.getElementById('stat-activos').textContent = activos;
                document.getElementById('stat-sus').textContent = sus;
            }

            async function cambiarEstado(id, estadoActual) {
                const paciente = pacientesData.find(p => p.codPa === id);
                if (!paciente) return;

                const nombreCompleto = `${paciente.nomPa || ''} ${paciente.paternoPa || ''} ${paciente.maternoPa || ''}`.trim();
                const nuevoEstado = estadoActual === 'activo' ? 'inactivo' : 'activo';
                const accion = nuevoEstado === 'activo' ? 'activar' : 'desactivar';

                const modal = document.getElementById('modalCambiarEstado');
                document.getElementById('nombrePacienteEstado').textContent = nombreCompleto;
                document.getElementById('accionEstado').textContent = accion;
                document.getElementById('estadoActualPaciente').textContent = estadoActual;
                document.getElementById('estadoNuevoPaciente').textContent = nuevoEstado;

                const iconoEstado = document.getElementById('iconoEstadoModal');
                const headerModal = document.getElementById('headerModalEstado');

                if (nuevoEstado === 'activo') {
                    iconoEstado.textContent = 'check_circle';
                    headerModal.className = 'bg-gradient-to-r from-pink-500 to-rose-600 rounded-t-xl p-4';
                } else {
                    iconoEstado.textContent = 'block';
                    headerModal.className = 'bg-gradient-to-r from-orange-500 to-red-600 rounded-t-xl p-4';
                }

                modal.classList.remove('hidden');
                modal.dataset.pacienteId = id;
            }

            function cerrarModalEstado() {
                const modal = document.getElementById('modalCambiarEstado');
                modal.classList.add('hidden');
                delete modal.dataset.pacienteId;
            }

            async function confirmarCambioEstado() {
                const modal = document.getElementById('modalCambiarEstado');
                const id = modal.dataset.pacienteId;

                if (!id) return;

                modal.classList.add('hidden');
                mostrarLoader(true);

                try {
                    const response = await fetch(`/api/personal/pacientes/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        mostrarAlerta('success', data.message);
                        cargarPacientes();
                    } else {
                        mostrarAlerta('error', data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('error', 'Error al cambiar el estado del paciente');
                } finally {
                    mostrarLoader(false);
                    delete modal.dataset.pacienteId;
                }
            }

            function verDetalle(id) {
                const paciente = pacientesData.find(p => p.codPa === id);
                if (!paciente) return;

                const nombreCompleto = `${paciente.nomPa || ''} ${paciente.paternoPa || ''} ${paciente.maternoPa || ''}`.trim();
                const edad = paciente.fechaNac ? calcularEdad(paciente.fechaNac) : 'N/A';
                const sexoIcon = paciente.sexo === 'M' ? 'male' : 'female';
                const sexoBg = paciente.sexo === 'M' ? 'bg-blue-500' : 'bg-pink-500';

                const modal = document.getElementById('modalDetalle');
                const contenido = `
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="h-20 w-20 rounded-2xl ${sexoBg} flex items-center justify-center shadow-lg">
                                <span class="material-icons text-white text-4xl">${sexoIcon}</span>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">${nombreCompleto}</h3>
                                <p class="text-pink-600 font-semibold">${paciente.nroHCI || 'Sin HCI'}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Edad</p>
                                <p class="text-gray-900 font-bold text-lg">${edad} años</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Sexo</p>
                                <p class="text-gray-900 font-bold text-lg">${paciente.sexo === 'M' ? 'Masculino' : 'Femenino'}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Fecha de Nacimiento</p>
                                <p class="text-gray-900 font-bold">${paciente.fechaNac ? new Date(paciente.fechaNac).toLocaleDateString('es-ES') : 'N/A'}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Tipo de Paciente</p>
                                <p class="text-gray-900 font-bold">${paciente.tipoPac || 'N/A'}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg col-span-2">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Estado</p>
                                <span class="px-3 py-1.5 text-sm font-bold rounded-full border ${paciente.estado === 'activo' ? 'bg-pink-100 text-pink-700 border-pink-300' : 'bg-red-100 text-red-700 border-red-300'}">
                                    ${paciente.estado}
                                </span>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <button onclick="cerrarModal()" class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all">
                                <span class="material-icons text-base mr-1">close</span>
                                Cerrar
                            </button>
                            <a href="/enfermera/pacientes/editar/${paciente.codPa}" class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-pink-500 to-rose-600 rounded-lg hover:from-pink-600 hover:to-teal-700 transition-all shadow-md">
                                <span class="material-icons text-base mr-1">edit</span>
                                Editar
                            </a>
                        </div>
                    </div>
                `;

                document.getElementById('modalContenido').innerHTML = contenido;
                modal.classList.remove('hidden');
            }

            function cerrarModal() {
                document.getElementById('modalDetalle').classList.add('hidden');
            }

            async function verHistorial(id) {
                console.log('verHistorial called with id:', id);
                
                const modal = document.getElementById('modalHistorial');
                const tbody = document.getElementById('tabla-historial');
                const loader = document.getElementById('loader-historial');
                const contenido = document.getElementById('contenido-historial');
                const pacienteNombre = document.getElementById('nombrePacienteHistorial');
                
                console.log('Modal elements:', { modal, tbody, loader, contenido, pacienteNombre });
                
                if (!modal) {
                    console.error('Modal no encontrado!');
                    alert('Error: No se pudo abrir el modal de historial');
                    return;
                }
                
                // Buscar nombre del paciente
                const paciente = pacientesData.find(p => p.codPa === id);
                if (paciente && pacienteNombre) {
                    pacienteNombre.textContent = `${paciente.nomPa || ''} ${paciente.paternoPa || ''} ${paciente.maternoPa || ''}`;
                }

                modal.classList.remove('hidden');
                modal.style.display = 'flex'; // Forzar display flex
                console.log('Modal después de remover hidden:', modal.classList.contains('hidden'));
                console.log('Modal display style:', window.getComputedStyle(modal).display);
                
                if (loader) loader.classList.remove('hidden');
                if (contenido) contenido.classList.add('hidden');
                if (tbody) tbody.innerHTML = '';

                try {
                    const response = await fetch(`/api/personal/pacientes/${id}/historial`);
                    console.log('Response status:', response.status);
                    const data = await response.json();
                    console.log('Response data:', data);

                    if (data.success) {
                        renderHistorial(data.data);
                    } else {
                        mostrarAlerta('error', data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('error', 'Error al cargar el historial');
                } finally {
                    if (loader) loader.classList.add('hidden');
                    if (contenido) contenido.classList.remove('hidden');
                }
            }

            function renderHistorial(servicios) {
                const tbody = document.getElementById('tabla-historial');
                
                if (servicios.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="material-icons text-gray-300 text-4xl">history_toggle_off</span>
                                    <p>No hay historial de atenciones registrado</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }

                tbody.innerHTML = servicios.map(s => {
                    const fecha = s.fechaAten ? new Date(s.fechaAten).toLocaleDateString('es-ES') : 'Pendiente';
                    const medico = s.medico ? `${s.medico.nomMed} ${s.medico.paternoMed}` : 'No asignado';
                    const estudio = (s.tipo_estudio || s.tipoEstudio) ? (s.tipo_estudio?.descripcion || s.tipoEstudio?.descripcion) : 'N/A';
                    
                    let estadoClass = '';
                    switch(s.estado) {
                        case 'Atendido': estadoClass = 'bg-pink-100 text-pink-700 border-pink-300'; break;
                        case 'Programado': estadoClass = 'bg-amber-100 text-amber-700 border-amber-300'; break;
                        case 'EnProceso': estadoClass = 'bg-blue-100 text-blue-700 border-blue-300'; break;
                        default: estadoClass = 'bg-gray-100 text-gray-700 border-gray-300';
                    }

                    return `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">${fecha}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">${estudio}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">${medico}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-bold rounded-full border ${estadoClass}">
                                    ${s.estado}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                ${s.diagnosticos && s.diagnosticos.length > 0 
                                    ? s.diagnosticos.map(d => d.descripDiag).join(', ').substring(0, 50) + (s.diagnosticos[0].descripDiag.length > 50 ? '...' : '')
                                    : '<span class="italic text-gray-400">Sin diagnóstico</span>'}
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            function cerrarModalHistorial() {
                const modal = document.getElementById('modalHistorial');
                modal.classList.add('hidden');
                modal.style.display = ''; // Limpiar el estilo inline
            }

            function mostrarAlerta(tipo, mensaje) {
                const alerta = document.getElementById('alerta');
                const iconos = {
                    success: 'check_circle',
                    error: 'error',
                    info: 'info'
                };
                const colores = {
                    success: 'bg-pink-50 border-pink-300 text-emerald-800',
                    error: 'bg-red-50 border-red-300 text-red-800',
                    info: 'bg-blue-50 border-blue-300 text-blue-800'
                };

                alerta.className = `p-4 rounded-xl border-2 flex items-center ${colores[tipo]} mb-4 shadow-md`;
                alerta.innerHTML = `
                    <span class="material-icons mr-2 text-xl">${iconos[tipo]}</span>
                    <span class="font-semibold">${mensaje}</span>
                `;
                alerta.classList.remove('hidden');

                setTimeout(() => {
                    alerta.classList.add('hidden');
                }, 5000);
            }

            function mostrarLoader(mostrar) {
                const loader = document.getElementById('loader');
                if (mostrar) {
                    loader.classList.remove('hidden');
                } else {
                    loader.classList.add('hidden');
                }
            }

            function limpiarFiltros() {
                document.getElementById('busqueda').value = '';
                document.getElementById('filtroEstado').value = '';
                document.getElementById('filtroTipo').value = '';
                filtrarPacientes();
            }
        </script>
    @endpush

    <div class="space-y-6">
        <!-- Alerta -->
        <div id="alerta" class="hidden"></div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0 bg-white bg-opacity-20 rounded-xl p-4 backdrop-blur-sm">
                        <span class="material-icons text-4xl">people</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium opacity-90 mb-1">Total Pacientes</p>
                        <p class="text-4xl font-bold" id="stat-total">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0 bg-white bg-opacity-20 rounded-xl p-4 backdrop-blur-sm">
                        <span class="material-icons text-4xl">check_circle</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium opacity-90 mb-1">Pacientes Activos</p>
                        <p class="text-4xl font-bold" id="stat-activos">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0 bg-white bg-opacity-20 rounded-xl p-4 backdrop-blur-sm">
                        <span class="material-icons text-4xl">medical_services</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium opacity-90 mb-1">Con SUS</p>
                        <p class="text-4xl font-bold" id="stat-sus">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Encabezado y Filtros -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="material-icons text-white text-2xl">people</span>
                        </div>
                        Lista de Pacientes
                    </h1>
                    <p class="text-pink-600 font-medium ml-15" id="resultados-count">Cargando...</p>
                </div>
                <a href="{{ route('enfermera.pacientes.agregar') }}"
                    class="mt-4 md:mt-0 inline-flex items-center px-5 py-3 bg-gradient-to-r from-pink-500 to-rose-600 text-white rounded-xl hover:from-pink-600 hover:to-teal-700 transition-all shadow-md hover:shadow-lg font-semibold transform hover:scale-105">
                    <span class="material-icons mr-2">add</span>
                    Agregar Paciente
                </a>
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Buscar</label>
                    <div class="relative">
                        <span class="material-icons absolute left-3 top-3 text-gray-400">search</span>
                        <input type="text" id="busqueda" placeholder="Buscar por nombre o HCI..."
                            class="pl-10 w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-pink-500 focus:border-pink-500 p-3 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                    <select id="filtroEstado"
                        class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-pink-500 focus:border-pink-500 p-3 transition-all">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo</label>
                    <select id="filtroTipo"
                        class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-pink-500 focus:border-pink-500 p-3 transition-all">
                        <option value="">Todos</option>
                        <option value="SUS">SUS</option>
                        <option value="SINSUS">SIN SUS</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button onclick="limpiarFiltros()" class="inline-flex items-center text-sm text-pink-600 hover:text-emerald-800 font-semibold transition-colors">
                    <span class="material-icons text-sm mr-1">clear</span>
                    Limpiar filtros
                </button>
            </div>
        </div>

        <!-- Loader -->
        <div id="loader" class="flex justify-center items-center py-12">
            <div class="flex flex-col items-center gap-3">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-pink-200 border-t-pink-600"></div>
                <span class="text-sm font-medium text-gray-600">Cargando pacientes...</span>
            </div>
        </div>

        <!-- Mensaje sin datos -->
        <div id="no-data" class="hidden bg-white rounded-xl shadow-sm border border-gray-100 p-12">
            <div class="flex flex-col items-center justify-center">
                <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <span class="material-icons text-gray-300" style="font-size: 80px;">folder_open</span>
                </div>
                <p class="text-gray-800 text-xl font-bold mb-2">No hay pacientes registrados</p>
                <p class="text-gray-500 text-sm">Comienza agregando tu primer paciente al sistema</p>
            </div>
        </div>

        <!-- Tabla de pacientes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" id="tabla-container">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold">Paciente</th>
                            <th scope="col" class="px-6 py-4 font-bold">Edad</th>
                            <th scope="col" class="px-6 py-4 font-bold">Tipo</th>
                            <th scope="col" class="px-6 py-4 font-bold">Estado</th>
                            <th scope="col" class="px-6 py-4 font-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-pacientes"></tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div id="paginacion"></div>
            </div>
        </div>
    </div>

    <!-- Modal Detalle -->
    <div id="modalDetalle" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-0 border w-full max-w-2xl shadow-2xl rounded-xl bg-white">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="material-icons text-pink-600">badge</span>
                    Detalle del Paciente
                </h3>
                <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div id="modalContenido" class="p-6"></div>
        </div>
    </div>

    <!-- Modal Cambiar Estado -->
    <div id="modalCambiarEstado" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-0 border w-full max-w-md shadow-2xl rounded-xl bg-white">
            <!-- Header -->
            <div id="headerModalEstado" class="bg-gradient-to-r from-orange-500 to-red-600 rounded-t-xl p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-white bg-opacity-20 rounded-xl p-3">
                        <span id="iconoEstadoModal" class="material-icons text-white text-4xl">sync</span>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold text-white">Confirmar Cambio de Estado</h3>
                        <p class="text-sm text-white opacity-90">Esta acción modificará el estado del paciente</p>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="p-6">
                <p class="text-gray-700 mb-4 font-medium">
                    ¿Estás seguro de que deseas <strong class="text-orange-600" id="accionEstado"></strong> al siguiente paciente?
                </p>

                <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-xl p-4 mb-4 border-l-4 border-orange-500">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                            <span class="material-icons text-white">person</span>
                        </div>
                        <p class="font-bold text-gray-900 text-lg" id="nombrePacienteEstado"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white rounded-lg p-3">
                            <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Estado actual</p>
                            <p class="text-sm font-bold text-gray-900" id="estadoActualPaciente"></p>
                        </div>
                        <div class="bg-white rounded-lg p-3">
                            <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Nuevo estado</p>
                            <p class="text-sm font-bold text-gray-900" id="estadoNuevoPaciente"></p>
                        </div>
                    </div>
                </div>

                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                    <p class="text-sm text-orange-800 flex items-start font-medium">
                        <span class="material-icons text-orange-600 mr-2 text-lg">info</span>
                        <span>Este cambio se aplicará inmediatamente en el sistema.</span>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex gap-3">
                <button onclick="cerrarModalEstado()"
                    class="flex-1 inline-flex justify-center items-center px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition-all">
                    <span class="material-icons text-base mr-1">close</span>
                    Cancelar
                </button>
                <button onclick="confirmarCambioEstado()"
                    class="flex-1 inline-flex justify-center items-center px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-orange-500 to-red-600 rounded-lg hover:from-orange-600 hover:to-red-700 transition-all shadow-md">
                    <span class="material-icons text-base mr-1">check</span>
                    Confirmar
                </button>
            </div>
            </div>
        </div>
    </div>

    <!-- Modal Historial -->
    <div id="modalHistorial" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-0 border w-full max-w-4xl shadow-2xl rounded-xl bg-white">
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-t-xl p-5 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <span class="material-icons text-white text-3xl">history</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Historial de Atenciones</h3>
                        <p class="text-purple-100 text-sm" id="nombrePacienteHistorial">Paciente</p>
                    </div>
                </div>
                <button onclick="cerrarModalHistorial()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-1 transition-colors">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6">
                <!-- Loader -->
                <div id="loader-historial" class="flex justify-center items-center py-12 hidden">
                    <div class="flex flex-col items-center gap-3">
                        <div class="animate-spin rounded-full h-10 w-10 border-4 border-purple-200 border-t-purple-600"></div>
                        <span class="text-sm font-medium text-gray-600">Cargando historial...</span>
                    </div>
                </div>

                <!-- Contenido -->
                <div id="contenido-historial">
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-bold">
                                <tr>
                                    <th class="px-6 py-3">Fecha</th>
                                    <th class="px-6 py-3">Estudio</th>
                                    <th class="px-6 py-3">Médico</th>
                                    <th class="px-6 py-3">Estado</th>
                                    <th class="px-6 py-3">Diagnóstico</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-historial" class="divide-y divide-gray-200">
                                <!-- Se llena dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
