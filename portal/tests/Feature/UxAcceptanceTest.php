<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UxAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_wizard_muestra_leyenda_obligatorios_y_aria_required(): void
    {
        $this->get(route('alumno.registro'))
            ->assertOk()
            ->assertSee('Los campos marcados con', false)
            ->assertSee('name="curp"', false)
            ->assertSee('aria-required="true"', false)
            ->assertSee('Folio de examen', false);
    }
}
