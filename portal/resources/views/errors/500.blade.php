@extends('layouts.alumno')

@section('titulo', 'Algo salió mal')

@section('contenido')
    <x-error-page
        code="500"
        title="Algo salió mal de nuestro lado"
        message="Intenta de nuevo más tarde. Si el problema continúa, acude a control escolar para recibir apoyo."
    />
@endsection
