<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;

// Página inicial -> redirige al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas públicas (no autenticadas)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// Rutas protegidas (autenticadas) con middleware para prevenir caché
Route::middleware(['auth', 'prevent.back.history'])->group(function () {

    // Panel del supervisor
    Route::prefix('supervisor')->name('supervisor.')->group(function () {
        Route::get('/home', function () {
            return view('supervisor.home');
        })->name('home');

        Route::get('/accesos', function () {
            return view('supervisor.accesos.accesos');
        })->name('accesos.accesos');

        Route::get('/clinicas', function () {
            return view('supervisor.clinicas.clinicas');
        })->name('clinicas.clinicas');

        Route::get('/estadisticas', function () {
            return view('supervisor.estadisticas.estadisticas');
        })->name('estadisticas.estadisticas');
    });

    // Panel del personal de imagen
    Route::prefix('personal')->name('personal.')->group(function () {
        Route::get('/home', function () {
            return view('personal.home');
        })->name('home');

        Route::get('/medicos', function () {
            return view('personal.medicos.medicos');
        })->name('medicos.medicos');

        Route::get('/cronogramas', function () {
            return view('personal.cronogramas.cronogramas');
        })->name('cronogramas.cronogramas');

        // Rutas de pacientes
        Route::prefix('pacientes')->name('pacientes.')->group(function () {
            Route::get('/', function () {
                return view('personal.pacientes.pacientes');
            })->name('pacientes');

            Route::get('/agregar', function () {
                return view('personal.pacientes.agregar-paciente');
            })->name('agregar');

            Route::get('/editar/{id}', function ($id) {
                return view('personal.pacientes.actualizar-paciente', compact('id'));
            })->name('editar');
        });

        Route::get('/servicios', function () {
            return view('personal.servicios.servicios');
        })->name('servicios.servicios');
    });

    // Cerrar sesión - Solo POST, no GET
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Ruta fallback - Maneja URLs no encontradas
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
    return redirect()->route('login')->with('error', 'Rol no válido');
});
