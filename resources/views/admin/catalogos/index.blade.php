@extends('layouts.admin')
@section('titulo', 'Catalogos')
@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Catalogos</h1>
    <form method="POST" action="{{ route('admin.catalogos.store') }}" class="mt-4 rounded bg-white p-4 shadow-sm">
        @csrf
        <input name="tipo" required placeholder="tipo" class="rounded border-gray-300">
        <input name="clave" required placeholder="clave" class="rounded border-gray-300">
        <input name="nombre" required placeholder="nombre" class="rounded border-gray-300">
        <button class="rounded bg-cobaem-900 px-4 py-2 text-white">Guardar</button>
    </form>
    <div class="mt-4 rounded bg-white p-4 text-sm shadow-sm">
        @foreach ($catalogos as $catalogo)
            <p>{{ $catalogo->tipo }} / {{ $catalogo->clave }} - {{ $catalogo->nombre }}</p>
        @endforeach
    </div>
@endsection
