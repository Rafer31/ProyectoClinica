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

            async function verDetallesPersonal(codPer) {
                const modal = document.getElementById('modal-detalles-personal');
                const contenido = document.getElementById('detalles-personal-content');

                try {
                    modal.classList.remove('hidden');
                    contenido.innerHTML = `
                        <div class="text-center py-12">
                            <div class="inline-block animate-spin rounded-full h-16 w-16 border-4 border-indigo-200 border-t-indigo-600"></div>
                            <p class="mt-4 text-gray-600 font-medium">Cargando estadísticas detalladas...</p>
                        </div>
                    `;

                    const response = await fetch(`/api/supervisor/estadisticas/personal?codPer=${codPer}`);
                    const data = await response.json();

                    if (data.success) {
                        const p = data.data.personal;
                        const r = data.data.resumen;

                        // Generar HTML para tipos de estudio
                        const tiposEstudioHTML = r.porTipoEstudio && r.porTipoEstudio.length > 0
                            ? r.porTipoEstudio.map((te, idx) => `
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                                            ${idx + 1}
                                        </div>
                                        <span class="font-semibold text-gray-900">${te.descripcion}</span>
                                    </div>
                                    <span class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-lg font-bold text-lg shadow-md">
                                        ${te.total}
                                    </span>
                                </div>
                            `).join('')
                            : '<p class="text-center text-gray-500 italic py-8">No hay tipos de estudios registrados</p>';

                        contenido.innerHTML = `
                            <div class="space-y-6">
                                <!-- Encabezado con nombre -->
                                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 rounded-xl border-2 border-indigo-200">
                                    <div class="flex items-center gap-4">
                                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex items-center justify-center">
                                            <span class="material-icons text-white text-3xl">person</span>
                                        </div>
                                        <div>
                                            <h3 class="text-2xl font-bold text-gray-900">${p.nombre}</h3>
                                            <p class="text-sm text-indigo-600 font-medium">Usuario: ${p.usuario}  |  ID: ${p.codPer}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Estadísticas generales -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 p-5 rounded-xl border-2 border-blue-200">
                                        <p class="text-sm text-blue-600 font-semibold uppercase mb-2">Total Servicios</p>
                                        <p class="text-4xl font-bold text-blue-700">${r.totalServicios}</p>
                                    </div>
                                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-5 rounded-xl border-2 border-green-200">
                                        <p class="text-sm text-green-600 font-semibold uppercase mb-2">Atendidos</p>
                                        <p class="text-4xl font-bold text-green-700">${r.atendidos}</p>
                                    </div>
                                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-5 rounded-xl border-2 border-purple-200">
                                        <p class="text-sm text-purple-600 font-semibold uppercase mb-2">Tasa Atención</p>
                                        <p class="text-4xl font-bold text-purple-700">${r.totalServicios > 0 ? Math.round((r.atendidos / r.totalServicios) * 100) : 0}%</p>
                                    </div>
                                </div>

                                <!-- Tipos de estudios más realizados -->
                                <div class="bg-white p-6 rounded-xl border-2 border-gray-200">
                                    <h4 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                        <span class="material-icons text-purple-600">science</span>
                                        Tipos de Estudios Más Realizados (Este Mes)
                                    </h4>
                                    <div class="space-y-3">
                                        ${tiposEstudioHTML}
                                    </div>
                                </div>

                                <!-- Estados de servicios -->
                                <div class="bg-white p-6 rounded-xl border-2 border-gray-200">
                                    <h4 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                        <span class="material-icons text-blue-600">pie_chart</span>
                                        Servicios por Estado
                                    </h4>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        ${Object.entries(r.porEstado || {}).map(([estado, total]) => `
                                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                                <p class="text-xs text-gray-600 font-semibold mb-1">${estado}</p>
                                                <p class="text-2xl font-bold text-gray-900">${total}</p>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        mostrarAlerta('error', data.message || 'Error al cargar los detalles');
                        cerrarModal('modal-detalles-personal');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('error', 'Error al cargar los detalles del personal');
                    cerrarModal('modal-detalles-personal');
                }
            }

            function cerrarModal(modalId) {
                const modal = document.getElementById(modalId);
                modal.classList.add('hidden');
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
                    Estado de Consulta
                </h3>
                <div class="chart-container">
                    <canvas id="chartEstados"></canvas>
                </div>
            </div>

            <!-- Por Tipo de Aseguramiento -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="material-icons text-purple-600">pie_chart</span>
                    Tipo de Seguro
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
                    Personal Más Productivo del Mes
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

        <!-- Reporte Consolidado Mensual -->
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl shadow-lg p-6 border-2 border-indigo-200">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="material-icons text-indigo-600 text-3xl">assessment</span>
                        Reporte Consolidado Mensual
                    </h3>
                    <p class="text-sm text-gray-600 mt-2">Genera un reporte con todos los servicios del personal de imagen en un mes específico</p>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-md">
                <form action="{{ route('supervisor.reportes.consolidado-mensual') }}" method="GET" target="_blank">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="mesConsolidado" class="block text-sm font-bold text-gray-700 mb-2">
                                <span class="material-icons text-sm align-middle mr-1">calendar_month</span>
                                Seleccionar Mes y Año
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <select id="mesSelect" required
                                    class="px-4 py-3 bg-gray-50 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                    <option value="01">Enero</option>
                                    <option value="02">Febrero</option>
                                    <option value="03">Marzo</option>
                                    <option value="04">Abril</option>
                                    <option value="05">Mayo</option>
                                    <option value="06">Junio</option>
                                    <option value="07">Julio</option>
                                    <option value="08">Agosto</option>
                                    <option value="09">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                                <select id="anioSelect" required
                                    class="px-4 py-3 bg-gray-50 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                    @for ($year = date('Y'); $year >= 2020; $year--)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                            <input type="hidden" id="mesConsolidado" name="mes" value="{{ date('Y-m') }}">
                            <p class="text-xs text-gray-500 mt-2">
                                <span class="material-icons text-xs align-middle">info</span>
                                El reporte incluirá servicios atendidos, entregados y cancelados
                            </p>
                            <script>
                                // Actualizar el mes actual por defecto
                                document.getElementById('mesSelect').value = '{{ date("m") }}';
                                document.getElementById('anioSelect').value = '{{ date("Y") }}';
                                
                                // Actualizar campo hidden cuando cambien los selects
                                function actualizarMesConsolidado() {
                                    const mes = document.getElementById('mesSelect').value;
                                    const anio = document.getElementById('anioSelect').value;
                                    document.getElementById('mesConsolidado').value = `${anio}-${mes}`;
                                }
                                
                                document.getElementById('mesSelect').addEventListener('change', actualizarMesConsolidado);
                                document.getElementById('anioSelect').addEventListener('change', actualizarMesConsolidado);
                                
                                // Inicializar
                                actualizarMesConsolidado();
                            </script>
                        </div>

                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                                <span class="material-icons mr-2">picture_as_pdf</span>
                                <span class="font-bold">Generar Reporte Consolidado</span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                        <h4 class="text-sm font-bold text-indigo-900 mb-2">El reporte incluye:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs text-indigo-700">
                            <div class="flex items-center gap-2">
                                <span class="material-icons text-sm text-green-600">check_circle</span>
                                <span>Total de servicios atendidos</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-icons text-sm text-purple-600">check_circle</span>
                                <span>Total de servicios entregados</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-icons text-sm text-red-600">cancel</span>
                                <span>Total de servicios cancelados</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-icons text-sm text-blue-600">groups</span>
                                <span>Desglose por personal de imagen</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <!-- Modal Detalles de Personal -->
        <div id="modal-detalles-personal"
            class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
            <div class="relative mx-auto p-0 border w-full max-w-4xl shadow-2xl rounded-xl bg-white max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div class="sticky top-0 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-xl p-6 z-10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                                <span class="material-icons text-white text-3xl">analytics</span>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">Estadísticas Detalladas</h3>
                                <p class="text-sm text-indigo-100">Información del personal del mes actual</p>
                            </div>
                        </div>
                        <button onclick="cerrarModal('modal-detalles-personal')"
                            class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div id="detalles-personal-content" class="p-6">
                    <!-- Contenido dinámico -->
                </div>
            </div>
        </div>

    </div>

@endsection
