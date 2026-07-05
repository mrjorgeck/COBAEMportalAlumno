@extends('layouts.admin')

@section('titulo', 'Examenes')

@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Examenes</h1>

    <form method="POST" action="{{ route('admin.examenes.store') }}" class="mt-4 grid gap-3 rounded bg-white p-4 shadow-sm md:grid-cols-3">
        @csrf
        <select name="ciclo_ingreso_id" class="rounded border-gray-300" required>
            @foreach ($ciclos as $ciclo)
                <option value="{{ $ciclo->id }}">{{ $ciclo->generacion }}</option>
            @endforeach
        </select>
        <input name="nombre" class="rounded border-gray-300" placeholder="Nombre" required>
        <select name="tipo" class="rounded border-gray-300" required>
            <option value="diagnostico_inicial">Diagnostico inicial</option>
            <option value="evaluacion_posterior">Evaluacion posterior</option>
        </select>
        <input type="date" name="fecha_aplicacion" class="rounded border-gray-300">
        <input name="version" class="rounded border-gray-300" placeholder="Version">
        <input type="number" name="total_preguntas" class="rounded border-gray-300" placeholder="Preguntas" min="1" required>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="activo" value="1" checked> Activo</label>
        <button class="rounded bg-cobaem-900 px-4 py-2 text-white md:col-span-2">Guardar examen</button>
    </form>

    <div class="mt-4 space-y-3">
        @forelse ($examenes as $examen)
            <article class="rounded bg-white p-4 shadow-sm">
                <form method="POST" action="{{ route('admin.examenes.update', $examen) }}" class="grid gap-3 md:grid-cols-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="ciclo_ingreso_id" value="{{ $examen->ciclo_ingreso_id }}">
                    <input name="nombre" value="{{ $examen->nombre }}" class="rounded border-gray-300 md:col-span-2">
                    <select name="tipo" class="rounded border-gray-300">
                        <option value="diagnostico_inicial" @selected($examen->tipo === 'diagnostico_inicial')>Diagnostico</option>
                        <option value="evaluacion_posterior" @selected($examen->tipo === 'evaluacion_posterior')>Posterior</option>
                    </select>
                    <input type="date" name="fecha_aplicacion" value="{{ $examen->fecha_aplicacion?->toDateString() }}" class="rounded border-gray-300">
                    <input name="version" value="{{ $examen->version }}" class="rounded border-gray-300">
                    <input type="number" name="total_preguntas" value="{{ $examen->total_preguntas }}" class="rounded border-gray-300">
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="activo" value="1" @checked($examen->activo)> Activo</label>
                    <button class="rounded bg-gray-800 px-3 py-2 text-sm text-white">Actualizar</button>
                </form>
                <form method="POST" action="{{ route('admin.examenes.destroy', $examen) }}" class="mt-2" onsubmit="return confirm('Eliminar este examen puede afectar cargas o consultas asociadas. ¿Deseas continuar?')">
                    @csrf
                    @method('DELETE')
                    <button class="text-sm font-semibold text-red-700">Eliminar</button>
                </form>
                <p class="mt-2 text-xs text-gray-500">ID para CSV: {{ $examen->id }} · {{ $examen->ciclo->generacion }}</p>
            </article>
        @empty
            <p class="rounded bg-white p-4 text-sm text-gray-600 shadow-sm">Aun no hay examenes configurados.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $examenes->links() }}</div>
@endsection
