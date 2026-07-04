<?php

namespace App\Enums;

/** Estados de documentación (§11.2 requerimientos). */
enum EstadoDocumento: string
{
    case Pendiente = 'pendiente';
    case Recibido = 'recibido';
    case Validado = 'validado';
    case Rechazado = 'rechazado';
    case RequiereCorreccion = 'requiere_correccion';
    case NoAplica = 'no_aplica';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::Recibido => 'Recibido',
            self::Validado => 'Validado',
            self::Rechazado => 'Rechazado',
            self::RequiereCorreccion => 'Requiere corrección',
            self::NoAplica => 'No aplica',
        };
    }
}
