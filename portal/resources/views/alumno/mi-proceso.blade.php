@extends('layouts.alumno')

@section('titulo', 'Mi proceso')

@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Mi proceso</h1>
    <p class="mt-1 text-sm text-gray-600">{{ $proceso->alumno->nombre_completo }} - {{ $proceso->folio_registro }}</p>

    <div class="mt-4 space-y-3">
        @foreach ($etapas as $etapa => $estado)
            @php
                $estadoNormalizado = str($estado)->lower()->replace('_', ' ')->toString();
                $completo = in_array($estado, ['registrado', 'validado', 'generado', 'asignado', 'publicada'], true);
                $alerta = in_array($estado, ['requiere_correccion', 'rechazado'], true);
                $color = $completo ? 'bg-green-50 text-green-800 border-green-200' : ($alerta ? 'bg-yellow-50 text-yellow-900 border-yellow-200' : 'bg-gray-50 text-gray-700 border-gray-200');
                $icono = $completo ? '✓' : ($alerta ? '!' : '…');
                $siguiente = match (true) {
                    $etapa === 'Registro' => $completo ? 'Tu registro fue recibido. Conserva tu folio para cualquier aclaracion.' : 'Completa los datos pendientes para continuar.',
                    $etapa === 'Formato' => $completo ? 'Tu formato ya fue generado. Puedes descargarlo cuando lo necesites.' : 'Descarga e imprime tu formato PDF para entregarlo en el plantel.',
                    str_contains($etapa, 'Document') => $completo ? 'Tu documentacion esta validada.' : ($alerta ? 'Revisa las observaciones de control escolar y corrige lo solicitado.' : 'Tu documentacion esta en revision; te avisaremos aqui.'),
                    str_contains($etapa, 'Evalu') => 'Cuando se publiquen resultados, los veras en esta seccion.',
                    $etapa === 'Grupo escolar' => $completo ? 'Tu grupo ya esta asignado. Revisa tambien tu horario.' : 'El plantel publicara tu grupo cuando este listo.',
                    $etapa === 'Matricula' => $completo ? 'Tu matricula ya esta publicada.' : 'La matricula se publicara cuando el plantel la confirme.',
                    default => 'Revisa esta etapa para conocer el siguiente paso.',
                };
            @endphp
            <div class="rounded border bg-white p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border text-sm font-bold {{ $color }}" aria-hidden="true">{{ $icono }}</span>
                    <div>
                        <p class="font-semibold">{{ $etapa }}</p>
                        <p class="text-sm font-medium text-gray-700">{{ ucfirst($estadoNormalizado) }}</p>
                        <p class="mt-1 text-sm text-gray-600">{{ $siguiente }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <nav class="mt-6 grid gap-2">
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'datos') }}">Mis datos</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'documentacion') }}">Documentacion</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'resultados') }}">Evaluacion diagnostica</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'areas-mejora') }}">Areas de mejora</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'materiales') }}">Materiales recomendados</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'propedeutico') }}">Curso propedeutico</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'evaluacion-posterior') }}">Evaluacion posterior</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'avance') }}">Mi avance</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'grupo-escolar') }}">Grupo escolar</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'matricula') }}">Matricula</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'horario') }}">Horario de clases</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'sicobaem') }}">SICOBaEM</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'regularizacion') }}">Regularizacion autodirigida</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.mi-proceso.seccion', 'avisos') }}">Avisos</a>
        <a class="rounded bg-white p-3 shadow-sm" href="{{ route('alumno.formato.descargar') }}">Formato de inscripcion</a>
    </nav>
@endsection
