@extends('layouts.alumno')

@section('titulo', 'Registro completo')

@section('contenido')
    <div class="space-y-5 rounded-xl bg-white p-6 shadow">
        <div>
            <h1 class="text-xl font-bold text-cobaem-900">Registro completado</h1>
            <p class="mt-2 text-sm text-gray-600">Tu informacion quedo registrada para el ciclo activo.</p>
        </div>

        <div class="rounded border border-cobaem-100 bg-cobaem-100 p-4">
            <p class="text-sm font-semibold text-cobaem-900">Folio interno</p>
            <p class="mt-2 break-all text-3xl font-bold tracking-wide text-cobaem-900">{{ $proceso?->folio_registro ?? 'No disponible' }}</p>
            <p class="mt-2 text-sm text-cobaem-900">Conserva este folio para consultar tu proceso y entregar documentos.</p>
        </div>

        <div class="rounded border border-gray-200 p-4 text-sm text-gray-700">
            <p class="font-semibold text-gray-900">Siguiente paso presencial</p>
            <p class="mt-1">Descarga e imprime tu formato PDF. Presentalo en el plantel junto con la documentacion solicitada para continuar tu inscripcion.</p>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <a href="{{ route('alumno.formato.descargar') }}" class="min-h-11 rounded bg-cobaem-900 px-4 py-3 text-center font-semibold text-white">Descargar formato PDF</a>
            <a href="{{ route('alumno.mi-proceso') }}" class="min-h-11 rounded bg-gray-200 px-4 py-3 text-center font-semibold">Ver mi proceso</a>
        </div>
    </div>
@endsection
