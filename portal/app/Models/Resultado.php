<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class Resultado extends Model
{
    use HasFactory, LogsActivity, LogsPortalActivity;

    protected $table = 'resultados';

    protected $fillable = [
        'proceso_ingreso_id', 'examen_id', 'origen', 'puntaje_total',
        'porcentaje_total', 'nivel_riesgo_id', 'nivel_desempeno_id',
        'fecha_calculo',
    ];

    protected function casts(): array
    {
        return [
            'puntaje_total' => 'decimal:2',
            'porcentaje_total' => 'decimal:2',
            'fecha_calculo' => 'datetime',
        ];
    }

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(ProcesoIngreso::class, 'proceso_ingreso_id');
    }

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class);
    }

    public function nivelRiesgo(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'nivel_riesgo_id');
    }

    public function nivelDesempeno(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'nivel_desempeno_id');
    }

    public function areas(): HasMany
    {
        return $this->hasMany(ResultadoArea::class);
    }
}
