<?php

namespace App\Support;

class MetricsFormat
{
    public static function decimal(mixed $value, int $decimals = 15): string
    {
        if ($value === null) {
            return '—';
        }

        $value = (float) $value;

        if ($value == 0.0) {
            return '0';
        }

        if (abs($value) < 1e-6 || abs($value) >= 1e6) {
            return sprintf('%.4e', $value);
        }

        $formatted = number_format($value, $decimals, '.', '');

        return rtrim(rtrim($formatted, '0'), '.') ?: '0';
    }

    /**
     * History / Charts tables: 4 decimal places, same as Run Details cards.
     * Values that would collapse to 0.0000 (energy, tiny latency) use scientific notation.
     */
    public static function table(mixed $value): string
    {
        return self::card($value);
    }

    public static function f1(mixed $value): string
    {
        return self::table($value);
    }

    public static function rate(mixed $value): string
    {
        return self::table($value);
    }

    public static function latency(mixed $value): string
    {
        return self::table($value);
    }

    public static function throughput(mixed $value): string
    {
        return self::table($value);
    }

    public static function energy(mixed $joules): string
    {
        return self::table($joules);
    }

    /** Display-only formatting for dashboard / show stat cards (4 decimal places). */
    public static function card(mixed $value): string
    {
        if ($value === null) {
            return '—';
        }

        $value = (float) $value;

        if ($value == 0.0) {
            return '0.0000';
        }

        if (abs($value) < 0.0001 || abs($value) >= 1e6) {
            return sprintf('%.4e', $value);
        }

        return number_format($value, 4, '.', '');
    }
}
