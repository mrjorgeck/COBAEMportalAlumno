<?php

namespace App\Support;

use Illuminate\Http\Request;

class FechaInput
{
    public static function toDatabase(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = trim($value);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
            return $value;
        }

        $date = \DateTimeImmutable::createFromFormat('!d/m/Y', $value);

        if ($date === false || $date->format('d/m/Y') !== $value) {
            return $value;
        }

        return $date->format('Y-m-d');
    }

    public static function toDisplay(mixed $value): string
    {
        if (blank($value)) {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('d/m/Y');
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', (string) $value);

        return $date ? $date->format('d/m/Y') : (string) $value;
    }

    public static function normalizeRequest(Request $request, array $fields): void
    {
        $data = [];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $data[$field] = self::toDatabase($request->input($field));
            }
        }

        $request->merge($data);
    }
}
