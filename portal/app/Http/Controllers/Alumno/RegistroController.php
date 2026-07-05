<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Catalogo;
use App\Models\ProcesoIngreso;
use App\Services\CurpValidator;
use App\Services\RegistroAlumnoService;
use App\Support\RegistroAlumnoRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistroController extends Controller
{
    public function create(Request $request): View
    {
        return view('alumno.registro', [
            'curp' => $request->session()->get('registro_curp'),
            'catalogos' => $this->catalogos(),
        ]);
    }

    public function store(Request $request, CurpValidator $curpValidator, RegistroAlumnoService $service): RedirectResponse
    {
        $data = $request->validate(RegistroAlumnoRules::rules());
        $data['curp'] = mb_strtoupper($data['curp']);

        if (! $curpValidator->esValida($data['curp'])) {
            return back()->withErrors(['curp' => 'Revisa tu CURP: debe tener 18 caracteres y coincidir con el formato oficial.'])->withInput();
        }

        if ($data['folio_examen'] !== $data['folio_examen_confirmacion']) {
            return back()->withErrors(['folio_examen_confirmacion' => 'Los folios no coinciden. Escríbelo igual que aparece en tu hoja de respuestas.'])->withInput();
        }

        $proceso = $service->registrar($data);
        $request->session()->put([
            'alumno_proceso_id' => $proceso->id,
            'alumno_ciclo_id' => $proceso->ciclo_ingreso_id,
            'alumno_nivel_sensible' => true,
        ]);
        $request->session()->forget('registro_curp');

        return redirect()->route('alumno.registro.exito')->with('mensaje', 'Registro completado. Tu folio interno es '.$proceso->folio_registro.'.');
    }

    public function exito(Request $request): View
    {
        return view('alumno.registro-exito', [
            'proceso' => ProcesoIngreso::query()->find($request->session()->get('alumno_proceso_id')),
        ]);
    }

    private function catalogos(): array
    {
        $tipos = [
            'sexo', 'nacionalidad', 'estado_civil', 'entidad', 'municipio', 'localidad',
            'tipo_estudiante', 'paraescolar', 'tipo_secundaria', 'turno', 'ocupacion',
            'nivel_estudios', 'beca', 'tipo_sangre',
        ];

        return collect($tipos)->mapWithKeys(fn (string $tipo) => [
            $tipo => Catalogo::deTipo($tipo)->get(),
        ])->all();
    }
}
