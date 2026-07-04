<?php

namespace Database\Factories;

use App\Models\Alumno;
use App\Models\Catalogo;
use Database\Factories\Concerns\GeneratesSyntheticCurps;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Alumno> */
class AlumnoFactory extends Factory
{
    use GeneratesSyntheticCurps;

    protected $model = Alumno::class;

    public function definition(): array
    {
        $sequence = $this->faker->unique()->numberBetween(1, 9000);
        $entidad = Catalogo::deTipo('entidad')->first() ?? Catalogo::factory()->create(['tipo' => 'entidad']);
        $municipio = Catalogo::where('parent_id', $entidad->id)->where('tipo', 'municipio')->first()
            ?? Catalogo::factory()->create(['tipo' => 'municipio', 'parent_id' => $entidad->id]);

        return [
            'curp' => $this->syntheticCurp($sequence),
            'nombres' => 'Alumno',
            'primer_apellido' => 'Prueba',
            'segundo_apellido' => 'Sintetica',
            'fecha_nacimiento' => '2008-01-01',
            'sexo_id' => Catalogo::deTipo('sexo')->first()->id,
            'nacionalidad_id' => Catalogo::deTipo('nacionalidad')->first()->id,
            'estado_civil_id' => Catalogo::deTipo('estado_civil')->first()->id,
            'entidad_nacimiento_id' => $entidad->id,
            'municipio_nacimiento_id' => $municipio->id,
        ];
    }
}
