<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

class ClaveRespuesta extends Model
{
    use HasFactory, LogsActivity, LogsPortalActivity;

    protected $table = 'claves_respuesta';

    protected $fillable = [
        'examen_id', 'pregunta', 'respuesta_correcta', 'area_id',
        'materia_id', 'competencia', 'ponderacion',
    ];

    protected function casts(): array
    {
        return ['ponderacion' => 'decimal:2'];
    }

    public function setRespuestaCorrectaAttribute(?string $value): void
    {
        $this->attributes['respuesta_correcta'] = self::normalizarRespuestasCorrectas($value);
    }

    public function respuestasCorrectas(): array
    {
        return self::separarRespuestasCorrectas($this->respuesta_correcta);
    }

    public function esRespuestaCorrecta(?string $respuesta): bool
    {
        $respuesta = self::normalizarRespuesta($respuesta);

        return $respuesta !== '' && in_array($respuesta, $this->respuestasCorrectas(), true);
    }

    public static function normalizarRespuestasCorrectas(?string $value): string
    {
        $respuestas = self::separarRespuestasCorrectas($value);

        return implode(',', $respuestas);
    }

    private static function separarRespuestasCorrectas(?string $value): array
    {
        $value = mb_strtoupper(trim((string) $value));

        if ($value === '') {
            return [];
        }

        preg_match_all('/[A-Z]/', $value, $matches);

        return collect($matches[0] ?? [])
            ->map(fn (string $respuesta) => self::normalizarRespuesta($respuesta))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private static function normalizarRespuesta(?string $value): string
    {
        return mb_substr(mb_strtoupper(trim((string) $value)), 0, 1);
    }

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'area_id');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'materia_id');
    }
}
