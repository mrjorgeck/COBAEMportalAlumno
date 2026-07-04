<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ModuloPortal;
use App\Http\Controllers\Controller;
use App\Models\CicloIngreso;
use App\Models\ModuloCiclo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModuloController extends Controller
{
    public function index(): View
    {
        return view('admin.modulos.index', [
            'ciclos' => CicloIngreso::orderByDesc('anio')->get(),
            'modulos' => ModuloPortal::cases(),
            'publicaciones' => ModuloCiclo::all()->keyBy(fn ($m) => $m->ciclo_ingreso_id.'-'.$m->modulo),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ciclo_ingreso_id' => ['required', 'exists:ciclos_ingreso,id'],
            'modulos' => ['array'],
            'modulos.*' => ['string'],
        ]);

        foreach (ModuloPortal::cases() as $modulo) {
            $visible = in_array($modulo->value, $data['modulos'] ?? [], true);
            ModuloCiclo::updateOrCreate(
                ['ciclo_ingreso_id' => $data['ciclo_ingreso_id'], 'modulo' => $modulo->value],
                [
                    'visible' => $visible,
                    'publicado_desde' => $visible ? now() : null,
                    'publicado_por' => $request->user()?->id,
                ],
            );
        }

        return back()->with('mensaje', 'Publicación de módulos actualizada.');
    }
}
