@extends('supervisor.layouts.supervisor')
@section('title', 'Estadísticas del Sistema')
@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('supervisor.home')],
            ['label' => 'Estadísticas']
        ];
    @endphp

    @push('styles')
        <style>
            .chart-container {
                position: relative;
                height: 300px;
            }
            .stat-card {
                transition: transform 0.2s;
            }
            .stat-card:hover {
                transform: translateY(-4px);
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            let estadisticasGenerales = null;
            let chartServicios = null;
            let chartAtendidos = null;
            let chartEstados = null;
            let chartTipoAseg = null;
            let chartPacientes = null;

            document.addEventListener("DOMContentLoaded", function () {
                cargarEstadisticasGenerales();
                cargarListaPersonal();
            });

            async function cargarEstadisticasGenerales() {
                mostrarLoader(true);

                try {
                    const response = await fetch('/api/supervisor/estadisticas/generales');
                    const data = await response.json();

                    if (data.success) {
                        estadisticasGenerales = data.data;
                        renderizarEstadisticas(data.data);
                        crearGraficos(data.data);
                    } else {
                        mostrarAlerta('error', data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('error', 'Error al cargar las estadísticas');
                } finally {
                    mostrarLoader(false);
                }
            }

            function renderizarEstadisticas(data) {
                // Servicios
                document.getElementById('stat-servicios-hoy').textContent = data.servicios.hoy;
                document.getElementById('stat-servicios-semana').textContent = data.servicios.semana;
                document.getElementById('stat-servicios-mes').textContent = data.servicios.mes;
                document.getElementById('stat-servicios-anio').textContent = data.servicios.anio;

                // Atendidos
                document.getElementById('stat-atendidos-hoy').textContent = data.atendidos.hoy;
                document.getElementById('stat-atendidos-semana').textContent = data.atendidos.semana;
                document.getElementById('stat-atendidos-mes').textContent = data.atendidos.mes;

                // Pacientes
                document.getElementById('stat-pacientes-total').textContent = data.pacientes.total;
                document.getElementById('stat-pacientes-activos').textContent = data.pacientes.activos;

                // Personal
                document.getElementById('stat-personal-total').textContent = data.personal.total;
                document.getElementById('stat-personal-activo').textContent = data.personal.activo;

                // Personal más productivo
                renderizarPersonalProductivo(data.personalProductivo);
            }

            function renderizarPersonalProductivo(personal) {
                const tbody = document.getElementById('tabla-personal-productivo');
                tbody.innerHTML = '';

                if (personal.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">No hay datos disponibles</td></tr>';
                    return;
                }

                personal.forEach((p, index) => {
                    const nombreCompleto = `${p.nomPer} ${p.paternoPer} ${p.maternoPer || ''}`.trim();
                    const medalla = index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : '';

                    const row = `
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">${medalla}</span>
                                    <div>
                                        <p class="font-semibold text-gray-900">${nombreCompleto}</p>
                                        <p class="text-xs text-gray-500">ID: ${p.codPer}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-lg font-bold text-lg shadow-md">
                                    ${p.total_atendidos}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="verDetallesPersonal(${p.codPer})"
                                    class="inline-flex items-center px-3 py-2 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                    <span class="material-icons text-sm mr-1">visibility</span>
                                    Ver detalles
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            }

            function crearGraficos(data) {
                // Gráfico de servicios por día
                const ctxServicios = document.getElementById('chartServicios').getContext('2d');
                const fechasServicios = data.graficos.serviciosPorDia.map(d => new Date(d.fecha).toLocaleDateString('es-ES', { day: '2-digit', month: 'short' }));
                const totalesServicios = data.graficos.serviciosPorDia.map(d => d.total);

                if (chartServicios) chartServicios.destroy();

                chartServicios = new Chart(ctxServicios, {
                    type: 'line',
                    data: {
                        labels: fechasServicios,
                        datasets: [{
                            label: 'Servicios Solicitados',
                            data: totalesServicios,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

                // Gráfico de atendidos por día
                const ctxAtendidos = document.getElementById('chartAtendidos').getContext('2d');
                const fechasAtendidos = data.graficos.atendidosPorDia.map(d => new Date(d.fecha).toLocaleDateString('es-ES', { day: '2-digit', month: 'short' }));
                const totalesAtendidos = data.graficos.atendidosPorDia.map(d => d.total);

                if (chartAtendidos) chartAtendidos.destroy();

                chartAtendidos = new Chart(ctxAtendidos, {
                    type: 'bar',
                    data: {
                        labels: fechasAtendidos,
                        datasets: [{
                            label: 'Servicios Atendidos',
                            data: totalesAtendidos,
                            backgroundColor: 'rgba(16, 185, 129, 0.8)',
                            borderColor: 'rgb(16, 185, 129)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

                // Gráfico por estado
                const ctxEstados = document.getElementById('chartEstados').getContext('2d');
                const estados = Object.keys(data.servicios.porEstado);
                const totalesEstados = Object.values(data.servicios.porEstado);
                const coloresEstados = {
                    'Programado': '#3B82F6',
                    'EnProceso': '#F59E0B',
                    'Atendido': '#10B981',
                    'Entregado': '#8B5CF6',
                    'Cancelado': '#EF4444'
                };

                if (chartEstados) chartEstados.destroy();

                chartEstados = new Chart(ctxEstados, {
                    type: 'doughnut',
                    data: {
                        labels: estados,
                        datasets: [{
                            data: totalesEstados,
                            backgroundColor: estados.map(e => coloresEstados[e] || '#6B7280'),
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // Gráfico por tipo de aseguramiento
                const ctxTipoAseg = document.getElementById('chartTipoAseg').getContext('2d');
                const tiposAseg = Object.keys(data.servicios.porTipoAseg);
                const totalesTipoAseg = Object.values(data.servicios.porTipoAseg);

                if (chartTipoAseg) chartTipoAseg.destroy();

                chartTipoAseg = new Chart(ctxTipoAseg, {
                    type: 'pie',
                    data: {
                        labels: tiposAseg,
                        datasets: [{
                            data: totalesTipoAseg,
                            backgroundColor: [
                                '#EF4444',
                                '#3B82F6',
                                '#F59E0B',
                                '#10B981'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // Gráfico de pacientes
                const ctxPacientes = document.getElementById('chartPacientes').getContext('2d');

                if (chartPacientes) chartPacientes.destroy();

                chartPacientes = new Chart(ctxPacientes, {
                    type: 'bar',
                    data: {
                        labels: ['SUS', 'SIN SUS'],
                        datasets: [{
                            label: 'Pacientes',
                            data: [data.pacientes.SUS, data.pacientes.SINSUS],
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(249, 115, 22, 0.8)'
                            ],
                            borderColor: [
                                'rgb(59, 130, 246)',
                                'rgb(249, 115, 22)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // ===== REPORTES POR PERSONAL =====

            async function cargarListaPersonal() {
                try {
                    const response = await fetch('/api/supervisor/estadisticas/personal/lista');
                    const data = await response.json();

                    if (data.success) {
                        const select = document.getElementById('selectPersonal');
                        select.innerHTML = '<option value="">Seleccione un personal...</option>';

                        // Debug: Ver qué datos llegan
                        console.log('Datos del personal:', data.data);
                        console.log('Primer personal:', data.data[0]);

                        // Filtrar solo personal de imagen (codRol = 2)
                        const personalImagen = data.data.filter(p => {
                            console.log(`Personal ${p.nombre} - codRol: ${p.codRol}, tipo: ${typeof p.codRol}`);
                            return p.codRol === 2 || p.codRol === '2';
                        });

                        console.log('Personal de imagen filtrado:', personalImagen);

                        personalImagen.forEach(p => {
                            const option = document.createElement('option');
                            option.value = p.codPer;
                            option.textContent = `${p.nombre} (${p.usuario})`;
                            select.appendChild(option);
                        });

                        if (personalImagen.length === 0) {
                            console.warn('No se encontró personal de imagen con codRol = 2');
                        }
                    }
                } catch (error) {
                    console.error('Error al cargar personal:', error);
                }
            }

            async function buscarEstadisticasPersonal() {
                const codPer = document.getElementById('selectPersonal').value;
                const fecha = document.getElementById('fechaReporte').value;

                if (!codPer) {
                    mostrarAlerta('error', 'Por favor seleccione un personal');
                    return;
                }

                if (!fecha) {
                    mostrarAlerta('error', 'Por favor seleccione una fecha');
                    return;
                }

                mostrarLoader(true);

                try {
                    const url = `/api/supervisor/estadisticas/personal?codPer=${codPer}&fecha=${fecha}&periodo=mes`;
                    const response = await fetch(url);
                    const data = await response.json();

                    if (data.success) {
                        mostrarResultadosPersonal(data.data);
                    } else {
                        mostrarAlerta('error', data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('error', 'Error al obtener estadísticas del personal');
                } finally {
                    mostrarLoader(false);
                }
            }

            function mostrarResultadosPersonal(data) {
                const container = document.getElementById('resultadosPersonal');

                const html = `
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">${data.personal.nombre}</h3>
                                <p class="text-sm text-gray-600">Usuario: ${data.personal.usuario}</p>
                                <p class="text-sm text-gray-600">Periodo: ${new Date(data.periodo.fechaInicio).toLocaleDateString('es-ES')} - ${new Date(data.periodo.fechaFin).toLocaleDateString('es-ES')}</p>
                            </div>
                            <button onclick="generarPDFPersonal()" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                <span class="material-icons mr-2">picture_as_pdf</span>
                                Generar PDF
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                <p class="text-sm text-blue-600 font-medium mb-1">Total Servicios</p>
                                <p class="text-3xl font-bold text-blue-700">${data.resumen.totalServicios}</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                <p class="text-sm text-green-600 font-medium mb-1">Atendidos</p>
                                <p class="text-3xl font-bold text-green-700">${data.resumen.atendidos}</p>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                                <p class="text-sm text-purple-600 font-medium mb-1">Por Estado</p>
                                <p class="text-xs text-purple-700">${Object.entries(data.resumen.porEstado).map(([k,v]) => `${k}: ${v}`).join(', ')}</p>
                            </div>
                            <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                                <p class="text-sm text-orange-600 font-medium mb-1">Por Tipo</p>
                                <p class="text-xs text-orange-700">${Object.entries(data.resumen.porTipoAseg).map(([k,v]) => `${k}: ${v}`).join(', ')}</p>
                            </div>
                        </div>
                    </div>
                `;

                container.innerHTML = html;
                container.classList.remove('hidden');

                // Guardar datos para PDF
                window.datosReporteActual = {
                    codPer: data.personal.codPer,
                    fecha: document.getElementById('fechaReporte').value,
                    periodo: 'mes'
                };
            }

            function getEstadoClass(estado) {
                const classes = {
                    'Programado': 'bg-blue-100 text-blue-800',
                    'EnProceso': 'bg-yellow-100 text-yellow-800',
                    'Atendido': 'bg-green-100 text-green-800',
                    'Entregado': 'bg-purple-100 text-purple-800',
                    'Cancelado': 'bg-red-100 text-red-800'
                };
                return classes[estado] || 'bg-gray-100 text-gray-800';
            }

            function generarPDFPersonal() {
                if (!window.datosReporteActual) {
                    mostrarAlerta('error', 'No hay datos para generar el reporte');
                    return;
                }

                const { codPer, fecha } = window.datosReporteActual;
                const url = `/api/supervisor/estadisticas/personal/reporte-pdf?codPer=${codPer}&fecha=${fecha}&periodo=mes`;
                window.open(url, '_blank');
            }

            function verDetallesPersonal(codPer) {
                document.getElementById('selectPersonal').value = codPer;
                document.getElementById('fechaReporte').value = new Date().toISOString().split('T')[0];
                buscarEstadisticasPersonal();

                // Scroll a la sección
                document.getElementById('seccionReportes').scrollIntoView({ behavior: 'smooth' });
            }

            // ===== UTILIDADES =====

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
        </script>
    @endpush

    <div class="space-y-6">
        <!-- Alerta -->
        <div id="alerta" class="hidden"></div>

        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl shadow-2xl p-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2 flex items-center gap-3">
                        <span class="material-icons text-5xl">analytics</span>
                        Estadísticas del Sistema
                    </h1>
                    <p class="text-purple-100 text-lg">Panel completo de análisis y métricas del sistema</p>
                </div>
                <div class="hidden lg:block">
                    <button onclick="cargarEstadisticasGenerales()"
                        class="inline-flex items-center px-6 py-3 bg-white text-purple-600 rounded-xl hover:shadow-xl transition-all font-medium">
                        <span class="material-icons mr-2">refresh</span>
                        Actualizar
                    </button>
                </div>
            </div>
        </div>

        <!-- Loader -->
        <div id="loader" class="hidden flex justify-center items-center py-12">
            <div class="relative">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-purple-600"></div>
                <div class="absolute top-0 left-0 h-16 w-16 rounded-full border-4 border-purple-200"></div>
            </div>
        </div>

        <!-- Estadísticas Rápidas de Servicios -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="stat-card bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase">Hoy</p>
                        <p class="text-3xl font-bold text-gray-900" id="stat-servicios-hoy">0</p>
                        <p class="text-xs text-gray-500 mt-1">servicios</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-4 shadow-lg">
                        <span class="material-icons text-white text-3xl">today</span>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase">Semana</p>
                        <p class="text-3xl font-bold text-gray-900" id="stat-servicios-semana">0</p>
                        <p class="text-xs text-gray-500 mt-1">servicios</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-4 shadow-lg">
                        <span class="material-icons text-white text-3xl">date_range</span>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase">Mes</p>
                        <p class="text-3xl font-bold text-gray-900" id="stat-servicios-mes">0</p>
                        <p class="text-xs text-gray-500 mt-1">servicios</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-4 shadow-lg">
                        <span class="material-icons text-white text-3xl">calendar_month</span>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase">Año</p>
                        <p class="text-3xl font-bold text-gray-900" id="stat-servicios-anio">0</p>
                        <p class="text-xs text-gray-500 mt-1">servicios</p>
                    </div>
                    <div class="bg-gradient-to-br from-orange-500 to-red-600 rounded-xl p-4 shadow-lg">
                        <span class="material-icons text-white text-3xl">event</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas de Atendidos -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="stat-card bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl shadow-lg p-6 border-2 border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600 uppercase">Atendidos Hoy</p>
                        <p class="text-4xl font-bold text-green-700" id="stat-atendidos-hoy">0</p>
                    </div>
                    <span class="material-icons text-green-600 text-5xl">check_circle</span>
                </div>
            </div>

            <div class="stat-card bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-lg p-6 border-2 border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600 uppercase">Atendidos Semana</p>
                        <p class="text-4xl font-bold text-blue-700" id="stat-atendidos-semana">0</p>
                    </div>
                    <span class="material-icons text-blue-600 text-5xl">verified</span>
                </div>
            </div>

            <div class="stat-card bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl shadow-lg p-6 border-2 border-purple-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-600 uppercase">Atendidos Mes</p>
                        <p class="text-4xl font-bold text-purple-700" id="stat-atendidos-mes">0</p>
                    </div>
                    <span class="material-icons text-purple-600 text-5xl">task_alt</span>
                </div>
            </div>
        </div>

        <!-- Gráficos Principales -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Servicios por Día -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="material-icons text-blue-600">show_chart</span>
                    Servicios Solicitados (Últimos 30 días)
                </h3>
                <div class="chart-container">
                    <canvas id="chartServicios"></canvas>
                </div>
            </div>

            <!-- Atendidos por Día -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="material-icons text-green-600">bar_chart</span>
                    Servicios Atendidos (Últimos 30 días)
                </h3>
                <div class="chart-container">
                    <canvas id="chartAtendidos"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráficos Adicionales -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Por Estado -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="material-icons text-indigo-600">donut_large</span>
                    Por Estado
                </h3>
                <div class="chart-container">
                    <canvas id="chartEstados"></canvas>
                </div>
            </div>

            <!-- Por Tipo de Aseguramiento -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="material-icons text-purple-600">pie_chart</span>
                    Por Tipo Aseguramiento
                </h3>
                <div class="chart-container">
                    <canvas id="chartTipoAseg"></canvas>
                </div>
            </div>

            <!-- Pacientes -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="material-icons text-orange-600">people</span>
                    Pacientes SUS vs SIN SUS
                </h3>
                <div class="chart-container">
                    <canvas id="chartPacientes"></canvas>
                </div>
            </div>
        </div>

        <!-- Estadísticas de Pacientes y Personal -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl shadow-lg p-6 border-2 border-blue-200">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="material-icons text-blue-600">group</span>
                    Estadísticas de Pacientes
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow">
                        <p class="text-sm text-gray-600 mb-1">Total Pacientes</p>
                        <p class="text-3xl font-bold text-blue-700" id="stat-pacientes-total">0</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow">
                        <p class="text-sm text-gray-600 mb-1">Pacientes Activos</p>
                        <p class="text-3xl font-bold text-green-700" id="stat-pacientes-activos">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl shadow-lg p-6 border-2 border-purple-200">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="material-icons text-purple-600">badge</span>
                    Estadísticas de Personal
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow">
                        <p class="text-sm text-gray-600 mb-1">Total Personal</p>
                        <p class="text-3xl font-bold text-purple-700" id="stat-personal-total">0</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow">
                        <p class="text-sm text-gray-600 mb-1">Personal Activo</p>
                        <p class="text-3xl font-bold text-green-700" id="stat-personal-activo">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Más Productivo del Mes -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="material-icons text-yellow-500 text-3xl">emoji_events</span>
                    Top 5 Personal Más Productivo del Mes
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left font-bold">Personal</th>
                            <th class="px-6 py-4 text-center font-bold">Servicios Atendidos</th>
                            <th class="px-6 py-4 text-right font-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-personal-productivo">
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <span class="material-icons text-6xl text-gray-300 mb-2">hourglass_empty</span>
                                    <p>Cargando datos...</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sección de Reportes por Personal -->
        <div id="seccionReportes" class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center gap-2 mb-6">
                <span class="material-icons text-indigo-600 text-3xl">assignment</span>
                <h2 class="text-2xl font-bold text-gray-900">Reportes por Personal</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Personal de Imagen <span class="text-red-600">*</span>
                    </label>
                    <select id="selectPersonal"
                        class="w-full bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 p-3 transition">
                        <option value="">Seleccione...</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Fecha <span class="text-red-600">*</span>
                    </label>
                    <input type="date" id="fechaReporte"
                        class="w-full bg-gray-50 border-2 border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 p-3 transition">
                </div>

                <div class="flex items-end">
                    <button onclick="buscarEstadisticasPersonal()"
                        class="w-full inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl font-medium">
                        <span class="material-icons mr-2">search</span>
                        Buscar
                    </button>
                </div>
            </div>

            <div id="resultadosPersonal" class="hidden"></div>
        </div>
    </div>

@endsection
