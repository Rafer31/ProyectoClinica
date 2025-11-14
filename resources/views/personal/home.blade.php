@extends('personal.layouts.personal')
@section('title', 'Inicio - Personal')
@section('content')
    <div class="space-y-6">
        <!-- Encabezado -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        <span class="material-icons align-middle text-4xl text-blue-600">dashboard</span>
                        Panel de Control
                    </h1>
                    <p class="text-gray-600">Bienvenido, {{ Auth::user()->nomPer }} {{ Auth::user()->paternoPer }}</p>
                    <p class="text-sm text-gray-500">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
                </div>

                <!-- Botón Generar Reporte -->
                <div class="relative">
                    <button id="btn-menu-reporte"
                        class="flex items-center gap-2 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all hover:shadow-lg">
                        <span class="material-icons">picture_as_pdf</span>
                        <span class="font-medium">Generar Reporte</span>
                        <span class="material-icons text-sm">expand_more</span>
                    </button>

                    <!-- Menú Desplegable -->
                    <div id="menu-reporte"
                        class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                        <div class="py-2">
                            <a href="{{ route('personal.reportes.dia') }}" target="_blank"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                                <span class="material-icons text-blue-600">today</span>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Reporte del Día</p>
                                    <p class="text-xs text-gray-500">{{ now()->format('d/m/Y') }}</p>
                                </div>
                            </a>
                            <a href="{{ route('personal.reportes.semana') }}" target="_blank"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                                <span class="material-icons text-green-600">date_range</span>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Reporte Semanal</p>
                                    <p class="text-xs text-gray-500">Últimos 7 días</p>
                                </div>
                            </a>
                            <a href="{{ route('personal.reportes.mes') }}" target="_blank"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                                <span class="material-icons text-purple-600">calendar_month</span>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Reporte Mensual</p>
                                    <p class="text-xs text-gray-500">{{ now()->format('F Y') }}</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjetas de estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Pacientes Hoy -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-2xl">today</span>
                    </div>
                    <span class="text-xs bg-white bg-opacity-20 px-2 py-1 rounded">HOY</span>
                </div>
                <p class="text-3xl font-bold mb-1" id="stat-hoy">0</p>
                <p class="text-sm opacity-90">Pacientes Atendidos</p>
            </div>

            <!-- Pacientes Esta Semana -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-2xl">date_range</span>
                    </div>
                    <span class="text-xs bg-white bg-opacity-20 px-2 py-1 rounded">SEMANA</span>
                </div>
                <p class="text-3xl font-bold mb-1" id="stat-semana">0</p>
                <p class="text-sm opacity-90">Últimos 7 días</p>
            </div>

            <!-- Pacientes Este Mes -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-2xl">calendar_month</span>
                    </div>
                    <span class="text-xs bg-white bg-opacity-20 px-2 py-1 rounded">MES</span>
                </div>
                <p class="text-3xl font-bold mb-1" id="stat-mes">0</p>
                <p class="text-sm opacity-90">{{ now()->format('F Y') }}</p>
            </div>

            <!-- Total General -->
            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-2xl">analytics</span>
                    </div>
                    <span class="text-xs bg-white bg-opacity-20 px-2 py-1 rounded">TOTAL</span>
                </div>
                <p class="text-3xl font-bold mb-1" id="stat-total">0</p>
                <p class="text-sm opacity-90">Todos los pacientes</p>
            </div>
        </div>

        <!-- Gráficos y Detalles -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Servicios por Estado -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="material-icons text-blue-600">assignment</span>
                    Servicios por Estado
                </h3>
                <div class="space-y-3" id="servicios-estado">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                <span class="material-icons text-amber-600">schedule</span>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Programados</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900" id="estado-programado">0</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <span class="material-icons text-blue-600">pending_actions</span>
                            </div>
                            <span class="text-sm font-medium text-gray-700">En Proceso</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900" id="estado-proceso">0</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <span class="material-icons text-green-600">check_circle</span>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Atendidos</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900" id="estado-atendido">0</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <span class="material-icons text-purple-600">done_all</span>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Entregados</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900" id="estado-entregado">0</span>
                    </div>
                </div>
            </div>

            <!-- Tipos de Seguro -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="material-icons text-blue-600">security</span>
                    Distribución por Tipo de Seguro
                </h3>
                <div class="space-y-3" id="tipos-seguro">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Asegurado - Emergencia</span>
                        <span class="text-xl font-bold text-red-600" id="aseg-emergencia">0</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Asegurado - Regular</span>
                        <span class="text-xl font-bold text-green-600" id="aseg-regular">0</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">No Asegurado - Emergencia</span>
                        <span class="text-xl font-bold text-orange-600" id="noaseg-emergencia">0</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">No Asegurado - Regular</span>
                        <span class="text-xl font-bold text-blue-600" id="noaseg-regular">0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimos Servicios -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <span class="material-icons text-blue-600">history</span>
                    Últimos Servicios Atendidos
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nro. Servicio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo Estudio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody id="ultimos-servicios" class="divide-y divide-gray-200">
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600">
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Menú desplegable de reportes
        document.getElementById('btn-menu-reporte').addEventListener('click', function (e) {
            e.stopPropagation();
            document.getElementById('menu-reporte').classList.toggle('hidden');
        });

        // Cerrar menú al hacer clic fuera
        document.addEventListener('click', function () {
            document.getElementById('menu-reporte').classList.add('hidden');
        });

        // Cargar estadísticas
        async function cargarEstadisticas() {
            try {
                const response = await fetch('/api/personal/home/estadisticas');
                const data = await response.json();

                if (data.success) {
                    const stats = data.data;

                    // Estadísticas principales
                    document.getElementById('stat-hoy').textContent = stats.hoy || 0;
                    document.getElementById('stat-semana').textContent = stats.semana || 0;
                    document.getElementById('stat-mes').textContent = stats.mes || 0;
                    document.getElementById('stat-total').textContent = stats.total || 0;

                    // Servicios por estado
                    document.getElementById('estado-programado').textContent = stats.porEstado?.Programado || 0;
                    document.getElementById('estado-proceso').textContent = stats.porEstado?.EnProceso || 0;
                    document.getElementById('estado-atendido').textContent = stats.porEstado?.Atendido || 0;
                    document.getElementById('estado-entregado').textContent = stats.porEstado?.Entregado || 0;

                    // Tipos de seguro
                    document.getElementById('aseg-emergencia').textContent = stats.porTipoAseg?.AsegEmergencia || 0;
                    document.getElementById('aseg-regular').textContent = stats.porTipoAseg?.AsegRegular || 0;
                    document.getElementById('noaseg-emergencia').textContent = stats.porTipoAseg?.NoAsegEmergencia || 0;
                    document.getElementById('noaseg-regular').textContent = stats.porTipoAseg?.NoAsegRegular || 0;

                    // Últimos servicios
                    renderUltimosServicios(stats.ultimosServicios || []);
                }
            } catch (error) {
                console.error('Error al cargar estadísticas:', error);
            }
        }
        function formatFecha(fechaStr) {
            if (!fechaStr) return '—';

            // Si ya es número (timestamp)
            if (typeof fechaStr === 'number') {
                const d = new Date(fechaStr);
                if (!isNaN(d)) return new Intl.DateTimeFormat('es-ES', { day: '2-digit', month: 'long', year: 'numeric' }).format(d);
                return '—';
            }

            // Si viene en formato ISO o con espacio en lugar de 'T', normalizamos
            let s = String(fechaStr).trim();

            // Si viene en formato DD/MM/YYYY → convertir a YYYY-MM-DD
            const ddmmyyyy = /^(\d{2})\/(\d{2})\/(\d{4})$/;
            if (ddmmyyyy.test(s)) {
                const [, dd, mm, yyyy] = s.match(ddmmyyyy);
                s = `${yyyy}-${mm}-${dd}`;
            }

            // Si viene como 'YYYY-MM-DD' sin hora, añadimos 'T00:00:00' para que sea ISO
            const ymd = /^(\d{4})-(\d{2})-(\d{2})$/;
            if (ymd.test(s)) s = s + 'T00:00:00';

            // Reemplazar espacio entre fecha y hora por 'T' (p. ej. '2025-11-13 08:00:00')
            s = s.replace(' ', 'T');

            const d = new Date(s);
            if (isNaN(d)) return '—';

            return new Intl.DateTimeFormat('es-ES', { day: '2-digit', month: 'long', year: 'numeric' }).format(d);
        }

        function renderUltimosServicios(servicios) {
            const tbody = document.getElementById('ultimos-servicios');

            if (servicios.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        No hay servicios recientes
                    </td>
                </tr>
            `;
                return;
            }

            tbody.innerHTML = servicios.map(servicio => {
                const paciente = `${servicio.paciente?.nomPa || ''} ${servicio.paciente?.paternoPa || ''}`;
                const fecha = formatFecha(servicio.fechaAten);

                let estadoClass = '';
                if (servicio.estado === 'Programado') estadoClass = 'bg-amber-100 text-amber-800';
                else if (servicio.estado === 'EnProceso') estadoClass = 'bg-blue-100 text-blue-800';
                else if (servicio.estado === 'Atendido') estadoClass = 'bg-green-100 text-green-800';
                else estadoClass = 'bg-purple-100 text-purple-800';

                return `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap font-medium">${servicio.nroServ}</td>
                    <td class="px-6 py-4">${paciente}</td>
                    <td class="px-6 py-4">${servicio.tipo_estudio?.descripcion || 'N/A'}</td>
                    <td class="px-6 py-4">${fecha}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${estadoClass}">
                            ${servicio.estado}
                        </span>
                    </td>
                </tr>
            `;
            }).join('');
        }

        // Cargar al iniciar
        document.addEventListener('DOMContentLoaded', cargarEstadisticas);
    </script>
@endsection
