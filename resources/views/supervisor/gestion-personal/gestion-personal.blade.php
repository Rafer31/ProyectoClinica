@extends('supervisor.layouts.supervisor')
@section('title', 'Gestión de Personal de Salud')
@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('supervisor.home')],
            ['label' => 'Gestión de Personal']
        ];
    @endphp

    @push('scripts')
        <script>
            let personalData = [];
            let personalFiltrado = [];
            let asignacionesActivas = {};
            let rolesData = [];

            let consultoriosDisponibles = [];
            document.addEventListener("DOMContentLoaded", function () {
                cargarPersonal();
                cargarRoles(); // NUEVO: cargar roles al iniciar

                document.getElementById('busqueda').addEventListener('input', filtrarPersonal);
                document.getElementById('filtroRol').addEventListener('change', filtrarPersonal);
                document.getElementById('filtroEstado').addEventListener('change', filtrarPersonal);
            });
            async function cargarRoles() {
                try {
                    const response = await fetch('/api/roles');
                    const data = await response.json();

                    if (data.success) {
                        rolesData = data.data;
                        const select = document.getElementById('filtroRol');
                        select.innerHTML = '<option value="">Todos los roles</option>';

                        rolesData.forEach(rol => {
                            const option = document.createElement('option');
                            option.value = rol.codRol;
                            option.textContent = rol.nombreRol;
                            select.appendChild(option);
                        });
                    }
                } catch (error) {
                    console.error('Error al cargar roles:', error);
                }
            }
            async function cargarPersonal() {
                mostrarLoader(true);

                try {
                    // Cargar personal y TODAS las asignaciones (no solo activas)
                    const [responsePersonal, responseAsignaciones] = await Promise.all([
                        fetch('/api/personal-salud'),
                        fetch('/api/asignaciones-consultorio') // Cambiar esta ruta para obtener TODAS
                    ]);

                    const dataPersonal = await responsePersonal.json();
                    const dataAsignaciones = await responseAsignaciones.json();

                    console.log('Personal cargado:', dataPersonal);
                    console.log('Todas las asignaciones cargadas:', dataAsignaciones);

                    if (dataPersonal.success) {
                        personalData = dataPersonal.data;
                        personalFiltrado = [...personalData];

                        // Crear un mapa con la asignación más reciente por personal
                        if (dataAsignaciones.success) {
                            asignacionesActivas = {};

                            // Ordenar asignaciones por fecha de inicio (más reciente primero)
                            const asignacionesOrdenadas = dataAsignaciones.data.sort((a, b) =>
                                new Date(b.fechaInicio) - new Date(a.fechaInicio)
                            );

                            // Tomar la asignación más reciente para cada personal
                            asignacionesOrdenadas.forEach(asignacion => {
                                if (!asignacionesActivas[asignacion.codPer]) {
                                    asignacionesActivas[asignacion.codPer] = asignacion;
                                }
                            });

                            console.log('Mapa de asignaciones (más recientes):', asignacionesActivas);
                        }

                        renderPersonal(personalFiltrado);
                        actualizarEstadisticas();
                    } else {
                        mostrarAlerta('error', dataPersonal.message);
                    }
                } catch (error) {
                    console.error('Error en la API:', error);
                    mostrarAlerta('error', 'Error al cargar el personal de salud');
                } finally {
                    mostrarLoader(false);
                }
            }

            function filtrarPersonal() {
                const busqueda = document.getElementById('busqueda').value.toLowerCase();
                const filtroRol = document.getElementById('filtroRol').value;
                const filtroEstado = document.getElementById('filtroEstado').value;

                personalFiltrado = personalData.filter(p => {
                    const nombreCompleto = `${p.nomPer || ''} ${p.paternoPer || ''} ${p.maternoPer || ''}`.toLowerCase();
                    const usuario = (p.usuarioPer || '').toLowerCase();
                    const cumpleBusqueda = nombreCompleto.includes(busqueda) || usuario.includes(busqueda);
                    const cumpleRol = !filtroRol || (p.rol && p.rol.codRol == filtroRol);
                    const cumpleEstado = !filtroEstado || p.estado === filtroEstado;

                    return cumpleBusqueda && cumpleRol && cumpleEstado;
                });

                renderPersonal(personalFiltrado);
            }

          function renderPersonal(personal) {
    const tbody = document.getElementById('tabla-personal');
    const tablaContainer = document.getElementById('tabla-container');
    const noData = document.getElementById('no-data');
    const resultadosCount = document.getElementById('resultados-count');

    tbody.innerHTML = "";

    if (personal.length > 0) {
        tablaContainer.classList.remove('hidden');
        noData.classList.add('hidden');
        resultadosCount.textContent = `Mostrando ${personal.length} registro(s)`;

        personal.forEach((p, index) => {
            const nombreCompleto = `${p.nomPer || ''} ${p.paternoPer || ''} ${p.maternoPer || ''}`.trim();
            const rolNombre = p.rol ? p.rol.nombreRol : 'Sin rol';
            const estadoClass = p.estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
            const esSupervisor = p.rol && p.rol.nombreRol.toLowerCase().includes('supervisor');

            // Obtener la asignación más reciente (sin importar si está activa)
            const asignacion = asignacionesActivas[p.codPer];
            console.log(`Personal ${p.codPer} - Asignación:`, asignacion);

            let consultorioHTML = '';
            let estadoAsignacion = '';

            if (asignacion && asignacion.consultorio) {
                const ahora = new Date();
                const fechaInicio = new Date(asignacion.fechaInicio);
                const fechaFin = asignacion.fechaFin ? new Date(asignacion.fechaFin) : null;

                // Determinar el estado de la asignación
                if (fechaFin && ahora > fechaFin) {
                    estadoAsignacion = 'expirada';
                } else if (ahora < fechaInicio) {
                    estadoAsignacion = 'pendiente';
                } else {
                    estadoAsignacion = 'activa';
                }

                // Estilos según el estado de la asignación
                let estiloConsultorio = '';
                let icono = '';
                let textoEstado = '';

                switch(estadoAsignacion) {
                    case 'activa':
                        estiloConsultorio = 'bg-green-100 text-green-800 border-green-200';
                        icono = 'check_circle';
                        textoEstado = 'Activo';
                        break;
                    case 'pendiente':
                        estiloConsultorio = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                        icono = 'schedule';
                        textoEstado = 'Pendiente';
                        break;
                    case 'expirada':
                        estiloConsultorio = 'bg-gray-100 text-gray-600 border-gray-200';
                        icono = 'event_busy';
                        textoEstado = 'Expirado';
                        break;
                }

                consultorioHTML = `
                    <span class="inline-block px-3 py-1.5 text-xs leading-5 font-semibold rounded-lg ${estiloConsultorio} border whitespace-nowrap">
                        <span class="material-icons text-xs mr-1">${icono}</span>
                        Consultorio ${asignacion.consultorio.numCons}
                        <span class="ml-1 text-xs opacity-75">(${textoEstado})</span>
                    </span>
                `;
            } else {
                consultorioHTML = `
                    <span class="inline-block px-3 py-1.5 text-xs leading-5 font-semibold rounded-lg bg-gray-100 text-gray-600 border border-gray-200 whitespace-nowrap">
                        <span class="material-icons text-xs mr-1">block</span>
                        Sin asignar
                    </span>
                `;
            }
                        const fila = `
                                        <tr class="bg-white border-b hover:bg-blue-50 transition-all duration-200">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-12 w-12">
                                                        <div class="h-12 w-12 rounded-xl ${p.estado === 'activo' ? 'bg-gradient-to-br from-blue-500 to-indigo-600' : 'bg-gradient-to-br from-gray-400 to-gray-500'} flex items-center justify-center shadow-lg">
                                                            <span class="material-icons text-white text-xl">person</span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-semibold text-gray-900">${nombreCompleto}</div>
                                                        <div class="text-xs text-gray-500 flex items-center gap-1">
                                                            <span class="material-icons text-xs">badge</span>
                                                            ${p.usuarioPer || 'N/A'}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-block px-3 py-1.5 text-xs leading-5 font-semibold rounded-lg bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border border-purple-200 whitespace-nowrap">
                                                    <span class="material-icons text-xs mr-1">work</span>
                                                    ${rolNombre}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                ${consultorioHTML}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-block px-3 py-1.5 text-xs leading-5 font-semibold rounded-lg ${estadoClass} border ${p.estado === 'activo' ? 'border-green-300' : 'border-red-300'} whitespace-nowrap">
                                                    <span class="material-icons text-xs mr-1">${p.estado === 'activo' ? 'check_circle' : 'block'}</span>
                                                    ${p.estado === 'activo' ? 'Activo' : 'Inactivo'}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                <div class="flex items-center gap-2">
                                                    <button onclick="verDetalle(${p.codPer})"
                                                        class="inline-flex items-center px-3 py-2 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 hover:shadow-md transition-all duration-200 border border-blue-200"
                                                        title="Ver detalles">
                                                        <span class="material-icons text-sm mr-1">visibility</span>
                                                        Ver
                                                    </button>
                                                    <a href="/supervisor/gestion-personal/editar/${p.codPer}"
                                                        class="inline-flex items-center px-3 py-2 text-xs font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 hover:shadow-md transition-all duration-200 border border-green-200"
                                                        title="Editar">
                                                        <span class="material-icons text-sm mr-1">edit</span>
                                                        Editar
                                                    </a>
                                                     <button onclick="abrirModalAsignacion(${p.codPer})"
                                                        class="inline-flex items-center px-3 py-2 text-xs font-medium text-indigo-700 bg-indigo-50 rounded-lg hover:bg-indigo-100 hover:shadow-md transition-all duration-200 border border-indigo-200"
                                                        title="Asignar Consultorio">
                                                        <span class="material-icons text-sm mr-1">meeting_room</span>
                                                        Asignar
                                                    </button>
                                                    ${p.codRol === 2 ? `
                                                    <button onclick="imprimirReportePersonal(${p.codPer})"
                                                        class="inline-flex items-center px-3 py-2 text-xs font-medium text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100 hover:shadow-md transition-all duration-200 border border-purple-200"
                                                        title="Imprimir Reporte">
                                                        <span class="material-icons text-sm mr-1">print</span>
                                                        Reporte
                                                    </button>
                                                    ` : ''}
                                                    ${!esSupervisor ? `
                                                    <button onclick="cambiarEstado(${p.codPer}, '${p.estado}')"
                                                        class="inline-flex items-center px-3 py-2 text-xs font-medium ${p.estado === 'activo' ? 'text-red-700 bg-red-50 hover:bg-red-100 border-red-200' : 'text-green-700 bg-green-50 hover:bg-green-100 border-green-200'} rounded-lg hover:shadow-md transition-all duration-200 border"
                                                        title="${p.estado === 'activo' ? 'Desactivar' : 'Activar'}">
                                                        <span class="material-icons text-sm mr-1">${p.estado === 'activo' ? 'block' : 'check_circle'}</span>
                                                        ${p.estado === 'activo' ? 'Desactivar' : 'Activar'}
                                                    </button>
                                                    ` : ''}
                                                </div>
                                            </td>
                                        </tr>
                                    `;
                        tbody.innerHTML += fila;
                    });
                } else {
                    tablaContainer.classList.add('hidden');
                    noData.classList.remove('hidden');
                    resultadosCount.textContent = 'No se encontraron resultados';
                }
            }


            function actualizarEstadisticas() {
                const total = personalData.length;
                const activos = personalData.filter(p => p.estado === 'activo').length;
                const inactivos = personalData.filter(p => p.estado === 'inactivo').length;

                document.getElementById('stat-total').textContent = total;
                document.getElementById('stat-activos').textContent = activos;
                document.getElementById('stat-inactivos').textContent = inactivos;
            }
            function cambiarEstado(id, estadoActual) {
                const personal = personalData.find(p => p.codPer === id);
                if (!personal) return;

                const nombreCompleto = `${personal.nomPer || ''} ${personal.paternoPer || ''} ${personal.maternoPer || ''}`.trim();
                const nuevoEstado = estadoActual === 'activo' ? 'inactivo' : 'activo';

                const modal = document.getElementById('modalConfirmacionEstado');
                document.getElementById('nombrePersonalConfirm').textContent = nombreCompleto;
                document.getElementById('usuarioPersonalConfirm').textContent = personal.usuarioPer || 'N/A';
                document.getElementById('estadoActualConfirm').textContent = estadoActual === 'activo' ? 'activo' : 'inactivo';
                document.getElementById('estadoNuevoConfirm').textContent = nuevoEstado === 'activo' ? 'activar' : 'desactivar';

                const accionTexto = document.getElementById('accionEstadoTextoConfirm');
                const iconoAccion = document.getElementById('iconoAccionEstado');
                const btnConfirmar = document.getElementById('btnConfirmarEstado');

                if (nuevoEstado === 'inactivo') {
                    accionTexto.className = 'text-sm text-red-600 font-medium';
                    accionTexto.textContent = 'Esta persona no podrá acceder al sistema hasta que sea reactivada';
                    iconoAccion.textContent = 'block';
                    iconoAccion.className = 'material-icons text-red-600 text-5xl';
                    btnConfirmar.className = 'flex-1 inline-flex justify-center items-center px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-pink-600 rounded-xl hover:from-red-700 hover:to-pink-700 transition-all duration-200 shadow-lg hover:shadow-xl';
                } else {
                    accionTexto.className = 'text-sm text-green-600 font-medium';
                    accionTexto.textContent = 'Esta persona podrá acceder nuevamente al sistema';
                    iconoAccion.textContent = 'check_circle';
                    iconoAccion.className = 'material-icons text-green-600 text-5xl';
                    btnConfirmar.className = 'flex-1 inline-flex justify-center items-center px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl';
                }

                modal.classList.remove('hidden');
                modal.dataset.personalId = id;
            }
            function cerrarModalConfirmacionEstado() {
                const modal = document.getElementById('modalConfirmacionEstado');
                modal.classList.add('hidden');
                delete modal.dataset.personalId;
            }
            async function confirmarCambioEstadoIndividual() {
                const modal = document.getElementById('modalConfirmacionEstado');
                const id = modal.dataset.personalId;

                if (!id) return;

                modal.classList.add('hidden');
                mostrarLoader(true);

                try {
                    const response = await fetch(`/api/personal-salud/${id}/cambiar-estado`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        mostrarAlerta('success', data.message);
                        cargarPersonal();
                    } else {
                        mostrarAlerta('error', data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('error', 'Error al cambiar el estado');
                } finally {
                    mostrarLoader(false);
                    delete modal.dataset.personalId;
                }
            }
            function cerrarModalEstado() {
                const modal = document.getElementById('modalCambiarEstado');
                modal.classList.add('hidden');
                delete modal.dataset.personalId;
            }

            async function confirmarCambioEstado() {
                const modal = document.getElementById('modalCambiarEstado');
                const id = modal.dataset.personalId;

                if (!id) return;

                modal.classList.add('hidden');
                mostrarLoader(true);

                try {
                    const response = await fetch(`/api/personal-salud/${id}/cambiar-estado`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        mostrarAlerta('success', data.message);
                        cargarPersonal();
                    } else {
                        mostrarAlerta('error', data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('error', 'Error al cambiar el estado');
                } finally {
                    mostrarLoader(false);
                    delete modal.dataset.personalId;
                }
            }

            // Funciones para asignación de consultorio

            async function abrirModalAsignacion(codPer) {
                const personal = personalData.find(p => p.codPer === codPer);
                if (!personal) return;

                const nombreCompleto = `${personal.nomPer || ''} ${personal.paternoPer || ''} ${personal.maternoPer || ''}`.trim();

                document.getElementById('nombrePersonalAsignacion').textContent = nombreCompleto;
                document.getElementById('codPerAsignacion').value = codPer;

                // Cargar TODOS los consultorios inicialmente
                await cargarTodosConsultorios();

                const modal = document.getElementById('modalAsignacion');
                modal.classList.remove('hidden');
            }
            async function cargarTodosConsultorios() {
                try {
                    const response = await fetch('/api/consultorios');
                    const data = await response.json();

                    if (data.success) {
                        consultoriosDisponibles = data.data;
                        const select = document.getElementById('codConsAsignacion');
                        select.innerHTML = '<option value="">Seleccione un consultorio...</option>';

                        consultoriosDisponibles.forEach(cons => {
                            const option = document.createElement('option');
                            option.value = cons.codCons;
                            option.textContent = `Consultorio ${cons.numCons}`;
                            select.appendChild(option);
                        });
                    }
                } catch (error) {
                    console.error('Error al cargar consultorios:', error);
                    mostrarAlerta('error', 'Error al cargar los consultorios');
                }
            }
            document.addEventListener('DOMContentLoaded', function () {
                const fechaInicio = document.getElementById('fechaInicio');
                const fechaFin = document.getElementById('fechaFin');
                const consultorio = document.getElementById('codConsAsignacion');

                if (fechaInicio && fechaFin && consultorio) {
                    fechaInicio.addEventListener('change', validarDisponibilidadConsultorio);
                    fechaFin.addEventListener('change', validarDisponibilidadConsultorio);
                    consultorio.addEventListener('change', validarDisponibilidadConsultorio);
                }
            });
            async function validarDisponibilidadConsultorio() {
                const codCons = document.getElementById('codConsAsignacion').value;
                const fechaInicio = document.getElementById('fechaInicio').value;
                const fechaFin = document.getElementById('fechaFin').value;

                if (!codCons || !fechaInicio) {
                    return; // No validar si falta información
                }

                try {
                    const params = new URLSearchParams({
                        fechaInicio: fechaInicio,
                        ...(fechaFin && { fechaFin: fechaFin })
                    });

                    const response = await fetch(`/api/consultorios/${codCons}/verificar-disponibilidad?${params}`, {
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    const alertaDiv = document.getElementById('alertaDisponibilidad');

                    if (!data.disponible) {
                        if (!alertaDiv) {
                            const alert = document.createElement('div');
                            alert.id = 'alertaDisponibilidad';
                            alert.className = 'bg-red-50 border-2 border-red-200 rounded-xl p-4 flex items-start gap-3 mt-4';
                            alert.innerHTML = `
                                                                            <span class="material-icons text-red-600 mt-0.5">error</span>
                                                                            <div class="text-sm text-red-800">
                                                                                <p class="font-semibold mb-1">Consultorio no disponible</p>
                                                                                <p>${data.message}</p>
                                                                            </div>
                                                                        `;
                            document.getElementById('formAsignacion').insertBefore(
                                alert,
                                document.getElementById('formAsignacion').lastElementChild
                            );
                        }
                    } else {
                        if (alertaDiv) {
                            alertaDiv.remove();
                        }
                    }
                } catch (error) {
                    console.error('Error al validar disponibilidad:', error);
                }
            }

            async function cargarConsultorios() {
                try {
                    const response = await fetch('/api/consultorios');
                    const data = await response.json();

                    if (data.success) {
                        consultoriosDisponibles = data.data;
                        const select = document.getElementById('codConsAsignacion');
                        select.innerHTML = '<option value="">Seleccione un consultorio...</option>';

                        consultoriosDisponibles.forEach(cons => {
                            const option = document.createElement('option');
                            option.value = cons.codCons;
                            option.textContent = `Consultorio ${cons.numCons}`;
                            select.appendChild(option);
                        });
                    }
                } catch (error) {
                    console.error('Error al cargar consultorios:', error);
                }
            }

            function cerrarModalAsignacion() {
                const modal = document.getElementById('modalAsignacion');
                modal.classList.add('hidden');
                document.getElementById('formAsignacion').reset();

                const alertaDiv = document.getElementById('alertaDisponibilidad');
                if (alertaDiv) {
                    alertaDiv.remove();
                }
            }


            async function guardarAsignacion() {
                const codPer = document.getElementById('codPerAsignacion').value;
                const codCons = document.getElementById('codConsAsignacion').value;
                const fechaInicio = document.getElementById('fechaInicio').value;
                const fechaFin = document.getElementById('fechaFin').value;

                if (!codCons || !fechaInicio) {
                    mostrarAlerta('error', 'Por favor complete todos los campos obligatorios');
                    return;
                }

                if (fechaFin && fechaInicio && fechaFin < fechaInicio) {
                    mostrarAlerta('error', 'La fecha fin debe ser posterior o igual a la fecha de inicio');
                    return;
                }

                mostrarLoader(true);
                cerrarModalAsignacion();

                try {
                    const response = await fetch('/api/asignaciones-consultorio', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            codPer: parseInt(codPer),
                            codCons: parseInt(codCons),
                            fechaInicio: fechaInicio,
                            fechaFin: fechaFin || null
                        })
                    });

                    const data = await response.json();
                    console.log('Respuesta de guardar asignación:', data); // Debug

                    if (data.success) {
                        mostrarAlerta('success', 'Consultorio asignado exitosamente');
                        // Recargar la lista completa
                        await cargarPersonal();
                    } else {
                        mostrarAlerta('error', data.message || 'Error al asignar el consultorio');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('error', 'Error al asignar el consultorio');
                } finally {
                    mostrarLoader(false);
                }
            }

            function toggleAccesoSistema() {
                const modal = document.getElementById('modalToggleAcceso');
                modal.classList.remove('hidden');
            }
            function cerrarModalToggle() {
                const modal = document.getElementById('modalToggleAcceso');
                modal.classList.add('hidden');
            }
            function abrirConfirmacionMasiva(accion) {
                // Cerrar el modal de selección
                cerrarModalToggle();

                const modal = document.getElementById('modalConfirmacionMasiva');
                const accionTexto = document.getElementById('accionMasivaTexto');
                const iconoMasivo = document.getElementById('iconoAccionMasiva');
                const btnConfirmarMasivo = document.getElementById('btnConfirmarMasivo');

                // Contar personal que será afectado
                const personalNoSupervisor = personalData.filter(p => {
                    return p.rol && !p.rol.nombreRol.toLowerCase().includes('supervisor');
                });

                const nuevoEstado = accion === 'bloquear' ? 'inactivo' : 'activo';
                const personalAfectado = personalNoSupervisor.filter(p => p.estado !== nuevoEstado);

                document.getElementById('cantidadAfectada').textContent = personalAfectado.length;
                document.getElementById('accionMasivaLabel').textContent = accion === 'bloquear' ? 'desactivar' : 'activar';

                if (accion === 'bloquear') {
                    accionTexto.className = 'text-sm text-red-600 font-medium mt-2';
                    accionTexto.textContent = 'Estas personas no podrán acceder al sistema hasta que sean reactivadas';
                    iconoMasivo.textContent = 'block';
                    iconoMasivo.className = 'material-icons text-red-600 text-6xl';
                    btnConfirmarMasivo.className = 'flex-1 inline-flex justify-center items-center px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-pink-600 rounded-xl hover:from-red-700 hover:to-pink-700 transition-all duration-200 shadow-lg hover:shadow-xl';
                } else {
                    accionTexto.className = 'text-sm text-green-600 font-medium mt-2';
                    accionTexto.textContent = 'Estas personas podrán acceder nuevamente al sistema';
                    iconoMasivo.textContent = 'check_circle';
                    iconoMasivo.className = 'material-icons text-green-600 text-6xl';
                    btnConfirmarMasivo.className = 'flex-1 inline-flex justify-center items-center px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl';
                }

                modal.dataset.accion = accion;
                modal.classList.remove('hidden');
            }
            function cerrarModalConfirmacionMasiva() {
                const modal = document.getElementById('modalConfirmacionMasiva');
                modal.classList.add('hidden');
                delete modal.dataset.accion;
            }
            async function confirmarCambioMasivo() {
                const modal = document.getElementById('modalConfirmacionMasiva');
                const accion = modal.dataset.accion;

                if (!accion) return;

                cerrarModalConfirmacionMasiva();
                mostrarLoader(true);

                try {
                    const nuevoEstado = accion === 'bloquear' ? 'inactivo' : 'activo';

                    // Obtener todo el personal que no es supervisor
                    const personalNoSupervisor = personalData.filter(p => {
                        return p.rol && !p.rol.nombreRol.toLowerCase().includes('supervisor');
                    });

                    let exitosos = 0;
                    let errores = 0;

                    // Cambiar estado a cada uno
                    for (const personal of personalNoSupervisor) {
                        // Solo cambiar si el estado es diferente al deseado
                        if (personal.estado !== nuevoEstado) {
                            try {
                                const response = await fetch(`/api/personal-salud/${personal.codPer}/cambiar-estado`, {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                });

                                if (response.ok) {
                                    exitosos++;
                                } else {
                                    errores++;
                                }
                            } catch (error) {
                                console.error(`Error al cambiar estado de ${personal.codPer}:`, error);
                                errores++;
                            }
                        }
                    }

                    if (exitosos > 0) {
                        mostrarAlerta('success', `${exitosos} personal(es) ${nuevoEstado === 'activo' ? 'activado(s)' : 'desactivado(s)'} exitosamente`);
                    }

                    if (errores > 0) {
                        mostrarAlerta('error', `${errores} error(es) al cambiar estados`);
                    }

                    // Recargar la lista
                    await cargarPersonal();

                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('error', 'Error al cambiar estados del personal');
                } finally {
                    mostrarLoader(false);
                }
            }
            async function confirmarToggleAcceso(accion) {
                const mensaje = accion === 'bloquear'
                    ? '¿Estás seguro de desactivar a todo el personal (excepto supervisores)?'
                    : '¿Estás seguro de activar a todo el personal?';

                if (!confirm(mensaje)) return;

                cerrarModalToggle();
                mostrarLoader(true);

                try {
                    const nuevoEstado = accion === 'bloquear' ? 'inactivo' : 'activo';

                    // Obtener todo el personal que no es supervisor
                    const personalNoSupervisor = personalData.filter(p => {
                        return p.rol && !p.rol.nombreRol.toLowerCase().includes('supervisor');
                    });

                    let exitosos = 0;
                    let errores = 0;

                    // Cambiar estado a cada uno
                    for (const personal of personalNoSupervisor) {
                        // Solo cambiar si el estado es diferente al deseado
                        if (personal.estado !== nuevoEstado) {
                            try {
                                const response = await fetch(`/api/personal-salud/${personal.codPer}/cambiar-estado`, {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                });

                                if (response.ok) {
                                    exitosos++;
                                } else {
                                    errores++;
                                }
                            } catch (error) {
                                console.error(`Error al cambiar estado de ${personal.codPer}:`, error);
                                errores++;
                            }
                        }
                    }

                    if (exitosos > 0) {
                        mostrarAlerta('success', `${exitosos} personal(es) ${nuevoEstado === 'activo' ? 'activado(s)' : 'desactivado(s)'} exitosamente`);
                    }

                    if (errores > 0) {
                        mostrarAlerta('error', `${errores} error(es) al cambiar estados`);
                    }

                    // Recargar la lista
                    await cargarPersonal();

                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('error', 'Error al cambiar estados del personal');
                } finally {
                    mostrarLoader(false);
                }
            }
            function verDetalle(id) {
                const personal = personalData.find(p => p.codPer === id);
                if (!personal) return;

                const nombreCompleto = `${personal.nomPer || ''} ${personal.paternoPer || ''} ${personal.maternoPer || ''}`.trim();
                const rolNombre = personal.rol ? personal.rol.nombreRol : 'Sin rol';

                // Obtener consultorio asignado
                const asignacion = asignacionesActivas[personal.codPer];
                let consultorioInfo = '';
                if (asignacion && asignacion.consultorio) {
                    const fechaInicio = new Date(asignacion.fechaInicio).toLocaleDateString('es-ES');
                    const fechaFin = asignacion.fechaFin ? new Date(asignacion.fechaFin).toLocaleDateString('es-ES') : 'Indefinido';

                    consultorioInfo = `
                                                                                                                                                                    <div class="col-span-2 p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg border border-blue-200">
                                                                                                                                                                        <p class="text-xs font-medium text-gray-500 uppercase mb-2">Consultorio Asignado</p>
                                                                                                                                                                        <div class="flex items-center gap-2 mb-2">
                                                                                                                                                                            <span class="material-icons text-blue-600">meeting_room</span>
                                                                                                                                                                            <p class="text-gray-900 font-semibold text-lg">Consultorio ${asignacion.consultorio.numCons}</p>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="text-xs text-gray-600 space-y-1">
                                                                                                                                                                            <p><span class="font-medium">Desde:</span> ${fechaInicio}</p>
                                                                                                                                                                            <p><span class="font-medium">Hasta:</span> ${fechaFin}</p>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                `;
                } else {
                    consultorioInfo = `
                                                                                                                                                                    <div class="col-span-2 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                                                                                                                                                        <p class="text-xs font-medium text-gray-500 uppercase mb-2">Consultorio Asignado</p>
                                                                                                                                                                        <div class="flex items-center gap-2">
                                                                                                                                                                            <span class="material-icons text-gray-400">block</span>
                                                                                                                                                                            <p class="text-gray-600 font-semibold">Sin consultorio asignado</p>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                `;
                }

                const modal = document.getElementById('modalDetalle');
                const contenido = `
                                                                                                                                                                <div class="space-y-6">
                                                                                                                                                                    <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
                                                                                                                                                                        <div class="h-20 w-20 rounded-xl ${personal.estado === 'activo' ? 'bg-gradient-to-br from-blue-500 to-indigo-600' : 'bg-gradient-to-br from-gray-400 to-gray-500'} flex items-center justify-center shadow-lg">
                                                                                                                                                                            <span class="material-icons ${personal.estado === 'activo' ? 'text-white' : 'text-white'} text-4xl">person</span>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div>
                                                                                                                                                                            <h3 class="text-2xl font-bold text-gray-900">${nombreCompleto}</h3>
                                                                                                                                                                            <p class="text-gray-600 flex items-center gap-1 mt-1">
                                                                                                                                                                                <span class="material-icons text-sm">badge</span>
                                                                                                                                                                                ID: ${personal.codPer}
                                                                                                                                                                            </p>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>

                                                                                                                                                                    <div class="grid grid-cols-2 gap-4">
                                                                                                                                                                        <div class="p-4 bg-gray-50 rounded-lg">
                                                                                                                                                                            <p class="text-xs font-medium text-gray-500 uppercase mb-1">Usuario</p>
                                                                                                                                                                            <p class="text-gray-900 font-semibold">${personal.usuarioPer || 'N/A'}</p>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="p-4 bg-gray-50 rounded-lg">
                                                                                                                                                                            <p class="text-xs font-medium text-gray-500 uppercase mb-1">Rol</p>
                                                                                                                                                                            <span class="px-3 py-1 text-xs font-semibold rounded-lg bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border border-purple-200 inline-block">
                                                                                                                                                                                ${rolNombre}
                                                                                                                                                                            </span>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="p-4 bg-gray-50 rounded-lg">
                                                                                                                                                                            <p class="text-xs font-medium text-gray-500 uppercase mb-1">Nombre</p>
                                                                                                                                                                            <p class="text-gray-900 font-semibold">${personal.nomPer || 'N/A'}</p>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="p-4 bg-gray-50 rounded-lg">
                                                                                                                                                                            <p class="text-xs font-medium text-gray-500 uppercase mb-1">Apellido Paterno</p>
                                                                                                                                                                            <p class="text-gray-900 font-semibold">${personal.paternoPer || 'N/A'}</p>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="p-4 bg-gray-50 rounded-lg">
                                                                                                                                                                            <p class="text-xs font-medium text-gray-500 uppercase mb-1">Apellido Materno</p>
                                                                                                                                                                            <p class="text-gray-900 font-semibold">${personal.maternoPer || 'N/A'}</p>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="p-4 bg-gray-50 rounded-lg">
                                                                                                                                                                            <p class="text-xs font-medium text-gray-500 uppercase mb-1">Estado</p>
                                                                                                                                                                            <span class="px-3 py-1 text-xs font-semibold rounded-lg ${personal.estado === 'activo' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300'} inline-block">
                                                                                                                                                                                ${personal.estado === 'activo' ? 'Activo' : 'Inactivo'}
                                                                                                                                                                            </span>
                                                                                                                                                                        </div>

                                                                                                                                                                        ${consultorioInfo}
                                                                                                                                                                    </div>

                                                                                                                                                                    <div class="flex justify-end space-x-3 pt-4 border-t">
                                                                                                                                                                        <button onclick="cerrarModal()" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:shadow-md transition-all duration-200">
                                                                                                                                                                            <span class="material-icons text-sm mr-1">close</span>
                                                                                                                                                                            Cerrar
                                                                                                                                                                        </button>
                                                                                                                                                                        <a href="/supervisor/gestion-personal/editar/${personal.codPer}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                                                                                                                                                            <span class="material-icons text-sm mr-1">edit</span>
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

            function mostrarAlerta(tipo, mensaje) {
                const alerta = document.getElementById('alerta');
                const iconos = {
                    success: 'check_circle',
                    error: 'error',
                    info: 'info'
                };
                const colores = {
                    success: 'bg-green-100 border-green-400 text-green-800',
                    error: 'bg-red-100 border-red-400 text-red-800',
                    info: 'bg-blue-100 border-blue-400 text-blue-800'
                };

                alerta.className = `p-4 rounded-xl border-2 flex items-center shadow-lg ${colores[tipo]} mb-4`;
                alerta.innerHTML = `
                                                                                                                                <span class="material-icons mr-2">${iconos[tipo]}</span>
                                                                                                                                <span class="font-medium">${mensaje}</span>
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

            function imprimirReportePersonal(codPer) {
                const personal = personalData.find(p => p.codPer === codPer);
                if (!personal) return;

                const nombreCompleto = `${personal.nomPer || ''} ${personal.paternoPer || ''} ${personal.maternoPer || ''}`.trim();
                
                document.getElementById('nombrePersonalReporte').textContent = nombreCompleto;
                document.getElementById('codPerReporte').value = codPer;
                
                // Establecer fecha actual por defecto
                const hoy = new Date().toISOString().split('T')[0];
                document.getElementById('fechaReporte').value = hoy;
                
                const modal = document.getElementById('modalReportePersonal');
                modal.classList.remove('hidden');
            }

            function cerrarModalReporte() {
                const modal = document.getElementById('modalReportePersonal');
                modal.classList.add('hidden');
                document.getElementById('formReporte').reset();
            }

            function generarReportePersonal() {
                const codPer = document.getElementById('codPerReporte').value;
                const fecha = document.getElementById('fechaReporte').value;

                if (!fecha) {
                    mostrarAlerta('error', 'Por favor seleccione una fecha');
                    return;
                }

                const url = `/api/supervisor/estadisticas/personal/reporte-pdf?codPer=${codPer}&fecha=${fecha}&periodo=dia`;
                window.open(url, '_blank');
                
                cerrarModalReporte();
            }



            function limpiarFiltros() {
                document.getElementById('busqueda').value = '';
                document.getElementById('filtroRol').value = '';
                document.getElementById('filtroEstado').value = '';
                filtrarPersonal();
            }
        </script>
    @endpush

    <div class="space-y-6">
        <!-- Alerta -->
        <div id="alerta" class="hidden"></div>

        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-2xl p-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2 flex items-center gap-3">
                        <span class="material-icons text-5xl">admin_panel_settings</span>
                        Gestión de Personal de Salud
                    </h1>
                    <p class="text-blue-100 text-lg">Administra el personal clínico y sus asignaciones</p>
                </div>
                <div class="hidden lg:flex gap-3">
                    <a href="{{ route('supervisor.gestion-personal.consultorios') }}"
                        class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm text-white rounded-xl hover:bg-opacity-30 transition-all shadow-lg hover:shadow-xl font-medium">
                        <span class="material-icons mr-2">meeting_room</span>
                        Consultorios
                    </a>
                    <button onclick="toggleAccesoSistema()"
                        class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm text-white rounded-xl hover:bg-opacity-30 transition-all shadow-lg hover:shadow-xl font-medium">
                        <span class="material-icons mr-2">lock</span>
                        Control de Acceso
                    </button>
                    <a href="{{ route('supervisor.gestion-personal.agregar') }}"
                        class="inline-flex items-center px-6 py-3 bg-white text-blue-600 rounded-xl hover:shadow-xl transition-all font-medium">
                        <span class="material-icons mr-2">person_add</span>
                        Agregar Personal
                    </a>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-4 shadow-lg">
                        <span class="material-icons text-white text-3xl">people</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 uppercase">Total Personal</p>
                        <p class="text-3xl font-bold text-gray-900" id="stat-total">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-4 shadow-lg">
                        <span class="material-icons text-white text-3xl">check_circle</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 uppercase">Personal Activo</p>
                        <p class="text-3xl font-bold text-gray-900" id="stat-activos">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-red-500 to-pink-600 rounded-xl p-4 shadow-lg">
                        <span class="material-icons text-white text-3xl">block</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 uppercase">Personal Inactivo</p>
                        <p class="text-3xl font-bold text-gray-900" id="stat-inactivos">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="material-icons text-blue-600 text-2xl">filter_list</span>
                <h2 class="text-xl font-bold text-gray-800">Filtros de Búsqueda</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <div class="relative">
                        <span class="material-icons absolute left-3 top-3 text-gray-400">search</span>
                        <input type="text" id="busqueda" placeholder="Buscar por nombre o usuario..."
                            class="pl-10 w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
                    <select id="filtroRol"
                        class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 transition">
                        <option value="">Todos los roles</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="filtroEstado"
                        class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 transition">
                        <option value="">Todos los estados</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button onclick="limpiarFiltros()"
                    class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium hover:underline">
                    <span class="material-icons text-sm mr-1">clear</span>
                    Limpiar filtros
                </button>
                <span class="text-gray-500 ml-2" id="resultados-count">Cargando...</span>
            </div>
        </div>

        <!-- Loader -->
        <div id="loader" class="flex justify-center items-center py-12">
            <div class="relative">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-600"></div>
                <div class="absolute top-0 left-0 h-16 w-16 rounded-full border-4 border-blue-200"></div>
            </div>
        </div>

        <!-- Mensaje sin datos -->
        <div id="no-data" class="hidden bg-white rounded-xl shadow-lg p-12">
            <div class="flex flex-col items-center justify-center">
                <div
                    class="w-32 h-32 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mb-6">
                    <span class="material-icons text-blue-400" style="font-size: 80px;">people</span>
                </div>
                <p class="text-gray-800 text-xl font-bold mb-2">No hay personal registrado</p>
                <p class="text-gray-500 text-sm mb-6">Comienza agregando personal de salud al sistema</p>
                <a href="{{ route('supervisor.gestion-personal.agregar') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl font-medium">
                    <span class="material-icons mr-2">person_add</span>
                    Agregar Personal
                </a>
            </div>
        </div>

        <!-- Tabla de personal -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden" id="tabla-container">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead
                        class="text-xs text-gray-700 uppercase bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold">Personal</th>
                            <th scope="col" class="px-6 py-4 font-bold">Rol</th>
                            <th scope="col" class="px-6 py-4 font-bold">Consultorio</th> {{-- NUEVO --}}
                            <th scope="col" class="px-6 py-4 font-bold">Estado</th>
                            <th scope="col" class="px-6 py-4 font-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-personal"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detalle -->
    <div id="modalDetalle"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full z-50 flex items-center justify-center backdrop-blur-sm">
        <div class="relative mx-auto p-6 border-0 w-full max-w-2xl shadow-2xl rounded-2xl bg-white">
            <div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-gray-100">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="material-icons text-blue-600">person</span>
                    Detalle del Personal
                </h3>
                <button onclick="cerrarModal()"
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-2 transition">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div id="modalContenido"></div>
        </div>
    </div>
    <div id="modalConfirmacionEstado"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full z-50 flex items-center justify-center backdrop-blur-sm">
        <div class="relative mx-auto p-6 border-0 w-full max-w-md shadow-2xl rounded-2xl bg-white">
            <div class="flex justify-center mb-4">
                <div class="rounded-full bg-gradient-to-br from-yellow-100 to-orange-100 p-4 shadow-lg">
                    <span id="iconoAccionEstado" class="material-icons text-yellow-600 text-5xl">warning</span>
                </div>
            </div>

            <h3 class="text-2xl font-bold text-gray-900 text-center mb-2">¿Cambiar estado?</h3>

            <div class="text-center mb-6">
                <p class="text-gray-600 mb-3">Estás a punto de <span id="estadoNuevoConfirm"
                        class="font-semibold text-blue-600"></span> a:</p>
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 mb-3 border border-gray-200">
                    <p class="font-bold text-gray-900 text-lg" id="nombrePersonalConfirm"></p>
                    <p class="text-sm text-gray-600 mt-1">Usuario: <span id="usuarioPersonalConfirm"
                            class="font-semibold"></span></p>
                    <p class="text-sm text-gray-600">Estado actual: <span id="estadoActualConfirm"
                            class="font-semibold"></span></p>
                </div>
                <p id="accionEstadoTextoConfirm" class="text-sm font-medium"></p>
            </div>

            <div class="flex gap-3">
                <button onclick="cerrarModalConfirmacionEstado()"
                    class="flex-1 inline-flex justify-center items-center px-4 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:shadow-md transition-all duration-200">
                    <span class="material-icons text-base mr-1">close</span>
                    Cancelar
                </button>
                <button id="btnConfirmarEstado" onclick="confirmarCambioEstadoIndividual()"
                    class="flex-1 inline-flex justify-center items-center px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-yellow-500 to-orange-500 rounded-xl hover:from-yellow-600 hover:to-orange-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <span class="material-icons text-base mr-1">swap_horiz</span>
                    Confirmar
                </button>
            </div>
        </div>
    </div>


    <!-- Modal Asignación de Consultorio -->
    <div id="modalAsignacion"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full z-50 flex items-center justify-center backdrop-blur-sm">
        <div class="relative mx-auto p-6 border-0 w-full max-w-lg shadow-2xl rounded-2xl bg-white">
            <div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-gray-100">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="material-icons text-indigo-600">meeting_room</span>
                    Asignar Consultorio
                </h3>
                <button onclick="cerrarModalAsignacion()"
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-2 transition">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <div class="mb-6 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-indigo-200">
                <p class="text-sm text-gray-600 mb-1">Personal:</p>
                <p class="font-bold text-gray-900 text-lg" id="nombrePersonalAsignacion"></p>
            </div>

            <form id="formAsignacion" class="space-y-4">
                <input type="hidden" id="codPerAsignacion">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Consultorio <span class="text-red-600">*</span>
                    </label>
                    <select id="codConsAsignacion" required
                        class="w-full bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 p-3 transition">
                        <option value="">Seleccione un consultorio...</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Fecha de Inicio <span class="text-red-600">*</span>
                    </label>
                    <input type="date" id="fechaInicio" required
                        class="w-full bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 p-3 transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Fecha de Fin <span class="text-gray-500 text-xs">(Opcional)</span>
                    </label>
                    <input type="date" id="fechaFin"
                        class="w-full bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 p-3 transition">
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                    <span class="material-icons text-blue-600 mt-0.5">info</span>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Información:</p>
                        <p>Si no especificas una fecha de fin, la asignación será indefinida hasta que la modifiques.</p>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="cerrarModalAsignacion()"
                        class="flex-1 inline-flex justify-center items-center px-4 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:shadow-md transition-all duration-200">
                        <span class="material-icons text-base mr-1">close</span>
                        Cancelar
                    </button>
                    <button type="button" onclick="guardarAsignacion()"
                        class="flex-1 inline-flex justify-center items-center px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <span class="material-icons text-base mr-1">save</span>
                        Asignar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Toggle Acceso Sistema -->
    <div id="modalToggleAcceso"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full z-50 flex items-center justify-center backdrop-blur-sm">
        <div class="relative mx-auto p-6 border-0 w-full max-w-lg shadow-2xl rounded-2xl bg-white">
            <div class="flex justify-center mb-4">
                <div class="rounded-full bg-gradient-to-br from-purple-100 to-pink-100 p-4 shadow-lg">
                    <span class="material-icons text-purple-600 text-5xl">lock</span>
                </div>
            </div>

            <h3 class="text-2xl font-bold text-gray-900 text-center mb-2">Control de Acceso al Sistema</h3>

            <div class="text-center mb-6">
                <p class="text-gray-600 mb-4">Selecciona la acción que deseas realizar para todo el personal (excepto
                    supervisores):</p>

                <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4 mb-4">
                    <div class="flex items-start">
                        <span class="material-icons text-blue-600 mr-2 mt-0.5">info</span>
                        <div class="text-sm text-blue-800 text-left">
                            <p class="font-semibold mb-1">Información importante:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Esta acción afectará a todo el personal no supervisor</li>
                                <li>Los supervisores mantienen siempre su acceso</li>
                                <li>Puedes revertir esta acción en cualquier momento</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button onclick="confirmarToggleAcceso('bloquear')"
                        class="flex flex-col items-center justify-center p-4 bg-red-50 border-2 border-red-200 rounded-xl hover:bg-red-100 transition-colors duration-200 group">
                        <span
                            class="material-icons text-red-600 text-3xl mb-2 group-hover:scale-110 transition-transform">block</span>
                        <span class="font-semibold text-red-800">Bloquear Acceso</span>
                        <span class="text-xs text-red-600 mt-1">Desactivar a todos</span>
                    </button>

                    <button onclick="confirmarToggleAcceso('activar')"
                        class="flex flex-col items-center justify-center p-4 bg-green-50 border-2 border-green-200 rounded-xl hover:bg-green-100 transition-colors duration-200 group">
                        <span
                            class="material-icons text-green-600 text-3xl mb-2 group-hover:scale-110 transition-transform">check_circle</span>
                        <span class="font-semibold text-green-800">Habilitar Acceso</span>
                        <span class="text-xs text-green-600 mt-1">Activar a todos</span>
                    </button>
                </div>
            </div>

            <div class="flex justify-center">
                <button onclick="cerrarModalToggle()"
                    class="inline-flex items-center px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <span class="material-icons text-base mr-1">close</span>
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Reporte Personal -->
    <div id="modalReportePersonal"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full z-50 flex items-center justify-center backdrop-blur-sm">
        <div class="relative mx-auto p-6 border-0 w-full max-w-md shadow-2xl rounded-2xl bg-white">
            <div class="flex justify-center mb-4">
                <div class="rounded-full bg-gradient-to-br from-purple-100 to-pink-100 p-4 shadow-lg">
                    <span class="material-icons text-purple-600 text-5xl">print</span>
                </div>
            </div>

            <h3 class="text-2xl font-bold text-gray-900 text-center mb-2">Generar Reporte de Personal</h3>

            <form id="formReporte" class="space-y-4 mt-6">
                <input type="hidden" id="codPerReporte">
                
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-start gap-2">
                        <span class="material-icons text-blue-600 text-sm mt-0.5">person</span>
                        <div>
                            <p class="text-xs font-medium text-blue-600 uppercase mb-1">Personal Seleccionado</p>
                            <p class="text-sm font-semibold text-gray-900" id="nombrePersonalReporte"></p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="material-icons text-sm mr-1 align-middle">event</span>
                        Seleccionar Fecha <span class="text-red-600">*</span>
                    </label>
                    <input type="date" id="fechaReporte" required
                        class="w-full bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 p-3 transition">
                    <p class="text-xs text-gray-500 mt-2">
                        <span class="material-icons text-xs align-middle">info</span>
                        Se generará el reporte del mes de la fecha seleccionada
                    </p>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="cerrarModalReporte()"
                        class="flex-1 inline-flex justify-center items-center px-4 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:shadow-md transition-all duration-200">
                        <span class="material-icons text-base mr-1">close</span>
                        Cancelar
                    </button>
                    <button type="button" onclick="generarReportePersonal()"
                        class="flex-1 inline-flex justify-center items-center px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <span class="material-icons text-base mr-1">picture_as_pdf</span>
                        Generar PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
