<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aviso;
use App\Models\Catalogo;
use App\Models\CicloIngreso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvisoController extends Controller
{
    public function index(): View
    {
        return view('admin.avisos.index', ['avisos' => Aviso::latest()->paginate(20)]);
    }

    public function create(): View
    {
        return view('admin.avisos.form', $this->formData(new Aviso));
    }

    public function store(Request $request): RedirectResponse
    {
        Aviso::create($this->validated($request) + ['created_by' => $request->user()?->id]);

        return redirect()->route('admin.avisos.index')->with('mensaje', 'Aviso publicado.');
    }

    public function edit(Aviso $aviso): View
    {
        return view('admin.avisos.form', $this->formData($aviso));
    }

    public function update(Request $request, Aviso $aviso): RedirectResponse
    {
        $aviso->update($this->validated($request));

        return redirect()->route('admin.avisos.index')->with('mensaje', 'Aviso actualizado.');
    }

    public function destroy(Aviso $aviso): RedirectResponse
    {
        $aviso->delete();

        return back()->with('mensaje', 'Aviso eliminado.');
    }

    private function formData(Aviso $aviso): array
    {
        return [
            'aviso' => $aviso,
            'tipos' => Catalogo::deTipo('tipo_aviso')->get(),
            'ciclos' => CicloIngreso::orderByDesc('anio')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'mensaje' => ['required', 'string'],
            'tipo_aviso_id' => ['required', 'exists:catalogos,id'],
            'prioridad' => ['required', 'in:informativo,importante,urgente'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date'],
            'dirigido_a' => ['required', 'in:todos,ciclo,grupo_propedeutico,grupo_escolar,alumno'],
            'ciclo_ingreso_id' => ['nullable', 'exists:ciclos_ingreso,id'],
            'url_o_archivo' => ['nullable', 'string', 'max:255'],
            'visible' => ['boolean'],
        ]);
    }
}
