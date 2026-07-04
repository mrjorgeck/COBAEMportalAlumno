<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Aviso;
use App\Models\ProcesoIngreso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MiProcesoController extends Controller
{
    public function index(Request $request): View
    {
        $proceso = $this->proceso($request);

        return view('alumno.mi-proceso', [
            'proceso' => $proceso,
            'etapas' => [
                'Registro' => $proceso->estatus_proceso,
                'Formato' => $proceso->descargasFormato()->exists() ? 'generado' : 'pendiente',
                'Documentación' => $proceso->estatus_documentacion,
                'Evaluación' => 'pendiente',
                'Grupo y matrícula' => $proceso->matricula ? 'publicado' : 'pendiente',
            ],
        ]);
    }

    public function seccion(Request $request, string $seccion): View|RedirectResponse
    {
        if (in_array($seccion, ['datos', 'documentacion'], true) && ! $request->session()->get('alumno_nivel_sensible', false)) {
            return redirect()->route('alumno.verificacion');
        }

        $proceso = $this->proceso($request);
        $avisos = collect();

        if ($seccion === 'avisos') {
            $avisos = Aviso::where('visible', true)
                ->where(function ($query) use ($proceso) {
                    $query->where('dirigido_a', 'todos')
                        ->orWhere(fn ($q) => $q->where('dirigido_a', 'ciclo')->where('ciclo_ingreso_id', $proceso->ciclo_ingreso_id))
                        ->orWhere(fn ($q) => $q->where('dirigido_a', 'alumno')->where('alumno_id', $proceso->alumno_id));
                })
                ->latest()
                ->get();
        }

        return view('alumno.seccion', compact('proceso', 'seccion', 'avisos'));
    }

    public function marcarAviso(Request $request, Aviso $aviso): RedirectResponse
    {
        $proceso = $this->proceso($request);
        $proceso->alumno->avisosLeidos()->syncWithoutDetaching([
            $aviso->id => ['leido' => true, 'fecha_lectura' => now()],
        ]);

        return back()->with('mensaje', 'Aviso marcado como leído.');
    }

    private function proceso(Request $request): ProcesoIngreso
    {
        return ProcesoIngreso::with(['alumno', 'documentos.tipoDocumento', 'descargasFormato'])
            ->findOrFail($request->session()->get('alumno_proceso_id'));
    }
}
