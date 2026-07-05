@extends('layouts.alumno')

@section('titulo', 'Sesión expirada')

@section('contenido')
    <x-error-page
        code="419"
        title="Tu sesión expiró"
        message="Por seguridad cerramos la sesión después de un tiempo sin actividad. Vuelve a entrar con tu CURP para continuar."
        action="Entrar con mi CURP"
    />
@endsection
