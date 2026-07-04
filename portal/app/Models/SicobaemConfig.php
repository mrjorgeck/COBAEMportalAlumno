<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

class SicobaemConfig extends Model
{
    use HasFactory, LogsActivity, LogsPortalActivity;

    protected $table = 'sicobaem_config';

    protected $fillable = [
        'ciclo_ingreso_id', 'url', 'fecha_disponibilidad', 'pasos_activacion',
        'contacto_soporte', 'mensaje', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_disponibilidad' => 'date',
            'activo' => 'boolean',
        ];
    }

    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(CicloIngreso::class, 'ciclo_ingreso_id');
    }
}
