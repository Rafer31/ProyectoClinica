<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

// 🔹 Rutas públicas (usuarios no autenticados)
Route::middleware('guest')->group(function () {
    // Mostrar formulario de login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    // Procesar inicio de sesión
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// 🔹 Rutas protegidas (usuarios autenticados)
Route::middleware('auth')->group(function () {
    // Cerrar sesión
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

