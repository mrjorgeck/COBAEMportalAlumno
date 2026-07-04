<?php

namespace App\Jobs;

use App\Models\Alumno;
use App\Models\Catalogo;
use App\Models\CicloIngreso;
use App\Models\ClaveRespuesta;
use App\Models\DocumentoAlumno;
use App\Models\Examen;
use App\Models\GrupoPropedeutico;
use App\Models\ImportacionCsv;
use App\Models\Plantel;
use App\Models\ProcesoIngreso;
use App\Services\CalculoResultadosService;
use App\Services\CurpValidator;
use App\Services\FolioService;
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
        $headers = fgetcsv($handle) ?: [];
        $resumen = [];
        $creados = $actualizados = $errores = $total = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $total++;
            $data = array_combine($headers, $row) ?: [];

            if ($importacion->tipo_importacion === 'clave_respuestas') {
                $examen = Examen::find((int) ($data['examen_id'] ?? 0));
                $area = Catalogo::where('tipo', 'area_evaluacion')->where('clave', trim($data['area_clave'] ?? ''))->first();
                $materia = filled($data['materia_clave'] ?? null)
                    ? Catalogo::where('tipo', 'materia')->where('clave', trim($data['materia_clave']))->first()
                    : null;

                if (! $examen || ! $area || blank($data['pregunta'] ?? null) || blank($data['respuesta_correcta'] ?? null)) {
                    $errores++;
                    $resumen[] = ['fila' => $total + 1, 'error' => 'Examen, pregunta, respuesta o area no encontrados'];

                    continue;
                }

                ClaveRespuesta::updateOrCreate(
                    ['examen_id' => $examen->id, 'pregunta' => (int) $data['pregunta']],
                    [
                        'respuesta_correcta' => mb_strtoupper(trim($data['respuesta_correcta']))[0],
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

            $alumno = Alumno::firstOrCreate(
                ['curp' => $curp],
                [
                    'nombres' => $data['nombres'] ?? 'Alumno',
                    'primer_apellido' => $data['primer_apellido'] ?? 'Importado',
                    'segundo_apellido' => $data['segundo_apellido'] ?? null,
                    'fecha_nacimiento' => $data['fecha_nacimiento'] ?? '2008-01-01',
                    'sexo_id' => Catalogo::deTipo('sexo')->first()->id,
                    'nacionalidad_id' => Catalogo::deTipo('nacionalidad')->first()->id,
                    'estado_civil_id' => Catalogo::deTipo('estado_civil')->first()->id,
                    'entidad_nacimiento_id' => Catalogo::deTipo('entidad')->first()->id,
                    'municipio_nacimiento_id' => Catalogo::deTipo('municipio')->first()->id,
                ],
            );

            $proceso = ProcesoIngreso::firstOrNew(['alumno_id' => $alumno->id, 'ciclo_ingreso_id' => $ciclo->id]);
            if (! $proceso->exists) {
                $proceso->folio_registro = $folioService->generar($ciclo, $plantel);
                $creados++;
            } else {
                $actualizados++;
            }
            $proceso->fill([
                'plantel_id' => $plantel->id,
                'folio_examen' => $data['folio_examen'] ?? $proceso->folio_examen,
                'tipo_estudiante_id' => Catalogo::deTipo('tipo_estudiante')->first()->id,
                'promedio_secundaria' => $data['promedio_secundaria'] ?? 0,
                'estatus_proceso' => 'registrado',
                'acepto_privacidad_at' => now(),
                'fecha_registro' => now(),
            ])->save();
        }

        fclose($handle);

        $importacion->update([
            'total_filas' => $total,
            'registros_creados' => $creados,
            'registros_actualizados' => $actualizados,
            'registros_error' => $errores,
            'resumen' => $resumen,
            'estado' => $errores ? 'error' : 'completada',
        ]);
    }
}
