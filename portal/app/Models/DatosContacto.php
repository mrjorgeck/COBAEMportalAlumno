<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

class DatosContacto extends Model
{
    use LogsActivity, LogsPortalActivity;

    protected $table = 'datos_contacto';

    protected $fillable = [
        'proceso_ingreso_id', 'telefono', 'celular', 'correo', 'municipio_id',
        'localidad_id', 'colonia', 'domicilio', 'codigo_postal',
    ];

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(ProcesoIngreso::class, 'proceso_ingreso_id');
    }
}
