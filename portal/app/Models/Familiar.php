<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

class Familiar extends Model
{
    use LogsActivity, LogsPortalActivity;

    protected $table = 'familiares';

    protected $fillable = [
        'proceso_ingreso_id', 'tipo_familiar', 'nombres', 'primer_apellido',
        'segundo_apellido', 'telefono', 'celular', 'ocupacion_id', 'estudios_id',
    ];

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(ProcesoIngreso::class, 'proceso_ingreso_id');
    }

    public function ocupacion(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'ocupacion_id');
    }

    public function estudios(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'estudios_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres} {$this->primer_apellido} {$this->segundo_apellido}");
    }
}
