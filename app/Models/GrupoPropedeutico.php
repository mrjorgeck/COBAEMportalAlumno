<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class GrupoPropedeutico extends Model
{
    use HasFactory, LogsActivity, LogsPortalActivity;

    protected $table = 'grupos_propedeuticos';

    protected $fillable = [
        'ciclo_ingreso_id', 'nombre', 'aula', 'horario_texto', 'fecha_inicio',
        'fecha_fin', 'responsable', 'indicaciones', 'materiales_requeridos',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'activo' => 'boolean',
        ];
    }

    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(CicloIngreso::class, 'ciclo_ingreso_id');
    }

    public function procesos(): HasMany
    {
        return $this->hasMany(ProcesoIngreso::class, 'grupo_propedeutico_id');
    }
}
