<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ModuloPortal;
use App\Http\Controllers\Controller;
use App\Models\CicloIngreso;
use App\Models\ModuloCiclo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CicloController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'anio' => ['required', 'integer', 'min:2026', 'max:2100', 'unique:ciclos_ingreso,anio'],
            'periodo_escolar' => ['required', 'string', 'max:20'],
            'generacion' => ['required', 'string', 'max:100'],
        ]);

        $ciclo = CicloIngreso::create($data + ['activo' => false]);

        foreach (ModuloPortal::cases() as $modulo) {
            ModuloCiclo::create([
                'ciclo_ingreso_id' => $ciclo->id,
                'modulo' => $modulo->value,
                'visible' => $modulo->siempreActivo(),
                'publicado_desde' => $modulo->siempreActivo() ? now() : null,
                'publicado_por' => $request->user()?->id,
            ]);
        }

        return back()->with('mensaje', 'Ciclo creado con modulos iniciales.');
    }
}
