@extends('layouts.alumno')

@section('titulo', 'Mi proceso')

@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Mi proceso</h1>
    <p class="mt-1 text-sm text-gray-600">{{ $proceso->alumno->nombre_completo }} - {{ $proceso->folio_registro }}</p>

    <div class="mt-4 space-y-3">
        @foreach ($etapas as $etapa => $estado)
            <div class="rounded bg-white p-4 shadow-sm">
                <p class="font-semibold">{{ $etapa }}</p>
                <p class="text-sm text-gray-600">{{ $estado }}</p>
            </div>
        @endforeach
    </div>

    <nav class="mt-6 grid gap-2">
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'datos') }}">Mis datos</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'documentacion') }}">Documentacion</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'resultados') }}">Evaluacion diagnostica</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'areas-mejora') }}">Areas de mejora</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'materiales') }}">Materiales recomendados</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'propedeutico') }}">Curso propedeutico</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'evaluacion-posterior') }}">Evaluacion posterior</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'avance') }}">Mi avance</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'grupo-escolar') }}">Grupo escolar</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'matricula') }}">Matricula</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'horario') }}">Horario de clases</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'sicobaem') }}">SICOBaEM</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'regularizacion') }}">Regularizacion autodirigida</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'avisos') }}">Avisos</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.formato.descargar') }}">Formato de inscripcion</a>
    </nav>
@endsection
