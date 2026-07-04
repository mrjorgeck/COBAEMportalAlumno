@extends('layouts.alumno')

@section('titulo', 'Aún no disponible')

@section('contenido')
    <div class="rounded-xl bg-white p-6 shadow text-center">
        <h1 class="text-lg font-bold text-cobaem-900">{{ $modulo }}</h1>
        <p class="mt-3 text-sm text-gray-600">
            Esta sección aún no está disponible. Te avisaremos cuando se publique.
        </p>
        <a href="{{ url('/mi-proceso') }}"
           class="mt-5 inline-block rounded-lg bg-cobaem-700 px-4 py-2 text-sm text-white">
            Volver a mi proceso
        </a>
    </div>
@endsection
