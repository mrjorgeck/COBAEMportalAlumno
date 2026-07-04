@extends('layouts.alumno')

@section('titulo', 'Aviso de privacidad')

@section('contenido')
    <div class="rounded-xl bg-white p-6 shadow">
        <h1 class="text-xl font-bold text-cobaem-900">Aviso de privacidad</h1>

        {{-- PENDIENTE (docs/09 §6.2): texto legal definitivo proporcionado
             por el plantel/COBAEM. NO publicar el portal sin este texto. --}}
        <p class="mt-4 text-sm text-gray-600">
            [Pendiente: aviso de privacidad institucional proporcionado por el plantel.]
        </p>
    </div>
@endsection
