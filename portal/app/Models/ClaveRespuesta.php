<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

class ClaveRespuesta extends Model
{
    use HasFactory, LogsActivity, LogsPortalActivity;

    protected $table = 'claves_respuesta';

    protected $fillable = [
        'examen_id', 'pregunta', 'respuesta_correcta', 'area_id',
        'materia_id', 'competencia', 'ponderacion',
    ];

    protected function casts(): array
    {
        return ['ponderacion' => 'decimal:2'];
    }

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'area_id');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'materia_id');
    }
}
