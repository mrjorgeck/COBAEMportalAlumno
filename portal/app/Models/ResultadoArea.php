<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

class ResultadoArea extends Model
{
    use HasFactory, LogsActivity, LogsPortalActivity;

    protected $table = 'resultados_area';

    protected $fillable = ['resultado_id', 'area_id', 'puntaje', 'porcentaje', 'nivel_riesgo_id', 'recomendacion'];

    protected function casts(): array
    {
        return [
            'puntaje' => 'decimal:2',
            'porcentaje' => 'decimal:2',
        ];
    }

    public function resultado(): BelongsTo
    {
        return $this->belongsTo(Resultado::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'area_id');
    }

    public function nivelRiesgo(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'nivel_riesgo_id');
    }
}
