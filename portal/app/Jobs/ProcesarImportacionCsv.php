<?php

namespace App\Jobs;

use App\Models\Alumno;
use App\Models\Catalogo;
use App\Models\CicloIngreso;
use App\Models\ClaveRespuesta;
use App\Models\DocumentoAlumno;
use App\Models\Examen;
use App\Models\GrupoEscolar;
use App\Models\GrupoPropedeutico;
use App\Models\Horario;
use App\Models\ImportacionCsv;
use App\Models\Plantel;
use App\Models\ProcesoIngreso;
use App\Services\CalculoResultadosService;
use App\Services\CurpValidator;
use App\Services\FolioService;
use App\Support\CsvImportSchemas;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ProcesarImportacionCsv implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $importacionId) {}

    public function handle(CurpValidator $validator, FolioService $folioService, CalculoResultadosService $calculo): void
    {
        $importacion = ImportacionCsv::findOrFail($this->importacionId);
        $importacion->update(['estado' => 'procesando']);

        $handle = fopen(Storage::path($importacion->archivo_original_path), 'r');
        $headers = array_map(fn ($header) => trim((string) $header), fgetcsv($handle) ?: []);
        $resumen = [];
        $creados = $actualizados = $omitidos = $errores = $total = 0;

        $esperados = CsvImportSchemas::encabezados($importacion->tipo_importacion);
        if ($esperados === [] || $headers !== $esperados) {
            fclose($handle);
            $importacion->update([
                'total_filas' => 0,
                'registros_creados' => 0,
                'registros_actualizados' => 0,
                'registros_sin_cambios' => 0,
                'registros_error' => 1,
                'resumen' => [[
                    'fila' => 1,
                    'categoria' => 'error',
                    'error' => 'Encabezados invalidos para '.$importacion->tipo_importacion,
                    'esperados' => $esperados,
                    'recibidos' => $headers,
                ]],
                'estado' => 'error',
            ]);

            return;
        }

        while (($row = fgetcsv($handle)) !== false) {
            $total++;
            $data = array_combine($headers, $row) ?: [];

            if ($importacion->tipo_importacion === 'clave_respuestas') {
                $examen = Examen::find((int) ($data['examen_id'] ?? 0));
                $area = Catalogo::where('tipo', 'area_evaluacion')->where('clave', trim($data['area_clave'] ?? ''))->first();
                $materia = filled($data['materia_clave'] ?? null)
                    ? Catalogo::where('tipo', 'materia')->where('clave', trim($data['materia_clave']))->first()
                    : null;

                $respuestaCorrecta = $this->respuestaCorrecta($data);

                if (! $examen || ! $area || blank($data['pregunta'] ?? null) || $respuestaCorrecta === '') {
                    $errores++;
                    $resumen[] = ['fila' => $total + 1, 'error' => 'Examen, pregunta, respuesta o area no encontrados'];

                    continue;
                }

                ClaveRespuesta::updateOrCreate(
                    ['examen_id' => $examen->id, 'pregunta' => (int) $data['pregunta']],
                    [
                        'respuesta_correcta' => $respuestaCorrecta,
                        'area_id' => $area->id,
                        'materia_id' => $materia?->id,
                        'competencia' => $data['competencia'] ?? null,
                        'ponderacion' => (float) ($data['ponderacion'] ?? 1),
                    ],
                );
                $actualizados++;

                continue;
            }

            if (in_array($importacion->tipo_importacion, ['respuestas_examen', 'resultados_examen'], true)) {
                $examen = Examen::find((int) ($data['examen_id'] ?? 0));
                $proceso = $examen
                    ? ProcesoIngreso::where('ciclo_ingreso_id', $examen->ciclo_ingreso_id)
                        ->where('folio_examen', trim($data['folio_examen'] ?? ''))
                        ->first()
                    : null;

                if (! $examen || ! $proceso) {
                    $errores++;
                    $resumen[] = ['fila' => $total + 1, 'error' => 'Examen o folio de examen no encontrado'];

                    continue;
                }

                if ($importacion->tipo_importacion === 'respuestas_examen') {
                    $respuestas = [];
                    foreach ($data as $columna => $valor) {
                        if (ctype_digit((string) $columna)) {
                            $respuestas[(int) $columna] = $valor;
                        }
                    }
                    $calculo->calcularDesdeRespuestas($proceso, $examen, $respuestas);
                } else {
                    $calculo->importarResultado($proceso, $examen, $data);
                }

                $actualizados++;

                continue;
            }

            if ($importacion->tipo_importacion === 'grupo_propedeutico') {
                $ciclo = CicloIngreso::where('anio', (int) ($data['ciclo'] ?? 2026))->first() ?? CicloIngreso::vigente();
                $grupo = GrupoPropedeutico::where('ciclo_ingreso_id', $ciclo->id)
                    ->where('nombre', trim($data['grupo'] ?? ''))
                    ->first();
                $proceso = ProcesoIngreso::where('ciclo_ingreso_id', $ciclo->id)
                    ->where(function ($query) use ($data) {
                        $query->where('folio_examen', trim($data['folio_examen'] ?? ''))
                            ->orWhereHas('alumno', fn ($q) => $q->where('curp', mb_strtoupper(trim($data['curp'] ?? ''))));
                    })
                    ->first();

                if (! $grupo || ! $proceso) {
                    $errores++;
                    $resumen[] = ['fila' => $total + 1, 'error' => 'Grupo o alumno no encontrado'];

                    continue;
                }

                $proceso->update(['grupo_propedeutico_id' => $grupo->id]);
                $actualizados++;

                continue;
            }

            if ($importacion->tipo_importacion === 'horarios') {
                $ciclo = CicloIngreso::where('anio', (int) ($data['ciclo'] ?? 2026))->first() ?? CicloIngreso::vigente();
                $grupo = GrupoEscolar::where('ciclo_ingreso_id', $ciclo->id)
                    ->where('grupo', trim($data['grupo'] ?? ''))
                    ->first();

                if (! $grupo || blank($data['dia'] ?? null) || blank($data['hora_inicio'] ?? null) || blank($data['hora_fin'] ?? null) || blank($data['materia'] ?? null)) {
                    $errores++;
                    $resumen[] = ['fila' => $total + 1, 'error' => 'Grupo escolar u horario incompleto'];

                    continue;
                }

                Horario::updateOrCreate(
                    [
                        'grupo_escolar_id' => $grupo->id,
                        'dia' => (int) $data['dia'],
                        'hora_inicio' => trim($data['hora_inicio']),
                        'materia' => trim($data['materia']),
                    ],
                    [
                        'hora_fin' => trim($data['hora_fin']),
                        'docente' => $data['docente'] ?? null,
                        'aula' => $data['aula'] ?? null,
                    ],
                );
                $actualizados++;

                continue;
            }

            if (in_array($importacion->tipo_importacion, ['grupo_escolar', 'matriculas'], true)) {
                $ciclo = CicloIngreso::where('anio', (int) ($data['ciclo'] ?? 2026))->first() ?? CicloIngreso::vigente();
                $proceso = ProcesoIngreso::where('ciclo_ingreso_id', $ciclo->id)
                    ->where(function ($query) use ($data) {
                        $query->where('folio_examen', trim($data['folio_examen'] ?? ''))
                            ->orWhereHas('alumno', fn ($q) => $q->where('curp', mb_strtoupper(trim($data['curp'] ?? ''))));
                    })
                    ->first();

                if (! $proceso) {
                    $errores++;
                    $resumen[] = ['fila' => $total + 1, 'error' => 'Alumno no encontrado en el ciclo indicado'];

                    continue;
                }

                if ($importacion->tipo_importacion === 'grupo_escolar') {
                    $grupo = GrupoEscolar::where('ciclo_ingreso_id', $ciclo->id)
                        ->where('grupo', trim($data['grupo'] ?? ''))
                        ->first();

                    if (! $grupo) {
                        $errores++;
                        $resumen[] = ['fila' => $total + 1, 'error' => 'Grupo escolar no encontrado'];

                        continue;
                    }

                    $proceso->update(['grupo_escolar_id' => $grupo->id]);
                    $actualizados++;

                    continue;
                }

                $matricula = trim($data['matricula'] ?? '');
                if ($matricula === '' || ProcesoIngreso::where('matricula', $matricula)->whereKeyNot($proceso->id)->exists()) {
                    $errores++;
                    $resumen[] = ['fila' => $total + 1, 'error' => 'Matricula vacia o duplicada'];

                    continue;
                }

                $proceso->update(['matricula' => $matricula]);
                $actualizados++;

                continue;
            }

            $curp = mb_strtoupper(trim($data['curp'] ?? ''));

            if (! $validator->esValida($curp)) {
                $errores++;
                $resumen[] = ['fila' => $total + 1, 'error' => 'CURP inválida'];

                continue;
            }

            $ciclo = CicloIngreso::where('anio', (int) ($data['ciclo'] ?? 2026))->first() ?? CicloIngreso::vigente();
            $plantel = Plantel::where('activo', true)->first();

            if ($importacion->tipo_importacion === 'documentacion') {
                $proceso = ProcesoIngreso::where('ciclo_ingreso_id', $ciclo->id)
                    ->whereHas('alumno', fn ($q) => $q->where('curp', $curp))
                    ->first();
                $tipo = Catalogo::where('tipo', 'tipo_documento')->where('clave', $data['documento'] ?? '')->first();

                if (! $proceso || ! $tipo) {
                    $errores++;
                    $resumen[] = ['fila' => $total + 1, 'error' => 'Proceso o documento no encontrado'];

                    continue;
                }

                DocumentoAlumno::updateOrCreate(
                    ['proceso_ingreso_id' => $proceso->id, 'tipo_documento_id' => $tipo->id],
                    ['estado_documento' => $data['estado'] ?? 'pendiente', 'observacion' => $data['observacion'] ?? null],
                );
                $actualizados++;

                continue;
            }

            $erroresAlumno = $this->validarFilaAlumno($data);
            if ($erroresAlumno !== []) {
                $errores++;
                $resumen[] = [
                    'fila' => $total + 1,
                    'categoria' => 'error',
                    'error' => 'Fila de alumno incompleta',
                    'campos' => $erroresAlumno,
                ];

                continue;
            }

            $alumno = Alumno::where('curp', $curp)->first();
            $folioExamen = trim((string) ($data['folio_examen'] ?? ''));
            if ($folioExamen !== '' && ProcesoIngreso::where('ciclo_ingreso_id', $ciclo->id)
                ->where('folio_examen', $folioExamen)
                ->when($alumno, fn ($query) => $query->where('alumno_id', '!=', $alumno->id))
                ->exists()) {
                $errores++;
                $resumen[] = [
                    'fila' => $total + 1,
                    'categoria' => 'error',
                    'error' => 'Folio de examen duplicado en el ciclo',
                    'curp' => $curp,
                ];

                continue;
            }

            if (! $alumno) {
                $alumno = Alumno::create([
                    'curp' => $curp,
                    'nombres' => trim($data['nombres']),
                    'primer_apellido' => trim($data['primer_apellido']),
                    'segundo_apellido' => blank($data['segundo_apellido'] ?? null) ? null : trim($data['segundo_apellido']),
                    'fecha_nacimiento' => trim($data['fecha_nacimiento']),
                    'sexo_id' => Catalogo::deTipo('sexo')->first()->id,
                    'nacionalidad_id' => Catalogo::deTipo('nacionalidad')->first()->id,
                    'estado_civil_id' => Catalogo::deTipo('estado_civil')->first()->id,
                    'entidad_nacimiento_id' => Catalogo::deTipo('entidad')->first()->id,
                    'municipio_nacimiento_id' => Catalogo::deTipo('municipio')->first()->id,
                ]);
            }

            $proceso = ProcesoIngreso::firstOrNew(['alumno_id' => $alumno->id, 'ciclo_ingreso_id' => $ciclo->id]);
            if (! $proceso->exists) {
                $proceso->folio_registro = $folioService->generar($ciclo, $plantel);
                $creados++;
                $categoria = 'creado';
            } else {
                $actualizados++;
                $categoria = 'actualizado';
            }
            $proceso->fill([
                'plantel_id' => $plantel->id,
                'folio_examen' => $folioExamen !== '' ? $folioExamen : $proceso->folio_examen,
                'tipo_estudiante_id' => Catalogo::deTipo('tipo_estudiante')->first()->id,
                'promedio_secundaria' => $data['promedio_secundaria'] ?? 0,
                'estatus_proceso' => 'registrado',
                'acepto_privacidad_at' => now(),
                'fecha_registro' => now(),
            ])->save();
            $resumen[] = ['fila' => $total + 1, 'categoria' => $categoria, 'curp' => $curp];
        }

        fclose($handle);

        $resumen = array_map(fn (array $fila) => $fila + ['categoria' => 'error'], $resumen);

        $importacion->update([
            'total_filas' => $total,
            'registros_creados' => $creados,
            'registros_actualizados' => $actualizados,
            'registros_sin_cambios' => $omitidos,
            'registros_error' => $errores,
            'resumen' => $resumen,
            'estado' => $errores ? 'error' : 'completada',
        ]);
    }

    private function respuestaCorrecta(array $data): string
    {
        $valores = [
            $data['respuesta_correcta'] ?? null,
            $data['respuesta_alterna'] ?? null,
            $data['respuesta_alternativa'] ?? null,
            $data['opcion_2'] ?? null,
            $data['opción_2'] ?? null,
        ];

        return ClaveRespuesta::normalizarRespuestasCorrectas(implode(',', array_filter($valores, filled(...))));
    }

    private function validarFilaAlumno(array $data): array
    {
        $errores = [];

        foreach (['nombres', 'primer_apellido', 'fecha_nacimiento'] as $campo) {
            if (blank($data[$campo] ?? null)) {
                $errores[] = $campo;
            }
        }

        if (! blank($data['fecha_nacimiento'] ?? null)) {
            $fecha = \DateTimeImmutable::createFromFormat('Y-m-d', trim($data['fecha_nacimiento']));
            if (! $fecha || $fecha->format('Y-m-d') !== trim($data['fecha_nacimiento'])) {
                $errores[] = 'fecha_nacimiento';
            }
        }

        return array_values(array_unique($errores));
    }
}
