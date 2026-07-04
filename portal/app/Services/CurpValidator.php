<?php

namespace App\Services;

/**
 * Validación de CURP (RF-02).
 *
 * Valida estructura oficial (RENAPO) y dígito verificador.
 * No consulta servicios externos: validación local únicamente.
 */
class CurpValidator
{
    private const PATRON = '/^[A-Z][AEIOUX][A-Z]{2}'.  // iniciales
        '\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])'.   // fecha AAMMDD
        '[HM]'.                                         // sexo
        '(AS|BC|BS|CC|CL|CM|CS|CH|DF|DG|GT|GR|HG|JC|MC|MN|MS|NT|NL|OC|PL|QT|QR|SP|SL|SR|TC|TS|TL|VZ|YN|ZS|NE)'. // entidad
        '[B-DF-HJ-NP-TV-Z]{3}'.                         // consonantes internas
        '[0-9A-Z]\d$/';                                  // homoclave + dígito

    private const ALFABETO = '0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ';

    public function esValida(string $curp): bool
    {
        $curp = mb_strtoupper(trim($curp));

        if (mb_strlen($curp) !== 18) {
            return false;
        }

        if (! preg_match(self::PATRON, $curp)) {
            return false;
        }

        return $this->digitoVerificadorCorrecto($curp);
    }

    /**
     * Algoritmo oficial del dígito verificador (posición 18).
     */
    private function digitoVerificadorCorrecto(string $curp): bool
    {
        $suma = 0;

        for ($i = 0; $i < 17; $i++) {
            $caracter = mb_substr($curp, $i, 1);
            $valor = mb_strpos(self::ALFABETO, $caracter);

            if ($valor === false) {
                return false;
            }

            $suma += $valor * (18 - $i);
        }

        $digitoEsperado = (10 - ($suma % 10)) % 10;

        return (int) mb_substr($curp, 17, 1) === $digitoEsperado;
    }

    /**
     * Extrae la fecha de nacimiento contenida en la CURP.
     * Siglo: homoclave numérica (pos. 17) => 1900s; alfabética => 2000s.
     */
    public function fechaNacimiento(string $curp): ?\DateTimeImmutable
    {
        $curp = mb_strtoupper(trim($curp));

        if (! $this->esValida($curp)) {
            return null;
        }

        $anio = (int) mb_substr($curp, 4, 2);
        $mes = mb_substr($curp, 6, 2);
        $dia = mb_substr($curp, 8, 2);

        $siglo = ctype_digit(mb_substr($curp, 16, 1)) ? 1900 : 2000;
        $anioCompleto = $siglo + $anio;

        $fecha = \DateTimeImmutable::createFromFormat('Y-m-d', "{$anioCompleto}-{$mes}-{$dia}");

        return $fecha ?: null;
    }
}
