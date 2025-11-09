<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PacienteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
// ===========================
// RUTAS DE PRUEBA (BACKEND) - PUEDES ELIMINARLAS EN PRODUCCIÓN
// ===========================
Route::prefix('test')->group(function () {

    // ===== PRUEBAS DE PACIENTE =====
    Route::get('/paciente/registrar', function () {
        $paciente = App\Models\Paciente::create([
            'nomPa' => 'Test',
            'paternoPa' => 'Usuario',
            'maternoPa' => 'Prueba',
            'fechaNac' => '1990-01-01',
            'sexo' => 'M',
            'nroHCI' => 'TEST' . time(),
            'tipoPac' => 'SUS',
            'estado' => 'activo'
        ]);

        return response()->json([
            'mensaje' => 'Paciente creado',
            'data' => $paciente
        ]);
    });

    Route::get('/paciente/listar', function () {
        $pacientes = App\Models\Paciente::all();

        return response()->json([
            'total' => $pacientes->count(),
            'data' => $pacientes
        ]);
    });

    Route::get('/paciente/actualizar/{id}', function ($id) {
        $paciente = App\Models\Paciente::find($id);

        if (!$paciente) {
            return response()->json(['error' => 'Paciente no encontrado']);
        }

        $antes = [
            'nombre' => $paciente->nomPa,
            'tipo' => $paciente->tipoPac,
            'estado' => $paciente->estado
        ];

        $paciente->nomPa = 'Nombre Actualizado';
        $paciente->tipoPac = 'SINSUS';
        $paciente->save();

        return response()->json([
            'mensaje' => 'Paciente actualizado',
            'antes' => $antes,
            'despues' => [
                'nombre' => $paciente->nomPa,
                'tipo' => $paciente->tipoPac,
                'estado' => $paciente->estado
            ]
        ]);
    });

    Route::get('/paciente/estado/{id}', function ($id) {
        $paciente = App\Models\Paciente::find($id);

        if (!$paciente) {
            return response()->json(['error' => 'Paciente no encontrado']);
        }

        $estadoAnterior = $paciente->estado;
        $paciente->estado = $paciente->estado === 'activo' ? 'inactivo' : 'activo';
        $paciente->save();

        return response()->json([
            'mensaje' => 'Estado cambiado',
            'nombre' => "{$paciente->nomPa} {$paciente->paternoPa}",
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $paciente->estado
        ]);
    });

    // ===== PRUEBAS DE PERSONAL DE SALUD =====
    Route::get('/personal/registrar', function () {
        // Primero crear rol si no existe
        $rol = App\Models\Rol::firstOrCreate(
            ['codRol' => 1],
            ['nombreRol' => 'Personal Imagen']
        );

        $personal = App\Models\PersonalSalud::create([
            'usuarioPer' => 'test_' . time(),
            'clavePer' => Hash::make('password123'),
            'nomPer' => 'Test',
            'paternoPer' => 'Usuario',
            'maternoPer' => 'Prueba',
            'codRol' => $rol->codRol,
            'estado' => 'activo'
        ]);

        return response()->json([
            'mensaje' => 'Personal creado',
            'data' => $personal
        ]);
    });

    Route::get('/personal/listar', function () {
        $personal = App\Models\PersonalSalud::with('rol')->get();

        return response()->json([
            'total' => $personal->count(),
            'data' => $personal
        ]);
    });

    Route::get('/personal/actualizar/{id}', function ($id) {
        $personal = App\Models\PersonalSalud::find($id);

        if (!$personal) {
            return response()->json(['error' => 'Personal no encontrado']);
        }

        $antes = [
            'nombre' => $personal->nomPer,
            'usuario' => $personal->usuarioPer,
            'estado' => $personal->estado
        ];

        $personal->nomPer = 'Nombre Actualizado';
        $personal->usuarioPer = 'usuario_' . time();
        $personal->save();

        return response()->json([
            'mensaje' => 'Personal actualizado',
            'antes' => $antes,
            'despues' => [
                'nombre' => $personal->nomPer,
                'usuario' => $personal->usuarioPer,
                'estado' => $personal->estado
            ]
        ]);
    });

    Route::get('/personal/estado/{id}', function ($id) {
        $personal = App\Models\PersonalSalud::find($id);

        if (!$personal) {
            return response()->json(['error' => 'Personal no encontrado']);
        }

        $estadoAnterior = $personal->estado;
        $personal->estado = $personal->estado === 'activo' ? 'inactivo' : 'activo';
        $personal->save();

        return response()->json([
            'mensaje' => 'Estado cambiado',
            'nombre' => "{$personal->nomPer} {$personal->paternoPer}",
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $personal->estado
        ]);
    });
});

// Ruta fallback - Maneja URLs no encontradas
Route::fallback(function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    if ($user->codRol == 1) {
        return redirect()->route('supervisor.home');
    } elseif ($user->codRol == 2) {
        return redirect()->route('personal.home');
    }

    Auth::logout();
    return redirect()->route('login')->with('error', 'Rol no válido');
});
