@extends('personal.layouts.personal')

@section('title', 'Cronogramas')

@section('content')
@php
$breadcrumbs = [
    ['label' => 'Inicio', 'url' => route('personal.home')],
    ['label' => 'Cronogramas']
];
@endphp

<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <span class="material-icons align-middle text-4xl text-blue-600">calendar_today</span>
                    Cronogramas de Atención
                </h1>
                <p class="text-gray-600">Gestiona los horarios y turnos del personal médico</p>
            </div>
            <button class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <span class="material-icons me-2">add</span>
                Crear Cronograma
            </button>
        </div>
    </div>

    <!-- Selector de semana -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between">
            <button class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                <span class="material-icons">chevron_left</span>
            </button>
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-900">Semana del 4 al 10 de Noviembre, 2025</h3>
                <p class="text-sm text-gray-600">Hoy: Viernes 7 de Noviembre</p>
            </div>
            <button class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                <span class="material-icons">chevron_right</span>
            </button>
        </div>
    </div>

    <!-- Tabla de cronogramas -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 min-w-[150px]">Médico</th>
                        <th scope="col" class="px-6 py-3 text-center">Lun 4</th>
                        <th scope="col" class="px-6 py-3 text-center">Mar 5</th>
                        <th scope="col" class="px-6 py-3 text-center">Mié 6</th>
                        <th scope="col" class="px-6 py-3 text-center bg-blue-50">Jue 7</th>
                        <th scope="col" class="px-6 py-3 text-center">Vie 8</th>
                        <th scope="col" class="px-6 py-3 text-center">Sáb 9</th>
                        <th scope="col" class="px-6 py-3 text-center">Dom 10</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dr. Juan Pérez -->
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center me-2">
                                    <span class="material-icons text-blue-600 text-sm">person</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Dr. Juan Pérez</p>
                                    <p class="text-xs text-gray-500">Radiología</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">08:00 - 16:00</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">08:00 - 16:00</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">08:00 - 16:00</span>
                        </td>
                        <td class="px-6 py-4 text-center bg-blue-50">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">08:00 - 16:00</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">08:00 - 16:00</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2 py-1 rounded">Descanso</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2 py-1 rounded">Descanso</span>
                        </td>
                    </tr>

                    <!-- Dra. María López -->
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center me-2">
                                    <span class="material-icons text-purple-600 text-sm">person</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Dra. María López</p>
                                    <p class="text-xs text-gray-500">Ecografía</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2 py-1 rounded">Descanso</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">14:00 - 20:00</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">14:00 - 20:00</span>
                        </td>
                        <td class="px-6 py-4 text-center bg-blue-50">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">14:00 - 20:00</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">14:00 - 20:00</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">08:00 - 14:00</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2 py-1 rounded">Descanso</span>
                        </td>
                    </tr>

                    <!-- Dr. Carlos Ruiz -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center me-2">
                                    <span class="material-icons text-green-600 text-sm">person</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Dr. Carlos Ruiz</p>
                                    <p class="text-xs text-gray-500">Tomografía</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">08:00 - 14:00</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">08:00 - 14:00</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2 py-1 rounded">Descanso</span>
                        </td>
                        <td class="px-6 py-4 text-center bg-blue-50">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">08:00 - 14:00</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">08:00 - 14:00</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">08:00 - 14:00</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2 py-1 rounded">Descanso</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Leyenda -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex flex-wrap gap-4">
            <div class="flex items-center">
                <span class="w-4 h-4 bg-green-100 rounded me-2"></span>
                <span class="text-sm text-gray-700">Turno programado</span>
            </div>
            <div class="flex items-center">
                <span class="w-4 h-4 bg-gray-100 rounded me-2"></span>
                <span class="text-sm text-gray-700">Descanso</span>
            </div>
            <div class="flex items-center">
                <span class="w-4 h-4 bg-blue-50 rounded me-2"></span>
                <span class="text-sm text-gray-700">Día actual</span>
            </div>
        </div>
    </div>
</div>
@endsection
