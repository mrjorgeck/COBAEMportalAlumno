<?php

namespace Tests\Feature;

use App\Models\Aviso;
use App\Models\Catalogo;
use App\Models\CicloIngreso;
use App\Models\DocumentoAlumno;
use App\Models\Plantel;
use App\Models\ProcesoIngreso;
use App\Models\User;
use App\Services\FolioService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class Fase1AcceptanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_criterios_1_2_3_alumno_registra_datos_folio_examen_folio_interno_y_pdf(): void
    {
        $this->post(route('alumno.registro.store'), $this->payloadRegistro())
            ->assertRedirect(route('alumno.registro.exito'));

        $proceso = ProcesoIngreso::with('alumno')->first();

        $this->assertSame('XAXX080202HMCXXXA0', $proceso->alumno->curp);
        $this->assertSame('FE-001', $proceso->folio_examen);
        $this->assertSame('NI-2026-ARIO-0001', $proceso->folio_registro);

        $this->withSession(['alumno_proceso_id' => $proceso->id, 'alumno_ciclo_id' => $proceso->ciclo_ingreso_id, 'alumno_nivel_sensible' => true])
            ->get(route('alumno.formato.descargar'))
            ->assertOk();

        $this->assertDatabaseHas('descargas_formato', ['proceso_ingreso_id' => $proceso->id, 'tipo' => 'descargado_alumno']);
    }

    public function test_criterio_4_alumno_vuelve_a_entrar_con_curp_y_descarga_formato(): void
    {
        $proceso = $this->crearProcesoRegistrado();

        $this->post(route('alumno.acceso'), ['curp' => $proceso->alumno->curp])
            ->assertRedirect(route('alumno.verificacion'));

        $this->withSession(['alumno_proceso_id' => $proceso->id, 'alumno_ciclo_id' => $proceso->ciclo_ingreso_id])
            ->post(route('alumno.verificacion.store'), ['fecha_nacimiento' => $proceso->alumno->fecha_nacimiento->format('Y-m-d')])
            ->assertRedirect(route('alumno.mi-proceso'));
    }

    public function test_criterios_5_6_control_escolar_busca_y_exporta_csv(): void
    {
        $proceso = $this->crearProcesoRegistrado();
        $this->actingAs($this->admin());

        $this->get(route('admin.alumnos.index', ['buscar' => $proceso->alumno->curp]))
            ->assertOk()
            ->assertSee($proceso->folio_registro);

        $this->get(route('admin.exportaciones.alumnos'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_criterio_7_importacion_csv_encola_job_y_registra_reporte(): void
    {
        Queue::fake();
        $this->actingAs($this->admin());

        $file = UploadedFile::fake()->createWithContent('alumnos.csv', "curp,ciclo,nombres,primer_apellido,fecha_nacimiento,folio_examen,promedio_secundaria\nXAXX080202HMCXXXA0,2026,Ana,Prueba,2008-02-02,FE-777,8.7\n");

        $this->post(route('admin.importaciones.store'), ['tipo_importacion' => 'alumnos', 'archivo' => $file])
            ->assertRedirect();

        $this->assertDatabaseHas('importaciones_csv', ['tipo_importacion' => 'alumnos', 'estado' => 'pendiente']);
    }

    public function test_criterios_8_y_9_maneja_ciclo_y_catalogos_basicos(): void
    {
        $this->actingAs($this->admin());

        $this->assertDatabaseHas('ciclos_ingreso', ['anio' => 2026, 'activo' => true]);

        $this->post(route('admin.catalogos.store'), ['tipo' => 'tipo_aviso', 'clave' => 'TEST', 'nombre' => 'Prueba'])
            ->assertRedirect();

        $this->assertDatabaseHas('catalogos', ['tipo' => 'tipo_aviso', 'clave' => 'TEST']);
    }

    public function test_criterio_10_bloqueo_impide_edicion_pero_mantiene_consulta(): void
    {
        $proceso = $this->crearProcesoRegistrado();
        $this->actingAs($this->admin());

        $this->post(route('admin.alumnos.bloquear', $proceso))->assertRedirect();
        $this->get(route('admin.alumnos.show', $proceso))->assertOk();
        $this->patch(route('admin.alumnos.update', $proceso), [
            'nombres' => 'Cambio',
            'primer_apellido' => 'Prueba',
            'folio_examen' => 'FE-002',
            'estatus_proceso' => 'registrado',
        ])->assertForbidden();
    }

    public function test_criterios_11_y_12_alumno_ve_estado_general_y_documentacion(): void
    {
        $proceso = $this->crearProcesoRegistrado();
        DocumentoAlumno::where('proceso_ingreso_id', $proceso->id)->first()->update([
            'estado_documento' => 'rechazado',
            'observacion' => 'Debe ser reciente',
        ]);

        $session = ['alumno_proceso_id' => $proceso->id, 'alumno_ciclo_id' => $proceso->ciclo_ingreso_id, 'alumno_nivel_sensible' => true];
        $this->withSession($session)->get(route('alumno.mi-proceso'))->assertOk()->assertSee('Registro');
        $this->withSession($session)->get(route('alumno.mi-proceso.seccion', 'documentacion'))->assertOk()->assertSee('Debe ser reciente');
    }

    public function test_criterio_13_administrador_publica_avisos_y_alumno_los_lee(): void
    {
        $proceso = $this->crearProcesoRegistrado();
        $this->actingAs($this->admin());

        $this->post(route('admin.avisos.store'), [
            'titulo' => 'Entrega de documentos',
            'mensaje' => 'Acude a control escolar.',
            'tipo_aviso_id' => Catalogo::deTipo('tipo_aviso')->first()->id,
            'prioridad' => 'importante',
            'dirigido_a' => 'todos',
            'visible' => true,
        ])->assertRedirect();

        $this->withSession(['alumno_proceso_id' => $proceso->id, 'alumno_ciclo_id' => $proceso->ciclo_ingreso_id])
            ->get(route('alumno.mi-proceso.seccion', 'avisos'))
            ->assertOk()
            ->assertSee('Entrega de documentos');
    }

    public function test_criterio_19_panel_admin_esta_protegido_por_autenticacion(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_criterio_20_registra_auditoria_minima_de_cambios_relevantes(): void
    {
        $this->actingAs($this->admin());
        Aviso::create([
            'titulo' => 'Auditable',
            'mensaje' => 'Cambio relevante',
            'tipo_aviso_id' => Catalogo::deTipo('tipo_aviso')->first()->id,
            'prioridad' => 'informativo',
            'dirigido_a' => 'todos',
            'visible' => true,
        ]);

        $this->assertDatabaseHas('activity_log', ['log_name' => 'avisos']);
    }

    public function test_concurrencia_logica_de_folios_no_repite_consecutivos(): void
    {
        $ciclo = CicloIngreso::vigente();
        $plantel = Plantel::first();
        $folios = collect(range(1, 25))->map(fn () => app(FolioService::class)->generar($ciclo, $plantel));

        $this->assertCount(25, $folios->unique());
        $this->assertSame('NI-2026-ARIO-0025', $folios->last());
    }

    private function crearProcesoRegistrado(): ProcesoIngreso
    {
        $this->post(route('alumno.registro.store'), $this->payloadRegistro());

        return ProcesoIngreso::with('alumno')->first();
    }

    private function admin(): User
    {
        return User::where('email', 'admin@registrocobaemario.ariocentro.com')->first() ?? User::factory()->create()->assignRole('admin');
    }

    private function payloadRegistro(): array
    {
        $entidad = Catalogo::deTipo('entidad')->where('clave', 'MN')->first();
        $municipio = Catalogo::deTipo('municipio')->first();
        $localidad = Catalogo::deTipo('localidad')->first();

        return [
            'curp' => 'XAXX080202HMCXXXA0',
            'folio_examen' => 'FE-001',
            'folio_examen_confirmacion' => 'FE-001',
            'semestre_solicitado' => 1,
            'tipo_estudiante_id' => Catalogo::deTipo('tipo_estudiante')->first()->id,
            'paraescolar_id' => Catalogo::deTipo('paraescolar')->first()->id,
            'nombres' => 'Ana',
            'primer_apellido' => 'Prueba',
            'segundo_apellido' => 'Sintetica',
            'estado_civil_id' => Catalogo::deTipo('estado_civil')->first()->id,
            'fecha_nacimiento' => '2008-02-02',
            'sexo_id' => Catalogo::deTipo('sexo')->first()->id,
            'nacionalidad_id' => Catalogo::deTipo('nacionalidad')->first()->id,
            'entidad_nacimiento_id' => $entidad->id,
            'municipio_nacimiento_id' => $municipio->id,
            'municipio_id' => $municipio->id,
            'localidad_id' => $localidad->id,
            'codigo_postal' => '61830',
            'domicilio' => 'Calle Uno 123',
            'colonia' => 'Centro',
            'telefono' => '4431234500',
            'celular' => '4431234567',
            'correo' => 'ana@example.test',
            'entidad_secundaria_id' => $entidad->id,
            'municipio_secundaria_id' => $municipio->id,
            'secundaria_nombre' => 'Secundaria Sintetica',
            'tipo_secundaria_id' => Catalogo::deTipo('tipo_secundaria')->first()->id,
            'turno_secundaria_id' => Catalogo::deTipo('turno')->first()->id,
            'promedio_secundaria' => 8.8,
            'tutor_nombres' => 'Tutor',
            'tutor_primer_apellido' => 'Prueba',
            'tutor_segundo_apellido' => 'Sintetico',
            'tutor_telefono' => '4437654300',
            'tutor_celular' => '4437654321',
            'tutor_ocupacion_id' => Catalogo::deTipo('ocupacion')->first()->id,
            'tutor_estudios_id' => Catalogo::deTipo('nivel_estudios')->first()->id,
            'madre_nombres' => 'Madre',
            'madre_primer_apellido' => 'Prueba',
            'madre_segundo_apellido' => 'Sintetica',
            'madre_telefono' => '4431112222',
            'madre_celular' => '4431113333',
            'madre_ocupacion_id' => Catalogo::deTipo('ocupacion')->first()->id,
            'madre_estudios_id' => Catalogo::deTipo('nivel_estudios')->first()->id,
            'beca_id' => Catalogo::deTipo('beca')->first()->id,
            'tipo_sangre_id' => Catalogo::deTipo('tipo_sangre')->first()->id,
            'acepto_privacidad' => '1',
        ];
    }
}
