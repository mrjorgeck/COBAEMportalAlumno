<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Catálogo genérico administrable (ADR-04, CAT-01..08).
 *
 * Tipos: sexo, estado_civil, nacionalidad, entidad, municipio, localidad,
 * secundaria, tipo_secundaria, turno, tipo_sangre, tipo_documento,
 * area_evaluacion, nivel_desempeno, nivel_riesgo, tipo_aviso,
 * tipo_estudiante, paraescolar, ocupacion, nivel_estudios, beca...
 *
 * Dependencias vía parent_id: entidad → municipio → localidad.
 * Nunca borrar físicamente: inactivar (CAT-02, RNF-23).
 */
class Catalogo extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'catalogos';

    protected $fillable = [
        'tipo', 'clave', 'nombre', 'descripcion',
        'parent_id', 'metadata', 'orden', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'activo' => 'boolean',
        ];
    }

    public function padre(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('orden')->orderBy('nombre');
    }

    /** Scope: valores activos de un tipo, ordenados para desplegables. */
    public function scopeDeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo)
            ->where('activo', true)
            ->orderBy('orden')
            ->orderBy('nombre');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['tipo', 'clave', 'nombre', 'activo'])
            ->logOnlyDirty()
            ->useLogName('catalogos');
    }
}
