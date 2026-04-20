<?php

namespace App\Helpers;

class NumberHelper
{
    public static function format(int | float $number): string
    {
        if ($number < 1000) {
            return (string) $number;
        }

        if ($number < 1000000) {
            return round($number / 1000, 1) . 'K';
        }

        return round($number / 1000000, 1) . 'M';
    }
}
