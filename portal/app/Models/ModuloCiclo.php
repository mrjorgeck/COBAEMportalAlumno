<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

class ModuloCiclo extends Model
{
    use LogsActivity, LogsPortalActivity;

    protected $table = 'modulos_ciclo';

    protected $fillable = ['ciclo_ingreso_id', 'modulo', 'visible', 'publicado_desde', 'publicado_por'];

    protected function casts(): array
    {
        return ['visible' => 'boolean', 'publicado_desde' => 'datetime'];
    }

    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(CicloIngreso::class, 'ciclo_ingreso_id');
    }
}
