<?php

namespace Tests\Feature;

use App\Models\Catalogo;
use App\Models\ClaveRespuesta;
use App\Models\Examen;
use App\Models\GrupoPropedeutico;
use App\Models\MaterialRecomendado;
use App\Models\ModuloCiclo;
use App\Models\ProcesoIngreso;
use App\Models\User;
use App\Services\CalculoResultadosService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class Fase2AcceptanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_criterio_14_importacion_de_resultados_diagnosticos_queda_encolada(): void
    {
        Queue::fake();
        $this->actingAs($this->admin());

        $file = UploadedFile::fake()->createWithContent('resultados.csv', "examen_id,folio_examen,puntaje_total,porcentaje_total,nivel_riesgo_clave,nivel_desempeno_clave\n1,FE-001,8,80,BAJO,ADECUADO\n");

        $this->post(route('admin.importaciones.store'), ['tipo_importacion' => 'resultados_examen', 'archivo' => $file])
            ->assertRedirect();

        $this->assertDatabaseHas('importaciones_csv', ['tipo_importacion' => 'resultados_examen', 'estado' => 'pendiente']);
    }

    public function test_rf12_calcula_resultados_totales_y_por_area_desde_respuestas(): void
    {
        [$proceso, $examen] = $this->escenarioConClave();

        $resultado = app(CalculoResultadosService::class)->calcularDesdeRespuestas($proceso, $examen, [
            1 => 'A',
            2 => 'C',
            3 => 'B',
            4 => 'D',
        ]);

        $this->assertSame('75.00', (string) $resultado->porcentaje_total);
        $this->assertCount(2, $resultado->areas);
        $this->assertDatabaseHas('resultados_area', ['resultado_id' => $resultado->id, 'porcentaje' => 50]);
        $this->assertDatabaseHas('resultados_area', ['resultado_id' => $resultado->id, 'porcentaje' => 100]);
    }

    public function test_criterio_15_alumno_consulta_resultado_y_areas_de_mejora_solo_con_modulos_publicados(): void
    {
        [$proceso, $examen] = $this->escenarioConClave();
        app(CalculoResultadosService::class)->calcularDesdeRespuestas($proceso, $examen, [1 => 'C', 2 => 'C', 3 => 'B', 4 => 'D']);

        $session = $this->sessionAlumno($proceso);

        $this->withSession($session)
            ->get(route('alumno.mi-proceso.seccion', 'resultados'))
            ->assertOk()
            ->assertSee('disponible');

        $this->publicar($proceso, 'resultados');
        $this->publicar($proceso, 'areas_mejora');

        $this->withSession($session)
            ->get(route('alumno.mi-proceso.seccion', 'resultados'))
            ->assertOk()
            ->assertSee('Resultado general');

        $this->withSession($session)
            ->get(route('alumno.mi-proceso.seccion', 'areas-mejora'))
            ->assertOk()
            ->assertSee('Nivel de riesgo');
    }

    public function test_materiales_recomendados_se_filtran_por_areas_debiles(): void
    {
        [$proceso, $examen, $matematicas] = $this->escenarioConClave();
        app(CalculoResultadosService::class)->calcularDesdeRespuestas($proceso, $examen, [1 => 'C', 2 => 'C', 3 => 'B', 4 => 'D']);
        MaterialRecomendado::create([
            'area_id' => $matematicas->id,
            'titulo' => 'Guia de fracciones',
            'descripcion' => 'Practica breve.',
            'tipo_material' => 'guia',
            'activo' => true,
        ]);
        $this->publicar($proceso, 'materiales');

        $this->withSession($this->sessionAlumno($proceso))
            ->get(route('alumno.mi-proceso.seccion', 'materiales'))
            ->assertOk()
            ->assertSee('Guia de fracciones');
    }

    public function test_criterio_17_sistema_carga_y_muestra_grupo_propedeutico(): void
    {
        $proceso = $this->crearProcesoRegistrado();
        $grupo = GrupoPropedeutico::create([
            'ciclo_ingreso_id' => $proceso->ciclo_ingreso_id,
            'nombre' => 'P-03',
            'aula' => 'Laboratorio 1',
            'horario_texto' => '8:00 a 10:00',
            'indicaciones' => 'Presentarse con cuaderno.',
            'activo' => true,
        ]);
        $this->actingAs($this->admin())
            ->post(route('admin.alumnos.grupo-propedeutico', $proceso), ['grupo_propedeutico_id' => $grupo->id])
            ->assertRedirect();
        $this->publicar($proceso, 'propedeutico');

        $this->withSession($this->sessionAlumno($proceso))
            ->get(route('alumno.mi-proceso.seccion', 'propedeutico'))
            ->assertOk()
            ->assertSee('P-03')
            ->assertSee('Laboratorio 1');
    }

    public function test_rf13_rf27_compara_evaluacion_inicial_y_posterior(): void
    {
        [$proceso, $inicial] = $this->escenarioConClave();
        $posterior = Examen::create([
            'ciclo_ingreso_id' => $proceso->ciclo_ingreso_id,
            'nombre' => 'Evaluacion posterior',
            'tipo' => 'evaluacion_posterior',
            'total_preguntas' => 4,
            'activo' => true,
        ]);
        foreach ($inicial->claves as $clave) {
            ClaveRespuesta::create($clave->only(['pregunta', 'respuesta_correcta', 'area_id', 'materia_id', 'competencia', 'ponderacion']) + ['examen_id' => $posterior->id]);
        }

        app(CalculoResultadosService::class)->calcularDesdeRespuestas($proceso, $inicial, [1 => 'C', 2 => 'C', 3 => 'B', 4 => 'D']);
        app(CalculoResultadosService::class)->calcularDesdeRespuestas($proceso, $posterior, [1 => 'A', 2 => 'C', 3 => 'B', 4 => 'D']);
        $this->publicar($proceso, 'avance');
        $this->publicar($proceso, 'evaluacion_posterior');

        $this->withSession($this->sessionAlumno($proceso))
            ->get(route('alumno.mi-proceso.seccion', 'avance'))
            ->assertOk()
            ->assertSee('Total')
            ->assertSee('+25.00');
    }

    public function test_criterio_16_dashboard_muestra_indicadores_basicos(): void
    {
        [$proceso, $examen] = $this->escenarioConClave();
        app(CalculoResultadosService::class)->calcularDesdeRespuestas($proceso, $examen, [1 => 'A', 2 => 'C', 3 => 'B', 4 => 'D']);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard-academico', ['ciclo' => $proceso->ciclo_ingreso_id, 'examen' => $examen->id]))
            ->assertOk()
            ->assertSee('Evaluados')
            ->assertSee('Promedio general');
    }

    private function escenarioConClave(): array
    {
        $proceso = $this->crearProcesoRegistrado();
        $matematicas = Catalogo::where('tipo', 'area_evaluacion')->where('clave', 'MAT')->first();
        $lectura = Catalogo::where('tipo', 'area_evaluacion')->where('clave', 'LEC')->first();
        $examen = Examen::create([
            'ciclo_ingreso_id' => $proceso->ciclo_ingreso_id,
            'nombre' => 'Diagnostico 2026',
            'tipo' => 'diagnostico_inicial',
            'total_preguntas' => 4,
            'activo' => true,
        ]);

        foreach ([[1, 'A', $matematicas], [2, 'B', $matematicas], [3, 'B', $lectura], [4, 'D', $lectura]] as [$pregunta, $respuesta, $area]) {
            ClaveRespuesta::create([
                'examen_id' => $examen->id,
                'pregunta' => $pregunta,
                'respuesta_correcta' => $respuesta,
                'area_id' => $area->id,
                'ponderacion' => 1,
            ]);
        }

        return [$proceso, $examen, $matematicas, $lectura];
    }

    private function crearProcesoRegistrado(): ProcesoIngreso
    {
        $this->post(route('alumno.registro.store'), $this->payloadRegistro());

        return ProcesoIngreso::with('alumno')->first();
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
