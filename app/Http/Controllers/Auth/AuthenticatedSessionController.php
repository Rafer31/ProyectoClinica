<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Personal;
use Illuminate\Routing\Controller;

class AuthenticatedSessionController extends Controller
{
    /**
     * Mostrar el formulario de login
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Procesar el inicio de sesión
     */
    public function store(Request $request)
    {
        $request->validate([
            'usuarioPer' => 'required|string',
            'clavePer' => 'required|string',
        ], [
            'usuarioPer.required' => 'El usuario es obligatorio',
            'clavePer.required' => 'La contraseña es obligatoria',
        ]);

        // Verificar si el usuario existe
        $user = \App\Models\PersonalSalud::where('usuarioPer', $request->usuarioPer)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'usuarioPer' => 'Las credenciales proporcionadas son incorrectas.',
            ]);
        }

        // Verificar si el usuario está activo
        if (isset($user->estado) && strtolower($user->estado) !== 'activo') {
            throw ValidationException::withMessages([
                'usuarioPer' => 'El usuario está deshabilitado. Contacte con el supervisor.',
            ]);
        }

        // Intentar autenticar credenciales
        if (Auth::attempt([
            'usuarioPer' => $request->usuarioPer,
            'password' => $request->clavePer
        ], $request->filled('remember'))) {

            $request->session()->regenerate();

            $user = Auth::user();

            // Redirigir según rol
            if ($user->codRol == 1) {
                return redirect()->intended(route('supervisor.home'));
            } elseif ($user->codRol == 2) {
                return redirect()->intended(route('personal.home'));
            } elseif ($user->codRol == 3) {
                return redirect()->intended(route('enfermera.home'));
            }

            // Si no tiene un rol válido
            Auth::logout();
            throw ValidationException::withMessages([
                'usuarioPer' => 'Tu cuenta no tiene un rol válido asignado.',
            ]);
        }

        // Si la contraseña no coincide
        throw ValidationException::withMessages([
            'usuarioPer' => 'Las credenciales proporcionadas son incorrectas.',
        ]);
    }

    /**
     * Cerrar sesión del usuario
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Sesión cerrada correctamente')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}