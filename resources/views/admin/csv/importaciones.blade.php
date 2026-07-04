@extends('layouts.admin')
@section('titulo', 'Importaciones')
@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Importaciones CSV</h1>
    <form method="POST" action="{{ route('admin.importaciones.store') }}" enctype="multipart/form-data" class="mt-4 rounded bg-white p-4 shadow-sm">
        @csrf
        <select name="tipo_importacion" class="rounded border-gray-300">
            <option value="alumnos">Alumnos</option>
            <option value="documentacion">Documentacion</option>
            <option value="clave_respuestas">Clave de respuestas</option>
            <option value="respuestas_examen">Respuestas de examen</option>
            <option value="resultados_examen">Resultados de examen</option>
            <option value="grupo_propedeutico">Grupo propedeutico</option>
        </select>
        <input type="file" name="archivo" required class="ml-2">
        <button class="rounded bg-cobaem-900 px-4 py-2 text-white">Importar</button>
    </form>
    <div class="mt-3 flex flex-wrap gap-2 text-sm">
        @foreach (['clave_respuestas', 'respuestas_examen', 'resultados_examen', 'grupo_propedeutico'] as $tipo)
            <a class="rounded bg-white px-3 py-2 shadow-sm" href="{{ route('admin.importaciones.plantilla', $tipo) }}">Plantilla {{ str_replace('_', ' ', $tipo) }}</a>
        @endforeach
    </div>
    <div class="mt-4 space-y-2">
        @foreach ($importaciones as $importacion)
            <div class="rounded bg-white p-3 text-sm shadow-sm">{{ $importacion->tipo_importacion }} - {{ $importacion->estado }} - {{ $importacion->total_filas }} filas</div>
        @endforeach
    </div>
@endsection
