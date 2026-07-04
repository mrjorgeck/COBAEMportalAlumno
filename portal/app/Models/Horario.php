<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

class Horario extends Model
{
    use HasFactory, LogsActivity, LogsPortalActivity;

    protected $table = 'horarios';

    protected $fillable = [
        'grupo_escolar_id', 'dia', 'hora_inicio', 'hora_fin', 'materia', 'docente', 'aula',
    ];

    public function grupoEscolar(): BelongsTo
    {
        return $this->belongsTo(GrupoEscolar::class);
    }

    public function diaNombre(): string
    {
        return match ((int) $this->dia) {
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miercoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sabado',
            default => 'Sin dia',
        };
    }
}
