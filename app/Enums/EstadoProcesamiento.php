<?php

namespace App\Enums;

/** Estados de procesamiento OMR de hojas de respuesta (§12.4 requerimientos). */
enum EstadoProcesamiento: string
{
    case Pendiente = 'pendiente';
    case Procesada = 'procesada';
    case RequiereRevision = 'requiere_revision';
    case Validada = 'validada';
    case Exportada = 'exportada';
    case Error = 'error';
}
