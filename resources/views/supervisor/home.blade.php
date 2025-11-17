@extends('supervisor.layouts.supervisor')
@section('title', 'Inicio - Supervisor')
@section('content')
    <div class="space-y-6">
        <!-- Hero de Bienvenida -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-2xl p-8 text-white">
            <div class="flex items-center justify-between">
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <span class="material-icons text-4xl">admin_panel_settings</span>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold">¡Bienvenido, {{ Auth::user()->nomPer }}!</h1>
                            <p class="text-blue-100 text-lg">Panel de Supervisión - Sistema Clínica</p>
                        </div>
                    </div>
                    <p class="text-blue-50 max-w-2xl text-lg">
                        Desde este panel puedes administrar el personal del sistema y visualizar estadísticas importantes de la clínica.
                    </p>
                    <div class="flex items-center gap-2 text-sm text-blue-100">
                        <span class="material-icons text-lg">event</span>
                        <span>{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</span>
                    </div>
                </div>
                <div class="hidden lg:block">
                    <div class="w-48 h-48 bg-white bg-opacity-10 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <span class="material-icons" style="font-size: 120px; opacity: 0.3;">
                            supervisor_account
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center gap-2 mb-6">
                <span class="material-icons text-blue-600 text-3xl">rocket_launch</span>
                <h2 class="text-2xl font-bold text-gray-800">Accesos Rápidos</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Gestión de Personal -->
                <a href="{{ route('supervisor.gestion-personal.gestion-personal') }}" 
                   class="group bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-6 hover:shadow-xl transition-all transform hover:-translate-y-1 hover:border-blue-400">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            <span class="material-icons text-white text-2xl">badge</span>
                        </div>
                        <span class="material-icons text-blue-400 group-hover:text-blue-600 transition-colors">arrow_forward</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Gestión de Personal</h3>
                    <p class="text-gray-600 text-sm mb-4">
                        Administra los usuarios del sistema, crea nuevos accesos, modifica roles y gestiona el personal clínico.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">Crear usuarios</span>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">Editar perfiles</span>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">Asignar roles</span>
                    </div>
                </a>

                <!-- Estadísticas -->
                <a href="{{ route('supervisor.estadisticas.estadisticas') }}" 
                   class="group bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl p-6 hover:shadow-xl transition-all transform hover:-translate-y-1 hover:border-purple-400">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            <span class="material-icons text-white text-2xl">bar_chart</span>
                        </div>
                        <span class="material-icons text-purple-400 group-hover:text-purple-600 transition-colors">arrow_forward</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Estadísticas del Sistema</h3>
                    <p class="text-gray-600 text-sm mb-4">
                        Visualiza reportes, gráficos y métricas importantes sobre el funcionamiento de la clínica.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">Reportes</span>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">Gráficos</span>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">Métricas</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Guía de Funcionalidades -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Tu Rol como Supervisor -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-icons text-blue-600 text-2xl">info</span>
                    <h3 class="text-xl font-bold text-gray-800">Tu Rol como Supervisor</h3>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="material-icons text-white text-sm">verified_user</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-1">Control Total de Accesos</h4>
                            <p class="text-sm text-gray-600">Tienes la capacidad de crear, modificar y gestionar todos los usuarios del sistema clínico.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-indigo-50 rounded-lg">
                        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="material-icons text-white text-sm">analytics</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-1">Monitoreo y Análisis</h4>
                            <p class="text-sm text-gray-600">Accede a estadísticas detalladas para tomar decisiones informadas sobre el personal.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-purple-50 rounded-lg">
                        <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="material-icons text-white text-sm">security</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-1">Seguridad del Sistema</h4>
                            <p class="text-sm text-gray-600">Garantiza que solo el personal autorizado tenga acceso a las funciones correspondientes.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-icons text-blue-600 text-2xl">bolt</span>
                    <h3 class="text-xl font-bold text-gray-800">Acciones Rápidas</h3>
                </div>
                <div class="space-y-3">
                    <a href="{{ route('supervisor.gestion-personal.agregar') }}" 
                       class="flex items-center gap-3 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg hover:shadow-md transition-all group">
                        <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-icons text-white">person_add</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800">Agregar Nuevo Personal</h4>
                            <p class="text-xs text-gray-500">Crear un nuevo usuario en el sistema</p>
                        </div>
                        <span class="material-icons text-green-600 group-hover:translate-x-1 transition-transform">chevron_right</span>
                    </a>

                    <a href="{{ route('supervisor.gestion-personal.gestion-personal') }}" 
                       class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg hover:shadow-md transition-all group">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-icons text-white">edit</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800">Ver Todo el Personal</h4>
                            <p class="text-xs text-gray-500">Lista completa de usuarios del sistema</p>
                        </div>
                        <span class="material-icons text-blue-600 group-hover:translate-x-1 transition-transform">chevron_right</span>
                    </a>

                    <a href="{{ route('supervisor.estadisticas.estadisticas') }}" 
                       class="flex items-center gap-3 p-4 bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg hover:shadow-md transition-all group">
                        <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-icons text-white">insights</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800">Ver Estadísticas</h4>
                            <p class="text-xs text-gray-500">Análisis y reportes del sistema</p>
                        </div>
                        <span class="material-icons text-purple-600 group-hover:translate-x-1 transition-transform">chevron_right</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Información del Sistema -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl border border-gray-200 p-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="material-icons text-white text-2xl">lightbulb</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">💡 Consejo</h3>
                    <p class="text-gray-700 mb-3">
                        Como supervisor, tu responsabilidad principal es mantener actualizada la información del personal. 
                        Asegúrate de revisar regularmente los accesos y roles asignados para garantizar la seguridad del sistema.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-white text-gray-700 text-xs font-medium rounded-full border border-gray-300">
                            <span class="material-icons text-xs align-middle">check_circle</span> Revisa permisos periódicamente
                        </span>
                        <span class="px-3 py-1 bg-white text-gray-700 text-xs font-medium rounded-full border border-gray-300">
                            <span class="material-icons text-xs align-middle">check_circle</span> Mantén datos actualizados
                        </span>
                        <span class="px-3 py-1 bg-white text-gray-700 text-xs font-medium rounded-full border border-gray-300">
                            <span class="material-icons text-xs align-middle">check_circle</span> Monitorea estadísticas
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .group:hover .material-icons {
            animation: float 2s ease-in-out infinite;
        }
    </style>
@endsection