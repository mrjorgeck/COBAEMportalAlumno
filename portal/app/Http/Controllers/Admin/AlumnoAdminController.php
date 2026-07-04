<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EstadoDocumento;
use App\Http\Controllers\Controller;
use App\Models\DocumentoAlumno;
use App\Models\GrupoEscolar;
use App\Models\GrupoPropedeutico;
use App\Models\ProcesoIngreso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AlumnoAdminController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = $request->string('buscar')->toString();
        $procesos = ProcesoIngreso::with(['alumno', 'ciclo'])
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('folio_registro', 'like', "%{$buscar}%")
                    ->orWhere('folio_examen', 'like', "%{$buscar}%")
                    ->orWhereHas('alumno', fn ($q) => $q
                        ->where('curp', 'like', "%{$buscar}%")
                        ->orWhere('nombres', 'like', "%{$buscar}%")
                        ->orWhere('primer_apellido', 'like', "%{$buscar}%"));
            })
            ->when($request->filled('estatus'), fn ($q) => $q->where('estatus_proceso', $request->estatus))
            ->when($request->filled('ciclo'), fn ($q) => $q->where('ciclo_ingreso_id', $request->ciclo))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.alumnos.index', compact('procesos', 'buscar'));
    }

    public function show(ProcesoIngreso $proceso): View
    {
        $proceso->load(['alumno', 'ciclo', 'plantel', 'contacto', 'tutor', 'madre', 'otrosDatos', 'documentos.tipoDocumento', 'grupoEscolar']);
        $gruposPropedeuticos = GrupoPropedeutico::where('ciclo_ingreso_id', $proceso->ciclo_ingreso_id)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();
        $gruposEscolares = GrupoEscolar::where('ciclo_ingreso_id', $proceso->ciclo_ingreso_id)
            ->where('activo', true)
            ->orderBy('grupo')
            ->get();

        return view('admin.alumnos.show', compact('proceso', 'gruposPropedeuticos', 'gruposEscolares'));
    }

    public function update(Request $request, ProcesoIngreso $proceso): RedirectResponse
    {
        abort_if($proceso->edicion_bloqueada, 403, 'La edición está bloqueada.');

        $data = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'primer_apellido' => ['required', 'string', 'max:100'],
            'segundo_apellido' => ['nullable', 'string', 'max:100'],
            'folio_examen' => ['required', 'string', 'max:20'],
            'estatus_proceso' => ['required', 'string', 'max:30'],
        ]);

        $proceso->alumno->update(collect($data)->only(['nombres', 'primer_apellido', 'segundo_apellido'])->all());
        $proceso->update(collect($data)->only(['folio_examen', 'estatus_proceso'])->all());

        return back()->with('mensaje', 'Alumno actualizado.');
    }

    public function matricula(Request $request, ProcesoIngreso $proceso): RedirectResponse
    {
        $data = $request->validate([
            'matricula' => ['nullable', 'string', 'max:20', Rule::unique('procesos_ingreso', 'matricula')->ignore($proceso->id)],
        ]);

        $proceso->update($data);

        return back()->with('mensaje', 'Matricula guardada.');
    }

    public function bloquear(ProcesoIngreso $proceso): RedirectResponse
    {
        $proceso->update(['edicion_bloqueada' => ! $proceso->edicion_bloqueada]);

        return back()->with('mensaje', $proceso->edicion_bloqueada ? 'Edición bloqueada.' : 'Edición desbloqueada.');
    }

    public function actualizarDocumento(Request $request, ProcesoIngreso $proceso, DocumentoAlumno $documento): RedirectResponse
    {
        abort_unless($documento->proceso_ingreso_id === $proceso->id, 404);

        $data = $request->validate([
            'estado_documento' => ['required', 'in:'.collect(EstadoDocumento::cases())->pluck('value')->implode(',')],
            'observacion' => ['nullable', 'string', 'max:1000'],
        ]);

        $documento->update($data + [
            'validado_por' => $request->user()?->id,
            'fecha_validacion' => now(),
            'fecha_recepcion' => $data['estado_documento'] === 'recibido' ? now() : $documento->fecha_recepcion,
        ]);

        $proceso->update(['estatus_documentacion' => $proceso->documentos()->where('estado_documento', 'rechazado')->exists() ? 'rechazado' : 'pendiente']);

        return back()->with('mensaje', 'Documento actualizado.');
    }
}
