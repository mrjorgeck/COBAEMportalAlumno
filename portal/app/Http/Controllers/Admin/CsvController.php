<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcesarImportacionCsv;
use App\Models\ImportacionCsv;
use App\Models\ProcesoIngreso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvController extends Controller
{
    public function exportaciones()
    {
        return view('admin.csv.exportaciones');
    }

    public function exportarAlumnos(Request $request): StreamedResponse
    {
        activity('csv')->causedBy($request->user())->log('Exportó base de alumnos');

        return response()->streamDownload(function (): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['curp', 'nombre', 'folio_registro', 'folio_examen', 'ciclo', 'estatus', 'documentacion']);
            ProcesoIngreso::with(['alumno', 'ciclo'])->chunk(100, function ($procesos) use ($out): void {
                foreach ($procesos as $proceso) {
                    $this->putCsvRow($out, [
                        $proceso->alumno->curp,
                        $proceso->alumno->nombre_completo,
                        $proceso->folio_registro,
                        $proceso->folio_examen,
                        $proceso->ciclo->anio,
                        $proceso->estatus_proceso,
                        $proceso->estatus_documentacion,
                    ]);
                }
            });
            fclose($out);
        }, 'alumnos.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportarDocumentacion(Request $request): StreamedResponse
    {
        activity('csv')->causedBy($request->user())->log('Exporto documentacion de alumnos');

        return response()->streamDownload(function (): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['curp', 'folio_registro', 'ciclo', 'documento', 'estado', 'observacion']);
            ProcesoIngreso::with(['alumno', 'ciclo', 'documentos.tipoDocumento'])->chunk(100, function ($procesos) use ($out): void {
                foreach ($procesos as $proceso) {
                    foreach ($proceso->documentos as $documento) {
                        $this->putCsvRow($out, [
                            $proceso->alumno->curp,
                            $proceso->folio_registro,
                            $proceso->ciclo->anio,
                            $documento->tipoDocumento->nombre,
                            $documento->estado_documento,
                            $documento->observacion,
                        ]);
                    }
                }
            });
            fclose($out);
        }, 'documentacion.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportarResultados(Request $request): StreamedResponse
    {
        activity('csv')->causedBy($request->user())->log('Exporto resultados de evaluacion');

        return response()->streamDownload(function (): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['curp', 'folio_examen', 'ciclo', 'examen', 'puntaje_total', 'porcentaje_total', 'nivel_riesgo', 'nivel_desempeno']);
            ProcesoIngreso::with(['alumno', 'ciclo', 'resultados.examen', 'resultados.nivelRiesgo', 'resultados.nivelDesempeno'])->chunk(100, function ($procesos) use ($out): void {
                foreach ($procesos as $proceso) {
                    foreach ($proceso->resultados as $resultado) {
                        $this->putCsvRow($out, [
                            $proceso->alumno->curp,
                            $proceso->folio_examen,
                            $proceso->ciclo->anio,
                            $resultado->examen->nombre,
                            $resultado->puntaje_total,
                            $resultado->porcentaje_total,
                            $resultado->nivelRiesgo->nombre,
                            $resultado->nivelDesempeno->nombre,
                        ]);
                    }
                }
            });
            fclose($out);
        }, 'resultados.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function importaciones()
    {
        return view('admin.csv.importaciones', ['importaciones' => ImportacionCsv::latest()->paginate(20)]);
    }

    public function importar(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tipo_importacion' => ['required', 'in:alumnos,documentacion,clave_respuestas,resultados_examen,respuestas_examen,grupo_propedeutico,grupo_escolar,matriculas,horarios'],
            'archivo' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $path = $data['archivo']->store('importaciones', 'local');
        $importacion = ImportacionCsv::create([
            'tipo_importacion' => $data['tipo_importacion'],
            'archivo_original_path' => $path,
            'usuario_id' => $request->user()?->id,
            'estado' => 'pendiente',
        ]);

        ProcesarImportacionCsv::dispatch($importacion->id);

        return back()->with('mensaje', 'Importación encolada para procesamiento.');
    }

    public function plantilla(string $tipo): StreamedResponse
    {
        $plantillas = [
            'clave_respuestas' => ['examen_id', 'pregunta', 'respuesta_correcta', 'area_clave', 'materia_clave', 'competencia', 'ponderacion'],
            'respuestas_examen' => ['examen_id', 'folio_examen', '1', '2', '3'],
            'resultados_examen' => ['examen_id', 'folio_examen', 'puntaje_total', 'porcentaje_total', 'nivel_riesgo_clave', 'nivel_desempeno_clave', 'MAT_puntaje', 'MAT_porcentaje', 'MAT_riesgo'],
            'grupo_propedeutico' => ['ciclo', 'curp', 'folio_examen', 'grupo'],
            'grupo_escolar' => ['ciclo', 'curp', 'folio_examen', 'grupo'],
            'matriculas' => ['ciclo', 'curp', 'folio_examen', 'matricula'],
            'horarios' => ['ciclo', 'grupo', 'dia', 'hora_inicio', 'hora_fin', 'materia', 'docente', 'aula'],
        ];

        abort_unless(array_key_exists($tipo, $plantillas), 404);

        return response()->streamDownload(function () use ($plantillas, $tipo): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, $plantillas[$tipo]);
            fputcsv($out, match ($tipo) {
                'clave_respuestas' => [1, 20, 'B,C', 'MAT', '', 'Acepta B o C', 1],
                'respuestas_examen' => [1, 'EX-001', 'A', 'B', 'C'],
                'resultados_examen' => [1, 'EX-001', 8, 80, 'BAJO', 'ADECUADO', 4, 80, 'BAJO'],
                'grupo_escolar' => [2026, 'AEXA000101HMNXXXA1', 'EX-001', '1-A'],
                'matriculas' => [2026, 'AEXA000101HMNXXXA1', 'EX-001', '26000001'],
                'horarios' => [2026, '1-A', 1, '08:00', '09:00', 'Matematicas I', 'Docente Sintetico', 'Aula 1'],
                default => [2026, 'AEXA000101HMNXXXA1', 'EX-001', 'P-03'],
            });
            fclose($out);
        }, $tipo.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * @param  resource  $out
     */
    private function putCsvRow($out, array $row): void
    {
        fputcsv($out, array_map($this->neutralizeFormula(...), $row));
    }

    private function neutralizeFormula(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $trimmed = ltrim($value);

        if ($trimmed !== '' && str_contains('=+-@', $trimmed[0])) {
            return "'".$value;
        }

        return $value;
    }
}
