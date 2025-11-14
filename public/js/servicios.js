// public/js/servicios.js - VERSIÓN CORREGIDA

// Estado global
let servicios = [];
let pacientes = [];
let medicos = [];
let tiposEstudio = [];
let cronogramas = [];
let diagnosticos = [];
let servicioEditando = null;
let servicioSeleccionado = null;
let currentStep = 1;
const totalSteps = 4;

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    cargarDatosIniciales();
    configurarEventos();
    configurarEventosModales();
    configurarStepper();
    configurarContadorDiagnostico();
});

// ============================================
// FUNCIONES PARA PDF - DEFINIDAS GLOBALMENTE
// ============================================

function descargarPDF(nroServ) {
    Swal.fire({
        title: 'Descargando PDF...',
        text: 'El documento se descargará en breve',
        icon: 'info',
        timer: 2000,
        showConfirmButton: false
    });
    window.open(`/personal/servicios/${nroServ}/pdf`, '_blank');
}

function verPDF(nroServ) {
    Swal.fire({
        title: 'Abriendo PDF...',
        text: 'El documento se abrirá en una nueva pestaña',
        icon: 'info',
        timer: 2000,
        showConfirmButton: false
    });
    window.open(`/personal/servicios/${nroServ}/pdf/ver`, '_blank');
}

// Cargar datos iniciales
async function cargarDatosIniciales() {
    try {
        await Promise.all([
            cargarServicios(),
            cargarDatosFormulario(),
            cargarEstadisticas()
        ]);
    } catch (error) {
        console.error('Error al cargar datos iniciales:', error);
        mostrarError('Error al cargar datos iniciales');
    }
}

