@extends('layouts.admin')

@section('titulo', 'Grupos propedeuticos')

@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Grupos propedeuticos</h1>

    <form method="POST" action="{{ route('admin.grupos-propedeuticos.store') }}" class="mt-4 grid gap-3 rounded bg-white p-4 shadow-sm md:grid-cols-3">
        @csrf
        <select name="ciclo_ingreso_id" class="rounded border-gray-300" required>
            @foreach ($ciclos as $ciclo)
                <option value="{{ $ciclo->id }}">{{ $ciclo->generacion }}</option>
            @endforeach
        </select>
        <input name="nombre" class="rounded border-gray-300" placeholder="Grupo, ej. P-03" required>
        <input name="aula" class="rounded border-gray-300" placeholder="Aula">
        <input name="horario_texto" class="rounded border-gray-300" placeholder="Horario">
        <input name="fecha_inicio" inputmode="numeric" pattern="\d{2}/\d{2}/\d{4}" placeholder="Inicio dd/mm/aaaa" class="rounded border-gray-300">
        <input name="fecha_fin" inputmode="numeric" pattern="\d{2}/\d{2}/\d{4}" placeholder="Fin dd/mm/aaaa" class="rounded border-gray-300">
        <input name="responsable" class="rounded border-gray-300 md:col-span-3" placeholder="Responsable">
        <textarea name="indicaciones" class="rounded border-gray-300 md:col-span-3" placeholder="Indicaciones"></textarea>
        <textarea name="materiales_requeridos" class="rounded border-gray-300 md:col-span-3" placeholder="Materiales requeridos"></textarea>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="activo" value="1" checked> Activo</label>
        <button class="rounded bg-cobaem-900 px-4 py-2 text-white md:col-span-2">Guardar grupo</button>
    </form>

    <div class="mt-4 space-y-3">
        @foreach ($grupos as $grupo)
            <article class="rounded bg-white p-4 shadow-sm">
                <p class="font-semibold">{{ $grupo->nombre }} · {{ $grupo->procesos_count }} alumnos</p>
                <p class="text-sm text-gray-600">{{ $grupo->horario_texto }} · {{ $grupo->aula }} · {{ $grupo->responsable }}</p>
                <p class="text-sm text-gray-600">{{ \App\Support\FechaInput::toDisplay($grupo->fecha_inicio) ?: 'Inicio por confirmar' }} - {{ \App\Support\FechaInput::toDisplay($grupo->fecha_fin) ?: 'Fin por confirmar' }}</p>
            </article>
        @endforeach
    </div>

    <div class="mt-4">{{ $grupos->links() }}</div>
@endsection
