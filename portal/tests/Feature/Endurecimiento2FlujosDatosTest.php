<?php

namespace Tests\Feature;

use App\Jobs\ProcesarImportacionCsv;
use App\Livewire\RegistroWizard;
use App\Models\Alumno;
use App\Models\Aviso;
use App\Models\Catalogo;
use App\Models\ImportacionCsv;
use App\Models\ProcesoIngreso;
use App\Models\User;
use App\Services\CalculoResultadosService;
use App\Services\CurpValidator;
use App\Services\FolioService;
use App\Support\RegistroAlumnoRules;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Livewire;
use Tests\TestCase;

class Endurecimiento2FlujosDatosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_importacion_alumnos_rechaza_encabezado_invalido(): void
    {
        Storage::disk('local')->put('importaciones/alumnos-header-mal.csv', "curp,ciclo,nombres\nXAXX080202HMCXXXA0,2026,Ana\n");
        $importacion = ImportacionCsv::create([
            'tipo_importacion' => 'alumnos',
            'archivo_original_path' => 'importaciones/alumnos-header-mal.csv',
            'estado' => 'pendiente',
        ]);

        $this->procesar($importacion);

        $importacion->refresh();
        $this->assertSame('error', $importacion->estado);
        $this->assertSame(0, $importacion->total_filas);
        $this->assertSame('Encabezados invalidos para alumnos', $importacion->resumen[0]['error']);
        $this->assertSame('error', $importacion->resumen[0]['categoria']);
        $this->assertDatabaseMissing('alumnos', ['curp' => 'XAXX080202HMCXXXA0']);
    }

    public function test_importacion_alumnos_reporta_fila_incompleta_sin_placeholders(): void
    {
        Storage::disk('local')->put(
            'importaciones/alumnos-incompleto.csv',
            "curp,ciclo,nombres,primer_apellido,segundo_apellido,fecha_nacimiento,folio_examen,promedio_secundaria\n".
            "XAXX080202HMCXXXA0,2026,Ana,,,,FE-777,8.7\n"
        );
        $importacion = ImportacionCsv::create([
            'tipo_importacion' => 'alumnos',
            'archivo_original_path' => 'importaciones/alumnos-incompleto.csv',
            'estado' => 'pendiente',
        ]);

        $this->procesar($importacion);

        $importacion->refresh();
        $this->assertSame('error', $importacion->estado);
        $this->assertSame(1, $importacion->total_filas);
        $this->assertSame(1, $importacion->registros_error);
        $this->assertSame('error', $importacion->resumen[0]['categoria']);
        $this->assertSame('Fila de alumno incompleta', $importacion->resumen[0]['error']);
        $this->assertDatabaseMissing('alumnos', ['curp' => 'XAXX080202HMCXXXA0']);
        $this->assertFalse(Alumno::where('nombres', 'Alumno')->where('primer_apellido', 'Importado')->exists());
    }

    public function test_importacion_alumnos_reporta_folio_examen_duplicado_en_ciclo(): void
    {
        ProcesoIngreso::factory()->create(['folio_examen' => 'FE-DUP']);
        Storage::disk('local')->put(
            'importaciones/alumnos-folio-duplicado.csv',
            "curp,ciclo,nombres,primer_apellido,segundo_apellido,fecha_nacimiento,folio_examen,promedio_secundaria\n".
            "XAXX080202HMCXXXA0,2026,Ana,Prueba,Sintetica,2008-02-02,FE-DUP,8.7\n"
        );
        $importacion = ImportacionCsv::create([
            'tipo_importacion' => 'alumnos',
            'archivo_original_path' => 'importaciones/alumnos-folio-duplicado.csv',
            'estado' => 'pendiente',
        ]);

        $this->procesar($importacion);

        $importacion->refresh();
        $this->assertSame('error', $importacion->estado);
        $this->assertSame('Folio de examen duplicado en el ciclo', $importacion->resumen[0]['error']);
        $this->assertSame(1, ProcesoIngreso::where('folio_examen', 'FE-DUP')->count());
        $this->assertDatabaseMissing('alumnos', ['curp' => 'XAXX080202HMCXXXA0']);
    }

    public function test_catalogos_permiten_dependencias_y_cambio_de_estado_sin_borrar_historico(): void
    {
        $this->actingAs($this->admin());
        $entidad = Catalogo::deTipo('entidad')->where('clave', 'MN')->first();
        $municipio = Catalogo::deTipo('municipio')->first();

        $this->post(route('admin.catalogos.store'), [
            'tipo' => 'localidad',
            'clave' => 'LOCAL-H2',
            'nombre' => 'Localidad Historica',
            'parent_id' => $municipio->id,
            'orden' => 77,
            'activo' => '1',
        ])->assertRedirect();

        $localidad = Catalogo::where('tipo', 'localidad')->where('clave', 'LOCAL-H2')->firstOrFail();
        $this->assertSame($municipio->id, $localidad->parent_id);

        $this->patch(route('admin.catalogos.update', $municipio), [
            'tipo' => 'municipio',
            'clave' => $municipio->clave,
            'nombre' => $municipio->nombre,
            'parent_id' => $entidad->id,
            'orden' => 10,
            'activo' => '1',
        ])->assertRedirect();

        $this->patch(route('admin.catalogos.toggle', $localidad))->assertRedirect();

        $localidad->refresh();
        $this->assertFalse($localidad->activo);
        $this->assertSame($municipio->id, $localidad->parent_id);
        $this->assertTrue(Catalogo::whereKey($localidad->id)->exists());
        $this->assertFalse(Catalogo::deTipo('localidad')->whereKey($localidad->id)->exists());
    }

    public function test_paraescolar_es_catalogo_configurable_y_preserva_historico_inactivo(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('admin.catalogos.store'), [
            'tipo' => 'paraescolar',
            'clave' => 'DANZA',
            'nombre' => 'Danza regional',
            'orden' => 1,
            'activo' => '1',
        ])->assertRedirect();

        $paraescolar = Catalogo::where('tipo', 'paraescolar')->where('clave', 'DANZA')->firstOrFail();
        $this->assertTrue(Catalogo::deTipo('paraescolar')->whereKey($paraescolar->id)->exists());
        Livewire::test(RegistroWizard::class)
            ->set('step', 2)
            ->assertSee('Danza regional');

        $proceso = ProcesoIngreso::factory()->create(['paraescolar_id' => $paraescolar->id]);

        $this->patch(route('admin.catalogos.toggle', $paraescolar))->assertRedirect();

        $paraescolar->refresh();
        $this->assertFalse($paraescolar->activo);
        $this->assertFalse(Catalogo::deTipo('paraescolar')->whereKey($paraescolar->id)->exists());
        Livewire::test(RegistroWizard::class)
            ->set('step', 2)
            ->assertDontSee('Danza regional');
        $this->assertSame('Danza regional', $proceso->fresh('paraescolar')->paraescolar->nombre);
        $this->assertTrue(Validator::make(
            ['paraescolar_id' => $paraescolar->id],
            ['paraescolar_id' => RegistroAlumnoRules::rules()['paraescolar_id']],
        )->fails());
    }

    public function test_paraescolar_inicial_incluye_opciones_por_categoria(): void
    {
        foreach ([
            'CIV_BANDA_GUERRA' => ['Banda de guerra', 'Cívicos'],
            'CIV_ESCOLTA' => ['Escolta', 'Cívicos'],
            'CUL_BASTONERAS' => ['Bastoneras', 'Cultural'],
            'CUL_DANZA' => ['Danza', 'Cultural'],
            'CUL_BAILE_MODERNO' => ['Baile moderno', 'Cultural'],
            'CUL_MUSICA' => ['Música', 'Cultural'],
            'DEP_FUTBOL_VARONIL' => ['Fútbol varonil', 'Deportivo'],
            'DEP_FUTBOL_FEMENIL' => ['Fútbol femenil', 'Deportivo'],
            'DEP_VOLEIBOL_VARONIL' => ['Voleibol varonil', 'Deportivo'],
            'DEP_VOLEIBOL_FEMENIL' => ['Voleibol femenil', 'Deportivo'],
            'DEP_BASQUETBOL_VARONIL' => ['Basquetbol varonil', 'Deportivo'],
            'DEP_BASQUETBOL_FEMENIL' => ['Basquetbol femenil', 'Deportivo'],
            'CLUB_PROTECCION_CIVIL' => ['Protección civil', 'Club'],
            'CLUB_CICLISMO' => ['Ciclismo', 'Club'],
            'CLUB_SERVICIO_SOCIAL' => ['Servicio social', 'Club'],
        ] as $clave => [$nombre, $categoria]) {
            $catalogo = Catalogo::where('tipo', 'paraescolar')->where('clave', $clave)->first();

            $this->assertNotNull($catalogo, "Falta paraescolar {$clave}");
            $this->assertSame($nombre, $catalogo->nombre);
            $this->assertSame($categoria, $catalogo->metadata['categoria'] ?? null);
            $this->assertTrue($catalogo->activo);
        }

        Livewire::test(RegistroWizard::class)
            ->set('step', 2)
            ->assertSee('Cívicos')
            ->assertSee('Cultural')
            ->assertSee('Deportivo')
            ->assertSee('Club')
            ->assertSee('Banda de guerra')
            ->assertSee('Servicio social');
    }

    public function test_alumno_no_puede_marcar_aviso_dirigido_a_otro_alumno(): void
    {
        $tipoAviso = Catalogo::deTipo('tipo_aviso')->first();
        $procesoSesion = ProcesoIngreso::factory()->create();
        $procesoDestino = ProcesoIngreso::factory()->create([
            'ciclo_ingreso_id' => $procesoSesion->ciclo_ingreso_id,
        ]);
        $aviso = Aviso::create([
            'titulo' => 'Aviso privado',
            'mensaje' => 'Solo para otro alumno',
            'tipo_aviso_id' => $tipoAviso->id,
            'prioridad' => 'informativo',
            'dirigido_a' => 'alumno',
            'alumno_id' => $procesoDestino->alumno_id,
            'visible' => true,
        ]);

        $this
            ->withSession([
                'alumno_proceso_id' => $procesoSesion->id,
                'alumno_ciclo_id' => $procesoSesion->ciclo_ingreso_id,
            ])
            ->post(route('alumno.avisos.leido', $aviso))
            ->assertNotFound();

        $this->assertDatabaseMissing('alumno_avisos', [
            'alumno_id' => $procesoSesion->alumno_id,
            'aviso_id' => $aviso->id,
        ]);
    }

    public function test_aviso_privacidad_renderiza_html_formateado_sin_markdown_crudo(): void
    {
        $this->get(route('alumno.privacidad'))
            ->assertOk()
            ->assertSee('<h1>Aviso de Privacidad Integral del Portal Académico de Nuevo Ingreso</h1>', false)
            ->assertSee('<h2>II. Finalidades principales del tratamiento de datos personales</h2>', false)
            ->assertSee('<h2>XIV. Cambios al aviso de privacidad</h2>', false)
            ->assertSee('<ol>', false)
            ->assertDontSee('## Versi')
            ->assertDontSee('Pendiente: aviso de privacidad institucional');

        $this->assertStringNotContainsString(
            'file_get_contents',
            file_get_contents(resource_path('views/alumno/aviso-privacidad.blade.php')),
        );
    }

    private function procesar(ImportacionCsv $importacion): void
    {
        (new ProcesarImportacionCsv($importacion->id))->handle(
            app(CurpValidator::class),
            app(FolioService::class),
            app(CalculoResultadosService::class),
        );
    }

    private function admin(): User
    {
        return User::where('email', 'admin@registrocobaemario.ariocentro.com')->first()
            ?? User::factory()->create()->assignRole('admin');
    }
}
