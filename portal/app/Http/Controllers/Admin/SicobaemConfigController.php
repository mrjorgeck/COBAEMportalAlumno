<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CicloIngreso;
use App\Models\SicobaemConfig;
use App\Support\FechaInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SicobaemConfigController extends Controller
{
    public function index(): View
    {
        return view('admin.sicobaem.index', [
            'ciclos' => CicloIngreso::orderByDesc('anio')->get(),
            'configs' => SicobaemConfig::with('ciclo')->get()->keyBy('ciclo_ingreso_id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        FechaInput::normalizeRequest($request, ['fecha_disponibilidad']);

        $data = $request->validate([
            'ciclo_ingreso_id' => ['required', 'exists:ciclos_ingreso,id'],
            'url' => ['nullable', 'url', 'max:255'],
            'fecha_disponibilidad' => ['nullable', 'date'],
            'pasos_activacion' => ['nullable', 'string'],
            'contacto_soporte' => ['nullable', 'string', 'max:180'],
            'mensaje' => ['nullable', 'string'],
            'activo' => ['boolean'],
        ]);

        SicobaemConfig::updateOrCreate(
            ['ciclo_ingreso_id' => $data['ciclo_ingreso_id']],
            $data,
        );

        return back()->with('mensaje', 'Configuracion SICOBaEM guardada.');
    }
}
