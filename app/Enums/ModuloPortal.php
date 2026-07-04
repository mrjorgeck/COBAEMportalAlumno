<?php

namespace App\Enums;

/**
 * Módulos del portal del alumno publicables por ciclo (§27 requerimientos).
 */
enum ModuloPortal: string
{
    case Registro = 'registro';
    case Formato = 'formato';
    case Documentacion = 'documentacion';
    case Avisos = 'avisos';
    case Resultados = 'resultados';
    case AreasMejora = 'areas_mejora';
    case Materiales = 'materiales';
    case Regularizacion = 'regularizacion';
    case Propedeutico = 'propedeutico';
    case EvaluacionPosterior = 'evaluacion_posterior';
    case Avance = 'avance';
    case GrupoEscolar = 'grupo_escolar';
    case Matricula = 'matricula';
    case Horario = 'horario';
    case Sicobaem = 'sicobaem';

    /** Módulos activos desde el inicio del ciclo (no requieren publicación). */
    public function siempreActivo(): bool
    {
        return in_array($this, [
            self::Registro,
            self::Formato,
            self::Documentacion,
            self::Avisos,
        ]);
    }

    public function etiqueta(): string
    {
        return match ($this) {
            self::Registro => 'Registro de datos',
            self::Formato => 'Formato de inscripción',
            self::Documentacion => 'Documentación',
            self::Avisos => 'Avisos',
            self::Resultados => 'Resultados de evaluación diagnóstica',
            self::AreasMejora => 'Áreas de mejora',
            self::Materiales => 'Materiales recomendados',
            self::Regularizacion => 'Regularización autodirigida',
            self::Propedeutico => 'Curso propedéutico',
            self::EvaluacionPosterior => 'Evaluación posterior',
            self::Avance => 'Mi avance',
            self::GrupoEscolar => 'Grupo escolar',
            self::Matricula => 'Matrícula',
            self::Horario => 'Horario de clases',
            self::Sicobaem => 'Acceso SICOBaEM',
        };
    }
}
