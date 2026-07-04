<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class Alumno extends Model
{
    use HasFactory, LogsActivity, LogsPortalActivity;

    protected $table = 'alumnos';

    protected $fillable = [
        'curp', 'nombres', 'primer_apellido', 'segundo_apellido',
        'fecha_nacimiento', 'sexo_id', 'nacionalidad_id', 'estado_civil_id',
        'entidad_nacimiento_id', 'municipio_nacimiento_id',
    ];

    protected function casts(): array
    {
        return ['fecha_nacimiento' => 'date'];
    }

    public function procesos(): HasMany
    {
        return $this->hasMany(ProcesoIngreso::class);
    }

    public function avisosLeidos(): BelongsToMany
    {
        return $this->belongsToMany(Aviso::class, 'alumno_avisos')->withPivot(['leido', 'fecha_lectura']);
    }

    public function sexo(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'sexo_id');
    }

    public function nacionalidad(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'nacionalidad_id');
    }

    public function estadoCivil(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'estado_civil_id');
    }

    public function entidadNacimiento(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'entidad_nacimiento_id');
    }

    public function municipioNacimiento(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'municipio_nacimiento_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres} {$this->primer_apellido} {$this->segundo_apellido}");
    }

    public function getEdadAttribute(): ?int
    {
        return $this->fecha_nacimiento?->age;
    }
}
