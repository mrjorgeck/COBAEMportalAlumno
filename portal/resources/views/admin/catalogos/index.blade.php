@extends('layouts.admin')
@section('titulo', 'Catalogos')

@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Catalogos</h1>

    <form method="POST" action="{{ route('admin.catalogos.store') }}" class="mt-4 rounded bg-white p-4 shadow-sm">
        @csrf
        <div class="grid gap-3 md:grid-cols-6">
            <input name="tipo" required placeholder="tipo" class="rounded border-gray-300">
            <input name="clave" required placeholder="clave" class="rounded border-gray-300">
            <input name="nombre" required placeholder="nombre" class="rounded border-gray-300 md:col-span-2">
            <input name="orden" type="number" min="0" value="0" class="rounded border-gray-300">
            <select name="parent_id" class="rounded border-gray-300">
                <option value="">Sin dependencia</option>
                @foreach ($padres as $padre)
                    <option value="{{ $padre->id }}">{{ $padre->tipo }} / {{ $padre->nombre }}</option>
                @endforeach
            </select>
        </div>
        <label class="mt-3 inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="activo" value="1" checked class="rounded border-gray-300">
            Activo
        </label>
        <button class="mt-3 rounded bg-cobaem-900 px-4 py-2 text-white">Guardar</button>
    </form>

    <div class="mt-4 rounded bg-white p-4 text-sm shadow-sm">
        <div class="grid grid-cols-7 gap-2 pb-2 text-xs uppercase text-gray-500">
            <span>Tipo</span>
            <span>Clave</span>
            <span class="col-span-2">Nombre</span>
            <span>Dependencia</span>
            <span>Orden</span>
            <span>Acciones</span>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach ($catalogos as $catalogo)
                <div class="py-3">
                    <form method="POST" action="{{ route('admin.catalogos.update', $catalogo) }}" class="grid grid-cols-7 gap-2">
                        @csrf
                        @method('PATCH')
                        <input name="tipo" value="{{ $catalogo->tipo }}" required class="min-w-0 rounded border-gray-300">
                        <input name="clave" value="{{ $catalogo->clave }}" required class="min-w-0 rounded border-gray-300">
                        <input name="nombre" value="{{ $catalogo->nombre }}" required class="col-span-2 min-w-0 rounded border-gray-300">
                        <select name="parent_id" class="min-w-0 rounded border-gray-300">
                            <option value="">Sin dependencia</option>
                            @foreach ($padres->where('id', '!=', $catalogo->id) as $padre)
                                <option value="{{ $padre->id }}" @selected($catalogo->parent_id === $padre->id)>
                                    {{ $padre->tipo }} / {{ $padre->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <input name="orden" type="number" min="0" value="{{ $catalogo->orden }}" class="min-w-0 rounded border-gray-300">
                        <div class="flex gap-2">
                            <label class="inline-flex items-center gap-1">
                                <input type="checkbox" name="activo" value="1" @checked($catalogo->activo) class="rounded border-gray-300">
                                {{ $catalogo->activo ? 'Activo' : 'Inactivo' }}
                            </label>
                            <button class="rounded bg-cobaem-900 px-3 py-2 text-white">Actualizar</button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('admin.catalogos.toggle', $catalogo) }}" class="mt-2 inline-block">
                        @csrf
                        @method('PATCH')
                        <button class="rounded bg-gray-100 px-3 py-2 text-gray-800">{{ $catalogo->activo ? 'Inactivar' : 'Activar' }}</button>
                    </form>
                    @if ($catalogo->padre)
                        <span class="ml-2 text-xs text-gray-500">Depende de {{ $catalogo->padre->nombre }}</span>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $catalogos->links() }}</div>
    </div>
@endsection
