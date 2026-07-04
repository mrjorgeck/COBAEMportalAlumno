<?php

namespace Database\Factories;

use App\Models\Catalogo;
use App\Models\ClaveRespuesta;
use App\Models\Examen;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ClaveRespuesta> */
class ClaveRespuestaFactory extends Factory
{
    protected $model = ClaveRespuesta::class;

    public function definition(): array
    {
        return [
            'examen_id' => Examen::factory(),
            'pregunta' => $this->faker->unique()->numberBetween(1, 100),
            'respuesta_correcta' => $this->faker->randomElement(['A', 'B', 'C', 'D']),
            'area_id' => Catalogo::factory()->create(['tipo' => 'area_evaluacion'])->id,
            'ponderacion' => 1,
        ];
    }
}
