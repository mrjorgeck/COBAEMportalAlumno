@extends('layouts.admin')
@section('titulo', 'Exportaciones')
@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Exportaciones CSV</h1>
    <div class="mt-4 flex flex-wrap gap-3">
        <a class="rounded bg-cobaem-900 px-4 py-2 text-white" href="{{ route('admin.exportaciones.alumnos') }}">Exportar base de alumnos</a>
        <a class="rounded bg-cobaem-900 px-4 py-2 text-white" href="{{ route('admin.exportaciones.documentacion') }}">Exportar documentacion</a>
        <a class="rounded bg-cobaem-900 px-4 py-2 text-white" href="{{ route('admin.exportaciones.resultados') }}">Exportar resultados</a>
    </div>
@endsection
