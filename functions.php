<?php 

if (!function_exists('formatDateValue')) {
    /**
     * Format a number as currency.
     *
     * @param float $amount
     * @param string $currency
     * @return string
     */
    // Helper function to safely format dates
    function formatDateValue($value) {
        if (empty($value)) return '';
        if ($value instanceof \Carbon\Carbon || $value instanceof \DateTime) {
            return $value->format('Y-m-d');
        }
        // Already a string, return as-is if it looks like a date
        if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
            return substr($value, 0, 10);
        }
        return $value;
    }
}