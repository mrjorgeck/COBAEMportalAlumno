<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportacionCsv extends Model
{
    protected $table = 'importaciones_csv';

    protected $fillable = [
        'tipo_importacion', 'archivo_original_path', 'usuario_id', 'total_filas',
        'registros_creados', 'registros_actualizados', 'registros_sin_cambios',
        'registros_error', 'resumen', 'estado',
    ];

    protected function casts(): array
    {
        return ['resumen' => 'array'];
    }
}
