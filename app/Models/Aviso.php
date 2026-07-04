<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;

class Aviso extends Model
{
    use LogsActivity, LogsPortalActivity;

    protected $table = 'avisos';

    protected $fillable = [
        'titulo', 'mensaje', 'tipo_aviso_id', 'prioridad', 'fecha_inicio',
        'fecha_fin', 'dirigido_a', 'ciclo_ingreso_id', 'grupo_propedeutico_id',
        'grupo_escolar_id', 'alumno_id', 'url_o_archivo', 'visible', 'created_by',
    ];

    protected function casts(): array
    {
        return ['fecha_inicio' => 'datetime', 'fecha_fin' => 'datetime', 'visible' => 'boolean'];
    }

    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(CicloIngreso::class, 'ciclo_ingreso_id');
    }

    public function alumnosLeidos(): BelongsToMany
    {
        return $this->belongsToMany(Alumno::class, 'alumno_avisos')->withPivot(['leido', 'fecha_lectura']);
    }
}
