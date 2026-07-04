<?php

namespace Database\Factories;

use App\Models\GrupoEscolar;
use App\Models\Horario;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Horario> */
class HorarioFactory extends Factory
{
    protected $model = Horario::class;

    public function definition(): array
    {
        return [
            'grupo_escolar_id' => GrupoEscolar::factory(),
            'dia' => $this->faker->numberBetween(1, 5),
            'hora_inicio' => '08:00',
            'hora_fin' => '09:00',
            'materia' => $this->faker->randomElement(['Matematicas I', 'Taller de lectura', 'Ingles I']),
            'docente' => 'Docente Sintetico',
            'aula' => 'Aula 1',
        ];
    }
}
