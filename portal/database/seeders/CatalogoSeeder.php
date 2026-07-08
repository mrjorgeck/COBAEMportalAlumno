<?php

namespace Database\Seeders;

use App\Models\Catalogo;
use Illuminate\Database\Seeder;

/**
 * Catálogos prioritarios del MVP (§21.2 requerimientos).
 * Todos administrables después desde el panel; esto es solo el punto de partida.
 */
class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        $this->simple('sexo', ['H' => 'Hombre', 'M' => 'Mujer']);

        $this->simple('estado_civil', [
            'SOL' => 'Soltero(a)', 'CAS' => 'Casado(a)', 'UNI' => 'Unión libre', 'OTR' => 'Otro',
        ]);

        $this->simple('nacionalidad', ['MEX' => 'Mexicana', 'EXT' => 'Extranjera']);

        $this->simple('tipo_estudiante', [
            'REG' => 'Regular',
            'REP' => 'Repetidor',
            'CON' => 'Condicionado',
            'DMS' => 'Debe materias secundaria',
        ]);

        $this->simple('tipo_secundaria', [
            'GEN' => 'General', 'TEC' => 'Técnica', 'TEL' => 'Telesecundaria',
            'PAR' => 'Particular', 'OTR' => 'Otra',
        ]);

        $this->simple('turno', [
            'MAT' => 'Matutino', 'VES' => 'Vespertino', 'NOC' => 'Nocturno', 'MIX' => 'Mixto',
        ]);

        $this->simple('tipo_sangre', [
            'O+' => 'O positivo', 'O-' => 'O negativo',
            'A+' => 'A positivo', 'A-' => 'A negativo',
            'B+' => 'B positivo', 'B-' => 'B negativo',
            'AB+' => 'AB positivo', 'AB-' => 'AB negativo',
            'ND' => 'No sabe',
        ]);

        // Documentos iniciales (§11.1)
        $this->simple('tipo_documento', [
            'ACTA' => 'Acta de nacimiento',
            'CURP' => 'CURP',
            'CERT_SEC' => 'Certificado de secundaria',
            'DOMICILIO' => 'Comprobante de domicilio',
            'FOTOS' => 'Fotografías',
            'SOLICITUD' => 'Solicitud de inscripción firmada',
            'PAGO' => 'Comprobante de pago',
        ]);

        // Áreas de evaluación (§13.3) — ajustar a la evaluación federal vigente
        $this->simple('area_evaluacion', [
            'MAT' => 'Matemáticas',
            'LEC' => 'Comprensión lectora',
            'CIE' => 'Ciencias',
            'SOC' => 'Ciencias sociales',
            'COM' => 'Comunicación',
            'SOCIO' => 'Habilidades socioemocionales',
        ]);

        $this->simple('nivel_desempeno', [
            'INSUF' => 'Insuficiente',
            'BASICO' => 'Básico',
            'MEDIO' => 'Medio',
            'ADECUADO' => 'Adecuado',
            'SOBRES' => 'Sobresaliente',
        ]);

        // Niveles de riesgo con rangos configurables en metadata (§13.5)
        $desempenos = [
            'INSUF' => ['min' => 0, 'max' => 39.99],
            'BASICO' => ['min' => 40, 'max' => 59.99],
            'MEDIO' => ['min' => 60, 'max' => 79.99],
            'ADECUADO' => ['min' => 80, 'max' => 89.99],
            'SOBRES' => ['min' => 90, 'max' => 100],
        ];
        foreach ($desempenos as $clave => $metadata) {
            Catalogo::where('tipo', 'nivel_desempeno')->where('clave', $clave)->update(['metadata' => $metadata]);
        }

        $riesgos = [
            'BAJO' => ['nombre' => 'Bajo', 'metadata' => ['min' => 80, 'max' => 100]],
            'MEDIO' => ['nombre' => 'Medio', 'metadata' => ['min' => 60, 'max' => 79.99]],
            'ALTO' => ['nombre' => 'Alto', 'metadata' => ['min' => 40, 'max' => 59.99]],
            'CRITICO' => ['nombre' => 'Crítico', 'metadata' => ['min' => 0, 'max' => 39.99]],
        ];
        $orden = 0;
        foreach ($riesgos as $clave => $datos) {
            Catalogo::updateOrCreate(
                ['tipo' => 'nivel_riesgo', 'clave' => $clave],
                ['nombre' => $datos['nombre'], 'metadata' => $datos['metadata'], 'orden' => $orden++],
            );
        }

        $this->simple('tipo_aviso', [
            'GENERAL' => 'General',
            'DOCS' => 'Documentación',
            'ACAD' => 'Académico',
            'PROPE' => 'Propedéutico',
            'CONTROL' => 'Control escolar',
            'HORARIO' => 'Horario',
            'SICOBAEM' => 'SICOBaEM',
        ]);

        $this->paraescolares();

        $this->simple('ocupacion', [
            'EMPLEADO' => 'Empleado(a)', 'COMERCIANTE' => 'Comerciante',
            'CAMPO' => 'Trabajo de campo', 'HOGAR' => 'Labores del hogar', 'OTRA' => 'Otra',
        ]);

        $this->simple('nivel_estudios', [
            'PRIMARIA' => 'Primaria', 'SECUNDARIA' => 'Secundaria',
            'BACHILLERATO' => 'Bachillerato', 'LICENCIATURA' => 'Licenciatura',
            'POSGRADO' => 'Posgrado',
        ]);

        $this->simple('beca', [
            'NINGUNA' => 'Ninguna', 'BENITO' => 'Benito Juárez', 'OTRA' => 'Otra',
        ]);
    }

    private function simple(string $tipo, array $valores): void
    {
        $orden = 0;
        foreach ($valores as $clave => $nombre) {
            Catalogo::updateOrCreate(
                ['tipo' => $tipo, 'clave' => (string) $clave],
                ['nombre' => $nombre, 'orden' => $orden++],
            );
        }
    }

    private function paraescolares(): void
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

        Catalogo::where('tipo', 'paraescolar')
            ->whereNotIn('clave', collect($valores)->pluck(0))
            ->update(['activo' => false]);

        foreach ($valores as $orden => [$clave, $nombre, $categoria]) {
            Catalogo::updateOrCreate(
                ['tipo' => 'paraescolar', 'clave' => $clave],
                [
                    'nombre' => $nombre,
                    'metadata' => ['categoria' => $categoria],
                    'orden' => $orden,
                    'activo' => true,
                ],
            );
        }
    }
}
