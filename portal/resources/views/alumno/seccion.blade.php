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
    @elseif ($seccion === 'evaluacion-posterior')
        <div class="mt-4 rounded bg-white p-4 shadow-sm">
            @if ($resultadoPosterior)
                <p class="text-sm text-gray-600">Resultado posterior al propedeutico</p>
                <p class="text-3xl font-bold text-cobaem-900">{{ number_format((float) $resultadoPosterior->porcentaje_total, 2) }}%</p>
                <p class="mt-1 text-sm">Nivel: {{ $resultadoPosterior->nivelDesempeno->nombre }} · Riesgo {{ $resultadoPosterior->nivelRiesgo->nombre }}</p>
            @else
                <p class="text-sm">Tu evaluacion posterior aun no ha sido publicada.</p>
            @endif
        </div>
    @elseif ($seccion === 'avance')
        <div class="mt-4 rounded bg-white p-4 shadow-sm">
            @if ($resultadoInicial && $resultadoPosterior)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead><tr class="border-b"><th class="py-2">Area</th><th>Diagnostico inicial</th><th>Despues del propedeutico</th><th>Avance</th></tr></thead>
                        <tbody>
                            @foreach ($resultadoInicial->areas as $areaInicial)
                                @php
                                    $areaPosterior = $resultadoPosterior->areas->firstWhere('area_id', $areaInicial->area_id);
                                    $avance = $areaPosterior ? ((float) $areaPosterior->porcentaje - (float) $areaInicial->porcentaje) : null;
                                @endphp
                                <tr class="border-b">
                                    <td class="py-2">{{ $areaInicial->area->nombre }}</td>
                                    <td>{{ number_format((float) $areaInicial->porcentaje, 2) }}%</td>
                                    <td>{{ $areaPosterior ? number_format((float) $areaPosterior->porcentaje, 2).'%' : 'Sin dato' }}</td>
                                    <td>{{ $avance === null ? 'Sin dato' : sprintf('%+0.2f', $avance) }}</td>
                                </tr>
                            @endforeach
                            @php $avanceTotal = (float) $resultadoPosterior->porcentaje_total - (float) $resultadoInicial->porcentaje_total; @endphp
                            <tr class="font-semibold">
                                <td class="py-2">Total</td>
                                <td>{{ number_format((float) $resultadoInicial->porcentaje_total, 2) }}%</td>
                                <td>{{ number_format((float) $resultadoPosterior->porcentaje_total, 2) }}%</td>
                                <td>{{ sprintf('%+0.2f', $avanceTotal) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm">El comparativo estara disponible cuando existan ambas evaluaciones.</p>
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
    @elseif ($seccion === 'propedeutico')
        <div class="mt-4 rounded bg-white p-4 shadow-sm">
            @if ($proceso->grupoPropedeutico)
                <p class="text-sm text-gray-600">Tu grupo propedeutico asignado es:</p>
                <p class="text-2xl font-bold text-cobaem-900">{{ $proceso->grupoPropedeutico->nombre }}</p>
                <dl class="mt-4 space-y-2 text-sm">
                    <div><dt class="font-semibold">Horario</dt><dd>{{ $proceso->grupoPropedeutico->horario_texto ?? 'Por confirmar' }}</dd></div>
                    <div><dt class="font-semibold">Aula</dt><dd>{{ $proceso->grupoPropedeutico->aula ?? 'Por confirmar' }}</dd></div>
                    <div><dt class="font-semibold">Inicio</dt><dd>{{ $proceso->grupoPropedeutico->fecha_inicio?->translatedFormat('d/m/Y') ?? 'Por confirmar' }}</dd></div>
                    <div><dt class="font-semibold">Responsable</dt><dd>{{ $proceso->grupoPropedeutico->responsable ?? 'Por confirmar' }}</dd></div>
                    <div><dt class="font-semibold">Indicaciones</dt><dd>{{ $proceso->grupoPropedeutico->indicaciones ?? 'Por confirmar' }}</dd></div>
                    <div><dt class="font-semibold">Materiales requeridos</dt><dd>{{ $proceso->grupoPropedeutico->materiales_requeridos ?? 'Por confirmar' }}</dd></div>
                </dl>
            @else
                <p class="text-sm">Tu grupo propedeutico aun no ha sido asignado.</p>
            @endif
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
    @elseif ($seccion === 'grupo-escolar')
        <div class="mt-4 rounded bg-white p-4 shadow-sm">
            @if ($proceso->grupoEscolar)
                <p class="text-sm text-gray-600">Tu grupo escolar definitivo es:</p>
                <p class="text-3xl font-bold text-cobaem-900">{{ $proceso->grupoEscolar->grupo }}</p>
                <dl class="mt-4 space-y-2 text-sm">
                    <div><dt class="font-semibold">Turno</dt><dd>{{ $proceso->grupoEscolar->turno->nombre }}</dd></div>
                    <div><dt class="font-semibold">Aula base</dt><dd>{{ $proceso->grupoEscolar->aula_base ?? 'Por confirmar' }}</dd></div>
                    <div><dt class="font-semibold">Inicio de clases</dt><dd>{{ $proceso->grupoEscolar->fecha_inicio_clases?->translatedFormat('d/m/Y') ?? 'Por confirmar' }}</dd></div>
                    <div><dt class="font-semibold">Indicaciones</dt><dd>{{ $proceso->grupoEscolar->indicaciones ?? 'Por confirmar' }}</dd></div>
                </dl>
            @else
                <p class="text-sm">Tu grupo escolar aun no ha sido asignado.</p>
            @endif
        </div>
    @elseif ($seccion === 'matricula')
        <div class="mt-4 rounded bg-white p-4 shadow-sm">
            @if ($proceso->matricula)
                <p class="text-sm text-gray-600">Tu matricula escolar es:</p>
                <p class="text-3xl font-bold text-cobaem-900">{{ $proceso->matricula }}</p>
            @else
                <p class="text-sm">Tu matricula aun no ha sido publicada.</p>
            @endif
        </div>
    @elseif ($seccion === 'horario')
        <div class="mt-4 rounded bg-white p-4 shadow-sm">
            @if ($proceso->grupoEscolar)
                <p class="font-semibold">Horario de {{ $proceso->grupoEscolar->grupo }}</p>
                <div class="mt-3 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead><tr class="border-b"><th class="py-2">Dia</th><th>Hora</th><th>Materia</th><th>Docente</th><th>Aula</th></tr></thead>
                        <tbody>
                            @forelse ($proceso->grupoEscolar->horarios->sortBy(['dia', 'hora_inicio']) as $horario)
                                <tr class="border-b">
                                    <td class="py-2">{{ $horario->diaNombre() }}</td>
                                    <td>{{ substr($horario->hora_inicio, 0, 5) }} - {{ substr($horario->hora_fin, 0, 5) }}</td>
                                    <td>{{ $horario->materia }}</td>
                                    <td>{{ $horario->docente ?? 'Por confirmar' }}</td>
                                    <td>{{ $horario->aula ?? $proceso->grupoEscolar->aula_base ?? 'Por confirmar' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-2 text-gray-600">El horario de tu grupo aun no ha sido cargado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm">El horario estara disponible cuando tengas grupo escolar asignado.</p>
            @endif
        </div>
    @elseif ($seccion === 'sicobaem')
        <div class="mt-4 rounded bg-white p-4 shadow-sm">
            @if ($sicobaemConfig)
                <p class="text-sm">{{ $sicobaemConfig->mensaje }}</p>
                @if ($sicobaemConfig->fecha_disponibilidad)
                    <p class="mt-3 text-sm"><strong>Disponibilidad:</strong> {{ $sicobaemConfig->fecha_disponibilidad->translatedFormat('d/m/Y') }}</p>
                @endif
                @if ($sicobaemConfig->pasos_activacion)
                    <div class="mt-3 whitespace-pre-line text-sm">{{ $sicobaemConfig->pasos_activacion }}</div>
                @endif
                @if ($sicobaemConfig->url)
                    <a class="mt-3 inline-block rounded bg-cobaem-900 px-4 py-2 text-sm font-semibold text-white" href="{{ $sicobaemConfig->url }}" target="_blank" rel="noopener">Abrir SICOBaEM</a>
                @endif
                @if ($sicobaemConfig->contacto_soporte)
                    <p class="mt-3 text-sm"><strong>Soporte:</strong> {{ $sicobaemConfig->contacto_soporte }}</p>
                @endif
            @else
                <p class="text-sm">Las instrucciones de SICOBaEM aun no han sido publicadas.</p>
            @endif
        </div>
    @elseif ($seccion === 'regularizacion')
        <div class="mt-4 rounded bg-white p-4 shadow-sm">
            <p class="font-semibold">Regularizacion autodirigida</p>
            <p class="mt-2 text-sm">Esta seccion queda preparada para conectar una plataforma de regularizacion autodirigida.</p>
            <dl class="mt-4 space-y-2 text-sm">
                <div><dt class="font-semibold">Estatus</dt><dd>{{ $proceso->regularizacion?->estatus ?? 'pendiente' }}</dd></div>
                <div><dt class="font-semibold">Ruta</dt><dd>{{ $proceso->regularizacion?->ruta?->nombre ?? 'Por asignar' }}</dd></div>
            </dl>
            @if ($proceso->regularizacion?->plataforma_externa_url)
                <a class="mt-3 inline-block text-sm font-semibold text-cobaem-900" href="{{ $proceso->regularizacion->plataforma_externa_url }}" target="_blank" rel="noopener">Abrir plataforma</a>
            @endif
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
