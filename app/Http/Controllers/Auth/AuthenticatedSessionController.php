<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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

        // Intentar autenticar
        if (Auth::attempt([
            'usuarioPer' => $request->usuarioPer,
            'password' => $request->clavePer
        ], $request->filled('remember'))) {

            $request->session()->regenerate();

            $user = Auth::user();

            // Redirigir según el rol
            if ($user->codRol == 1) {
                return redirect()->intended(route('supervisor.home'));
            } elseif ($user->codRol == 2) {
                return redirect()->intended(route('personal.home'));
            }

            // Si no tiene un rol válido, cerrar sesión
            Auth::logout();
            throw ValidationException::withMessages([
                'usuarioPer' => 'Tu cuenta no tiene un rol válido asignado.',
            ]);
        }

        // Credenciales incorrectas
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

        // Redirigir al login con mensaje de éxito
        return redirect()->route('login')
            ->with('success', 'Sesión cerrada correctamente')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
