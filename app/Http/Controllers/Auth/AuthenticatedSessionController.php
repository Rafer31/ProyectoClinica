<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Mostrar el formulario de login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Procesar la autenticación del usuario.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'usuarioPer' => ['required', 'string'],
            'clavePer' => ['required', 'string'],
        ]);

        if (
            Auth::attempt([
                'usuarioPer' => $credentials['usuarioPer'],
                'password' => $credentials['clavePer']
            ])
        ) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->codRol == 1) {
                return redirect()->route('supervisor.home');
            } elseif ($user->codRol == 2) {
                return redirect()->route('personal.home');
            }

            Auth::logout();
            return back()->withErrors(['usuarioPer' => 'Rol no válido o sin asignar.']);
        }

        return back()->withErrors([
            'usuarioPer' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('usuarioPer');
    }


    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
