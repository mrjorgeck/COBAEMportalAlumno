<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

class RegularizacionAlumno extends Model
{
    use HasFactory, LogsActivity, LogsPortalActivity;

    protected $table = 'regularizacion_alumno';

    protected $fillable = [
        'proceso_ingreso_id', 'ruta_regularizacion_id', 'plataforma_externa_url',
        'estatus', 'fecha_asignacion', 'fecha_ultima_consulta',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'datetime',
            'fecha_ultima_consulta' => 'datetime',
        ];
    }

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(ProcesoIngreso::class, 'proceso_ingreso_id');
    }

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'ruta_regularizacion_id');
    }
}
