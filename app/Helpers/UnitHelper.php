<?php

namespace App\Helpers;

class UnitHelper
{
    /**
     * Convert a quantity stored in Quintal to the display unit.
     *
     * @param  float|null  $qtl       Quantity in Quintal (as stored in DB)
     * @param  string      $unit      Target display unit: Quintal | Kg | Ton | Bags
     * @param  float       $bagWeight Weight of one bag in Kg (from company setting)
     * @return float
     */
    public static function fromQtl(?float $qtl, string $unit, float $bagWeight = 50): float
    {
        $qtl = (float) $qtl;
        $unit = ucfirst(strtolower(trim($unit)));
        return match ($unit) {
            'Kg'    => $qtl * 100,
            'Ton'   => $qtl / 10,
            'Bags'  => ($qtl * 100) / max($bagWeight, 1),
            default => $qtl, // Quintal
        };
    }

    /**
     * Convert a quantity FROM the given unit TO Quintal (for storage or comparison).
     *
     * @param  float|null  $qty       Quantity in the given unit
     * @param  string      $unit      Source unit: Quintal | Kg | Ton | Bags
     * @param  float       $bagWeight Weight of one bag in Kg
     * @return float
     */
    public static function toQtl(?float $qty, string $unit, float $bagWeight = 50): float
    {
        $qty = (float) $qty;
        $unit = ucfirst(strtolower(trim($unit)));
        return match ($unit) {
            'Kg'    => $qty / 100,
            'Ton'   => $qty * 10,
            'Bags'  => ($qty * max($bagWeight, 1)) / 100,
            default => $qty, // Quintal
        };
    }

    /**
     * Return the short label for a unit.
     */
    public static function label(string $unit): string
    {
        $unit = ucfirst(strtolower(trim($unit)));
        return match ($unit) {
            'Kg'    => 'Kg',
            'Ton'   => 'Ton',
            'Bags'  => 'Bags',
            default => 'Qtl',
        };
    }

    /**
     * Format a quantity: convert from Qtl + append label.
     * Example: formatQty(150, 'Kg', 50) → "15000.00 Kg"
     */
    public static function formatQty(?float $qtl, string $unit, float $bagWeight = 50, int $decimals = 2): string
    {
        $converted = self::fromQtl($qtl, $unit, $bagWeight);
        return number_format($converted, $decimals) . ' ' . self::label($unit);
    }

    /**
     * Convert a Rate stored as per-Quintal to the display unit.
     * e.g., if Rate is ₹2000 per Quintal. In Kg, it should be 2000 / 100 = ₹20 per Kg.
     */
    public static function rateFromQtl(?float $ratePerQtl, string $unit, float $bagWeight = 50): float
    {
        $rate = (float) $ratePerQtl;
        $unit = ucfirst(strtolower(trim($unit)));
        return match ($unit) {
            'Kg'    => $rate / 100,
            'Ton'   => $rate * 10,
            'Bags'  => ($rate * max($bagWeight, 1)) / 100,
            default => $rate, // Quintal
        };
    }

    /**
     * Convert a Rate FROM the given unit TO per-Quintal.
     * e.g., if Rate is ₹20 per Kg. In Quintal, it should be 20 * 100 = ₹2000 per Quintal.
     */
    public static function rateToQtl(?float $rate, string $unit, float $bagWeight = 50): float
    {
        $rate = (float) $rate;
        $unit = ucfirst(strtolower(trim($unit)));
        return match ($unit) {
            'Kg'    => $rate * 100,
            'Ton'   => $rate / 10,
            'Bags'  => ($rate * 100) / max($bagWeight, 1),
            default => $rate, // Quintal
        };
    }
}
