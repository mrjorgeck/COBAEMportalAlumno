<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catalogo;
use App\Models\CicloIngreso;
use App\Models\GrupoEscolar;
use App\Models\Horario;
use App\Models\ProcesoIngreso;
use App\Support\FechaInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GrupoEscolarController extends Controller
{
    public function index(): View
    {
        return view('admin.grupos-escolares.index', [
            'ciclos' => CicloIngreso::orderByDesc('anio')->get(),
            'turnos' => Catalogo::deTipo('turno')->get(),
            'grupos' => GrupoEscolar::with(['ciclo', 'turno', 'horarios'])
                ->withCount('procesos')
                ->orderByDesc('ciclo_ingreso_id')
                ->orderBy('grupo')
                ->paginate(30),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        GrupoEscolar::create($this->validated($request));

        return back()->with('mensaje', 'Grupo escolar guardado.');
    }

    public function update(Request $request, GrupoEscolar $grupos_escolare): RedirectResponse
    {
        $grupos_escolare->update($this->validated($request));

        return back()->with('mensaje', 'Grupo escolar actualizado.');
    }

    public function destroy(GrupoEscolar $grupos_escolare): RedirectResponse
    {
        $grupos_escolare->delete();

        return back()->with('mensaje', 'Grupo escolar eliminado.');
    }

    public function asignar(Request $request, ProcesoIngreso $proceso): RedirectResponse
    {
        $data = $request->validate([
            'grupo_escolar_id' => ['nullable', 'exists:grupos_escolares,id'],
        ]);

        if (filled($data['grupo_escolar_id'] ?? null)) {
            abort_unless(
                GrupoEscolar::whereKey($data['grupo_escolar_id'])
                    ->where('ciclo_ingreso_id', $proceso->ciclo_ingreso_id)
                    ->exists(),
                422,
                'El grupo escolar no pertenece al ciclo del alumno.',
            );
        }

        $proceso->update(['grupo_escolar_id' => $data['grupo_escolar_id'] ?? null]);

        return back()->with('mensaje', 'Grupo escolar asignado.');
    }

    public function guardarHorario(Request $request, GrupoEscolar $grupo): RedirectResponse
    {
        $data = $request->validate([
            'dia' => ['required', 'integer', 'between:1,6'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'materia' => ['required', 'string', 'max:100'],
            'docente' => ['nullable', 'string', 'max:150'],
            'aula' => ['nullable', 'string', 'max:50'],
        ]);

        $grupo->horarios()->create($data);

        return back()->with('mensaje', 'Horario agregado.');
    }

    public function eliminarHorario(Horario $horario): RedirectResponse
    {
        $horario->delete();

        return back()->with('mensaje', 'Horario eliminado.');
    }

    private function validated(Request $request): array
    {
        FechaInput::normalizeRequest($request, ['fecha_inicio_clases']);

        return $request->validate([
            'ciclo_ingreso_id' => ['required', 'exists:ciclos_ingreso,id'],
            'grupo' => ['required', 'string', 'max:50'],
            'semestre' => ['required', 'integer', 'between:1,6'],
            'turno_id' => ['required', 'exists:catalogos,id'],
            'aula_base' => ['nullable', 'string', 'max:80'],
            'fecha_inicio_clases' => ['nullable', 'date'],
            'indicaciones' => ['nullable', 'string'],
            'activo' => ['boolean'],
        ]);
    }
}
