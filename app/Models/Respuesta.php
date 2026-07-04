<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

class Respuesta extends Model
{
    use HasFactory, LogsActivity, LogsPortalActivity;

    protected $table = 'respuestas';

    protected $fillable = [
        'hoja_respuesta_id', 'pregunta', 'respuesta_detectada',
        'respuesta_validada', 'confianza', 'requiere_revision',
        'corregida_manualmente',
    ];

    protected function casts(): array
    {
        return [
            'confianza' => 'decimal:2',
            'requiere_revision' => 'boolean',
            'corregida_manualmente' => 'boolean',
        ];
    }

    public function hojaRespuesta(): BelongsTo
    {
        return $this->belongsTo(HojaRespuesta::class);
    }
}
