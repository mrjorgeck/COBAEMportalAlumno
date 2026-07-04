<?php

namespace Database\Factories;

use App\Models\Catalogo;
use App\Models\CicloIngreso;
use App\Models\GrupoEscolar;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<GrupoEscolar> */
class GrupoEscolarFactory extends Factory
{
    protected $model = GrupoEscolar::class;

    public function definition(): array
    {
        return [
            'ciclo_ingreso_id' => CicloIngreso::query()->first()->id,
            'grupo' => $this->faker->unique()->randomElement(['1-A', '1-B', '1-C', '1-D']),
            'semestre' => 1,
            'turno_id' => Catalogo::deTipo('turno')->first()->id,
            'aula_base' => 'Aula '.$this->faker->numberBetween(1, 8),
            'fecha_inicio_clases' => now()->addMonth()->toDateString(),
            'indicaciones' => 'Presentarse con uniforme e identificacion.',
            'activo' => true,
        ];
    }
}
