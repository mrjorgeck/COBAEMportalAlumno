<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $valores = [
            ['CIV_BANDA_GUERRA', 'Banda de guerra', 'Cívicos'],
            ['CIV_ESCOLTA', 'Escolta', 'Cívicos'],
            ['CUL_BASTONERAS', 'Bastoneras', 'Cultural'],
            ['CUL_DANZA', 'Danza', 'Cultural'],
            ['CUL_BAILE_MODERNO', 'Baile moderno', 'Cultural'],
            ['CUL_MUSICA', 'Música', 'Cultural'],
            ['DEP_FUTBOL_VARONIL', 'Fútbol varonil', 'Deportivo'],
            ['DEP_FUTBOL_FEMENIL', 'Fútbol femenil', 'Deportivo'],
            ['DEP_VOLEIBOL_VARONIL', 'Voleibol varonil', 'Deportivo'],
            ['DEP_VOLEIBOL_FEMENIL', 'Voleibol femenil', 'Deportivo'],
            ['DEP_BASQUETBOL_VARONIL', 'Basquetbol varonil', 'Deportivo'],
            ['DEP_BASQUETBOL_FEMENIL', 'Basquetbol femenil', 'Deportivo'],
            ['CLUB_PROTECCION_CIVIL', 'Protección civil', 'Club'],
            ['CLUB_CICLISMO', 'Ciclismo', 'Club'],
            ['CLUB_SERVICIO_SOCIAL', 'Servicio social', 'Club'],
        ];

        DB::table('catalogos')
            ->where('tipo', 'paraescolar')
            ->whereNotIn('clave', collect($valores)->pluck(0)->all())
            ->update([
                'activo' => false,
                'updated_at' => now(),
            ]);

        foreach ($valores as $orden => [$clave, $nombre, $categoria]) {
            DB::table('catalogos')->updateOrInsert(
                ['tipo' => 'paraescolar', 'clave' => $clave],
                [
                    'nombre' => $nombre,
                    'metadata' => json_encode(['categoria' => $categoria], JSON_UNESCAPED_UNICODE),
                    'orden' => $orden,
                    'activo' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }
    }

    public function down(): void
    {
        DB::table('catalogos')
            ->where('tipo', 'paraescolar')
            ->whereIn('clave', [
                'CIV_BANDA_GUERRA',
                'CIV_ESCOLTA',
                'CUL_BASTONERAS',
                'CUL_DANZA',
                'CUL_BAILE_MODERNO',
                'CUL_MUSICA',
                'DEP_FUTBOL_VARONIL',
                'DEP_FUTBOL_FEMENIL',
                'DEP_VOLEIBOL_VARONIL',
                'DEP_VOLEIBOL_FEMENIL',
                'DEP_BASQUETBOL_VARONIL',
                'DEP_BASQUETBOL_FEMENIL',
                'CLUB_PROTECCION_CIVIL',
                'CLUB_CICLISMO',
                'CLUB_SERVICIO_SOCIAL',
            ])
            ->update([
                'activo' => false,
                'updated_at' => now(),
            ]);

        DB::table('catalogos')
            ->where('tipo', 'paraescolar')
            ->whereIn('clave', ['DEPORTE', 'CULTURA', 'OTRA'])
            ->update([
                'activo' => true,
                'updated_at' => now(),
            ]);
    }
};
