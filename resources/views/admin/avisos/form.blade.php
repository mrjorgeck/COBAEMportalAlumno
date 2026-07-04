@extends('layouts.admin')
@section('titulo', 'Aviso')
@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">{{ $aviso->exists ? 'Editar aviso' : 'Nuevo aviso' }}</h1>
    <form method="POST" action="{{ $aviso->exists ? route('admin.avisos.update', $aviso) : route('admin.avisos.store') }}" class="mt-4 rounded bg-white p-4 shadow-sm">
        @csrf
        @if ($aviso->exists) @method('PATCH') @endif
        <input name="titulo" value="{{ old('titulo', $aviso->titulo) }}" placeholder="Titulo" required class="w-full rounded border-gray-300">
        <textarea name="mensaje" required placeholder="Mensaje" class="mt-3 w-full rounded border-gray-300">{{ old('mensaje', $aviso->mensaje) }}</textarea>
        <select name="tipo_aviso_id" class="mt-3 rounded border-gray-300">@foreach ($tipos as $tipo)<option @selected($aviso->tipo_aviso_id === $tipo->id) value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>@endforeach</select>
        <select name="prioridad" class="mt-3 rounded border-gray-300">@foreach (['informativo', 'importante', 'urgente'] as $p)<option @selected($aviso->prioridad === $p) value="{{ $p }}">{{ $p }}</option>@endforeach</select>
        <select name="dirigido_a" class="mt-3 rounded border-gray-300">@foreach (['todos', 'ciclo'] as $d)<option @selected($aviso->dirigido_a === $d) value="{{ $d }}">{{ $d }}</option>@endforeach</select>
        <select name="ciclo_ingreso_id" class="mt-3 rounded border-gray-300"><option value="">Sin ciclo</option>@foreach ($ciclos as $ciclo)<option @selected($aviso->ciclo_ingreso_id === $ciclo->id) value="{{ $ciclo->id }}">{{ $ciclo->generacion }}</option>@endforeach</select>
        <label class="mt-3 flex gap-2 text-sm"><input type="checkbox" name="visible" value="1" @checked(old('visible', $aviso->visible ?? true))> Visible</label>
        <button class="mt-4 rounded bg-cobaem-900 px-4 py-2 text-white">Guardar</button>
    </form>
@endsection
