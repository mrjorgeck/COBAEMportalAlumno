<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\ProcesoIngreso;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EndurecimientoProduccionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_aviso_de_privacidad_integral_usa_texto_oficial(): void
    {
        $this->get(route('alumno.privacidad'))
            ->assertOk()
            ->assertSee('Aviso de Privacidad Integral del Portal')
            ->assertDontSee('Pendiente: aviso de privacidad institucional');
    }

    public function test_produccion_redirige_a_https_y_agrega_hsts(): void
    {
        app()->detectEnvironment(fn () => 'production');

        $this->get('http://localhost/aviso-de-privacidad')
            ->assertRedirect('https://localhost/aviso-de-privacidad');

        $this->withHeader('X-Forwarded-Proto', 'https')
            ->get('https://localhost/aviso-de-privacidad')
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    }

    public function test_exportacion_alumnos_neutraliza_formulas_csv(): void
    {
        $user = User::factory()->create();
        $user->assignRole('control_escolar');

        $alumno = Alumno::factory()->create([
            'nombres' => '=cmd',
            'primer_apellido' => '+apellido',
            'segundo_apellido' => '@riesgo',
        ]);
        ProcesoIngreso::factory()->create([
            'alumno_id' => $alumno->id,
            'folio_examen' => '-FE001',
        ]);

        $csv = $this->actingAs($user)
            ->get(route('admin.exportaciones.alumnos'))
            ->streamedContent();

        $this->assertStringContainsString("'=cmd +apellido @riesgo", $csv);
        $this->assertStringContainsString("'-FE001", $csv);
    }

    public function test_admin_con_password_inicial_debe_rotarla_en_primer_acceso(): void
    {
        $user = User::factory()->create([
            'email' => 'admin-inicial@example.test',
            'password' => Hash::make('Temporal-segura-123'),
            'debe_cambiar_password' => true,
        ]);
        $user->assignRole('admin');

        $this->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'Temporal-segura-123',
        ])->assertRedirect(route('admin.password.edit'));

        $this->post(route('admin.password.update'), [
            'password_actual' => 'Temporal-segura-123',
            'password' => 'Nueva-segura-456',
            'password_confirmation' => 'Nueva-segura-456',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertFalse($user->fresh()->debe_cambiar_password);
    }
}
