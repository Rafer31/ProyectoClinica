{{-- ============================================ --}}
{{-- resources/views/personal/home.blade.php --}}
{{-- ============================================ --}}
@extends('personal.layouts.personal')

@section('title', 'Inicio - Personal')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <span class="material-icons align-middle text-4xl text-blue-600">dashboard</span>
            Panel de Control
        </h1>
        <p class="text-gray-600">Bienvenido, {{ Auth::user()->nombrePer }}</p>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pacientes Hoy</p>
                    <p class="text-3xl font-bold text-blue-600">24</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-blue-600 text-2xl">people</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
