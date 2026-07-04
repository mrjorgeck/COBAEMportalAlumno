<?php

namespace App\Enums;

/** Estatus del proceso de ingreso (docs/03 §6). */
enum EstadoProceso: string
{
    case RegistroIncompleto = 'registro_incompleto';
    case Registrado = 'registrado';
    case RequiereCorreccion = 'requiere_correccion';
    case Validado = 'validado';

    public function etiqueta(): string
    {
        return match ($this) {
            self::RegistroIncompleto => 'Registro incompleto',
            self::Registrado => 'Registrado',
            self::RequiereCorreccion => 'Requiere corrección',
            self::Validado => 'Validado',
        };
    }
}
