<?php

namespace App\Http\Middleware;

use App\Enums\ModuloPortal;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controla la visibilidad de secciones del alumno por ciclo (§27 requerimientos).
 *
 * Uso: ->middleware('modulo.publicado:resultados')
 *
 * Requiere que la sesión del alumno tenga ciclo_ingreso_id (lo fija el acceso).
 * Si el módulo no está publicado, muestra "aún no disponible" (RNF-14),
 * nunca un error.
 */
class ModuloPublicado
{
    public function handle(Request $request, Closure $next, string $modulo): Response
    {
        if ($modulo === 'seccion') {
            $modulo = str_replace('-', '_', (string) $request->route('seccion'));
        }

        $modulo = match ($modulo) {
            'datos' => ModuloPortal::Registro->value,
            'areas_mejora' => ModuloPortal::AreasMejora->value,
            'grupo_escolar' => ModuloPortal::GrupoEscolar->value,
            default => $modulo,
        };

        $moduloEnum = ModuloPortal::tryFrom($modulo);

        // Módulos siempre activos (registro, formato, documentación, avisos)
        if ($moduloEnum !== null && $moduloEnum->siempreActivo()) {
            return $next($request);
        }

        $cicloId = $request->session()->get('alumno_ciclo_id');

        $publicado = DB::table('modulos_ciclo')
            ->where('ciclo_ingreso_id', $cicloId)
            ->where('modulo', $modulo)
            ->where('visible', true)
            ->exists();

        if (! $publicado) {
            return response()->view('alumno.no-disponible', [
                'modulo' => $moduloEnum?->etiqueta() ?? $modulo,
            ]);
        }

        return $next($request);
    }
}
