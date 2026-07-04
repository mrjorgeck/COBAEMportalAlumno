@extends('layouts.admin')

@section('titulo', 'Materiales')

@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Materiales recomendados</h1>

    <form method="POST" action="{{ route('admin.materiales.store') }}" class="mt-4 grid gap-3 rounded bg-white p-4 shadow-sm md:grid-cols-3">
        @csrf
        <select name="area_id" class="rounded border-gray-300" required>
            @foreach ($areas as $area)
                <option value="{{ $area->id }}">{{ $area->nombre }}</option>
            @endforeach
        </select>
        <select name="nivel_desempeno_id" class="rounded border-gray-300">
            <option value="">Cualquier nivel</option>
            @foreach ($niveles as $nivel)
                <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
            @endforeach
        </select>
        <select name="tipo_material" class="rounded border-gray-300" required>
            @foreach (['pdf', 'video', 'guia', 'actividad', 'sitio', 'curso_externo', 'plataforma_regularizacion'] as $tipo)
                <option value="{{ $tipo }}">{{ str_replace('_', ' ', $tipo) }}</option>
            @endforeach
        </select>
        <input name="titulo" class="rounded border-gray-300 md:col-span-2" placeholder="Titulo" required>
        <input name="url" class="rounded border-gray-300" placeholder="URL">
        <textarea name="descripcion" class="rounded border-gray-300 md:col-span-3" placeholder="Descripcion"></textarea>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="activo" value="1" checked> Activo</label>
        <button class="rounded bg-cobaem-900 px-4 py-2 text-white md:col-span-2">Guardar material</button>
    </form>

    <div class="mt-4 space-y-3">
        @foreach ($materiales as $material)
            <article class="rounded bg-white p-4 shadow-sm">
                <form method="POST" action="{{ route('admin.materiales.update', $material) }}" class="grid gap-3 md:grid-cols-4">
                    @csrf
                    @method('PUT')
                    <select name="area_id" class="rounded border-gray-300">
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}" @selected($material->area_id === $area->id)>{{ $area->nombre }}</option>
                        @endforeach
                    </select>
                    <select name="nivel_desempeno_id" class="rounded border-gray-300">
                        <option value="">Cualquier nivel</option>
                        @foreach ($niveles as $nivel)
                            <option value="{{ $nivel->id }}" @selected($material->nivel_desempeno_id === $nivel->id)>{{ $nivel->nombre }}</option>
                        @endforeach
                    </select>
                    <input name="titulo" value="{{ $material->titulo }}" class="rounded border-gray-300">
                    <input name="url" value="{{ $material->url }}" class="rounded border-gray-300">
                    <input type="hidden" name="tipo_material" value="{{ $material->tipo_material }}">
                    <textarea name="descripcion" class="rounded border-gray-300 md:col-span-3">{{ $material->descripcion }}</textarea>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="activo" value="1" @checked($material->activo)> Activo</label>
                    <button class="rounded bg-gray-800 px-3 py-2 text-white">Actualizar</button>
                </form>
            </article>
        @endforeach
    </div>

    <div class="mt-4">{{ $materiales->links() }}</div>
@endsection
