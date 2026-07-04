@extends('layouts.alumno')

@section('titulo', 'Mi proceso')

@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">{{ ucfirst(str_replace('-', ' ', $seccion)) }}</h1>

    @if ($seccion === 'documentacion')
        <div class="mt-4 space-y-3">
            @foreach ($proceso->documentos as $documento)
                <div class="rounded bg-white p-4 shadow-sm">
                    <p class="font-semibold">{{ $documento->tipoDocumento->nombre }}</p>
                    <p class="text-sm">{{ $documento->estado_documento }}</p>
                    @if ($documento->observacion)
                        <p class="mt-1 text-sm text-gray-600">{{ $documento->observacion }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @elseif ($seccion === 'avisos')
        <div class="mt-4 space-y-3">
            @forelse ($avisos as $aviso)
                <article class="rounded bg-white p-4 shadow-sm">
                    <p class="font-semibold">{{ $aviso->titulo }}</p>
                    <p class="mt-1 text-sm text-gray-700">{{ $aviso->mensaje }}</p>
                    <form method="POST" action="{{ route('alumno.avisos.leido', $aviso) }}" class="mt-3">
                        @csrf
                        <button class="text-sm font-semibold text-cobaem-900">Marcar como leido</button>
                    </form>
                </article>
            @empty
                <p class="rounded bg-white p-4 text-sm shadow-sm">No hay avisos publicados.</p>
            @endforelse
        </div>
    @else
        <div class="mt-4 rounded bg-white p-4 text-sm shadow-sm">
            <p><strong>CURP:</strong> {{ $proceso->alumno->curp }}</p>
            <p><strong>Nombre:</strong> {{ $proceso->alumno->nombre_completo }}</p>
            <p><strong>Folio examen:</strong> {{ $proceso->folio_examen }}</p>
            <p><strong>Estatus:</strong> {{ $proceso->estatus_proceso }}</p>
        </div>
    @endif
@endsection
