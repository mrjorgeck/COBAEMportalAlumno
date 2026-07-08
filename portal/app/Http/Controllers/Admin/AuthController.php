<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        if ($request->user()->debe_cambiar_password) {
            $request->session()->put('forzar_cambio_password', true);

            return redirect()->route('admin.password.edit');
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function editPassword(): View
    {
        return view('admin.auth.cambiar-password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'password_actual' => ['required', 'string'],
            'password' => ['required', 'string', 'min:12', 'confirmed', 'different:password_actual'],
        ]);

        $user = $request->user();

        if (! Hash::check($data['password_actual'], $user->password)) {
            throw ValidationException::withMessages([
                'password_actual' => 'La contraseña actual no coincide.',
            ]);
        }

        $user->forceFill([
            'password' => $data['password'],
            'debe_cambiar_password' => false,
        ])->save();
        $request->session()->forget('forzar_cambio_password');

        activity()
            ->causedBy($user)
            ->log('Cambio obligatorio de contraseña administrativa');

        return redirect()->route('admin.dashboard')->with('mensaje', 'Contraseña actualizada correctamente.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
