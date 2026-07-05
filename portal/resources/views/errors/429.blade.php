@extends('layouts.alumno')

@section('titulo', 'Demasiados intentos')

@section('contenido')
    <x-error-page
        code="429"
        title="Demasiados intentos"
        message="Recibimos muchos intentos en poco tiempo. Espera un minuto y vuelve a intentarlo."
    />
@endsection
