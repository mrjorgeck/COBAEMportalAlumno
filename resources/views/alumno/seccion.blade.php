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
    @elseif ($seccion === 'resultados')
        <div class="mt-4 rounded bg-white p-4 shadow-sm">
            @if ($resultadoInicial)
                <p class="text-sm text-gray-600">Resultado general</p>
                <p class="text-3xl font-bold text-cobaem-900">{{ number_format((float) $resultadoInicial->porcentaje_total, 2) }}%</p>
                <p class="mt-1 text-sm">Nivel: {{ $resultadoInicial->nivelDesempeno->nombre }} · Riesgo {{ $resultadoInicial->nivelRiesgo->nombre }}</p>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead><tr class="border-b"><th class="py-2">Area</th><th>Resultado</th><th>Diagnostico</th><th>Recomendacion</th></tr></thead>
                        <tbody>
                            @foreach ($resultadoInicial->areas as $area)
                                <tr class="border-b">
                                    <td class="py-2">{{ $area->area->nombre }}</td>
                                    <td>{{ number_format((float) $area->porcentaje, 2) }}%</td>
                                    <td>{{ $area->nivelRiesgo->nombre }}</td>
                                    <td>{{ $area->recomendacion }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm">Tus resultados aun no han sido publicados.</p>
            @endif
        </div>
    @elseif ($seccion === 'areas-mejora')
        <div class="mt-4 space-y-3">
            @php
                $areasMejora = $resultadoInicial?->areas->filter(fn ($area) => in_array($area->nivelRiesgo->clave, ['ALTO', 'CRITICO'], true)) ?? collect();
            @endphp
            @forelse ($areasMejora as $area)
                <article class="rounded bg-white p-4 shadow-sm">
                    <p class="font-semibold">{{ $area->area->nombre }} · {{ number_format((float) $area->porcentaje, 2) }}%</p>
                    <p class="mt-1 text-sm text-gray-600">Nivel de riesgo: {{ $area->nivelRiesgo->nombre }}</p>
                    <p class="mt-2 text-sm">{{ $area->recomendacion }}</p>
                </article>
            @empty
                <p class="rounded bg-white p-4 text-sm shadow-sm">No hay areas de mejora prioritarias publicadas.</p>
            @endforelse
        </div>
    @elseif ($seccion === 'materiales')
        <div class="mt-4 space-y-3">
            @forelse ($materiales as $material)
                <article class="rounded bg-white p-4 shadow-sm">
                    <p class="font-semibold">{{ $material->titulo }}</p>
                    <p class="text-sm text-gray-600">{{ $material->area->nombre }} · {{ str_replace('_', ' ', $material->tipo_material) }}</p>
                    @if ($material->descripcion)
                        <p class="mt-2 text-sm">{{ $material->descripcion }}</p>
                    @endif
                    @if ($material->url)
                        <a class="mt-3 inline-block text-sm font-semibold text-cobaem-900" href="{{ $material->url }}" target="_blank" rel="noopener">Abrir material</a>
                    @endif
                </article>
            @empty
                <p class="rounded bg-white p-4 text-sm shadow-sm">No hay materiales recomendados publicados para tus areas de mejora.</p>
            @endforelse
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
