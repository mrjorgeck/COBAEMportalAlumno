<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protege las secciones del portal del alumno.
 *
 * Niveles (SEG-02, docs/03 §2):
 *  - basico:   requiere CURP verificada en sesión (alumno_proceso_id).
 *  - sensible: requiere además segundo dato validado (fecha de nacimiento
 *              o folio de examen) en la misma sesión.
 *
 * Uso: ->middleware('alumno.sesion')  o  ->middleware('alumno.sesion:sensible')
 */
class VerificarSesionAlumno
{
    public function handle(Request $request, Closure $next, string $nivel = 'basico'): Response
    {
        if (! $request->session()->has('alumno_proceso_id')) {
            return redirect()->route('alumno.landing')
                ->with('mensaje', 'Ingresa tu CURP para continuar.');
        }

        if ($nivel === 'sensible' && ! $request->session()->get('alumno_nivel_sensible', false)) {
            return redirect()->route('alumno.verificacion');
        }

        // Datos personales: nunca cachear (SEG-10).
        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store, max-age=0');

        return $response;
    }
}
