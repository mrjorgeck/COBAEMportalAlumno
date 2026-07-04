<?php

namespace Database\Factories;

use App\Models\CicloIngreso;
use App\Models\Examen;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Examen> */
class ExamenFactory extends Factory
{
    protected $model = Examen::class;

    public function definition(): array
    {
        return [
            'ciclo_ingreso_id' => CicloIngreso::factory(),
            'nombre' => 'Evaluacion diagnostica '.$this->faker->year(),
            'tipo' => 'diagnostico_inicial',
            'fecha_aplicacion' => now()->toDateString(),
            'version' => '2026',
            'total_preguntas' => 10,
            'activo' => true,
        ];
    }
}
