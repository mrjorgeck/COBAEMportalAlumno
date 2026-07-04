@extends('layouts.admin')

@section('titulo', 'Detalle alumno')

@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">{{ $proceso->alumno->nombre_completo }}</h1>
    <p class="text-sm text-gray-600">{{ $proceso->alumno->curp }} - {{ $proceso->folio_registro }}</p>

    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <form method="POST" action="{{ route('admin.alumnos.update', $proceso) }}" class="rounded bg-white p-4 shadow-sm">
            @csrf @method('PATCH')
            <h2 class="font-semibold">Datos principales</h2>
            <input name="nombres" value="{{ old('nombres', $proceso->alumno->nombres) }}" class="mt-3 w-full rounded border-gray-300">
            <input name="primer_apellido" value="{{ old('primer_apellido', $proceso->alumno->primer_apellido) }}" class="mt-3 w-full rounded border-gray-300">
            <input name="segundo_apellido" value="{{ old('segundo_apellido', $proceso->alumno->segundo_apellido) }}" class="mt-3 w-full rounded border-gray-300">
            <input name="folio_examen" value="{{ old('folio_examen', $proceso->folio_examen) }}" class="mt-3 w-full rounded border-gray-300">
            <select name="estatus_proceso" class="mt-3 w-full rounded border-gray-300">
                @foreach (['registro_incompleto', 'registrado', 'requiere_correccion', 'validado'] as $estado)
                    <option @selected($proceso->estatus_proceso === $estado) value="{{ $estado }}">{{ $estado }}</option>
                @endforeach
            </select>
            <button @disabled($proceso->edicion_bloqueada) class="mt-3 rounded bg-cobaem-900 px-4 py-2 text-white disabled:bg-gray-400">Guardar</button>
        </form>

        <div class="rounded bg-white p-4 shadow-sm">
            <h2 class="font-semibold">Acciones</h2>
            <a class="mt-3 inline-block rounded bg-gray-200 px-4 py-2" href="{{ route('admin.alumnos.formato', $proceso) }}">Descargar PDF</a>
            @can('grupos.asignar')
                <form method="POST" action="{{ route('admin.alumnos.grupo-propedeutico', $proceso) }}" class="mt-3">
                    @csrf
                    <label class="text-sm font-semibold">Grupo propedeutico</label>
                    <select name="grupo_propedeutico_id" class="mt-2 w-full rounded border-gray-300">
                        <option value="">Sin asignar</option>
                        @foreach ($gruposPropedeuticos as $grupo)
                            <option value="{{ $grupo->id }}" @selected($proceso->grupo_propedeutico_id === $grupo->id)>{{ $grupo->nombre }}</option>
                        @endforeach
                    </select>
                    <button class="mt-2 rounded bg-gray-800 px-3 py-1 text-white">Asignar grupo</button>
                </form>
            @endcan
            <form method="POST" action="{{ route('admin.alumnos.bloquear', $proceso) }}" class="mt-3">
                @csrf
                <button class="rounded bg-cobaem-900 px-4 py-2 text-white">{{ $proceso->edicion_bloqueada ? 'Desbloquear edicion' : 'Bloquear edicion' }}</button>
            </form>
        </div>
    </div>

    <h2 class="mt-6 font-semibold">Documentacion</h2>
    <div class="mt-3 space-y-3">
        @foreach ($proceso->documentos as $documento)
            <form method="POST" action="{{ route('admin.alumnos.documentos.update', [$proceso, $documento]) }}" class="rounded bg-white p-4 shadow-sm">
                @csrf @method('PATCH')
                <p class="font-semibold">{{ $documento->tipoDocumento->nombre }}</p>
                <select name="estado_documento" class="mt-2 rounded border-gray-300">
                    @foreach (['pendiente', 'recibido', 'validado', 'rechazado', 'requiere_correccion', 'no_aplica'] as $estado)
                        <option @selected($documento->estado_documento === $estado) value="{{ $estado }}">{{ $estado }}</option>
                    @endforeach
                </select>
                <input name="observacion" value="{{ $documento->observacion }}" placeholder="Observacion" class="mt-2 w-full rounded border-gray-300">
                <button class="mt-2 rounded bg-gray-200 px-3 py-1">Actualizar</button>
            </form>
        @endforeach
    </div>
@endsection
