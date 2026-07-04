<?php

namespace App\Jobs;

use App\Models\Alumno;
use App\Models\Catalogo;
use App\Models\CicloIngreso;
use App\Models\DocumentoAlumno;
use App\Models\ImportacionCsv;
use App\Models\Plantel;
use App\Models\ProcesoIngreso;
use App\Services\CurpValidator;
use App\Services\FolioService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ProcesarImportacionCsv implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $importacionId) {}

    public function handle(CurpValidator $validator, FolioService $folioService): void
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
