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
            fputcsv($out, ['curp', 'nombre', 'folio_registro', 'folio_examen', 'ciclo', 'estatus', 'documentacion']);
            ProcesoIngreso::with(['alumno', 'ciclo'])->chunk(100, function ($procesos) use ($out): void {
                foreach ($procesos as $proceso) {
                    fputcsv($out, [
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

    public function importaciones()
    {
        return view('admin.csv.importaciones', ['importaciones' => ImportacionCsv::latest()->paginate(20)]);
    }

    public function importar(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tipo_importacion' => ['required', 'in:alumnos,documentacion'],
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
}
