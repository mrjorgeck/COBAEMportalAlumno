<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CicloIngreso;
use App\Models\GrupoPropedeutico;
use App\Models\ProcesoIngreso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GrupoPropedeuticoController extends Controller
{
    public function index(): View
    {
        return view('admin.grupos-propedeuticos.index', [
            'ciclos' => CicloIngreso::orderByDesc('anio')->get(),
            'grupos' => GrupoPropedeutico::withCount('procesos')->with('ciclo')->latest()->paginate(30),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        GrupoPropedeutico::create($this->validated($request));

        return back()->with('mensaje', 'Grupo propedeutico guardado.');
    }

    public function update(Request $request, GrupoPropedeutico $grupos_propedeutico): RedirectResponse
    {
        $grupos_propedeutico->update($this->validated($request));

        return back()->with('mensaje', 'Grupo propedeutico actualizado.');
    }

    public function destroy(GrupoPropedeutico $grupos_propedeutico): RedirectResponse
    {
        $grupos_propedeutico->delete();

        return back()->with('mensaje', 'Grupo propedeutico eliminado.');
    }

    public function asignar(Request $request, ProcesoIngreso $proceso): RedirectResponse
    {
        $data = $request->validate(['grupo_propedeutico_id' => ['nullable', 'exists:grupos_propedeuticos,id']]);
        $proceso->update(['grupo_propedeutico_id' => $data['grupo_propedeutico_id'] ?? null]);

        return back()->with('mensaje', 'Grupo propedeutico asignado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'ciclo_ingreso_id' => ['required', 'exists:ciclos_ingreso,id'],
            'nombre' => ['required', 'string', 'max:50'],
            'aula' => ['nullable', 'string', 'max:80'],
            'horario_texto' => ['nullable', 'string', 'max:180'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date'],
            'responsable' => ['nullable', 'string', 'max:150'],
            'indicaciones' => ['nullable', 'string'],
            'materiales_requeridos' => ['nullable', 'string'],
            'activo' => ['boolean'],
        ]);
    }
}
