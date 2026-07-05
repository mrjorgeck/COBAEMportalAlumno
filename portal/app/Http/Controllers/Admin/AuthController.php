<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('admin.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Solo usuarios activos pueden iniciar sesión.
        if (! Auth::attempt([...$credentials, 'activo' => true], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'No pudimos iniciar sesión con esos datos. Revisa tu correo y contraseña.',
            ]);
        }

        $request->session()->regenerate();

        activity()
            ->causedBy(Auth::user())
            ->log('Inicio de sesión administrativo');

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
