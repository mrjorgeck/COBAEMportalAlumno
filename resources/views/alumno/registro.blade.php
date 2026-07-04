@extends('layouts.alumno')

@section('titulo', 'Registro')

@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Registro de nuevo ingreso</h1>
    <livewire:registro-wizard :curp="$curp" />
@endsection
