<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Ciclo/año de ingreso (§22.2). Separa generaciones: nunca mezclar datos
 * entre ciclos (regla crítica de CLAUDE.md).
 */
class CicloIngreso extends Model
{
    use LogsActivity;

    protected $table = 'ciclos_ingreso';

    protected $fillable = [
        'anio', 'periodo_escolar', 'generacion', 'activo',
        'registro_abierto_desde', 'registro_abierto_hasta',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'registro_abierto_desde' => 'datetime',
            'registro_abierto_hasta' => 'datetime',
        ];
    }

    /** Ciclo activo para nuevos registros (solo debe existir uno). */
    public static function vigente(): ?self
    {
        return static::where('activo', true)->orderByDesc('anio')->first();
    }

    /** Ventana de edición del registro (SEG-06, docs/07 §5). */
    public function registroAbierto(): bool
    {
        $ahora = now();

        if ($this->registro_abierto_desde && $ahora->lt($this->registro_abierto_desde)) {
            return false;
        }

        if ($this->registro_abierto_hasta && $ahora->gt($this->registro_abierto_hasta)) {
            return false;
        }

        return $this->activo;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->useLogName('ciclos');
    }
}
