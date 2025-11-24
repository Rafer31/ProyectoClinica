@extends('enfermera.layouts.enfermera')
@section('title', 'Inicio - Enfermera')
@section('content')
    <div class="space-y-6">
        <!-- Hero de Bienvenida -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl shadow-2xl p-8 text-white">
            <div class="flex items-center justify-between">
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <span class="material-icons text-4xl">local_hospital</span>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold">¡Bienvenida, {{ Auth::user()->nomPer }}!</h1>
                            <p class="text-purple-100 text-lg">Panel de Enfermería - Sistema Clínica</p>
                        </div>
                    </div>
                    <p class="text-purple-50 max-w-2xl text-lg">
                        Gestiona las fichas de atención de los pacientes desde el calendario interactivo. Puedes registrar nuevos pacientes directamente en los horarios disponibles.
                    </p>
                    <div class="flex items-center gap-2 text-sm text-purple-100">
                        <span class="material-icons text-lg">event</span>
                        <span>{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</span>
                    </div>
                </div>
                <div class="hidden lg:block">
                    <div class="w-48 h-48 bg-white bg-opacity-10 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <span class="material-icons" style="font-size: 120px; opacity: 0.3;">
                            health_and_safety
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center gap-2 mb-6">
                <span class="material-icons text-purple-600 text-3xl">rocket_launch</span>
                <h2 class="text-2xl font-bold text-gray-800">Acceso Rápido</h2>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <!-- Calendario de Atención -->
                <a href="{{ route('enfermera.calendario.atencion') }}"
                   class="group bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl p-6 hover:shadow-xl transition-all transform hover:-translate-y-1 hover:border-purple-400">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            <span class="material-icons text-white text-2xl">event_available</span>
                        </div>
                        <span class="material-icons text-purple-400 group-hover:text-purple-600 transition-colors">arrow_forward</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Calendario de Atención</h3>
                    <p class="text-gray-600 text-sm mb-4">
                        Administra los horarios de atención cada media hora. Registra pacientes directamente en los espacios disponibles del día activo.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">Fichas cada 30 min</span>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">Registro rápido</span>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">08:00 - 20:00</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Guía de Funcionalidades -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Tu Rol como Enfermera -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-icons text-purple-600 text-2xl">info</span>
                    <h3 class="text-xl font-bold text-gray-800">Tu Rol como Enfermera</h3>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start gap-3 p-3 bg-purple-50 rounded-lg">
                        <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="material-icons text-white text-sm">schedule</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-1">Gestión de Horarios</h4>
                            <p class="text-sm text-gray-600">Organiza las fichas de atención cada media hora, desde las 8:00 AM hasta las 8:00 PM.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-pink-50 rounded-lg">
                        <div class="w-8 h-8 bg-pink-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="material-icons text-white text-sm">person_add</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-1">Registro de Pacientes</h4>
                            <p class="text-sm text-gray-600">Haz clic en cualquier horario disponible para registrar un nuevo paciente en ese espacio.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-purple-50 rounded-lg">
                        <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="material-icons text-white text-sm">visibility</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-1">Visualización en Tiempo Real</h4>
                            <p class="text-sm text-gray-600">El calendario se actualiza automáticamente mostrando las fichas ocupadas y disponibles.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Sistema -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-icons text-purple-600 text-2xl">help</span>
                    <h3 class="text-xl font-bold text-gray-800">¿Cómo Funciona?</h3>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center flex-shrink-0 text-white font-bold text-sm">
                            1
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-1">Accede al Calendario</h4>
                            <p class="text-sm text-gray-600">Ve a "Calendario de Atención" en el menú lateral para ver los horarios del día activo.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center flex-shrink-0 text-white font-bold text-sm">
                            2
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-1">Selecciona un Horario</h4>
                            <p class="text-sm text-gray-600">Haz clic en cualquier espacio disponible (marcado en verde) para abrir el formulario de registro.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center flex-shrink-0 text-white font-bold text-sm">
                            3
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-1">Completa los Datos</h4>
                            <p class="text-sm text-gray-600">Llena la información del paciente y guarda para asignar la ficha al horario seleccionado.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center flex-shrink-0 text-white font-bold text-sm">
                            4
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-1">Visualiza las Fichas</h4>
                            <p class="text-sm text-gray-600">Las fichas aparecerán con colores según su estado: programado, en proceso, atendido o entregado.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leyenda de Estados -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="material-icons text-purple-600 text-2xl">palette</span>
                <h3 class="text-xl font-bold text-gray-800">Leyenda de Estados</h3>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div class="flex flex-col items-center p-3 bg-gradient-to-br from-emerald-50 to-emerald-100 border-2 border-emerald-300 rounded-lg">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-emerald-200 border-2 border-emerald-400 rounded-lg mb-2"></div>
                    <span class="text-xs font-bold text-gray-700 text-center">Disponible</span>
                </div>
                <div class="flex flex-col items-center p-3 bg-orange-50 border-2 border-orange-300 rounded-lg">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg mb-2"></div>
                    <span class="text-xs font-bold text-gray-700 text-center">Programado</span>
                </div>
                <div class="flex flex-col items-center p-3 bg-blue-50 border-2 border-blue-300 rounded-lg">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg mb-2"></div>
                    <span class="text-xs font-bold text-gray-700 text-center">En Proceso</span>
                </div>
                <div class="flex flex-col items-center p-3 bg-emerald-50 border-2 border-emerald-300 rounded-lg">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg mb-2"></div>
                    <span class="text-xs font-bold text-gray-700 text-center">Atendido</span>
                </div>
                <div class="flex flex-col items-center p-3 bg-purple-50 border-2 border-purple-300 rounded-lg">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg mb-2"></div>
                    <span class="text-xs font-bold text-gray-700 text-center">Entregado</span>
                </div>
            </div>
        </div>

        <!-- Consejo -->
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border-2 border-purple-300 p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="material-icons text-white text-2xl">lightbulb</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">💡 Consejo Importante</h3>
                    <p class="text-gray-700 mb-3">
                        Los horarios están divididos en intervalos de media hora (30 minutos), con solo una ficha disponible por cada intervalo.
                        Esto permite una mejor organización y seguimiento de cada paciente durante el día.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-white text-gray-700 text-xs font-medium rounded-full border border-purple-300">
                            <span class="material-icons text-xs align-middle">check_circle</span> 1 ficha cada 30 minutos
                        </span>
                        <span class="px-3 py-1 bg-white text-gray-700 text-xs font-medium rounded-full border border-purple-300">
                            <span class="material-icons text-xs align-middle">check_circle</span> Horario 08:00 - 20:00
                        </span>
                        <span class="px-3 py-1 bg-white text-gray-700 text-xs font-medium rounded-full border border-purple-300">
                            <span class="material-icons text-xs align-middle">check_circle</span> Registro inmediato
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