// Cargar servicios
async function cargarServicios() {
    try {
        mostrarCargando('#tabla-servicios tbody');

        const response = await fetch('/api/personal/servicios');
        const data = await response.json();

        if (data.success) {
            servicios = data.data;
            renderizarTablaServicios();
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error al cargar servicios:', error);
        const tbody = document.querySelector('#tabla-servicios tbody');
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-red-500">
                        <span class="material-icons text-4xl mb-2 block">error_outline</span>
                        Error al cargar servicios: ${error.message}
                    </td>
                </tr>
            `;
        }
        mostrarError('Error al cargar servicios: ' + error.message);
    }
}

// Cargar datos para formulario
async function cargarDatosFormulario() {
    try {
        const response = await fetch('/api/personal/servicios/datos-formulario');
        const data = await response.json();

        if (data.success) {
            pacientes = data.data.pacientes;
            medicos = data.data.medicos;
            tiposEstudio = data.data.tiposEstudio;
            cronogramas = data.data.cronogramas;
            diagnosticos = data.data.diagnosticos || [];
        }
    } catch (error) {
        console.error('Error al cargar datos del formulario:', error);
    }
}

// Cargar estadísticas
async function cargarEstadisticas() {
    try {
        const response = await fetch('/api/personal/servicios/estadisticas');
        const data = await response.json();

        if (data.success) {
            actualizarEstadisticas(data.data);
        }
    } catch (error) {
        console.error('Error al cargar estadísticas:', error);
    }
}

// Calcular número de ficha automáticamente
async function calcularNumeroFicha(fechaCrono) {
    try {
        const response = await fetch(`/api/personal/servicios/calcular-ficha/${fechaCrono}`);

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            console.warn('Respuesta no es JSON, pero continuando...');
            return;
        }

        const result = await response.json();

        if (result.success) {
            const nroFichaInput = document.getElementById('nroFicha');
            nroFichaInput.value = result.data.nroFicha;
            nroFichaInput.readOnly = true;

            const parentDiv = nroFichaInput.parentElement;

            const oldInfo = parentDiv.querySelector('.ficha-info');
            if (oldInfo) oldInfo.remove();

            const infoDiv = document.createElement('p');
            infoDiv.className = 'text-xs text-blue-600 mt-1 ficha-info';
            infoDiv.innerHTML = `
                <span class="material-icons text-xs align-middle">info</span>
                Fichas restantes para esta fecha: ${result.data.fichasRestantes} de ${result.data.cantTotal}
            `;
            parentDiv.appendChild(infoDiv);
        }
    } catch (error) {
        console.warn('Error al calcular número de ficha (no crítico):', error);
    }
}

// Configurar contador de caracteres del diagnóstico
function configurarContadorDiagnostico() {
    const textarea = document.getElementById('diagnostico-texto');
    const contador = document.getElementById('caracteres-actuales');
    const maxCaracteres = 500;

    if (textarea && contador) {
        textarea.addEventListener('input', function() {
            const longitud = this.value.length;
            contador.textContent = longitud;

            if (longitud > maxCaracteres) {
                this.value = this.value.substring(0, maxCaracteres);
                contador.textContent = maxCaracteres;
            }

            const contadorParent = contador.parentElement;
            if (longitud > maxCaracteres * 0.9) {
                contadorParent.classList.add('text-red-600', 'font-bold');
                contadorParent.classList.remove('text-gray-500');
            } else {
                contadorParent.classList.add('text-gray-500');
                contadorParent.classList.remove('text-red-600', 'font-bold');
            }
        });
    }
}

// Renderizar tabla de servicios
function renderizarTablaServicios() {
    const tbody = document.querySelector('#tabla-servicios tbody');

    if (!tbody) return;

    if (servicios.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                    <span class="material-icons text-4xl mb-2 block">inbox</span>
                    No hay servicios registrados
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = servicios.map(servicio => {
        const pacienteNombre = servicio.paciente
            ? `${servicio.paciente.nomPa} ${servicio.paciente.paternoPa || ''} ${servicio.paciente.maternoPa || ''}`.trim()
            : 'N/A';

        const medicoNombre = servicio.medico
            ? `${servicio.medico.nomMed} ${servicio.medico.paternoMed || ''}`.trim()
            : 'N/A';

        const tipoEstudioDesc = servicio.tipo_estudio?.descripcion || 'N/A';
        const tipoAsegFormatted = formatearTipoAseg(servicio.tipoAseg);
        const tipoAsegClass = obtenerClaseTipoAseg(servicio.tipoAseg);

        const estadoClass = {
            'Programado': 'bg-amber-100 text-amber-800',
            'EnProceso': 'bg-blue-100 text-blue-800',
            'Atendido': 'bg-green-100 text-green-800',
            'Entregado': 'bg-purple-100 text-purple-800'
        }[servicio.estado] || 'bg-gray-100 text-gray-800';

        return `
            <tr class="border-b hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">${servicio.nroServ || 'N/A'}</div>
                    <div class="text-sm text-gray-500">${formatearFecha(servicio.fechaSol)}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">${pacienteNombre}</div>
                    <div class="text-sm text-gray-500">${servicio.paciente?.nroHCI || ''}</div>
                </td>
                <td class="px-6 py-4">${tipoEstudioDesc}</td>
                <td class="px-6 py-4">${medicoNombre}</td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-0.5 rounded text-xs font-medium ${tipoAsegClass}">
                        ${tipoAsegFormatted}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm">
                        ${servicio.fechaAten ? formatearFecha(servicio.fechaAten) : 'Pendiente'}
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-0.5 rounded text-xs font-medium ${estadoClass}">
                        ${servicio.estado}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        <button onclick="verServicio(${servicio.codServ})"
                                class="group relative inline-flex items-center justify-center p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200 hover:scale-110 hover:shadow-lg"
                                title="Ver detalle">
                            <span class="material-icons text-sm">visibility</span>
                        </button>
                        <button onclick="editarServicio(${servicio.codServ})"
                                class="group relative inline-flex items-center justify-center p-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-all duration-200 hover:scale-110 hover:shadow-lg"
                                title="Editar">
                            <span class="material-icons text-sm">edit</span>
                        </button>
                        <button onclick="abrirModalCambiarEstado(${servicio.codServ}, '${servicio.estado}')"
                                class="group relative inline-flex items-center justify-center p-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-all duration-200 hover:scale-110 hover:shadow-lg"
                                title="Cambiar estado">
                            <span class="material-icons text-sm">swap_horiz</span>
                        </button>
                        <button onclick="abrirModalEliminar(${servicio.codServ})"
                                class="group relative inline-flex items-center justify-center p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-200 hover:scale-110 hover:shadow-lg"
                                title="Eliminar">
                            <span class="material-icons text-sm">delete</span>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// Funciones de formateo
function formatearTipoAseg(tipoAseg) {
    const tipos = {
        'AsegEmergencia': 'Asegurado - Emergencia',
        'AsegRegular': 'Asegurado - Regular',
        'NoAsegEmergencia': 'No Asegurado - Emergencia',
        'NoAsegRegular': 'No Asegurado - Regular'
    };
    return tipos[tipoAseg] || tipoAseg || 'N/A';
}

function obtenerClaseTipoAseg(tipoAseg) {
    const clases = {
        'AsegEmergencia': 'bg-red-100 text-red-800',
        'AsegRegular': 'bg-green-100 text-green-800',
        'NoAsegEmergencia': 'bg-orange-100 text-orange-800',
        'NoAsegRegular': 'bg-blue-100 text-blue-800'
    };
    return clases[tipoAseg] || 'bg-gray-100 text-gray-800';
}

// Abrir modal de cambiar estado
function abrirModalCambiarEstado(codServ, estadoActual) {
    servicioSeleccionado = codServ;
    document.getElementById('nuevo-estado').value = estadoActual;
    document.getElementById('modal-cambiar-estado').classList.remove('hidden');
}

// Abrir modal de eliminar
function abrirModalEliminar(codServ) {
    servicioSeleccionado = codServ;
    document.getElementById('modal-eliminar').classList.remove('hidden');
}

// Configurar eventos de los modales personalizados
function configurarEventosModales() {
    const btnConfirmarCambioEstado = document.getElementById('btn-confirmar-cambio-estado');
    if (btnConfirmarCambioEstado) {
        btnConfirmarCambioEstado.addEventListener('click', async function() {
            const nuevoEstado = document.getElementById('nuevo-estado').value;

            if (!servicioSeleccionado || !nuevoEstado) {
                mostrarError('Debe seleccionar un estado válido');
                return;
            }

            try {
                const response = await fetch(`/api/personal/servicios/${servicioSeleccionado}/estado`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ estado: nuevoEstado })
                });

                const data = await response.json();

                if (data.success) {
                    cerrarModal('modal-cambiar-estado');
                    mostrarExito(data.message);
                    await cargarServicios();
                    await cargarEstadisticas();
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error al cambiar estado:', error);
                mostrarError('Error al cambiar el estado');
            }
        });
    }

    const btnConfirmarEliminar = document.getElementById('btn-confirmar-eliminar');
    if (btnConfirmarEliminar) {
        btnConfirmarEliminar.addEventListener('click', async function() {
            if (!servicioSeleccionado) {
                mostrarError('No se ha seleccionado ningún servicio');
                return;
            }

            try {
                const response = await fetch(`/api/personal/servicios/${servicioSeleccionado}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    cerrarModal('modal-eliminar');
                    mostrarExito(data.message);
                    await cargarServicios();
                    await cargarEstadisticas();
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error al eliminar servicio:', error);
                mostrarError('Error al eliminar el servicio');
            }
        });
    }

    ['modal-cambiar-estado', 'modal-eliminar', 'modal-servicio', 'modal-detalle-servicio'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    cerrarModal(modalId);
                }
            });
        }
    });
}

// Abrir modal de nuevo servicio
function abrirModalNuevoServicio() {
    servicioEditando = null;
    currentStep = 1;
    document.getElementById('form-servicio').reset();
    document.getElementById('titulo-modal').textContent = 'Nuevo Servicio';

    llenarSelectPacientes();
    llenarSelectMedicos();
    llenarSelectTiposEstudio();
    llenarSelectCronogramas();

    const ahora = new Date();
    document.getElementById('fechaSol').valueAsDate = ahora;
    document.getElementById('horaSol').value = ahora.toTimeString().slice(0, 5);

    const selectEstado = document.getElementById('estado');
    selectEstado.value = 'Programado';
    selectEstado.disabled = true;

    const nroFichaInput = document.getElementById('nroFicha');
    nroFichaInput.value = '';
    nroFichaInput.readOnly = true;

    const oldInfo = nroFichaInput.parentElement.querySelector('.ficha-info');
    if (oldInfo) oldInfo.remove();

    const textareaDiag = document.getElementById('diagnostico-texto');
    if (textareaDiag) {
        textareaDiag.value = '';
        const contador = document.getElementById('caracteres-actuales');
        if (contador) contador.textContent = '0';
    }

    const selectTipoDiag = document.getElementById('tipo-diagnostico');
    if (selectTipoDiag) {
        selectTipoDiag.value = '';
    }

    resetStepper();
    abrirModal('modal-servicio');
}

// Ver detalles del servicio
async function verServicio(codServ) {
    try {
        const response = await fetch(`/api/personal/servicios/${codServ}`);
        const data = await response.json();

        if (data.success) {
            mostrarDetalleServicio(data.data);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error al obtener servicio:', error);
        mostrarError('Error al obtener detalles del servicio');
    }
}

// Mostrar detalle del servicio CON BOTONES PDF
function mostrarDetalleServicio(servicio) {
    const pacienteNombre = servicio.paciente
        ? `${servicio.paciente.nomPa} ${servicio.paciente.paternoPa || ''} ${servicio.paciente.maternoPa || ''}`.trim()
        : 'N/A';

    const medicoNombre = servicio.medico
        ? `Dr. ${servicio.medico.nomMed} ${servicio.medico.paternoMed || ''}`.trim()
        : 'N/A';

    const contenidoDetalle = `
        <!-- Botones de PDF -->
        <div class="flex gap-2 mb-4 pb-4 border-b border-gray-200">
            <button onclick="descargarPDF('${servicio.nroServ}')"
                class="flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all hover:shadow-lg">
                <span class="material-icons text-sm">picture_as_pdf</span>
                <span class="font-medium">Descargar PDF</span>
            </button>
            <button onclick="verPDF('${servicio.nroServ}')"
                class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all hover:shadow-lg">
                <span class="material-icons text-sm">visibility</span>
                <span class="font-medium">Ver PDF</span>
            </button>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Nro. Servicio</label>
                    <p class="text-gray-900">${servicio.nroServ || 'N/A'}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Estado</label>
                    <p class="text-gray-900">${servicio.estado}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Paciente</label>
                    <p class="text-gray-900">${pacienteNombre}</p>
                    <p class="text-sm text-gray-500">${servicio.paciente?.nroHCI || ''}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Médico</label>
                    <p class="text-gray-900">${medicoNombre}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Tipo de Estudio</label>
                    <p class="text-gray-900">${servicio.tipo_estudio?.descripcion || 'N/A'}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Tipo de Seguro</label>
                    <p class="text-gray-900">${formatearTipoAseg(servicio.tipoAseg)}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Fecha Solicitud</label>
                    <p class="text-gray-900">${formatearFecha(servicio.fechaSol)} ${servicio.horaSol || ''}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Fecha Atención</label>
                    <p class="text-gray-900">${servicio.fechaAten ? formatearFecha(servicio.fechaAten) : 'Pendiente'}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Nro. Ficha</label>
                    <p class="text-gray-900">${servicio.nroFicha || 'N/A'}</p>
                </div>
            </div>

            ${servicio.diagnosticos && servicio.diagnosticos.length > 0 ? `
                <div class="mt-4 border-t pt-4">
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Diagnóstico</label>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-800 whitespace-pre-wrap">${servicio.diagnosticos[0].descripDiag}</p>
                    </div>
                </div>
            ` : '<div class="mt-4 border-t pt-4 text-sm text-gray-500 italic">Sin diagnóstico registrado</div>'}
        </div>
    `;

    document.getElementById('detalle-servicio-content').innerHTML = contenidoDetalle;
    abrirModal('modal-detalle-servicio');
}

// Editar servicio
async function editarServicio(codServ) {
    try {
        const response = await fetch(`/api/personal/servicios/${codServ}`);
        const data = await response.json();

        if (data.success) {
            servicioEditando = data.data;
            llenarFormularioEdicion(data.data);
            document.getElementById('titulo-modal').textContent = 'Editar Servicio';
            abrirModal('modal-servicio');
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error al obtener servicio:', error);
        mostrarError('Error al cargar datos del servicio');
    }
}

// Llenar formulario para edición
function llenarFormularioEdicion(servicio) {
    llenarSelectPacientes();
    llenarSelectMedicos();
    llenarSelectTiposEstudio();
    llenarSelectCronogramas();

    let fechaSol = '';
    if (servicio.fechaSol) {
        const fechaStr = servicio.fechaSol.toString();
        fechaSol = fechaStr.split('T')[0].split(' ')[0];
    }

    const horaSol = servicio.horaSol ? servicio.horaSol.substring(0, 5) : '';

    document.getElementById('fechaSol').value = fechaSol;
    document.getElementById('horaSol').value = horaSol;
    document.getElementById('nroServ').value = servicio.nroServ || '';
    document.getElementById('codPa').value = servicio.codPa;
    document.getElementById('codMed').value = servicio.codMed;
    document.getElementById('codTest').value = servicio.codTest;
    document.getElementById('tipoAseg').value = servicio.tipoAseg;
    document.getElementById('fechaCrono').value = servicio.fechaCrono;

    const selectEstado = document.getElementById('estado');
    selectEstado.value = servicio.estado;
    selectEstado.disabled = false;

    if (servicio.nroFicha) {
        document.getElementById('nroFicha').value = servicio.nroFicha;
    }

    const textareaDiag = document.getElementById('diagnostico-texto');
    const contador = document.getElementById('caracteres-actuales');
    const selectTipoDiag = document.getElementById('tipo-diagnostico');

    if (servicio.diagnosticos && servicio.diagnosticos.length > 0) {
        const diagnosticoTexto = servicio.diagnosticos[0].descripDiag;
        const tipoDiag = servicio.diagnosticos[0].pivot?.tipo || 'sol';

        textareaDiag.value = diagnosticoTexto;
        selectTipoDiag.value = tipoDiag;

        if (contador) {
            contador.textContent = diagnosticoTexto.length;
        }
    } else {
        textareaDiag.value = '';
        selectTipoDiag.value = '';
        if (contador) contador.textContent = '0';
    }

    resetStepper();
}

// Guardar servicio
async function guardarServicio(event) {
    event.preventDefault();

    const formData = {
        fechaSol: document.getElementById('fechaSol').value,
        horaSol: document.getElementById('horaSol').value,
        nroServ: document.getElementById('nroServ').value || null,
        tipoAseg: document.getElementById('tipoAseg').value,
        nroFicha: document.getElementById('nroFicha').value || null,
        codPa: document.getElementById('codPa').value,
        codMed: document.getElementById('codMed').value,
        codTest: document.getElementById('codTest').value,
        fechaCrono: document.getElementById('fechaCrono').value
    };

    if (servicioEditando) {
        formData.estado = document.getElementById('estado').value;
    } else {
        formData.estado = 'Programado';
    }

    const diagnosticoTexto = document.getElementById('diagnostico-texto').value.trim();
    const tipoDiagnostico = document.getElementById('tipo-diagnostico').value;

    if (diagnosticoTexto) {
        if (!tipoDiagnostico) {
            mostrarError('Debe seleccionar el tipo de diagnóstico');
            return;
        }
        formData.diagnosticoTexto = diagnosticoTexto;
        formData.tipoDiagnostico = tipoDiagnostico;
    }

    try {
        const url = servicioEditando
            ? `/api/personal/servicios/${servicioEditando.codServ}`
            : '/api/personal/servicios';

        const method = servicioEditando ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (data.success) {
            mostrarExito(data.message);
            cerrarModal('modal-servicio');
            await cargarServicios();
            await cargarEstadisticas();
        } else {
            if (data.errors) {
                const errores = Object.values(data.errors).flat();
                mostrarError(errores.join('<br>'));
            } else {
                throw new Error(data.message);
            }
        }
    } catch (error) {
        console.error('Error al guardar servicio:', error);
        mostrarError('Error al guardar el servicio');
    }
}

// Llenar selects
function llenarSelectPacientes() {
    const select = document.getElementById('codPa');
    select.innerHTML = '<option value="">Seleccione un paciente</option>' +
        pacientes.map(p => {
            const nombre = `${p.nomPa} ${p.paternoPa || ''} ${p.maternoPa || ''}`.trim();
            return `<option value="${p.codPa}">${nombre} - ${p.nroHCI || ''}</option>`;
        }).join('');
}

function llenarSelectMedicos() {
    const select = document.getElementById('codMed');
    select.innerHTML = '<option value="">Seleccione un médico</option>' +
        medicos.map(m => {
            const nombre = `${m.nomMed} ${m.paternoMed || ''}`.trim();
            return `<option value="${m.codMed}">${nombre} - ${m.tipoMed || ''}</option>`;
        }).join('');
}

function llenarSelectTiposEstudio() {
    const select = document.getElementById('codTest');
    select.innerHTML = '<option value="">Seleccione un tipo de estudio</option>' +
        tiposEstudio.map(t =>
            `<option value="${t.codTest}">${t.descripcion}</option>`
        ).join('');
}

function llenarSelectCronogramas() {
    const select = document.getElementById('fechaCrono');
    select.innerHTML = '<option value="">Seleccione una fecha disponible</option>' +cronogramas.map(c => {
            const personal = c.personal_salud
                ? `${c.personal_salud.nomPer} ${c.personal_salud.paternoPer || ''}`.trim()
                : 'Sin asignar';
            return `<option value="${c.fechaCrono}">${formatearFecha(c.fechaCrono)} - Disponibles: ${c.cantDispo} - ${personal}</option>`;
        }).join('');
}

// Actualizar estadísticas
function actualizarEstadisticas(stats) {
    document.getElementById('stat-hoy').textContent = stats.hoy || 0;
    document.getElementById('stat-proceso').textContent = stats.enProceso || 0;
    document.getElementById('stat-programados').textContent = stats.programados || 0;
    document.getElementById('stat-tipos').textContent = stats.tiposEstudio || 0;
}

// Configurar eventos
function configurarEventos() {
    const btnNuevo = document.getElementById('btn-nuevo-servicio');
    if (btnNuevo) {
        btnNuevo.addEventListener('click', abrirModalNuevoServicio);
    }

    const form = document.getElementById('form-servicio');
    if (form) {
        form.addEventListener('submit', guardarServicio);
    }

    const selectCronograma = document.getElementById('fechaCrono');
    if (selectCronograma) {
        selectCronograma.addEventListener('change', function() {
            const fechaSeleccionada = this.value;
            if (fechaSeleccionada) {
                calcularNumeroFicha(fechaSeleccionada);
            } else {
                document.getElementById('nroFicha').value = '';
                const nroFichaInput = document.getElementById('nroFicha');
                const oldInfo = nroFichaInput.parentElement.querySelector('.ficha-info');
                if (oldInfo) oldInfo.remove();
            }
        });
    }

    const filtroEstado = document.getElementById('filtro-estado');
    if (filtroEstado) {
        filtroEstado.addEventListener('change', aplicarFiltros);
    }

    const filtroTipoAseg = document.getElementById('filtro-tipo-aseg');
    if (filtroTipoAseg) {
        filtroTipoAseg.addEventListener('change', aplicarFiltros);
    }

    const filtroBusqueda = document.getElementById('buscar-servicio');
    if (filtroBusqueda) {
        filtroBusqueda.addEventListener('input', aplicarFiltros);
    }
}

// Aplicar filtros
function aplicarFiltros() {
    const estadoFiltro = document.getElementById('filtro-estado')?.value || '';
    const tipoAsegFiltro = document.getElementById('filtro-tipo-aseg')?.value || '';
    const busqueda = document.getElementById('buscar-servicio')?.value.toLowerCase() || '';

    const serviciosFiltrados = servicios.filter(servicio => {
        const cumpleEstado = !estadoFiltro || servicio.estado === estadoFiltro;
        const cumpleTipoAseg = !tipoAsegFiltro || servicio.tipoAseg === tipoAsegFiltro;

        const pacienteNombre = servicio.paciente
            ? `${servicio.paciente.nomPa} ${servicio.paciente.paternoPa || ''} ${servicio.paciente.maternoPa || ''}`.toLowerCase()
            : '';

        const cumpleBusqueda = !busqueda ||
            (servicio.nroServ && servicio.nroServ.toLowerCase().includes(busqueda)) ||
            pacienteNombre.includes(busqueda) ||
            (servicio.paciente?.nroHCI && servicio.paciente.nroHCI.toLowerCase().includes(busqueda));

        return cumpleEstado && cumpleTipoAseg && cumpleBusqueda;
    });

    renderizarTablaServiciosFiltrados(serviciosFiltrados);
}

// Renderizar servicios filtrados
function renderizarTablaServiciosFiltrados(serviciosFiltrados) {
    const serviciosOriginales = servicios;
    servicios = serviciosFiltrados;
    renderizarTablaServicios();
    servicios = serviciosOriginales;
}

// Utilidades
function formatearFecha(fecha) {
    if (!fecha) return 'No registrada';

    if (typeof fecha === 'string') {
        const fechaParseada = fecha.includes('T') ? fecha.split('T')[0] : fecha;
        const date = new Date(fechaParseada + 'T00:00:00');

        if (isNaN(date.getTime())) {
            return 'Fecha inválida';
        }

        const dia = String(date.getDate()).padStart(2, '0');
        const mes = String(date.getMonth() + 1).padStart(2, '0');
        const anio = date.getFullYear();

        return `${dia}/${mes}/${anio}`;
    }

    return 'No registrada';
}

function formatearHora(hora) {
    if (!hora) return '';
    if (typeof hora === 'string' && hora.includes(':')) {
        return hora;
    }
    return '';
}

function formatearFechaHora(fecha, hora) {
    if (!fecha) return 'No registrada';

    const fechaFormateada = formatearFecha(fecha);

    if (fechaFormateada === 'No registrada' || fechaFormateada === 'Fecha inválida') {
        return fechaFormateada;
    }

    if (hora) {
        return `${fechaFormateada} ${formatearHora(hora)}`;
    }

    return fechaFormateada;
}

function mostrarCargando(selector) {
    const elemento = document.querySelector(selector);
    if (elemento) {
        elemento.innerHTML = '<div class="text-center py-8"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';
    }
}

function abrirModal(idModal) {
    const modal = document.getElementById(idModal);
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function cerrarModal(idModal) {
    const modal = document.getElementById(idModal);
    if (modal) {
        modal.classList.add('hidden');
        if (idModal === 'modal-cambiar-estado' || idModal === 'modal-eliminar') {
            servicioSeleccionado = null;
        }
    }
}

function mostrarExito(mensaje) {
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: mensaje,
        timer: 3000,
        showConfirmButton: false
    });
}

function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        html: mensaje,
        confirmButtonText: 'Entendido'
    });
}

// ============================================
// FUNCIONES DEL STEPPER
// ============================================

function configurarStepper() {
    const btnSiguiente = document.getElementById('btn-siguiente');
    const btnAnterior = document.getElementById('btn-anterior');

    if (btnSiguiente) {
        btnSiguiente.addEventListener('click', siguienteStep);
    }

    if (btnAnterior) {
        btnAnterior.addEventListener('click', anteriorStep);
    }
}

function resetStepper() {
    currentStep = 1;
    actualizarStepper();
}

function siguienteStep() {
    if (!validarStepActual()) {
        return;
    }

    if (currentStep < totalSteps) {
        currentStep++;
        actualizarStepper();
    }
}

function anteriorStep() {
    if (currentStep > 1) {
        currentStep--;
        actualizarStepper();
    }
}

function validarStepActual() {
    const step = document.querySelector(`.form-step[data-step="${currentStep}"]`);
    const inputs = step.querySelectorAll('input[required], select[required]');

    let valido = true;
    inputs.forEach(input => {
        if (input.disabled) return;

        if (!input.value) {
            valido = false;
            input.classList.add('border-red-500');
            setTimeout(() => input.classList.remove('border-red-500'), 3000);
        }
    });

    if (currentStep === 4) {
        const diagnosticoTexto = document.getElementById('diagnostico-texto').value.trim();
        const tipoDiagnostico = document.getElementById('tipo-diagnostico').value;

        if (diagnosticoTexto && !tipoDiagnostico) {
            valido = false;
            const selectTipo = document.getElementById('tipo-diagnostico');
            selectTipo.classList.add('border-red-500');
            setTimeout(() => selectTipo.classList.remove('border-red-500'), 3000);
            mostrarError('Si ingresa un diagnóstico, debe seleccionar el tipo');
            return valido;
        }
    }

    if (!valido) {
        mostrarError('Por favor complete todos los campos requeridos');
    }

    return valido;
}

function actualizarStepper() {
    document.querySelectorAll('.form-step').forEach(step => {
        const stepNum = parseInt(step.dataset.step);
        if (stepNum === currentStep) {
            step.classList.remove('hidden');
            step.classList.add('active');
        } else {
            step.classList.add('hidden');
            step.classList.remove('active');
        }
    });

    document.querySelectorAll('.step-circle').forEach((circle, index) => {
        const stepNum = index + 1;
        const stepNumber = circle.querySelector('.step-number');
        const stepCheck = circle.querySelector('.step-check');
        const stepText = circle.parentElement.querySelector('div:last-child');

        if (stepNum < currentStep) {
            circle.classList.remove('border-gray-300', 'bg-white', 'text-gray-500', 'border-blue-600', 'bg-blue-600');
            circle.classList.add('border-green-600', 'bg-green-600', 'text-white');
            stepNumber.classList.add('hidden');
            stepCheck.classList.remove('hidden');
            stepText.classList.remove('text-gray-500', 'text-blue-600');
            stepText.classList.add('text-green-600');
        } else if (stepNum === currentStep) {
            circle.classList.remove('border-gray-300', 'bg-white', 'text-gray-500', 'border-green-600', 'bg-green-600');
            circle.classList.add('border-blue-600', 'bg-blue-600', 'text-white');
            stepNumber.classList.remove('hidden');
            stepCheck.classList.add('hidden');
            stepText.classList.remove('text-gray-500', 'text-green-600');
            stepText.classList.add('text-blue-600');
        } else {
            circle.classList.remove('border-blue-600', 'bg-blue-600', 'border-green-600', 'bg-green-600', 'text-white');
            circle.classList.add('border-gray-300', 'bg-white', 'text-gray-500');
            stepNumber.classList.remove('hidden');
            stepCheck.classList.add('hidden');
            stepText.classList.remove('text-blue-600', 'text-green-600');
            stepText.classList.add('text-gray-500');
        }
    });

    document.querySelectorAll('.step-line').forEach((line, index) => {
        if (index < currentStep - 1) {
            line.classList.remove('bg-gray-300');
            line.classList.add('bg-green-600');
        } else {
            line.classList.remove('bg-green-600');
            line.classList.add('bg-gray-300');
        }
    });

    const btnAnterior = document.getElementById('btn-anterior');
    const btnSiguiente = document.getElementById('btn-siguiente');
    const btnGuardar = document.getElementById('btn-guardar');

    if (btnAnterior) {
        btnAnterior.disabled = currentStep === 1;
    }

    if (btnSiguiente) {
        if (currentStep === totalSteps) {
            btnSiguiente.classList.add('hidden');
            if (btnGuardar) btnGuardar.classList.remove('hidden');
        } else {
            btnSiguiente.classList.remove('hidden');
            if (btnGuardar) btnGuardar.classList.add('hidden');
        }
    }
}
