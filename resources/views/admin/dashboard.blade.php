@extends('layouts.admin')

@section('titulo', 'Dashboard')

@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Dashboard de registros</h1>

    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($indicadores as $indicador => $valor)
            <div class="rounded-xl bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">{{ $indicador }}</p>
                <p class="mt-1 text-2xl font-bold text-cobaem-900">{{ $valor }}</p>
            </div>
        @endforeach
    </div>
@endsection
