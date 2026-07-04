<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\ProcesoIngreso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificacionController extends Controller
{
    public function create(): View
    {
        return view('alumno.verificacion');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fecha_nacimiento' => ['nullable', 'date'],
            'folio_examen' => ['nullable', 'string', 'max:20'],
        ]);

        $proceso = ProcesoIngreso::with('alumno')->findOrFail($request->session()->get('alumno_proceso_id'));
        $fechaOk = ! empty($data['fecha_nacimiento']) && $proceso->alumno->fecha_nacimiento->isSameDay($data['fecha_nacimiento']);
        $folioOk = ! empty($data['folio_examen']) && hash_equals((string) $proceso->folio_examen, (string) $data['folio_examen']);

        if (! $fechaOk && ! $folioOk) {
            return back()->withErrors(['verificacion' => 'El dato de verificación no coincide.']);
        }

        $request->session()->put('alumno_nivel_sensible', true);

        return redirect()->route('alumno.mi-proceso');
    }
}
