<?php

namespace Tests\Feature;

use App\Models\CicloIngreso;
use App\Models\Plantel;
use App\Services\FolioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FolioServiceTest extends TestCase
{
    use RefreshDatabase;

    private FolioService $servicio;

    private CicloIngreso $ciclo;

    private Plantel $plantel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->servicio = app(FolioService::class);

        $this->plantel = Plantel::create([
            'clave' => 'ARIO',
            'nombre' => 'COBAEM Plantel Ario de Rosales',
        ]);

        $this->ciclo = CicloIngreso::create([
            'anio' => 2026,
            'periodo_escolar' => '26-2',
            'generacion' => 'Nuevo ingreso 2026',
            'activo' => true,
        ]);
    }

    public function test_genera_folio_con_formato_correcto(): void
    {
        $folio = $this->servicio->generar($this->ciclo, $this->plantel);

        $this->assertSame('NI-2026-ARIO-0001', $folio);
    }

    public function test_los_folios_son_consecutivos(): void
    {
        $this->servicio->generar($this->ciclo, $this->plantel);
        $this->servicio->generar($this->ciclo, $this->plantel);
        $tercero = $this->servicio->generar($this->ciclo, $this->plantel);

        $this->assertSame('NI-2026-ARIO-0003', $tercero);
    }

    public function test_secuencias_independientes_por_ciclo(): void
    {
        $ciclo2027 = CicloIngreso::create([
            'anio' => 2027,
            'periodo_escolar' => '27-2',
            'generacion' => 'Nuevo ingreso 2027',
            'activo' => false,
        ]);

        $this->servicio->generar($this->ciclo, $this->plantel);
        $folio2027 = $this->servicio->generar($ciclo2027, $this->plantel);

        // Cada ciclo arranca su propio consecutivo (RF-39/40).
        $this->assertSame('NI-2027-ARIO-0001', $folio2027);
    }

    public function test_no_se_repiten_folios_en_generacion_masiva(): void
    {
        $folios = collect(range(1, 50))
            ->map(fn () => $this->servicio->generar($this->ciclo, $this->plantel));

        $this->assertSame(50, $folios->unique()->count());
        $this->assertSame('NI-2026-ARIO-0050', $folios->last());
    }
}
