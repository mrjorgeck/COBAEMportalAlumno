<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catalogo;
use App\Models\MaterialRecomendado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaterialRecomendadoController extends Controller
{
    public function index(): View
    {
        return view('admin.materiales.index', [
            'areas' => Catalogo::deTipo('area_evaluacion')->get(),
            'niveles' => Catalogo::deTipo('nivel_desempeno')->get(),
            'materiales' => MaterialRecomendado::with(['area', 'nivelDesempeno'])->latest()->paginate(30),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        MaterialRecomendado::create($this->validated($request));

        return back()->with('mensaje', 'Material guardado.');
    }

    public function update(Request $request, MaterialRecomendado $materiale): RedirectResponse
    {
        $materiale->update($this->validated($request));

        return back()->with('mensaje', 'Material actualizado.');
    }

    public function destroy(MaterialRecomendado $materiale): RedirectResponse
    {
        $materiale->delete();

        return back()->with('mensaje', 'Material eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'area_id' => ['required', 'exists:catalogos,id'],
            'nivel_desempeno_id' => ['nullable', 'exists:catalogos,id'],
            'titulo' => ['required', 'string', 'max:180'],
            'descripcion' => ['nullable', 'string'],
            'url' => ['nullable', 'url', 'max:255'],
            'archivo_path' => ['nullable', 'string', 'max:255'],
            'tipo_material' => ['required', 'in:pdf,video,guia,actividad,sitio,curso_externo,plataforma_regularizacion'],
            'activo' => ['boolean'],
        ]);
    }
}
