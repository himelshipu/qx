<?php

namespace App\Support;

class PlatformPricing
{
    public const PLATFORM_CHARGE_RATE = 0.20;

    public static function ratePercent(): int
    {
        return (int) round(self::PLATFORM_CHARGE_RATE * 100);
    }

    /**
     * Build pricing from the influencer net amount.
     *
     * @return array{net_subtotal: float, platform_charge: float, gross_total: float}
     */
    public static function calculateFromNet(float $netSubtotal): array
    {
        $normalizedSubtotal = round(max($netSubtotal, 0), 2);
        $platformCharge = round($normalizedSubtotal * self::PLATFORM_CHARGE_RATE, 2);

        return [
            'net_subtotal' => $normalizedSubtotal,
            'platform_charge' => $platformCharge,
            'gross_total' => round($normalizedSubtotal + $platformCharge, 2),
        ];
    }
}
