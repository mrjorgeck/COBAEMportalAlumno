<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesPermisosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccesoAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesPermisosSeeder::class);
    }

    public function test_landing_publica_responde(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_dashboard_requiere_autenticacion(): void
    {
        $this->get('/admin')->assertRedirect();
    }

    public function test_usuario_con_permiso_ve_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('control_escolar');

        $this->actingAs($user)->get('/admin')->assertOk();
    }

    public function test_usuario_sin_permiso_no_ve_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('tecnico'); // técnico no tiene dashboard.registros

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_creacion_de_ciclos_requiere_permiso_de_administrar_usuarios(): void
    {
        $controlEscolar = User::factory()->create();
        $controlEscolar->assignRole('control_escolar');

        $this->actingAs($controlEscolar)
            ->post(route('admin.ciclos.store'), [
                'anio' => 2027,
                'periodo_escolar' => '27-2',
                'generacion' => 'Nuevo ingreso 2027',
            ])
            ->assertForbidden();

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('admin.ciclos.store'), [
                'anio' => 2027,
                'periodo_escolar' => '27-2',
                'generacion' => 'Nuevo ingreso 2027',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('ciclos_ingreso', ['anio' => 2027]);
    }

    public function test_usuario_inactivo_no_puede_iniciar_sesion(): void
    {
        $user = User::factory()->inactivo()->create([
            'password' => 'secreto-seguro-123',
        ]);

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'secreto-seguro-123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
