@extends('layouts.admin')
@section('titulo', 'Exportaciones')
@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Exportaciones CSV</h1>
    <a class="mt-4 inline-block rounded bg-cobaem-900 px-4 py-2 text-white" href="{{ route('admin.exportaciones.alumnos') }}">Exportar base de alumnos</a>
@endsection
