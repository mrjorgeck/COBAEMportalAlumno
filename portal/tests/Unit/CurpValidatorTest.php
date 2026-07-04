<?php

namespace Tests\Unit;

use App\Services\CurpValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CurpValidatorTest extends TestCase
{
    private CurpValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new CurpValidator;
    }

    // CURPs SINTÉTICAS con estructura y dígito verificador válidos.
    // Nunca usar CURPs reales en el repositorio (CLAUDE.md).

    #[DataProvider('curpsInvalidas')]
    public function test_rechaza_curps_invalidas(string $curp): void
    {
        $this->assertFalse($this->validator->esValida($curp));
    }

    public static function curpsInvalidas(): array
    {
        return [
            'vacía' => [''],
            'corta' => ['ABC123'],
            'larga' => ['AAAA000101HMNXXX012'],
            'mes 13' => ['AEXA001301HMNXXXA1'],
            'día 32' => ['AEXA000132HMNXXXA1'],
            'sexo inválido' => ['AEXA000101XMNXXXA1'],
            'entidad inexistente' => ['AEXA000101HXXXXXA1'],
            'minúsculas con formato roto' => ['aexa00 101hmnxxxa1'],
            'dígito verificador incorrecto' => ['AEXA000101HMNXXXA9'],
        ];
    }

    public function test_acepta_curp_sintetica_con_digito_correcto(): void
    {
        // Estructura válida; el dígito 18 se calcula con el algoritmo oficial.
        $base = 'AEXA000101HMNXXXA'; // 17 caracteres

        $curp = $base.$this->digito($base);

        $this->assertTrue($this->validator->esValida($curp));
    }

    public function test_extrae_fecha_de_nacimiento(): void
    {
        $base = 'AEXA000101HMNXXXA';
        $curp = $base.$this->digito($base);

        $fecha = $this->validator->fechaNacimiento($curp);

        // Homoclave alfabética ('A' en posición 17) => siglo 2000.
        $this->assertSame('2000-01-01', $fecha?->format('Y-m-d'));
    }

    /** Réplica del algoritmo oficial para armar fixtures. */
    private function digito(string $base17): string
    {
        $alfabeto = '0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ';
        $suma = 0;

        for ($i = 0; $i < 17; $i++) {
            $suma += mb_strpos($alfabeto, mb_substr($base17, $i, 1)) * (18 - $i);
        }

        return (string) ((10 - ($suma % 10)) % 10);
    }
}
