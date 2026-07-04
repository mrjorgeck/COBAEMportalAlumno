<?php

namespace Database\Factories;

use App\Models\Alumno;
use App\Models\Catalogo;
use App\Models\CicloIngreso;
use App\Models\Plantel;
use App\Models\ProcesoIngreso;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProcesoIngreso> */
class ProcesoIngresoFactory extends Factory
{
    protected $model = ProcesoIngreso::class;

    public function definition(): array
    {
        return [
            'alumno_id' => Alumno::factory(),
            'ciclo_ingreso_id' => CicloIngreso::query()->first()->id,
            'plantel_id' => Plantel::query()->first()->id,
            'folio_registro' => 'NI-2026-ARIO-'.$this->faker->unique()->numerify('####'),
            'folio_examen' => $this->faker->unique()->numerify('FE####'),
            'semestre_solicitado' => 1,
            'tipo_estudiante_id' => Catalogo::deTipo('tipo_estudiante')->first()->id,
            'promedio_secundaria' => 8.5,
            'estatus_proceso' => 'registrado',
            'estatus_documentacion' => 'pendiente',
            'plantilla_pdf_version' => 'v2026',
            'acepto_privacidad_at' => now(),
            'fecha_registro' => now(),
        ];
    }
}
