<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Aviso;
use App\Models\MaterialRecomendado;
use App\Models\ProcesoIngreso;
use App\Models\SicobaemConfig;
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
                'Grupo escolar' => $proceso->grupo_escolar_id ? 'asignado' : 'pendiente',
                'Matricula' => $proceso->matricula ? 'publicada' : 'pendiente',
            ],
        ]);
    }

    public function seccion(Request $request, string $seccion): View|RedirectResponse
    {
        $sensibles = ['datos', 'documentacion', 'resultados', 'areas-mejora', 'evaluacion-posterior', 'avance', 'materiales', 'grupo-escolar', 'matricula', 'horario', 'sicobaem', 'regularizacion'];
        if (in_array($seccion, $sensibles, true) && ! $request->session()->get('alumno_nivel_sensible', false)) {
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

        $resultadoInicial = $proceso->resultados
            ->first(fn ($resultado) => $resultado->examen->tipo === 'diagnostico_inicial');
        $resultadoPosterior = $proceso->resultados
            ->first(fn ($resultado) => $resultado->examen->tipo === 'evaluacion_posterior');

        $areasDebiles = $resultadoInicial?->areas
            ->filter(fn ($area) => in_array($area->nivelRiesgo->clave, ['ALTO', 'CRITICO'], true))
            ->pluck('area_id')
            ->all() ?? [];
        $materiales = collect();
        if ($seccion === 'materiales' && $resultadoInicial) {
            $materiales = MaterialRecomendado::with(['area', 'nivelDesempeno'])
                ->where('activo', true)
                ->whereIn('area_id', $areasDebiles)
                ->where(function ($query) use ($resultadoInicial) {
                    $query->whereNull('nivel_desempeno_id')
                        ->orWhere('nivel_desempeno_id', $resultadoInicial->nivel_desempeno_id);
                })
                ->orderBy('titulo')
                ->get();
        }

        $sicobaemConfig = $seccion === 'sicobaem'
            ? SicobaemConfig::where('ciclo_ingreso_id', $proceso->ciclo_ingreso_id)->where('activo', true)->first()
            : null;

        if ($seccion === 'regularizacion') {
            $proceso->regularizacion()->firstOrCreate([], [
                'estatus' => 'pendiente',
                'fecha_asignacion' => now(),
            ])->update(['fecha_ultima_consulta' => now()]);
            $proceso->load('regularizacion.ruta');
        }

        return view('alumno.seccion', compact('proceso', 'seccion', 'avisos', 'resultadoInicial', 'resultadoPosterior', 'materiales', 'sicobaemConfig'));
    }

    public function marcarAviso(Request $request, Aviso $aviso): RedirectResponse
    {
        $proceso = $this->proceso($request);
        abort_unless($this->avisoDirigidoAlProceso($aviso, $proceso), 404);

        $proceso->alumno->avisosLeidos()->syncWithoutDetaching([
            $aviso->id => ['leido' => true, 'fecha_lectura' => now()],
        ]);

        return back()->with('mensaje', 'Aviso marcado como leído.');
    }

    private function proceso(Request $request): ProcesoIngreso
    {
        return ProcesoIngreso::with([
            'alumno',
            'documentos.tipoDocumento',
            'descargasFormato',
            'resultados.examen',
            'resultados.nivelRiesgo',
            'resultados.nivelDesempeno',
            'resultados.areas.area',
            'resultados.areas.nivelRiesgo',
            'grupoPropedeutico',
            'grupoEscolar.turno',
            'grupoEscolar.horarios',
            'regularizacion.ruta',
        ])
            ->findOrFail($request->session()->get('alumno_proceso_id'));
    }

    private function avisoDirigidoAlProceso(Aviso $aviso, ProcesoIngreso $proceso): bool
    {
        if (! $aviso->visible) {
            return false;
        }

        return match ($aviso->dirigido_a) {
            'todos' => true,
            'ciclo' => (int) $aviso->ciclo_ingreso_id === (int) $proceso->ciclo_ingreso_id,
            'alumno' => (int) $aviso->alumno_id === (int) $proceso->alumno_id,
            default => false,
        };
    }
}
