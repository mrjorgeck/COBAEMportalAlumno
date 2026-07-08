<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catalogo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogoController extends Controller
{
    public function index(): View
    {
        return view('admin.catalogos.index', [
            'catalogos' => Catalogo::with('padre')->orderBy('tipo')->orderBy('orden')->orderBy('nombre')->paginate(50),
            'padres' => Catalogo::orderBy('tipo')->orderBy('orden')->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Catalogo::updateOrCreate(
            ['tipo' => $data['tipo'], 'clave' => $data['clave']],
            [
                'nombre' => $data['nombre'],
                'parent_id' => $data['parent_id'] ?? null,
                'orden' => $data['orden'] ?? 0,
                'activo' => $request->boolean('activo', true),
            ],
        );

        return back()->with('mensaje', 'Catalogo guardado.');
    }

    public function update(Request $request, Catalogo $catalogo): RedirectResponse
    {
        $data = $this->validated($request);
        abort_if((int) ($data['parent_id'] ?? 0) === $catalogo->id, 422, 'Un catalogo no puede depender de si mismo.');

        $catalogo->update([
            'tipo' => $data['tipo'],
            'clave' => $data['clave'],
            'nombre' => $data['nombre'],
            'parent_id' => $data['parent_id'] ?? null,
            'orden' => $data['orden'] ?? 0,
            'activo' => $request->boolean('activo'),
        ]);

        return back()->with('mensaje', 'Catalogo actualizado.');
    }

    public function toggle(Catalogo $catalogo): RedirectResponse
    {
        $catalogo->update(['activo' => ! $catalogo->activo]);

        return back()->with('mensaje', $catalogo->activo ? 'Catalogo activado.' : 'Catalogo inactivado.');
    }

    public function reordenar(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'orden' => ['required', 'array'],
            'orden.*' => ['nullable', 'integer', 'min:0'],
        ]);

        foreach ($data['orden'] as $catalogoId => $orden) {
            Catalogo::whereKey($catalogoId)->update(['orden' => $orden ?? 0]);
        }

        return back()->with('mensaje', 'Orden actualizado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'tipo' => ['required', 'string', 'max:40'],
            'clave' => ['required', 'string', 'max:40'],
            'nombre' => ['required', 'string', 'max:150'],
            'parent_id' => ['nullable', 'exists:catalogos,id'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['boolean'],
        ]);
    }
}
