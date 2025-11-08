<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PacienteController;
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
                return view('personal.pacientes.form-paciente')
                    ->with('paciente', null);
            })->name('agregar');
            Route::get('/editar/{id}', function ($id) {
                $paciente = App\Models\Paciente::findOrFail($id);
                return view('personal.pacientes.form-paciente', compact('paciente'));
            })->name('editar');
        });

        Route::get('/servicios', function () {
            return view('personal.servicios.servicios');
        })->name('servicios.servicios');
    });

    Route::prefix('api')->name('api.')->group(function () {
        Route::prefix(('supervisor'))->name('supervisor.')->group(function () {});
        Route::prefix(('personal'))->name('personal.')->group(function () {
            Route::prefix(('pacientes'))->name('pacientes.')->group(function () {
                Route::get('/', [PacienteController::class, 'index'])->name('index');
                Route::post('/', [PacienteController::class, 'store'])->name('store');
                Route::get('/{id}', [PacienteController::class, 'show'])->name('show');
                Route::put('/{id}', [PacienteController::class, 'update'])->name('update');
                Route::delete('/{id}', [PacienteController::class, 'cambiarEstado'])->name('cambiarEstado');
            });
        });
    });

    // Cerrar sesión - Solo POST, no GET
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
