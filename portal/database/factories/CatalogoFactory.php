<?php

namespace Database\Factories;

use App\Models\Catalogo;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Catalogo> */
class CatalogoFactory extends Factory
{
    protected $model = Catalogo::class;

    public function definition(): array
    {
        return [
            'tipo' => 'generico',
            'clave' => $this->faker->unique()->bothify('GEN###'),
            'nombre' => $this->faker->words(2, true),
            'orden' => 0,
            'activo' => true,
        ];
    }
}
