<?php

namespace Database\Factories\Concerns;

trait GeneratesSyntheticCurps
{
    protected function syntheticCurp(int $sequence = 1): string
    {
        $anio = 8 + ($sequence % 7);
        $mes = str_pad((string) (($sequence % 12) + 1), 2, '0', STR_PAD_LEFT);
        $dia = str_pad((string) (($sequence % 27) + 1), 2, '0', STR_PAD_LEFT);
        $base = 'XAXX'.str_pad((string) $anio, 2, '0', STR_PAD_LEFT).$mes.$dia.'HMC'.'XXX'.'A';

        return $base.$this->curpDigit($base);
    }

    private function curpDigit(string $first17): int
    {
        $alphabet = '0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ';
        $sum = 0;

        for ($i = 0; $i < 17; $i++) {
            $sum += mb_strpos($alphabet, mb_substr($first17, $i, 1)) * (18 - $i);
        }

        return (10 - ($sum % 10)) % 10;
    }
}
