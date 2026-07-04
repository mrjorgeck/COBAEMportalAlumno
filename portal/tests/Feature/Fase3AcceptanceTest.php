<?php

namespace Tests\Feature;

use App\Jobs\ProcesarImportacionCsv;
use App\Models\Alumno;
use App\Models\CicloIngreso;
use App\Models\GrupoEscolar;
use App\Models\Horario;
use App\Models\ImportacionCsv;
use App\Models\ModuloCiclo;
use App\Models\Plantel;
use App\Models\ProcesoIngreso;
use App\Models\User;
use App\Services\CalculoResultadosService;
use App\Services\CurpValidator;
use App\Services\FolioService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Fase3AcceptanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_criterio_18_sistema_carga_matricula_grupo_escolar_y_horario(): void
    {
        Queue::fake();
        $this->actingAs($this->admin());
        $proceso = $this->crearProceso('XAXX080202HMCXXXA0', 'FE-301');
        $grupo = GrupoEscolar::factory()->create(['ciclo_ingreso_id' => $proceso->ciclo_ingreso_id, 'grupo' => '1-A']);
        Horario::factory()->create([
            'grupo_escolar_id' => $grupo->id,
            'dia' => 1,
            'materia' => 'Matematicas I',
            'hora_inicio' => '08:00',
            'hora_fin' => '09:00',
        ]);

        $this->post(route('admin.alumnos.grupo-escolar', $proceso), ['grupo_escolar_id' => $grupo->id])
            ->assertRedirect();
        $this->post(route('admin.alumnos.matricula', $proceso), ['matricula' => '26000001'])
            ->assertRedirect();
        $this->post(route('admin.importaciones.store'), [
            'tipo_importacion' => 'horarios',
            'archivo' => UploadedFile::fake()->createWithContent('horarios.csv', "ciclo,grupo,dia,hora_inicio,hora_fin,materia,docente,aula\n2026,1-A,2,09:00,10:00,Taller de lectura,Docente Sintetico,Aula 2\n"),
        ])->assertRedirect();

        Queue::assertPushed(ProcesarImportacionCsv::class);
        $this->publicar($proceso, 'grupo_escolar');
        $this->publicar($proceso, 'matricula');
        $this->publicar($proceso, 'horario');

        $session = $this->sessionAlumno($proceso);
        $this->withSession($session)->get(route('alumno.mi-proceso.seccion', 'grupo-escolar'))->assertOk()->assertSee('1-A');
        $this->withSession($session)->get(route('alumno.mi-proceso.seccion', 'matricula'))->assertOk()->assertSee('26000001');
        $this->withSession($session)->get(route('alumno.mi-proceso.seccion', 'horario'))->assertOk()->assertSee('Matematicas I');
    }

    public function test_rf29_matricula_es_unica_globalmente(): void
    {
        $primero = $this->crearProceso('XAXX080202HMCXXXA0', 'FE-311');
        $segundo = $this->crearProceso('XEXX080202HMCXXXA1', 'FE-312');
        $primero->update(['matricula' => '26000002']);

        $this->actingAs($this->admin())
            ->post(route('admin.alumnos.matricula', $segundo), ['matricula' => '26000002'])
            ->assertSessionHasErrors('matricula');
    }

    public function test_rf30_horario_mostrado_corresponde_al_grupo_escolar_del_alumno(): void
    {
        $proceso = $this->crearProceso('XAXX080202HMCXXXA0', 'FE-321');
        $grupoAlumno = GrupoEscolar::factory()->create(['ciclo_ingreso_id' => $proceso->ciclo_ingreso_id, 'grupo' => '1-A']);
        $otroGrupo = GrupoEscolar::factory()->create(['ciclo_ingreso_id' => $proceso->ciclo_ingreso_id, 'grupo' => '1-B']);
        Horario::factory()->create(['grupo_escolar_id' => $grupoAlumno->id, 'materia' => 'Ingles I']);
        Horario::factory()->create(['grupo_escolar_id' => $otroGrupo->id, 'materia' => 'Quimica I']);
        $proceso->update(['grupo_escolar_id' => $grupoAlumno->id]);
        $this->publicar($proceso, 'horario');

        $this->withSession($this->sessionAlumno($proceso))
            ->get(route('alumno.mi-proceso.seccion', 'horario'))
            ->assertOk()
            ->assertSee('Ingles I')
            ->assertDontSee('Quimica I');
    }

    public function test_rf31_sicobaem_es_administrable_por_ciclo(): void
    {
        $proceso = $this->crearProceso('XAXX080202HMCXXXA0', 'FE-331');
        $this->actingAs($this->admin())
            ->post(route('admin.sicobaem.store'), [
                'ciclo_ingreso_id' => $proceso->ciclo_ingreso_id,
                'url' => 'https://sicobaem.example.test',
                'fecha_disponibilidad' => '2026-08-01',
                'mensaje' => 'Usa tu matricula para activar tu acceso.',
                'pasos_activacion' => '1. Ingresa al portal.',
                'contacto_soporte' => 'Control escolar',
                'activo' => '1',
            ])
            ->assertRedirect();
        $this->publicar($proceso, 'sicobaem');

        $this->withSession($this->sessionAlumno($proceso))
            ->get(route('alumno.mi-proceso.seccion', 'sicobaem'))
            ->assertOk()
            ->assertSee('Usa tu matricula')
            ->assertSee('https://sicobaem.example.test');
    }

    public function test_tarea_35_reutilizacion_multiciclo_aisla_datos_historicos(): void
    {
        $admin = $this->admin();
        $proceso2026 = $this->crearProceso('XAXX080202HMCXXXA0', 'FE-351');
        $this->actingAs($admin)
            ->post(route('admin.ciclos.store'), [
                'anio' => 2027,
                'periodo_escolar' => '27-2',
                'generacion' => 'Nuevo ingreso 2027',
            ])
            ->assertRedirect();

        $ciclo2027 = CicloIngreso::where('anio', 2027)->firstOrFail();
        $this->assertTrue(ModuloCiclo::where('ciclo_ingreso_id', $ciclo2027->id)->where('modulo', 'registro')->where('visible', true)->exists());
        $this->assertTrue(ModuloCiclo::where('ciclo_ingreso_id', $ciclo2027->id)->where('modulo', 'horario')->where('visible', false)->exists());

        $proceso2027 = $this->crearProceso('XEXX080202HMCXXXA1', 'FE-352', $ciclo2027);
        $grupo2026 = GrupoEscolar::factory()->create(['ciclo_ingreso_id' => $proceso2026->ciclo_ingreso_id, 'grupo' => '1-A']);
        $grupo2027 = GrupoEscolar::factory()->create(['ciclo_ingreso_id' => $ciclo2027->id, 'grupo' => '1-Z']);
        Horario::factory()->create(['grupo_escolar_id' => $grupo2026->id, 'materia' => 'Historia 2026']);
        Horario::factory()->create(['grupo_escolar_id' => $grupo2027->id, 'materia' => 'Historia 2027']);
        $proceso2026->update(['grupo_escolar_id' => $grupo2026->id, 'matricula' => '26000010']);
        $proceso2027->update(['grupo_escolar_id' => $grupo2027->id, 'matricula' => '27000010']);
        $this->publicar($proceso2026, 'horario');
        $this->publicar($proceso2027, 'horario');

        $this->assertDatabaseHas('folio_secuencias', ['ciclo_ingreso_id' => $proceso2026->ciclo_ingreso_id, 'consecutivo' => 1]);
        $this->assertDatabaseHas('folio_secuencias', ['ciclo_ingreso_id' => $ciclo2027->id, 'consecutivo' => 1]);
        $this->withSession($this->sessionAlumno($proceso2026))
            ->get(route('alumno.mi-proceso.seccion', 'horario'))
            ->assertOk()
            ->assertSee('Historia 2026')
            ->assertDontSee('Historia 2027');
        $this->withSession($this->sessionAlumno($proceso2027))
            ->get(route('alumno.mi-proceso.seccion', 'horario'))
            ->assertOk()
            ->assertSee('Historia 2027')
            ->assertDontSee('Historia 2026');
    }

    public function test_importacion_csv_de_matriculas_reporta_duplicados(): void
    {
        Storage::fake('local');
        $proceso = $this->crearProceso('XAXX080202HMCXXXA0', 'FE-361');
        $otro = $this->crearProceso('XEXX080202HMCXXXA1', 'FE-362');
        $proceso->update(['matricula' => '26000020']);
        Storage::disk('local')->put('importaciones/matriculas.csv', "ciclo,curp,folio_examen,matricula\n2026,{$otro->alumno->curp},FE-362,26000020\n");
        $importacion = ImportacionCsv::create([
            'tipo_importacion' => 'matriculas',
            'archivo_original_path' => 'importaciones/matriculas.csv',
            'estado' => 'pendiente',
        ]);

        (new ProcesarImportacionCsv($importacion->id))->handle(app(CurpValidator::class), app(FolioService::class), app(CalculoResultadosService::class));

        $this->assertDatabaseHas('importaciones_csv', [
            'id' => $importacion->id,
            'registros_error' => 1,
            'estado' => 'error',
        ]);
    }

    private function crearProceso(string $curp, string $folioExamen, ?CicloIngreso $ciclo = null): ProcesoIngreso
    {
        $ciclo ??= CicloIngreso::vigente();
        $alumno = Alumno::factory()->create(['curp' => $curp]);

        return ProcesoIngreso::factory()->create([
            'alumno_id' => $alumno->id,
            'ciclo_ingreso_id' => $ciclo->id,
            'plantel_id' => Plantel::where('activo', true)->first()->id,
            'folio_registro' => app(FolioService::class)->generar($ciclo, Plantel::where('activo', true)->first()),
            'folio_examen' => $folioExamen,
        ]);
    }

    private function publicar(ProcesoIngreso $proceso, string $modulo): void
    {
        ModuloCiclo::updateOrCreate(
            ['ciclo_ingreso_id' => $proceso->ciclo_ingreso_id, 'modulo' => $modulo],
            ['visible' => true, 'publicado_desde' => now()],
        );
    }

    private function sessionAlumno(ProcesoIngreso $proceso): array
    {
        return [
            'alumno_proceso_id' => $proceso->id,
            'alumno_ciclo_id' => $proceso->ciclo_ingreso_id,
            'alumno_nivel_sensible' => true,
        ];
    }

    private function admin(): User
    {
        return User::where('email', 'admin@registrocobaemario.ariocentro.com')->first() ?? User::factory()->create()->assignRole('admin');
    }
}
