<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

class DocumentoAlumno extends Model
{
    use LogsActivity, LogsPortalActivity;

    protected $table = 'documentos_alumno';

    protected $fillable = [
        'proceso_ingreso_id', 'tipo_documento_id', 'estado_documento',
        'observacion', 'fecha_recepcion', 'validado_por', 'fecha_validacion',
    ];

    protected function casts(): array
    {
        return ['fecha_recepcion' => 'datetime', 'fecha_validacion' => 'datetime'];
    }

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(ProcesoIngreso::class, 'proceso_ingreso_id');
    }

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'tipo_documento_id');
    }
}
