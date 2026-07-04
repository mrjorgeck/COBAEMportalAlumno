<?php

namespace App\Services;

use App\Models\CicloIngreso;
use App\Models\Plantel;
use Illuminate\Support\Facades\DB;

/**
 * Generación del folio interno del portal (RF-37, regla crítica #1 de CLAUDE.md).
 *
 * Formato: NI-{AÑO}-{PLANTEL}-{CONSECUTIVO} (ej. NI-2026-ARIO-0001).
 * Consecutivo por ciclo + plantel, con transacción y bloqueo pesimista
 * sobre folio_secuencias para garantizar unicidad bajo concurrencia
 * (pico de ~300 registros simultáneos al terminar el examen).
 */
class FolioService
{
    /**
     * Genera y reserva el siguiente folio para el ciclo y plantel dados.
     */
    public function generar(CicloIngreso $ciclo, Plantel $plantel): string
    {
        return DB::transaction(function () use ($ciclo, $plantel) {
            $secuencia = DB::table('folio_secuencias')
                ->where('ciclo_ingreso_id', $ciclo->id)
                ->where('plantel_id', $plantel->id)
                ->lockForUpdate()
                ->first();

            if ($secuencia === null) {
                DB::table('folio_secuencias')->insert([
                    'ciclo_ingreso_id' => $ciclo->id,
                    'plantel_id' => $plantel->id,
                    'consecutivo' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $consecutivo = 1;
            } else {
                $consecutivo = $secuencia->consecutivo + 1;

                DB::table('folio_secuencias')
                    ->where('id', $secuencia->id)
                    ->update([
                        'consecutivo' => $consecutivo,
                        'updated_at' => now(),
                    ]);
            }

            return $this->formatear($ciclo->anio, $plantel->clave, $consecutivo);
        });
    }

    public function formatear(int $anio, string $clavePlantel, int $consecutivo): string
    {
        return sprintf(
            config('portal.folio.formato'),
            $anio,
            mb_strtoupper($clavePlantel),
            $consecutivo,
        );
    }
}
