<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class HojaRespuesta extends Model
{
    use HasFactory, LogsActivity, LogsPortalActivity;

    protected $table = 'hojas_respuesta';

    protected $fillable = [
        'examen_id', 'proceso_ingreso_id', 'folio_examen',
        'imagen_original_path', 'imagen_procesada_path', 'estado_procesamiento',
        'confianza_lectura', 'observaciones', 'procesado_por', 'fecha_subida',
    ];

    protected function casts(): array
    {
        return [
            'confianza_lectura' => 'decimal:2',
            'fecha_subida' => 'datetime',
        ];
    }

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class);
    }

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(ProcesoIngreso::class, 'proceso_ingreso_id');
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(Respuesta::class);
    }
}
