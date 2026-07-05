@extends('layouts.admin')
@section('titulo', 'Modulos')
@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Publicacion de modulos</h1>
    <form method="POST" action="{{ route('admin.ciclos.store') }}" class="mt-4 grid gap-3 rounded bg-white p-4 shadow-sm md:grid-cols-4">
        @csrf
        <input name="anio" type="number" min="2026" max="2100" placeholder="2027" class="rounded border-gray-300">
        <input name="periodo_escolar" placeholder="27-2" class="rounded border-gray-300">
        <input name="generacion" placeholder="Nuevo ingreso 2027" class="rounded border-gray-300">
        <button class="rounded bg-gray-800 px-4 py-2 text-white">Crear ciclo</button>
    </form>
    <form method="POST" action="{{ route('admin.modulos.store') }}" class="mt-4 rounded bg-white p-4 shadow-sm" onsubmit="return confirm('Esto cambia las secciones visibles para los alumnos del ciclo seleccionado. ¿Deseas publicar estos modulos?')">
        @csrf
        <select name="ciclo_ingreso_id" class="rounded border-gray-300">
            @foreach ($ciclos as $ciclo)<option value="{{ $ciclo->id }}">{{ $ciclo->generacion }}</option>@endforeach
        </select>
        <div class="mt-4 grid gap-2 sm:grid-cols-2">
            @foreach ($modulos as $modulo)
                <label class="flex gap-2 text-sm">
                    <input type="checkbox" name="modulos[]" value="{{ $modulo->value }}">
                    {{ $modulo->etiqueta() }}
                </label>
            @endforeach
        </div>
        <button class="mt-4 rounded bg-cobaem-900 px-4 py-2 text-white">Guardar</button>
    </form>
@endsection
