<?php

namespace App\Services;

use App\Models\Catalogo;
use App\Models\Examen;
use App\Models\ProcesoIngreso;
use App\Models\Resultado;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CalculoResultadosService
{
    public function calcularDesdeRespuestas(ProcesoIngreso $proceso, Examen $examen, array $respuestas): Resultado
    {
        $claves = $examen->claves()->with('area')->get();
        $puntajeTotal = 0.0;
        $ponderacionTotal = (float) $claves->sum('ponderacion');
        $areas = [];

        foreach ($claves as $clave) {
            $respuesta = mb_strtoupper(trim((string) ($respuestas[$clave->pregunta] ?? '')));
            $correcta = $respuesta !== '' && $respuesta === mb_strtoupper($clave->respuesta_correcta);
            $ponderacion = (float) $clave->ponderacion;

            $areas[$clave->area_id] ??= ['puntaje' => 0.0, 'total' => 0.0, 'area' => $clave->area];
            $areas[$clave->area_id]['total'] += $ponderacion;

            if ($correcta) {
                $puntajeTotal += $ponderacion;
                $areas[$clave->area_id]['puntaje'] += $ponderacion;
            }
        }

        $porcentajeTotal = $ponderacionTotal > 0 ? round(($puntajeTotal / $ponderacionTotal) * 100, 2) : 0;

        return DB::transaction(function () use ($proceso, $examen, $puntajeTotal, $porcentajeTotal, $areas) {
            $resultado = $this->guardarResultado($proceso, $examen, [
                'origen' => 'calculado',
                'puntaje_total' => $puntajeTotal,
                'porcentaje_total' => $porcentajeTotal,
            ]);

            foreach ($areas as $areaId => $datos) {
                $porcentaje = $datos['total'] > 0 ? round(($datos['puntaje'] / $datos['total']) * 100, 2) : 0;
                $this->guardarArea($resultado, (int) $areaId, $datos['puntaje'], $porcentaje);
            }

            $this->invalidarDashboard();

            return $resultado->load(['areas.area', 'areas.nivelRiesgo', 'nivelRiesgo', 'nivelDesempeno']);
        });
    }

    public function importarResultado(ProcesoIngreso $proceso, Examen $examen, array $data): Resultado
    {
        return DB::transaction(function () use ($proceso, $examen, $data) {
            $resultado = $this->guardarResultado($proceso, $examen, [
                'origen' => 'importado',
                'puntaje_total' => (float) ($data['puntaje_total'] ?? 0),
                'porcentaje_total' => (float) ($data['porcentaje_total'] ?? 0),
            ]);

            $areas = Catalogo::deTipo('area_evaluacion')->get();
            foreach ($areas as $area) {
                $prefijo = $area->clave.'_';
                if (! array_key_exists($prefijo.'porcentaje', $data)) {
                    continue;
                }

                $this->guardarArea(
                    $resultado,
                    $area->id,
                    (float) ($data[$prefijo.'puntaje'] ?? 0),
                    (float) $data[$prefijo.'porcentaje'],
                    $data[$prefijo.'recomendacion'] ?? null,
                    $data[$prefijo.'riesgo'] ?? null,
                );
            }

            $this->invalidarDashboard();

            return $resultado->load(['areas.area', 'areas.nivelRiesgo', 'nivelRiesgo', 'nivelDesempeno']);
        });
    }

    public function nivelRiesgoPara(float $porcentaje): Catalogo
    {
        $riesgo = Catalogo::where('tipo', 'nivel_riesgo')
            ->where('activo', true)
            ->get()
            ->first(fn (Catalogo $catalogo) => $this->porcentajeEnMetadata($porcentaje, $catalogo->metadata ?? []));

        if ($riesgo) {
            return $riesgo;
        }

        $fallback = collect(config('portal.riesgo'))
            ->first(fn (array $rango) => $porcentaje >= $rango['min'] && $porcentaje <= $rango['max']);

        return Catalogo::firstOrCreate(
            ['tipo' => 'nivel_riesgo', 'clave' => 'SIN_RANGO'],
            ['nombre' => 'Sin rango', 'metadata' => $fallback ?? ['min' => 0, 'max' => 100]],
        );
    }

    public function nivelDesempenoPara(float $porcentaje): Catalogo
    {
        $nivel = Catalogo::where('tipo', 'nivel_desempeno')
            ->where('activo', true)
            ->get()
            ->first(fn (Catalogo $catalogo) => $this->porcentajeEnMetadata($porcentaje, $catalogo->metadata ?? []));

        return $nivel ?? Catalogo::deTipo('nivel_desempeno')->orderByDesc('orden')->firstOrFail();
    }

    private function guardarResultado(ProcesoIngreso $proceso, Examen $examen, array $data): Resultado
    {
        $porcentaje = (float) $data['porcentaje_total'];

        return Resultado::updateOrCreate(
            ['proceso_ingreso_id' => $proceso->id, 'examen_id' => $examen->id],
            [
                ...$data,
                'nivel_riesgo_id' => $this->nivelRiesgoPara($porcentaje)->id,
                'nivel_desempeno_id' => $this->nivelDesempenoPara($porcentaje)->id,
                'fecha_calculo' => now(),
            ],
        );
    }

    private function guardarArea(Resultado $resultado, int $areaId, float $puntaje, float $porcentaje, ?string $recomendacion = null, ?string $riesgoClave = null): void
    {
        $riesgo = $riesgoClave
            ? Catalogo::where('tipo', 'nivel_riesgo')->where('clave', $riesgoClave)->first()
            : null;

        $resultado->areas()->updateOrCreate(
            ['area_id' => $areaId],
            [
                'puntaje' => $puntaje,
                'porcentaje' => $porcentaje,
                'nivel_riesgo_id' => ($riesgo ?? $this->nivelRiesgoPara($porcentaje))->id,
                'recomendacion' => $recomendacion ?? $this->recomendacion($porcentaje),
            ],
        );
    }

    private function porcentajeEnMetadata(float $porcentaje, array $metadata): bool
    {
        return array_key_exists('min', $metadata)
            && array_key_exists('max', $metadata)
            && $porcentaje >= (float) $metadata['min']
            && $porcentaje <= (float) $metadata['max'];
    }

    private function recomendacion(float $porcentaje): string
    {
        return match (true) {
            $porcentaje < 40 => 'Requiere apoyo prioritario y practica guiada antes del curso propedeutico.',
            $porcentaje < 60 => 'Reforzar los temas base y resolver ejercicios breves de practica.',
            $porcentaje < 80 => 'Practicar lectura de instrucciones y problemas representativos del area.',
            default => 'Mantener practica ligera y revisar conceptos clave antes del curso.',
        };
    }

    private function invalidarDashboard(): void
    {
        Cache::store('database')->flush();
    }
}
