<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Página inicial -> redirige al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas públicas (no autenticadas)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// Rutas protegidas (autenticadas)
Route::middleware('auth')->group(function () {
    // Panel del supervisor
    Route::get('/supervisor/home', function () {
        return view('supervisor.home');
    })->name('supervisor.home');

    // Panel del personal de imagen
    Route::get('/personal/home', function () {
        return view('personal.home');
    })->name('personal.home');

    // Cerrar sesión
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
Route::fallback(function () {
    if (!Auth::check()) {
        // No autenticado → redirige al login
        return redirect()->route('login');
    }

    // Usuario autenticado → redirige según rol
    $user = Auth::user();

    if ($user->codRol == 1) {
        return redirect()->route('supervisor.home');
    } elseif ($user->codRol == 2) {
        return redirect()->route('personal.home');
    }

    // Si no tiene rol válido, cerrar sesión
    Auth::logout();
    return redirect()->route('login');
});