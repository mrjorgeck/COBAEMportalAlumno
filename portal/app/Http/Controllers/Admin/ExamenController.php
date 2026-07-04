<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CicloIngreso;
use App\Models\Examen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamenController extends Controller
{
    public function index(): View
    {
        return view('admin.examenes.index', [
            'ciclos' => CicloIngreso::orderByDesc('anio')->get(),
            'examenes' => Examen::with('ciclo')->latest()->paginate(20),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Examen::create($data);

        return back()->with('mensaje', 'Examen guardado.');
    }

    public function update(Request $request, Examen $examene): RedirectResponse
    {
        $examene->update($this->validated($request));

        return back()->with('mensaje', 'Examen actualizado.');
    }

    public function destroy(Examen $examene): RedirectResponse
    {
        $examene->delete();

        return back()->with('mensaje', 'Examen eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'ciclo_ingreso_id' => ['required', 'exists:ciclos_ingreso,id'],
            'nombre' => ['required', 'string', 'max:150'],
            'tipo' => ['required', 'in:diagnostico_inicial,evaluacion_posterior'],
            'fecha_aplicacion' => ['nullable', 'date'],
            'version' => ['nullable', 'string', 'max:30'],
            'total_preguntas' => ['required', 'integer', 'min:1', 'max:500'],
            'activo' => ['boolean'],
        ]);
    }
}
