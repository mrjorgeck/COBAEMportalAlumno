@extends('layouts.alumno')

@section('titulo', 'Acceso no permitido')

@section('contenido')
    <x-error-page
        code="403"
        title="No tienes permiso para ver esta sección"
        message="Tu sesión no cuenta con permiso para entrar aquí. Si crees que necesitas acceso, pide apoyo a control escolar."
    />
@endsection
