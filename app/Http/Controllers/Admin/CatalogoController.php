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
        return view('admin.catalogos.index', ['catalogos' => Catalogo::orderBy('tipo')->orderBy('orden')->paginate(50)]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tipo' => ['required', 'string', 'max:40'],
            'clave' => ['required', 'string', 'max:40'],
            'nombre' => ['required', 'string', 'max:150'],
            'activo' => ['boolean'],
        ]);

        Catalogo::updateOrCreate(
            ['tipo' => $data['tipo'], 'clave' => $data['clave']],
            ['nombre' => $data['nombre'], 'activo' => $data['activo'] ?? true],
        );

        return back()->with('mensaje', 'Catálogo guardado.');
    }
}
