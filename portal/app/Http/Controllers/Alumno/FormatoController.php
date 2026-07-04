<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\DescargaFormato;
use App\Models\ProcesoIngreso;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FormatoController extends Controller
{
    public function alumno(Request $request): Response
    {
        $proceso = ProcesoIngreso::with(['alumno', 'ciclo', 'plantel', 'contacto', 'tutor', 'madre', 'otrosDatos'])
            ->findOrFail($request->session()->get('alumno_proceso_id'));

        return $this->descargar($proceso, 'descargado_alumno', null, $request->ip());
    }

    public function admin(Request $request, ProcesoIngreso $proceso): Response
    {
        $proceso->load(['alumno', 'ciclo', 'plantel', 'contacto', 'tutor', 'madre', 'otrosDatos']);

        return $this->descargar($proceso, 'descargado_admin', $request->user()?->id, $request->ip());
    }

    private function descargar(ProcesoIngreso $proceso, string $tipo, ?int $usuarioId, ?string $ip): Response
    {
        DescargaFormato::create([
            'proceso_ingreso_id' => $proceso->id,
            'tipo' => 'generado',
            'usuario_id' => $usuarioId,
            'ip' => $ip,
            'created_at' => now(),
        ]);
        DescargaFormato::create([
            'proceso_ingreso_id' => $proceso->id,
            'tipo' => $tipo,
            'usuario_id' => $usuarioId,
            'ip' => $ip,
            'created_at' => now(),
        ]);

        return Pdf::loadView('pdf.inscripcion.v2026.formato', compact('proceso'))
            ->download($proceso->folio_registro.'.pdf');
    }
}
