<?php

namespace Database\Seeders;

use App\Models\CicloIngreso;
use App\Models\Plantel;
use Illuminate\Database\Seeder;

class PlantelCicloSeeder extends Seeder
{
    public function run(): void
    {
        Plantel::updateOrCreate(
            ['clave' => 'ARIO'],
            [
                'nombre' => 'COBAEM Plantel Ario de Rosales',
                'clave_oficial' => null, // PENDIENTE: clave SEP del plantel
                'activo' => true,
            ],
        );

        CicloIngreso::updateOrCreate(
            ['anio' => 2026],
            [
                'periodo_escolar' => '26-2',
                'generacion' => 'Nuevo ingreso 2026',
                'activo' => true,
                // PENDIENTE: ajustar ventana real de registro (fecha del examen)
                'registro_abierto_desde' => null,
                'registro_abierto_hasta' => null,
            ],
        );
    }
}
