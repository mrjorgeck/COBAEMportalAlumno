<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Catalogo;
use App\Models\CicloIngreso;
use App\Models\Plantel;
use App\Models\ProcesoIngreso;
use App\Services\RegistroAlumnoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Regla crítica #4 (RF-15 / SEG-06): el bloqueo de edición y el cierre de la
 * ventana del ciclo impiden ESCRIBIR desde el lado del alumno, sin afectar la
 * consulta. Complementa el criterio 10 (que cubre el bloqueo del lado admin).
 */
class BloqueoEscrituraAlumnoTest extends TestCase
{
    use RefreshDatabase;

    private const CURP = 'AEXA000101HMNXXXA1'; // sintética

    private Plantel $plantel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plantel = Plantel::create([
            'clave' => 'ARIO',
            'nombre' => 'COBAEM Plantel Ario de Rosales',
        ]);
    }

    public function test_rechaza_escritura_con_ventana_de_registro_cerrada(): void
    {
        CicloIngreso::create([
            'anio' => 2026,
            'periodo_escolar' => '26-2',
            'generacion' => 'Nuevo ingreso 2026',
            'activo' => true,
            'registro_abierto_hasta' => now()->subDay(), // ventana vencida
        ]);

        $this->expectException(ValidationException::class);

        app(RegistroAlumnoService::class)->registrar(['curp' => self::CURP]);
    }

    public function test_rechaza_escritura_de_proceso_con_edicion_bloqueada(): void
    {
        $ciclo = CicloIngreso::create([
            'anio' => 2026,
            'periodo_escolar' => '26-2',
            'generacion' => 'Nuevo ingreso 2026',
            'activo' => true, // ventana abierta (sin fechas)
        ]);

        $catalogo = fn (string $tipo) => Catalogo::create([
            'tipo' => $tipo, 'clave' => 'T-'.$tipo, 'nombre' => $tipo,
        ]);

        $alumno = new Alumno;
        $alumno->forceFill([
            'curp' => self::CURP,
            'nombres' => 'Prueba',
            'primer_apellido' => 'Sintetica',
            'fecha_nacimiento' => '2010-01-01',
            'sexo_id' => $catalogo('sexo')->id,
            'nacionalidad_id' => $catalogo('nacionalidad')->id,
            'estado_civil_id' => $catalogo('estado_civil')->id,
            'entidad_nacimiento_id' => $catalogo('entidad')->id,
            'municipio_nacimiento_id' => $catalogo('municipio')->id,
        ])->save();

        $proceso = new ProcesoIngreso;
        $proceso->forceFill([
            'alumno_id' => $alumno->id,
            'ciclo_ingreso_id' => $ciclo->id,
            'plantel_id' => $this->plantel->id,
            'folio_registro' => 'NI-2026-ARIO-9999',
            'tipo_estudiante_id' => $catalogo('tipo_estudiante')->id,
            'estatus_proceso' => 'registrado',
            'edicion_bloqueada' => true,
        ])->save();

        try {
            app(RegistroAlumnoService::class)->registrar(['curp' => self::CURP]);
            $this->fail('Se esperaba ValidationException por edición bloqueada.');
        } catch (ValidationException) {
            // esperado
        }

        // La consulta sigue disponible: el proceso existe intacto (RNF-19).
        $this->assertDatabaseHas('procesos_ingreso', [
            'id' => $proceso->id,
            'edicion_bloqueada' => true,
            'estatus_proceso' => 'registrado',
        ]);
    }
}
