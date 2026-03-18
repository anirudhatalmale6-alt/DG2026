<?php

/**
 * CIMS Money Format
 * Formats a number for money display:
 * - Thousand separator: space
 * - Decimal: 2 places with full stop
 * - No currency symbol
 *
 * Example: 237100 => "237 100.00"
 *          44118  => "44 118.00"
 *          0      => "0.00"
 *
 * @param float|int|string $amount
 * @return string
 */
if (!function_exists('cims_money_format')) {
    function cims_money_format($amount)
    {
        return number_format((float) $amount, 2, '.', ' ');
    }
}

/**
 * Get the SARS tax year for a given date.
 * SA tax year runs 1 March to 28/29 February.
 * Tax year 2027 = 1 March 2026 to 28 February 2027.
 *
 * @param \Carbon\Carbon|string|null $date  Defaults to today
 * @return string  e.g. "2027"
 */
if (!function_exists('cims_tax_year')) {
    function cims_tax_year($date = null)
    {
        $d = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::now();
        // If month >= March, tax year = calendar year + 1
        // If month <= February, tax year = calendar year
        return (string) ($d->month >= 3 ? $d->year + 1 : $d->year);
    }
}
