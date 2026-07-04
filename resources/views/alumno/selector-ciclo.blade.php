@extends('layouts.alumno')

@section('titulo', 'Seleccionar ciclo')

@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Selecciona el ciclo</h1>
    <form method="POST" action="{{ route('alumno.seleccionar-ciclo') }}" class="mt-4 space-y-3">
        @csrf
        @foreach ($procesos as $proceso)
            <label class="block rounded bg-white p-4 shadow">
                <input type="radio" name="proceso_id" value="{{ $proceso->id }}" required>
                {{ $proceso->ciclo->generacion }} - {{ $proceso->folio_registro }}
            </label>
        @endforeach
        <button class="w-full rounded bg-cobaem-900 px-4 py-2 font-semibold text-white">Continuar</button>
    </form>
@endsection
