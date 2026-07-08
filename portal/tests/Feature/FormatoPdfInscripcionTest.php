<?php

namespace Tests\Feature;

use App\Models\Catalogo;
use App\Models\CicloIngreso;
use App\Models\DatosContacto;
use App\Models\Familiar;
use App\Models\OtrosDatosAlumno;
use App\Models\Plantel;
use App\Models\ProcesoIngreso;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormatoPdfInscripcionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_formato_pdf_se_descarga_por_alumno_admin_y_renderiza_maqueta_oficial(): void
    {
        $proceso = $this->crearProcesoCompleto();

        $alumnoResponse = $this
            ->withSession([
                'alumno_proceso_id' => $proceso->id,
                'alumno_ciclo_id' => $proceso->ciclo_ingreso_id,
                'alumno_nivel_sensible' => true,
            ])
            ->get(route('alumno.formato.descargar'));

        $alumnoResponse->assertOk();
        $this->assertStringContainsString('application/pdf', $alumnoResponse->headers->get('content-type'));
        file_put_contents(storage_path('framework/testing/formato-inscripcion-test.pdf'), $alumnoResponse->getContent());
        $this->assertFileExists(storage_path('framework/testing/formato-inscripcion-test.pdf'));
        $this->assertStringStartsWith('%PDF', $alumnoResponse->getContent());

        $adminResponse = $this
            ->actingAs($this->admin())
            ->get(route('admin.alumnos.formato', $proceso));

        $adminResponse->assertOk();
        $this->assertStringContainsString('application/pdf', $adminResponse->headers->get('content-type'));

        $html = view('pdf.inscripcion.v2026.formato', [
            'proceso' => $proceso->fresh([
                'alumno.sexo',
                'alumno.nacionalidad',
                'alumno.estadoCivil',
                'alumno.entidadNacimiento',
                'alumno.municipioNacimiento',
                'ciclo',
                'plantel',
                'tipoEstudiante',
                'paraescolar',
                'secundariaProcedencia',
                'entidadSecundaria',
                'municipioSecundaria',
                'contacto.municipio',
                'contacto.localidad',
                'tutor.ocupacion',
                'tutor.estudios',
                'madre.ocupacion',
                'madre.estudios',
                'otrosDatos.beca',
                'otrosDatos.tipoSangre',
            ]),
            'generadoEn' => now(),
        ])->render();

        foreach ([
            'SOLICITUD DE INSCRIPCI',
            'DATOS DEL ESTUDIANTE',
            'DIRECCI',
            'DATOS DE CONTACTO ACTUAL',
            'ESCUELA DE PROCEDENCIA',
            'DATOS DE TUTOR',
            'DATOS DE MADRE',
            'OTROS DATOS',
            'Ana',
            'XAXX080202HMCXXXA0',
            'FE-001',
            'NI-2026-ARIO-9001',
            'Regular',
            'Deportiva',
            'Ario de Rosales',
            'Secundaria Sintetica Oficial',
            'Benito',
        ] as $textoEsperado) {
            $this->assertStringContainsString($textoEsperado, $html);
        }
    }

    private function crearProcesoCompleto(): ProcesoIngreso
    {
        $entidad = Catalogo::deTipo('entidad')->where('clave', 'MN')->first();
        $municipio = Catalogo::deTipo('municipio')->where('nombre', 'Ario')->first();
        $localidad = Catalogo::deTipo('localidad')->where('nombre', 'Ario de Rosales')->first();
        $secundaria = Catalogo::firstOrCreate(
            ['tipo' => 'secundaria', 'clave' => 'SEC-TEST-PDF'],
            ['nombre' => 'Secundaria Sintetica Oficial', 'parent_id' => $municipio->id],
        );

        $proceso = ProcesoIngreso::factory()->create([
            'ciclo_ingreso_id' => CicloIngreso::vigente()->id,
            'plantel_id' => Plantel::first()->id,
            'folio_registro' => 'NI-2026-ARIO-9001',
            'folio_examen' => 'FE-001',
            'tipo_estudiante_id' => Catalogo::deTipo('tipo_estudiante')->where('clave', 'REG')->first()->id,
            'paraescolar_id' => Catalogo::deTipo('paraescolar')->where('clave', 'DEPORTE')->first()->id,
            'secundaria_procedencia_id' => $secundaria->id,
            'entidad_secundaria_id' => $entidad->id,
            'municipio_secundaria_id' => $municipio->id,
            'promedio_secundaria' => 9.2,
        ]);

        $proceso->alumno->update([
            'curp' => 'XAXX080202HMCXXXA0',
            'nombres' => 'Ana',
            'primer_apellido' => 'Prueba',
            'segundo_apellido' => 'Sintetica',
            'entidad_nacimiento_id' => $entidad->id,
            'municipio_nacimiento_id' => $municipio->id,
        ]);

        DatosContacto::create([
            'proceso_ingreso_id' => $proceso->id,
            'telefono' => '4431234500',
            'celular' => '4431234567',
            'correo' => 'ana@example.test',
            'municipio_id' => $municipio->id,
            'localidad_id' => $localidad->id,
            'colonia' => 'Centro',
            'domicilio' => 'Calle Uno 123',
            'codigo_postal' => '61830',
        ]);

        foreach (['tutor' => 'Tutor', 'madre' => 'Madre'] as $tipo => $nombre) {
            Familiar::create([
                'proceso_ingreso_id' => $proceso->id,
                'tipo_familiar' => $tipo,
                'nombres' => $nombre,
                'primer_apellido' => 'Prueba',
                'segundo_apellido' => 'Sintetico',
                'telefono' => '4437654300',
                'celular' => '4437654321',
                'ocupacion_id' => Catalogo::deTipo('ocupacion')->first()->id,
                'estudios_id' => Catalogo::deTipo('nivel_estudios')->first()->id,
            ]);
        }

        OtrosDatosAlumno::create([
            'proceso_ingreso_id' => $proceso->id,
            'no_seguro_medico' => 'NSS-TEST',
            'beca_id' => Catalogo::deTipo('beca')->where('clave', 'BENITO')->first()->id,
            'estatura' => 1.68,
            'peso' => 62.5,
            'tipo_sangre_id' => Catalogo::deTipo('tipo_sangre')->where('clave', 'O+')->first()->id,
        ]);

        return $proceso;
    }

    private function admin(): User
    {
        return User::where('email', 'admin@registrocobaemario.ariocentro.com')->first()
            ?? User::factory()->create()->assignRole('admin');
    }
}
