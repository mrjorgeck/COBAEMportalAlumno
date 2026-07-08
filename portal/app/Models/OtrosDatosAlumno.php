<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

class OtrosDatosAlumno extends Model
{
    use LogsActivity, LogsPortalActivity;

    protected $table = 'otros_datos_alumno';

    protected $fillable = [
        'proceso_ingreso_id', 'no_seguro_medico', 'beca_id', 'estatura',
        'peso', 'tipo_sangre_id',
    ];

    protected function casts(): array
    {
        return ['estatura' => 'decimal:2', 'peso' => 'decimal:2'];
    }

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(ProcesoIngreso::class, 'proceso_ingreso_id');
    }

    public function beca(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'beca_id');
    }

    public function tipoSangre(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'tipo_sangre_id');
    }
}
