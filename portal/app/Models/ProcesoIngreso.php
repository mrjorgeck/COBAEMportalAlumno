<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ProcesoIngreso extends Model
{
    use HasFactory, LogsActivity, LogsPortalActivity, SoftDeletes;

    protected $table = 'procesos_ingreso';

    protected $fillable = [
        'alumno_id', 'ciclo_ingreso_id', 'plantel_id', 'folio_registro',
        'folio_examen', 'semestre_solicitado', 'tipo_estudiante_id', 'paraescolar_id',
        'secundaria_procedencia_id', 'entidad_secundaria_id', 'municipio_secundaria_id',
        'tipo_secundaria_id', 'turno_secundaria_id', 'promedio_secundaria',
        'grupo_propedeutico_id', 'grupo_escolar_id', 'matricula', 'estatus_proceso',
        'estatus_documentacion', 'edicion_bloqueada', 'plantilla_pdf_version',
        'acepto_privacidad_at', 'fecha_registro', 'fecha_validacion',
    ];

    protected function casts(): array
    {
        return [
            'promedio_secundaria' => 'decimal:2',
            'edicion_bloqueada' => 'boolean',
            'acepto_privacidad_at' => 'datetime',
            'fecha_registro' => 'datetime',
            'fecha_validacion' => 'datetime',
        ];
    }

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(CicloIngreso::class, 'ciclo_ingreso_id');
    }

    public function plantel(): BelongsTo
    {
        return $this->belongsTo(Plantel::class);
    }

    public function tipoEstudiante(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'tipo_estudiante_id');
    }

    public function contacto(): HasOne
    {
        return $this->hasOne(DatosContacto::class);
    }

    public function familiares(): HasMany
    {
        return $this->hasMany(Familiar::class);
    }

    public function tutor(): HasOne
    {
        return $this->hasOne(Familiar::class)->where('tipo_familiar', 'tutor');
    }

    public function madre(): HasOne
    {
        return $this->hasOne(Familiar::class)->where('tipo_familiar', 'madre');
    }

    public function otrosDatos(): HasOne
    {
        return $this->hasOne(OtrosDatosAlumno::class);
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoAlumno::class);
    }

    public function descargasFormato(): HasMany
    {
        return $this->hasMany(DescargaFormato::class);
    }

    public function resultados(): HasMany
    {
        return $this->hasMany(Resultado::class);
    }

    public function grupoPropedeutico(): BelongsTo
    {
        return $this->belongsTo(GrupoPropedeutico::class, 'grupo_propedeutico_id');
    }

    public function hojasRespuesta(): HasMany
    {
        return $this->hasMany(HojaRespuesta::class);
    }
}
