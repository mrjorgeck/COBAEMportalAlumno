@extends('layouts.alumno')

@section('titulo', 'Portal en mantenimiento')

@section('contenido')
    <x-error-page
        code="503"
        title="Portal en mantenimiento"
        message="Estamos preparando el portal institucional. Intenta de nuevo más tarde o consulta la información con control escolar."
    />
@endsection
