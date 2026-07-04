<?php

namespace Database\Factories;

use App\Models\Catalogo;
use App\Models\Examen;
use App\Models\ProcesoIngreso;
use App\Models\Resultado;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Resultado> */
class ResultadoFactory extends Factory
{
    protected $model = Resultado::class;

    public function definition(): array
    {
        return [
            'proceso_ingreso_id' => ProcesoIngreso::factory(),
            'examen_id' => Examen::factory(),
            'origen' => 'calculado',
            'puntaje_total' => 8,
            'porcentaje_total' => 80,
            'nivel_riesgo_id' => Catalogo::factory()->create(['tipo' => 'nivel_riesgo', 'clave' => 'BAJO'])->id,
            'nivel_desempeno_id' => Catalogo::factory()->create(['tipo' => 'nivel_desempeno', 'clave' => 'ADECUADO'])->id,
            'fecha_calculo' => now(),
        ];
    }
}
