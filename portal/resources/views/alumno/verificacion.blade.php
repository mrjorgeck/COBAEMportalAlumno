@extends('layouts.alumno')

@section('titulo', 'Verificacion')

@section('contenido')
    <div class="rounded-xl bg-white p-6 shadow">
        <h1 class="text-xl font-bold text-cobaem-900">Verificacion de identidad</h1>
        <form method="POST" action="{{ route('alumno.verificacion.store') }}" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="mt-1 w-full rounded border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium">O folio de examen</label>
                <input name="folio_examen" class="mt-1 w-full rounded border-gray-300">
            </div>
            @error('verificacion') <p class="text-sm text-red-700">{{ $message }}</p> @enderror
            <button class="w-full rounded bg-cobaem-900 px-4 py-2 font-semibold text-white">Entrar</button>
        </form>
    </div>
@endsection
