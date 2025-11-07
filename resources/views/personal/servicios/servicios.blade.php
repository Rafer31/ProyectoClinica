@extends('personal.layouts.personal')

@section('title', 'Servicios')

@section('content')
@php
$breadcrumbs = [
    ['label' => 'Inicio', 'url' => route('personal.home')],
    ['label' => 'Servicios']
];
@endphp

<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <span class="material-icons align-middle text-4xl text-blue-600">miscellaneous_services</span>
                    Servicios Médicos
                </h1>
                <p class="text-gray-600">Administra los servicios de diagnóstico por imagen</p>
            </div>
            <button class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <span class="material-icons me-2">add</span>
                Nuevo Servicio
            </button>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <span class="material-icons text-4xl opacity-80">analytics</span>
                <span class="bg-white bg-opacity-20 px-2 py-1 rounded text-xs">Hoy</span>
            </div>
            <p class="text-3xl font-bold">24</p>
            <p class="text-sm opacity-90">Servicios realizados</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <span class="material-icons text-4xl opacity-80">pending_actions</span>
                <span class="bg-white bg-opacity-20 px-2 py-1 rounded text-xs">Ahora</span>
            </div>
            <p class="text-3xl font-bold">8</p>
            <p class="text-sm opacity-90">En proceso</p>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <span class="material-icons text-4xl opacity-80">schedule</span>
                <span class="bg-white bg-opacity-20 px-2 py-1 rounded text-xs">Hoy</span>
            </div>
            <p class="text-3xl font-bold">12</p>
            <p class="text-sm opacity-90">Programados</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <span class="material-icons text-4xl opacity-80">category</span>
                <span class="bg-white bg-opacity-20 px-2 py-1 rounded text-xs">Total</span>
            </div>
            <p class="text-3xl font-bold">15</p>
            <p class="text-sm opacity-90">Tipos de servicios</p>
        </div>
    </div>

    <!-- Catálogo de servicios -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Radiografía -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-blue-600 text-3xl">x_ray</span>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Disponible</span>
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-2">Radiografía Digital</h3>
                <p class="text-sm text-gray-600 mb-4">Estudios radiológicos de alta resolución para diagnóstico preciso</p>

                <div class="space-y-2 mb-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Duración:</span>
                        <span class="font-medium text-gray-900">15-20 min</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Precio:</span>
                        <span class="font-medium text-green-600">Bs. 80</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Realizados hoy:</span>
                        <span class="font-medium text-blue-600">8</span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        Agendar
                    </button>
                    <button class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <span class="material-icons text-sm">edit</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Ecografía -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-purple-600 text-3xl">monitor_heart</span>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Disponible</span>
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-2">Ecografía</h3>
                <p class="text-sm text-gray-600 mb-4">Estudios ecográficos abdominales, pélvicos y de partes blandas</p>

                <div class="space-y-2 mb-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Duración:</span>
                        <span class="font-medium text-gray-900">30-40 min</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Precio:</span>
                        <span class="font-medium text-green-600">Bs. 150</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Realizados hoy:</span>
                        <span class="font-medium text-blue-600">6</span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        Agendar
                    </button>
                    <button class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <span class="material-icons text-sm">edit</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tomografía -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-green-600 text-3xl">medical_information</span>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Disponible</span>
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-2">Tomografía Computarizada</h3>
                <p class="text-sm text-gray-600 mb-4">Imágenes detalladas en 3D para diagnósticos complejos</p>

                <div class="space-y-2 mb-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Duración:</span>
                        <span class="font-medium text-gray-900">45-60 min</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Precio:</span>
                        <span class="font-medium text-green-600">Bs. 500</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Realizados hoy:</span>
                        <span class="font-medium text-blue-600">4</span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        Agendar
                    </button>
                    <button class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <span class="material-icons text-sm">edit</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Resonancia Magnética -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-indigo-600 text-3xl">science</span>
                    </div>
                    <span class="bg-amber-100 text-amber-800 text-xs font-medium px-2.5 py-0.5 rounded">Mantenimiento</span>
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-2">Resonancia Magnética</h3>
                <p class="text-sm text-gray-600 mb-4">Imágenes de alta resolución sin radiación ionizante</p>

                <div class="space-y-2 mb-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Duración:</span>
                        <span class="font-medium text-gray-900">60-90 min</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Precio:</span>
                        <span class="font-medium text-green-600">Bs. 800</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Realizados hoy:</span>
                        <span class="font-medium text-gray-400">0</span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button disabled class="flex-1 px-3 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed text-sm font-medium">
                        No disponible
                    </button>
                    <button class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <span class="material-icons text-sm">edit</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mamografía -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 bg-pink-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-pink-600 text-3xl">favorite</span>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Disponible</span>
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-2">Mamografía Digital</h3>
                <p class="text-sm text-gray-600 mb-4">Detección temprana de cáncer de mama</p>

                <div class="space-y-2 mb-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Duración:</span>
                        <span class="font-medium text-gray-900">20-30 min</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Precio:</span>
                        <span class="font-medium text-green-600">Bs. 200</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Realizados hoy:</span>
                        <span class="font-medium text-blue-600">3</span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        Agendar
                    </button>
                    <button class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <span class="material-icons text-sm">edit</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Densitometría Ósea -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 bg-orange-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-orange-600 text-3xl">accessibility</span>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Disponible</span>
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-2">Densitometría Ósea</h3>
                <p class="text-sm text-gray-600 mb-4">Evaluación de la densidad mineral ósea</p>

                <div class="space-y-2 mb-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Duración:</span>
                        <span class="font-medium text-gray-900">15-25 min</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Precio:</span>
                        <span class="font-medium text-green-600">Bs. 120</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Realizados hoy:</span>
                        <span class="font-medium text-blue-600">3</span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        Agendar
                    </button>
                    <button class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <span class="material-icons text-sm">edit</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Servicios recientes -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Servicios Recientes</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Paciente</th>
                        <th scope="col" class="px-6 py-3">Servicio</th>
                        <th scope="col" class="px-6 py-3">Médico</th>
                        <th scope="col" class="px-6 py-3">Hora</th>
                        <th scope="col" class="px-6 py-3">Estado</th>
                        <th scope="col" class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">Ana García</td>
                        <td class="px-6 py-4">Radiografía de Tórax</td>
                        <td class="px-6 py-4">Dr. Juan Pérez</td>
                        <td class="px-6 py-4">10:30 AM</td>
                        <td class="px-6 py-4">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Completado</span>
                        </td>
                        <td class="px-6 py-4">
                            <button class="text-blue-600 hover:underline">Ver resultados</button>
                        </td>
                    </tr>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">Carlos Méndez</td>
                        <td class="px-6 py-4">Ecografía Abdominal</td>
                        <td class="px-6 py-4">Dra. María López</td>
                        <td class="px-6 py-4">11:00 AM</td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">En proceso</span>
                        </td>
                        <td class="px-6 py-4">
                            <button class="text-blue-600 hover:underline">Ver detalles</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">Laura Fernández</td>
                        <td class="px-6 py-4">Tomografía de Cráneo</td>
                        <td class="px-6 py-4">Dr. Carlos Ruiz</td>
                        <td class="px-6 py-4">11:45 AM</td>
                        <td class="px-6 py-4">
                            <span class="bg-amber-100 text-amber-800 text-xs font-medium px-2.5 py-0.5 rounded">Programado</span>
                        </td>
                        <td class="px-6 py-4">
                            <button class="text-blue-600 hover:underline">Editar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
