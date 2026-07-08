@extends('layouts.alumno')

@section('titulo', 'Aviso de privacidad')

@section('contenido')
    @php
        $fuente = file_get_contents(base_path('../docs/aviso-privacidad.md')) ?: '';
        preg_match('/## Versi.n integral\s*(.*?)\n---\s*\n## Versi.n simplificada/su', $fuente, $matches);
        $aviso = trim($matches[1] ?? '');
        $aviso = str_replace('[colocar fecha de publicacion]', config('portal.aviso_privacidad_fecha_publicacion'), $aviso);
        $aviso = str_replace('[colocar fecha de publicación]', config('portal.aviso_privacidad_fecha_publicacion'), $aviso);
    @endphp

    <section class="rounded bg-white p-6 shadow-sm">
        <div class="prose prose-sm max-w-none whitespace-pre-line text-gray-800">
            {{ $aviso }}
        </div>
    </section>
@endsection
