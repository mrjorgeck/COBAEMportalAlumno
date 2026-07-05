<?php

namespace App\Support;

use App\Models\CicloIngreso;
use Illuminate\Validation\Rule;

class RegistroAlumnoRules
{
    public static function rules(): array
    {
        $catalogo = fn (string $tipo) => ['required', Rule::exists('catalogos', 'id')->where('tipo', $tipo)];
        $telefono = ['nullable', 'regex:/^[0-9+()\-\s]{7,20}$/'];
        $telefonoRequerido = ['required', 'regex:/^[0-9+()\-\s]{7,20}$/'];

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
            'paraescolar_id' => $catalogo('paraescolar'),
            'nombres' => ['required', 'string', 'max:100'],
            'primer_apellido' => ['required', 'string', 'max:100'],
            'segundo_apellido' => ['required', 'string', 'max:100'],
            'estado_civil_id' => $catalogo('estado_civil'),
            'fecha_nacimiento' => ['required', 'date'],
            'sexo_id' => $catalogo('sexo'),
            'nacionalidad_id' => $catalogo('nacionalidad'),
            'entidad_nacimiento_id' => $catalogo('entidad'),
            'municipio_nacimiento_id' => $catalogo('municipio'),
            'municipio_id' => $catalogo('municipio'),
            'localidad_id' => $catalogo('localidad'),
            'codigo_postal' => ['required', 'string', 'max:10'],
            'domicilio' => ['required', 'string', 'max:255'],
            'colonia' => ['required', 'string', 'max:120'],
            'telefono' => $telefono,
            'celular' => $telefonoRequerido,
            'correo' => ['required', 'email:rfc', 'max:255'],
            'entidad_secundaria_id' => $catalogo('entidad'),
            'municipio_secundaria_id' => $catalogo('municipio'),
            'secundaria_nombre' => ['required', 'string', 'max:150'],
            'tipo_secundaria_id' => $catalogo('tipo_secundaria'),
            'turno_secundaria_id' => $catalogo('turno'),
            'promedio_secundaria' => ['required', 'numeric', 'min:0', 'max:10'],
            'tutor_nombres' => ['required', 'string', 'max:100'],
            'tutor_primer_apellido' => ['required', 'string', 'max:100'],
            'tutor_segundo_apellido' => ['required', 'string', 'max:100'],
            'tutor_telefono' => $telefono,
            'tutor_celular' => $telefonoRequerido,
            'tutor_ocupacion_id' => $catalogo('ocupacion'),
            'tutor_estudios_id' => $catalogo('nivel_estudios'),
            'madre_nombres' => ['required', 'string', 'max:100'],
            'madre_primer_apellido' => ['required', 'string', 'max:100'],
            'madre_segundo_apellido' => ['required', 'string', 'max:100'],
            'madre_telefono' => $telefonoRequerido,
            'madre_celular' => $telefonoRequerido,
            'madre_ocupacion_id' => $catalogo('ocupacion'),
            'madre_estudios_id' => $catalogo('nivel_estudios'),
            'no_seguro_medico' => ['nullable', 'string', 'max:100'],
            'beca_id' => $catalogo('beca'),
            'estatura' => ['nullable', 'numeric', 'min:0.5', 'max:2.5'],
            'peso' => ['nullable', 'numeric', 'min:1', 'max:250'],
            'tipo_sangre_id' => $catalogo('tipo_sangre'),
            'acepto_privacidad' => ['accepted'],
        ];
    }
}
