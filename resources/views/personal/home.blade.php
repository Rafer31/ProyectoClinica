@extends('personal.layouts.personal')
@section('title', 'Inicio - Personal')
@section('content')
    <div class="space-y-6">
        <!-- Encabezado -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="material-icons text-white text-2xl">dashboard</span>
                        </div>
                        Panel de Control
                    </h1>
                    <p class="text-gray-700 font-medium ml-15">Bienvenido, {{ Auth::user()->nomPer }} {{ Auth::user()->paternoPer }}</p>
                    <p class="text-sm text-emerald-600 font-medium ml-15">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
                </div>

                <!-- Botón Generar Reporte -->
                <div class="relative">
                    <button id="btn-menu-reporte"
                        class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-rose-500 to-red-600 text-white rounded-xl hover:from-rose-600 hover:to-red-700 transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                        <span class="material-icons">picture_as_pdf</span>
                        <span class="font-semibold">Generar Reporte</span>
                        <span class="material-icons text-sm">expand_more</span>
                    </button>

                    <!-- Menú Desplegable -->
                    <div id="menu-reporte"
                        class="hidden absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden">
                        <div class="py-2">
                            <a href="{{ route('personal.reportes.dia') }}" target="_blank"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 transition-colors group">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                    <span class="material-icons text-blue-600">today</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Reporte del Día</p>
                                    <p class="text-xs text-gray-500">{{ now()->format('d/m/Y') }}</p>
                                </div>
                            </a>
                            <a href="{{ route('personal.reportes.semana') }}" target="_blank"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 transition-colors group">
                                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                                    <span class="material-icons text-emerald-600">date_range</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Reporte Semanal</p>
                                    <p class="text-xs text-gray-500">Últimos 7 días</p>
                                </div>
                            </a>
                            <a href="{{ route('personal.reportes.mes') }}" target="_blank"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 transition-colors group">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                                    <span class="material-icons text-purple-600">calendar_month</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">Reporte Mensual</p>
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
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <span class="material-icons text-3xl">today</span>
                    </div>
                    <span class="text-xs font-bold bg-white bg-opacity-25 px-3 py-1 rounded-full">HOY</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="stat-hoy">0</p>
                <p class="text-sm opacity-90 font-medium">Pacientes Atendidos</p>
            </div>

            <!-- Pacientes Esta Semana -->
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <span class="material-icons text-3xl">date_range</span>
                    </div>
                    <span class="text-xs font-bold bg-white bg-opacity-25 px-3 py-1 rounded-full">SEMANA</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="stat-semana">0</p>
                <p class="text-sm opacity-90 font-medium">Últimos 7 días</p>
            </div>

            <!-- Pacientes Este Mes -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <span class="material-icons text-3xl">calendar_month</span>
                    </div>
                    <span class="text-xs font-bold bg-white bg-opacity-25 px-3 py-1 rounded-full">MES</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="stat-mes">0</p>
                <p class="text-sm opacity-90 font-medium">{{ now()->format('F Y') }}</p>
            </div>

            <!-- Total General -->
            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 text-white transform transition-all hover:scale-105">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <span class="material-icons text-3xl">analytics</span>
                    </div>
                    <span class="text-xs font-bold bg-white bg-opacity-25 px-3 py-1 rounded-full">TOTAL</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="stat-total">0</p>
                <p class="text-sm opacity-90 font-medium">Todos los pacientes</p>
            </div>
        </div>

        <!-- Gráficos y Detalles -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Servicios por Estado -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-white">assignment</span>
                    </div>
                    Servicios por Estado
                </h3>
                <div class="space-y-3" id="servicios-estado">
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-200 hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center shadow-md">
                                <span class="material-icons text-white">schedule</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-800">Programados</span>
                        </div>
                        <span class="text-2xl font-bold text-amber-600" id="estado-programado">0</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-200 hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-md">
                                <span class="material-icons text-white">pending_actions</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-800">En Proceso</span>
                        </div>
                        <span class="text-2xl font-bold text-blue-600" id="estado-proceso">0</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl border border-emerald-200 hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center shadow-md">
                                <span class="material-icons text-white">check_circle</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-800">Atendidos</span>
                        </div>
                        <span class="text-2xl font-bold text-emerald-600" id="estado-atendido">0</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl border border-purple-200 hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center shadow-md">
                                <span class="material-icons text-white">done_all</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-800">Entregados</span>
                        </div>
                        <span class="text-2xl font-bold text-purple-600" id="estado-entregado">0</span>
                    </div>
                </div>
            </div>

            <!-- Tipos de Seguro -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-white">security</span>
                    </div>
                    Distribución por Tipo de Seguro
                </h3>
                <div class="space-y-3" id="tipos-seguro">
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-red-50 to-rose-50 rounded-xl border border-red-200 hover:shadow-md transition-all">
                        <span class="text-sm font-semibold text-gray-800">Asegurado - Emergencia</span>
                        <span class="text-2xl font-bold text-red-600" id="aseg-emergencia">0</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl border border-emerald-200 hover:shadow-md transition-all">
                        <span class="text-sm font-semibold text-gray-800">Asegurado - Regular</span>
                        <span class="text-2xl font-bold text-emerald-600" id="aseg-regular">0</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border border-orange-200 hover:shadow-md transition-all">
                        <span class="text-sm font-semibold text-gray-800">No Asegurado - Emergencia</span>
                        <span class="text-2xl font-bold text-orange-600" id="noaseg-emergencia">0</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-200 hover:shadow-md transition-all">
                        <span class="text-sm font-semibold text-gray-800">No Asegurado - Regular</span>
                        <span class="text-2xl font-bold text-blue-600" id="noaseg-regular">0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimos Servicios -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-emerald-200">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-white">history</span>
                    </div>
                    Últimos Servicios Atendidos
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nro. Servicio</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Paciente</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tipo Estudio</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody id="ultimos-servicios" class="divide-y divide-gray-200">
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-emerald-200 border-t-emerald-600"></div>
                                    <span class="text-sm font-medium text-gray-600">Cargando servicios...</span>
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
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                <span class="material-icons text-gray-400 text-3xl">inbox</span>
                            </div>
                            <p class="text-gray-500 font-medium">No hay servicios recientes</p>
                        </div>
                    </td>
                </tr>
            `;
                return;
            }

            tbody.innerHTML = servicios.map(servicio => {
                const paciente = `${servicio.paciente?.nomPa || ''} ${servicio.paciente?.paternoPa || ''}`;
                const fecha = formatFecha(servicio.fechaAten);

                let estadoClass = '';
                if (servicio.estado === 'Programado') estadoClass = 'bg-amber-100 text-amber-700 border border-amber-300';
                else if (servicio.estado === 'EnProceso') estadoClass = 'bg-blue-100 text-blue-700 border border-blue-300';
                else if (servicio.estado === 'Atendido') estadoClass = 'bg-emerald-100 text-emerald-700 border border-emerald-300';
                else estadoClass = 'bg-purple-100 text-purple-700 border border-purple-300';

                return `
                <tr class="hover:bg-emerald-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-bold text-emerald-600">${servicio.nroServ}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-semibold text-gray-800">${paciente}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-700">${servicio.tipo_estudio?.descripcion || 'N/A'}</td>
                    <td class="px-6 py-4 text-gray-600">${fecha}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1.5 text-xs font-bold rounded-full ${estadoClass}">
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