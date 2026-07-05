@extends('layouts.alumno')

@section('titulo', 'Página no encontrada')

@section('contenido')
    <x-error-page
        code="404"
        title="No encontramos esta página"
        message="Puede que el enlace esté incompleto o que la sección ya no esté disponible. Vuelve al inicio para continuar."
    />
@endsection
