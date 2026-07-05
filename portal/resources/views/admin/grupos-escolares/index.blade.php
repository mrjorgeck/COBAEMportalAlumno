@extends('layouts.admin')

@section('titulo', 'Grupos escolares')

@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Grupos escolares</h1>

    <form method="POST" action="{{ route('admin.grupos-escolares.store') }}" class="mt-4 grid gap-3 rounded bg-white p-4 shadow-sm md:grid-cols-4">
        @csrf
        <select name="ciclo_ingreso_id" class="rounded border-gray-300">
            @foreach ($ciclos as $ciclo)
                <option value="{{ $ciclo->id }}">{{ $ciclo->generacion }}</option>
            @endforeach
        </select>
        <input name="grupo" placeholder="Grupo, ej. 1-A" class="rounded border-gray-300">
        <input name="semestre" type="number" min="1" max="6" value="1" class="rounded border-gray-300">
        <select name="turno_id" class="rounded border-gray-300">
            @foreach ($turnos as $turno)
                <option value="{{ $turno->id }}">{{ $turno->nombre }}</option>
            @endforeach
        </select>
        <input name="aula_base" placeholder="Aula base" class="rounded border-gray-300">
        <input name="fecha_inicio_clases" type="date" class="rounded border-gray-300">
        <textarea name="indicaciones" placeholder="Indicaciones" class="rounded border-gray-300 md:col-span-2"></textarea>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="activo" value="1" checked> Activo</label>
        <button class="rounded bg-cobaem-900 px-4 py-2 text-white md:col-span-4">Guardar grupo</button>
    </form>

    <div class="mt-4 space-y-4">
        @forelse ($grupos as $grupo)
            <article class="rounded bg-white p-4 shadow-sm">
                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                    <div>
                        <p class="font-semibold">{{ $grupo->grupo }} - {{ $grupo->ciclo->generacion }}</p>
                        <p class="text-sm text-gray-600">{{ $grupo->turno->nombre }} - {{ $grupo->aula_base ?? 'Aula por confirmar' }} - {{ $grupo->procesos_count }} alumnos</p>
                        @if ($grupo->indicaciones)
                            <p class="mt-2 text-sm">{{ $grupo->indicaciones }}</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('admin.grupos-escolares.destroy', $grupo) }}" onsubmit="return confirm('Eliminar este grupo quitara su horario y asignaciones visibles. ¿Deseas continuar?')">
                        @csrf @method('DELETE')
                        <button class="rounded bg-gray-200 px-3 py-1 text-sm">Eliminar</button>
                    </form>
                </div>

                <form method="POST" action="{{ route('admin.grupos-escolares.horarios.store', $grupo) }}" class="mt-4 grid gap-2 text-sm md:grid-cols-7">
                    @csrf
                    <select name="dia" class="rounded border-gray-300">
                        @foreach ([1 => 'Lunes', 2 => 'Martes', 3 => 'Miercoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sabado'] as $dia => $nombre)
                            <option value="{{ $dia }}">{{ $nombre }}</option>
                        @endforeach
                    </select>
                    <input name="hora_inicio" type="time" class="rounded border-gray-300">
                    <input name="hora_fin" type="time" class="rounded border-gray-300">
                    <input name="materia" placeholder="Materia" class="rounded border-gray-300">
                    <input name="docente" placeholder="Docente" class="rounded border-gray-300">
                    <input name="aula" placeholder="Aula" class="rounded border-gray-300">
                    <button class="rounded bg-gray-800 px-3 py-2 text-white">Agregar</button>
                </form>

                <div class="mt-3 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead><tr class="border-b"><th class="py-2">Dia</th><th>Hora</th><th>Materia</th><th>Docente</th><th>Aula</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($grupo->horarios->sortBy(['dia', 'hora_inicio']) as $horario)
                                <tr class="border-b">
                                    <td class="py-2">{{ $horario->diaNombre() }}</td>
                                    <td>{{ substr($horario->hora_inicio, 0, 5) }} - {{ substr($horario->hora_fin, 0, 5) }}</td>
                                    <td>{{ $horario->materia }}</td>
                                    <td>{{ $horario->docente ?? 'Por confirmar' }}</td>
                                    <td>{{ $horario->aula ?? $grupo->aula_base }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.horarios.destroy', $horario) }}" onsubmit="return confirm('Esta clase se quitara del horario publicado. ¿Deseas eliminarla?')">
                                            @csrf @method('DELETE')
                                            <button class="text-cobaem-900">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-2 text-gray-600">Sin horario cargado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        @empty
            <p class="rounded bg-white p-4 text-sm text-gray-600 shadow-sm">Aun no hay grupos escolares registrados.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $grupos->links() }}</div>
@endsection
