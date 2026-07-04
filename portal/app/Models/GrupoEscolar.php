<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class GrupoEscolar extends Model
{
    use HasFactory, LogsActivity, LogsPortalActivity;

    protected $table = 'grupos_escolares';

    protected $fillable = [
        'ciclo_ingreso_id', 'grupo', 'semestre', 'turno_id', 'aula_base',
        'fecha_inicio_clases', 'indicaciones', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio_clases' => 'date',
            'activo' => 'boolean',
        ];
    }

    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(CicloIngreso::class, 'ciclo_ingreso_id');
    }

    public function turno(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'turno_id');
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class);
    }

    public function procesos(): HasMany
    {
        return $this->hasMany(ProcesoIngreso::class, 'grupo_escolar_id');
    }
}
