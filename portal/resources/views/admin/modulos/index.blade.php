@extends('layouts.admin')
@section('titulo', 'Modulos')
@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Publicacion de modulos</h1>
    <form method="POST" action="{{ route('admin.modulos.store') }}" class="mt-4 rounded bg-white p-4 shadow-sm">
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
