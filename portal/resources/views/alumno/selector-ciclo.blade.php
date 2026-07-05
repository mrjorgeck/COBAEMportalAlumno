@extends('layouts.alumno')

@section('titulo', 'Seleccionar ciclo')

@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Selecciona el ciclo</h1>
    <form method="POST" action="{{ route('alumno.seleccionar-ciclo') }}" class="mt-4 space-y-3">
        @csrf
        @foreach ($procesos as $proceso)
            <label class="flex min-h-11 items-center gap-2 rounded bg-white p-4 shadow" for="proceso_{{ $proceso->id }}">
                <input id="proceso_{{ $proceso->id }}" type="radio" name="proceso_id" value="{{ $proceso->id }}" required>
                <span>{{ $proceso->ciclo->generacion }} - {{ $proceso->folio_registro }}</span>
            </label>
        @endforeach
        <button class="min-h-11 w-full rounded bg-cobaem-900 px-4 py-2 font-semibold text-white">Continuar</button>
    </form>
@endsection
