<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\Catalogo;
use App\Models\CicloIngreso;
use App\Models\DatosContacto;
use App\Models\DocumentoAlumno;
use App\Models\Familiar;
use App\Models\OtrosDatosAlumno;
use App\Models\Plantel;
use App\Models\ProcesoIngreso;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistroAlumnoService
{
    public function __construct(private readonly FolioService $folioService) {}

    public function registrar(array $data): ProcesoIngreso
    {
        return DB::transaction(function () use ($data) {
            $ciclo = CicloIngreso::vigente();
            $plantel = Plantel::where('activo', true)->firstOrFail();

            // Regla crítica #4 (RF-15 / SEG-06): el cierre de ventana o el
            // bloqueo impiden ESCRIBIR; la consulta permanece disponible.
            if ($ciclo === null || ! $ciclo->registroAbierto()) {
                throw ValidationException::withMessages([
                    'curp' => 'El periodo de registro y edición no está abierto. Puedes seguir consultando tu proceso.',
                ]);
            }

            $edicionBloqueada = ProcesoIngreso::where('ciclo_ingreso_id', $ciclo->id)
                ->where('edicion_bloqueada', true)
                ->whereHas('alumno', fn ($q) => $q->where('curp', mb_strtoupper($data['curp'])))
                ->exists();

            if ($edicionBloqueada) {
                throw ValidationException::withMessages([
                    'curp' => 'La edición de tu registro está bloqueada. Puedes seguir consultando tu proceso; para correcciones acude a control escolar.',
                ]);
            }

            $alumno = Alumno::updateOrCreate(
                ['curp' => mb_strtoupper($data['curp'])],
                Arr::only($data, [
                    'nombres', 'primer_apellido', 'segundo_apellido', 'fecha_nacimiento',
                    'sexo_id', 'nacionalidad_id', 'estado_civil_id', 'entidad_nacimiento_id',
                    'municipio_nacimiento_id',
                ]),
            );

            $secundaria = Catalogo::firstOrCreate(
                ['tipo' => 'secundaria', 'clave' => 'SEC-'.md5($data['secundaria_nombre'])],
                [
                    'nombre' => $data['secundaria_nombre'],
                    'parent_id' => $data['municipio_secundaria_id'],
                    'activo' => true,
                ],
            );

            $proceso = ProcesoIngreso::firstOrNew([
                'alumno_id' => $alumno->id,
                'ciclo_ingreso_id' => $ciclo->id,
            ]);

            if (! $proceso->exists) {
                $proceso->folio_registro = $this->folioService->generar($ciclo, $plantel);
            }

            $proceso->fill([
                'plantel_id' => $plantel->id,
                'folio_examen' => $data['folio_examen'],
                'semestre_solicitado' => $data['semestre_solicitado'] ?? 1,
                'tipo_estudiante_id' => $data['tipo_estudiante_id'],
                'paraescolar_id' => $data['paraescolar_id'] ?? null,
                'secundaria_procedencia_id' => $secundaria->id,
                'entidad_secundaria_id' => $data['entidad_secundaria_id'],
                'municipio_secundaria_id' => $data['municipio_secundaria_id'],
                'tipo_secundaria_id' => $data['tipo_secundaria_id'] ?? null,
                'turno_secundaria_id' => $data['turno_secundaria_id'] ?? null,
                'promedio_secundaria' => $data['promedio_secundaria'],
                'estatus_proceso' => 'registrado',
                'plantilla_pdf_version' => 'v2026',
                'acepto_privacidad_at' => now(),
                'fecha_registro' => now(),
            ])->save();

            DatosContacto::updateOrCreate(
                ['proceso_ingreso_id' => $proceso->id],
                Arr::only($data, [
                    'telefono', 'celular', 'correo', 'municipio_id', 'localidad_id',
                    'colonia', 'domicilio', 'codigo_postal',
                ]),
            );

            Familiar::updateOrCreate(
                ['proceso_ingreso_id' => $proceso->id, 'tipo_familiar' => 'tutor'],
                [
                    'nombres' => $data['tutor_nombres'],
                    'primer_apellido' => $data['tutor_primer_apellido'],
                    'segundo_apellido' => $data['tutor_segundo_apellido'] ?? null,
                    'telefono' => $data['tutor_telefono'] ?? null,
                    'celular' => $data['tutor_celular'],
                    'ocupacion_id' => $data['tutor_ocupacion_id'] ?? null,
                    'estudios_id' => $data['tutor_estudios_id'] ?? null,
                ],
            );

            Familiar::updateOrCreate(
                ['proceso_ingreso_id' => $proceso->id, 'tipo_familiar' => 'madre'],
                [
                    'nombres' => $data['madre_nombres'],
                    'primer_apellido' => $data['madre_primer_apellido'],
                    'segundo_apellido' => $data['madre_segundo_apellido'],
                    'telefono' => $data['madre_telefono'],
                    'celular' => $data['madre_celular'],
                    'ocupacion_id' => $data['madre_ocupacion_id'],
                    'estudios_id' => $data['madre_estudios_id'],
                ],
            );

            OtrosDatosAlumno::updateOrCreate(
                ['proceso_ingreso_id' => $proceso->id],
                Arr::only($data, ['no_seguro_medico', 'beca_id', 'estatura', 'peso', 'tipo_sangre_id']),
            );

            Catalogo::deTipo('tipo_documento')->get()->each(function (Catalogo $tipo) use ($proceso): void {
                DocumentoAlumno::firstOrCreate([
                    'proceso_ingreso_id' => $proceso->id,
                    'tipo_documento_id' => $tipo->id,
                ]);
            });

            return $proceso->fresh(['alumno', 'contacto', 'familiares', 'otrosDatos', 'documentos']);
        });
    }
}
