<?php

namespace App\Support;

class CsvImportSchemas
{
    public const TIPOS = [
        'alumnos',
        'documentacion',
        'clave_respuestas',
        'resultados_examen',
        'respuestas_examen',
        'grupo_propedeutico',
        'grupo_escolar',
        'matriculas',
        'horarios',
    ];

    public const ENCABEZADOS = [
        'alumnos' => ['curp', 'ciclo', 'nombres', 'primer_apellido', 'segundo_apellido', 'fecha_nacimiento', 'folio_examen', 'promedio_secundaria'],
        'documentacion' => ['ciclo', 'curp', 'documento', 'estado', 'observacion'],
        'clave_respuestas' => ['examen_id', 'pregunta', 'respuesta_correcta', 'area_clave', 'materia_clave', 'competencia', 'ponderacion'],
        'respuestas_examen' => ['examen_id', 'folio_examen', '1', '2', '3'],
        'resultados_examen' => ['examen_id', 'folio_examen', 'puntaje_total', 'porcentaje_total', 'nivel_riesgo_clave', 'nivel_desempeno_clave', 'MAT_puntaje', 'MAT_porcentaje', 'MAT_riesgo'],
        'grupo_propedeutico' => ['ciclo', 'curp', 'folio_examen', 'grupo'],
        'grupo_escolar' => ['ciclo', 'curp', 'folio_examen', 'grupo'],
        'matriculas' => ['ciclo', 'curp', 'folio_examen', 'matricula'],
        'horarios' => ['ciclo', 'grupo', 'dia', 'hora_inicio', 'hora_fin', 'materia', 'docente', 'aula'],
    ];

    public static function encabezados(string $tipo): array
    {
        return self::ENCABEZADOS[$tipo] ?? [];
    }
}
