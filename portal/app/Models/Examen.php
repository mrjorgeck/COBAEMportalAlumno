<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class Examen extends Model
{
    use HasFactory, LogsActivity, LogsPortalActivity;

    protected $table = 'examenes';

    protected $fillable = [
        'ciclo_ingreso_id', 'nombre', 'tipo', 'fecha_aplicacion', 'version',
        'total_preguntas', 'plantilla_omr_id', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_aplicacion' => 'date',
            'activo' => 'boolean',
        ];
    }

    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(CicloIngreso::class, 'ciclo_ingreso_id');
    }

    public function plantillaOmr(): BelongsTo
    {
        return $this->belongsTo(PlantillaOmr::class, 'plantilla_omr_id');
    }

    public function claves(): HasMany
    {
        return $this->hasMany(ClaveRespuesta::class);
    }

    public function resultados(): HasMany
    {
        return $this->hasMany(Resultado::class);
    }

    public function hojasRespuesta(): HasMany
    {
        return $this->hasMany(HojaRespuesta::class);
    }
}
