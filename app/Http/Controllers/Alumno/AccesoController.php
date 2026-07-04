<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\CicloIngreso;
use App\Models\ProcesoIngreso;
use App\Services\CurpValidator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccesoController extends Controller
{
    public function store(Request $request, CurpValidator $validator): RedirectResponse|View
    {
        $data = $request->validate(['curp' => ['required', 'string', 'size:18']]);
        $curp = mb_strtoupper($data['curp']);

        if (! $validator->esValida($curp)) {
            return back()->withErrors(['curp' => 'La CURP no tiene un formato válido.'])->withInput();
        }

        $alumno = Alumno::where('curp', $curp)->first();
        if (! $alumno) {
            $ciclo = CicloIngreso::vigente();
            if (! $ciclo?->registroAbierto()) {
                return back()->with('mensaje', 'El registro no está disponible en este momento.');
            }

            session(['registro_curp' => $curp]);

            return redirect()->route('alumno.registro');
        }

        $procesos = $alumno->procesos()->with('ciclo')->latest('ciclo_ingreso_id')->get();
        if ($procesos->count() > 1) {
            return view('alumno.selector-ciclo', compact('procesos'));
        }

        return $this->abrirProceso($request, $procesos->first());
    }

    public function seleccionarCiclo(Request $request): RedirectResponse
    {
        $data = $request->validate(['proceso_id' => ['required', 'integer', 'exists:procesos_ingreso,id']]);

        return $this->abrirProceso($request, ProcesoIngreso::findOrFail($data['proceso_id']));
    }

    private function abrirProceso(Request $request, ProcesoIngreso $proceso): RedirectResponse
    {
        $request->session()->put([
            'alumno_proceso_id' => $proceso->id,
            'alumno_ciclo_id' => $proceso->ciclo_ingreso_id,
            'alumno_nivel_sensible' => false,
        ]);

        return redirect()->route('alumno.verificacion');
    }

    public function salir(Request $request): RedirectResponse
    {
        $request->session()->forget(['alumno_proceso_id', 'alumno_ciclo_id', 'alumno_nivel_sensible']);

        return redirect()->route('alumno.landing')->with('mensaje', 'Sesión cerrada.');
    }
}
