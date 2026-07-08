@extends('layouts.admin')

@section('titulo', 'Cambiar contraseña')

@section('contenido')
    <div class="mx-auto max-w-lg rounded bg-white p-6 shadow-sm">
        <h1 class="text-xl font-bold text-cobaem-900">Cambiar contraseña inicial</h1>
        <p class="mt-2 text-sm text-gray-600">Antes de usar el panel, define una contraseña propia para esta cuenta administrativa.</p>

        <form method="POST" action="{{ route('admin.password.update') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="password_actual" class="block text-sm font-medium">Contraseña actual</label>
                <input id="password_actual" name="password_actual" type="password" required autocomplete="current-password" class="mt-1 w-full rounded border-gray-300 text-sm">
                @error('password_actual') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium">Nueva contraseña</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" class="mt-1 w-full rounded border-gray-300 text-sm">
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium">Confirmar nueva contraseña</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-1 w-full rounded border-gray-300 text-sm">
            </div>

            <button class="w-full rounded bg-cobaem-900 px-4 py-2 font-semibold text-white hover:bg-cobaem-700">
                Guardar contraseña
            </button>
        </form>
    </div>
@endsection
