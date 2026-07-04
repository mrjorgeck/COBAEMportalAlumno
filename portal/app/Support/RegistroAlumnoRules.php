<?php

namespace App\Support;

use App\Models\CicloIngreso;
use Illuminate\Validation\Rule;

class RegistroAlumnoRules
{
    public static function rules(): array
    {
        $catalogo = fn (string $tipo) => ['required', Rule::exists('catalogos', 'id')->where('tipo', $tipo)];
        $catalogoNullable = fn (string $tipo) => ['nullable', Rule::exists('catalogos', 'id')->where('tipo', $tipo)];

        return [
            'curp' => ['required', 'string', 'size:18'],
            'folio_examen' => [
                'required', 'string', 'max:20',
                Rule::unique('procesos_ingreso', 'folio_examen')
                    ->where('ciclo_ingreso_id', CicloIngreso::vigente()?->id),
            ],
            'folio_examen_confirmacion' => ['required', 'string', 'max:20'],
            'semestre_solicitado' => ['required', 'integer', 'min:1', 'max:6'],
            'tipo_estudiante_id' => $catalogo('tipo_estudiante'),
            'paraescolar_id' => $catalogoNullable('paraescolar'),
            'nombres' => ['required', 'string', 'max:100'],
            'primer_apellido' => ['required', 'string', 'max:100'],
            'segundo_apellido' => ['nullable', 'string', 'max:100'],
            'estado_civil_id' => $catalogo('estado_civil'),
            'fecha_nacimiento' => ['required', 'date'],
            'sexo_id' => $catalogo('sexo'),
            'nacionalidad_id' => $catalogo('nacionalidad'),
            'entidad_nacimiento_id' => $catalogo('entidad'),
            'municipio_nacimiento_id' => $catalogo('municipio'),
            'municipio_id' => $catalogo('municipio'),
            'localidad_id' => $catalogo('localidad'),
            'codigo_postal' => ['nullable', 'string', 'max:10'],
            'domicilio' => ['required', 'string', 'max:255'],
            'colonia' => ['nullable', 'string', 'max:120'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'celular' => ['required', 'string', 'max:30'],
            'correo' => ['nullable', 'email', 'max:255'],
            'entidad_secundaria_id' => $catalogo('entidad'),
            'municipio_secundaria_id' => $catalogo('municipio'),
            'secundaria_nombre' => ['required', 'string', 'max:150'],
            'tipo_secundaria_id' => $catalogoNullable('tipo_secundaria'),
            'turno_secundaria_id' => $catalogoNullable('turno'),
            'promedio_secundaria' => ['required', 'numeric', 'min:0', 'max:10'],
            'tutor_nombres' => ['required', 'string', 'max:100'],
            'tutor_primer_apellido' => ['required', 'string', 'max:100'],
            'tutor_segundo_apellido' => ['nullable', 'string', 'max:100'],
            'tutor_telefono' => ['nullable', 'string', 'max:30'],
            'tutor_celular' => ['required', 'string', 'max:30'],
            'tutor_ocupacion_id' => $catalogoNullable('ocupacion'),
            'tutor_estudios_id' => $catalogoNullable('nivel_estudios'),
            'madre_nombres' => ['nullable', 'string', 'max:100'],
            'madre_primer_apellido' => ['nullable', 'string', 'max:100'],
            'madre_segundo_apellido' => ['nullable', 'string', 'max:100'],
            'madre_telefono' => ['nullable', 'string', 'max:30'],
            'madre_celular' => ['nullable', 'string', 'max:30'],
            'madre_ocupacion_id' => $catalogoNullable('ocupacion'),
            'madre_estudios_id' => $catalogoNullable('nivel_estudios'),
            'no_seguro_medico' => ['nullable', 'string', 'max:100'],
            'beca_id' => $catalogoNullable('beca'),
            'estatura' => ['nullable', 'numeric', 'min:0.5', 'max:2.5'],
            'peso' => ['nullable', 'numeric', 'min:1', 'max:250'],
            'tipo_sangre_id' => $catalogoNullable('tipo_sangre'),
            'acepto_privacidad' => ['accepted'],
        ];
    }
}
