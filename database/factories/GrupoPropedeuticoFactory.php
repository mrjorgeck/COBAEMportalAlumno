<?php

namespace Database\Factories;

use App\Models\CicloIngreso;
use App\Models\GrupoPropedeutico;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<GrupoPropedeutico> */
class GrupoPropedeuticoFactory extends Factory
{
    protected $model = GrupoPropedeutico::class;

    public function definition(): array
    {
        return [
            'ciclo_ingreso_id' => CicloIngreso::factory(),
            'nombre' => 'P-'.$this->faker->unique()->numberBetween(1, 9),
            'aula' => 'Aula '.$this->faker->numberBetween(1, 6),
            'horario_texto' => '8:00 a 10:00',
            'fecha_inicio' => now()->addWeek()->toDateString(),
            'fecha_fin' => now()->addWeeks(3)->toDateString(),
            'responsable' => $this->faker->name(),
            'indicaciones' => 'Presentarse con cuaderno y lapiz.',
            'materiales_requeridos' => 'Cuaderno, lapiz y comprobante de registro.',
            'activo' => true,
        ];
    }
}
