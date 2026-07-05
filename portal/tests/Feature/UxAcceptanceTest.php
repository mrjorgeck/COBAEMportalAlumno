<?php

namespace Tests\Feature;

use App\Models\ProcesoIngreso;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
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
            ->assertSee('Tu avance se guarda en cada paso', false)
            ->assertSee('https://www.gob.mx/curp/', false)
            ->assertSee('Guardando...', false)
            ->assertSee('name="curp"', false)
            ->assertSee('aria-required="true"', false)
            ->assertSee('Folio de examen', false);
    }

    public function test_mensajes_de_validacion_salen_en_espanol_con_atributos_humanos(): void
    {
        $this->post(route('admin.login.store'), [])
            ->assertSessionHasErrors([
                'email' => 'El campo correo electrónico es obligatorio.',
                'password' => 'El campo contraseña es obligatorio.',
            ]);
    }

    public function test_mensaje_de_curp_invalida_es_amable_y_accionable(): void
    {
        $this->post(route('alumno.acceso'), ['curp' => 'AAAAAAAAAAAAAAAAAA'])
            ->assertSessionHasErrors([
                'curp' => 'Revisa tu CURP: debe tener 18 caracteres y coincidir con el formato oficial.',
            ]);
    }

    public function test_paginas_de_error_personalizadas_responden_con_status_y_texto_amable(): void
    {
        config(['app.debug' => false]);

        Route::get('/__ux-error/{code}', fn (string $code) => abort((int) $code))
            ->whereNumber('code');

        $casos = [
            403 => 'No tienes permiso para ver esta sección',
            404 => 'No encontramos esta página',
            419 => 'Tu sesión expiró',
            429 => 'Demasiados intentos',
            500 => 'Algo salió mal de nuestro lado',
            503 => 'Portal en mantenimiento',
        ];

        foreach ($casos as $codigo => $texto) {
            $this->get('/__ux-error/'.$codigo)
                ->assertStatus($codigo)
                ->assertSee($texto)
                ->assertDontSee('Stack trace')
                ->assertDontSee('Exception');
        }
    }

    public function test_pantalla_de_exito_muestra_folio_y_siguiente_paso(): void
    {
        $proceso = ProcesoIngreso::factory()->create([
            'folio_registro' => 'NI-2026-ARIO-1234',
        ]);

        $this->withSession([
            'alumno_proceso_id' => $proceso->id,
            'alumno_ciclo_id' => $proceso->ciclo_ingreso_id,
            'alumno_nivel_sensible' => true,
        ])->get(route('alumno.registro.exito'))
            ->assertOk()
            ->assertSee('NI-2026-ARIO-1234')
            ->assertSee('Descarga e imprime tu formato PDF')
            ->assertSee('Descargar formato PDF');
    }
}
