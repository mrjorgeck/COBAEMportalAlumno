@extends('layouts.alumno')

@section('titulo', 'Registro completo')

@section('contenido')
    <div class="rounded-xl bg-white p-6 shadow">
        <h1 class="text-xl font-bold text-cobaem-900">Registro completado</h1>
        <p class="mt-2 text-sm text-gray-600">Tu informacion quedo registrada para el ciclo activo.</p>
        <div class="mt-4 grid gap-3">
            <a href="{{ route('alumno.formato.descargar') }}" class="rounded bg-cobaem-900 px-4 py-2 text-center font-semibold text-white">Descargar formato PDF</a>
            <a href="{{ route('alumno.mi-proceso') }}" class="rounded bg-gray-200 px-4 py-2 text-center font-semibold">Ver mi proceso</a>
        </div>
    </div>
@endsection
