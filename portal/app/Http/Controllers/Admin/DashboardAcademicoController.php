<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CicloIngreso;
use App\Models\Examen;
use App\Models\ProcesoIngreso;
use App\Models\Resultado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardAcademicoController extends Controller
{
    public function __invoke(Request $request): View
    {
        $ciclo = CicloIngreso::find($request->integer('ciclo')) ?? CicloIngreso::vigente();
        $examen = Examen::when($ciclo, fn ($q) => $q->where('ciclo_ingreso_id', $ciclo->id))
            ->when($request->filled('examen'), fn ($q) => $q->whereKey($request->integer('examen')))
            ->orderByDesc('fecha_aplicacion')
            ->first();

        $cacheKey = 'dashboard-academico:'.($ciclo?->id ?? 'todos').':'.($examen?->id ?? 'sin-examen');
        $datos = Cache::store('database')->remember($cacheKey, now()->addMinutes(10), function () use ($ciclo, $examen) {
            $registrados = ProcesoIngreso::when($ciclo, fn ($q) => $q->where('ciclo_ingreso_id', $ciclo->id))->count();
            $resultados = Resultado::with(['nivelRiesgo', 'areas.area', 'proceso.grupoPropedeutico'])
                ->when($examen, fn ($q) => $q->where('examen_id', $examen->id))
                ->get();

            $evaluados = $resultados->pluck('proceso_ingreso_id')->unique()->count();
            $riesgoAlto = $resultados->filter(fn ($r) => $r->nivelRiesgo->clave === 'ALTO')->count();
            $riesgoCritico = $resultados->filter(fn ($r) => $r->nivelRiesgo->clave === 'CRITICO')->count();
            $promedioAreas = $resultados->flatMap->areas
                ->groupBy('area.nombre')
                ->map(fn ($areas) => round($areas->avg('porcentaje'), 2));
            $riesgos = $resultados->groupBy('nivelRiesgo.nombre')->map->count();
            $porGrupo = $resultados
                ->filter(fn ($r) => $r->proceso->grupoPropedeutico)
                ->groupBy(fn ($r) => $r->proceso->grupoPropedeutico->nombre)
                ->map(fn ($items) => round($items->avg('porcentaje_total'), 2));

            return [
                'registrados' => $registrados,
                'evaluados' => $evaluados,
                'promedio_general' => round($resultados->avg('porcentaje_total') ?? 0, 2),
                'riesgo_alto' => $riesgoAlto,
                'riesgo_critico' => $riesgoCritico,
                'sin_resultado' => max($registrados - $evaluados, 0),
                'sin_grupo' => ProcesoIngreso::when($ciclo, fn ($q) => $q->where('ciclo_ingreso_id', $ciclo->id))->whereNull('grupo_propedeutico_id')->count(),
                'promedio_areas' => $promedioAreas,
                'riesgos' => $riesgos,
                'por_grupo' => $porGrupo,
            ];
        });

        return view('admin.dashboard-academico', [
            'ciclos' => CicloIngreso::orderByDesc('anio')->get(),
            'examenes' => Examen::when($ciclo, fn ($q) => $q->where('ciclo_ingreso_id', $ciclo->id))->get(),
            'ciclo' => $ciclo,
            'examen' => $examen,
            'datos' => $datos,
        ]);
    }
}
