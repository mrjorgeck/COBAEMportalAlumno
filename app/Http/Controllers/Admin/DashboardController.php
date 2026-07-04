<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DescargaFormato;
use App\Models\DocumentoAlumno;
use App\Models\ProcesoIngreso;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'indicadores' => [
                'Total de alumnos registrados' => ProcesoIngreso::count(),
                'Registros completos' => ProcesoIngreso::where('estatus_proceso', 'registrado')->count(),
                'Registros incompletos' => ProcesoIngreso::where('estatus_proceso', 'registro_incompleto')->count(),
                'Alumnos sin folio de examen' => ProcesoIngreso::whereNull('folio_examen')->count(),
                'Formatos generados' => DescargaFormato::where('tipo', 'generado')->count(),
                'Formatos descargados' => DescargaFormato::whereIn('tipo', ['descargado_alumno', 'descargado_admin'])->count(),
                'Documentación pendiente' => DocumentoAlumno::where('estado_documento', 'pendiente')->count(),
                'Documentación validada' => DocumentoAlumno::where('estado_documento', 'validado')->count(),
                'Documentación rechazada' => DocumentoAlumno::where('estado_documento', 'rechazado')->count(),
                'Con grupo propedéutico' => ProcesoIngreso::whereNotNull('grupo_propedeutico_id')->count(),
                'Con grupo escolar' => ProcesoIngreso::whereNotNull('grupo_escolar_id')->count(),
                'Con matrícula' => ProcesoIngreso::whereNotNull('matricula')->count(),
            ],
        ]);
    }
}
