<?php

use App\Helpers\ShipmentHelper;

if (!function_exists('formatRupiah')) {
    /**
     * Format number to Indonesian Rupiah currency
     *
     * @param float|int $amount
     * @return string
     */
    function formatRupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('shipmentEnabled')) {
    /**
     * Check if shipment functionality is enabled
     *
     * @return bool
     */
    function shipmentEnabled()
    {
        return ShipmentHelper::isEnabled();
    }
}

if (!function_exists('formatRupiahShort')) {
    /**
     * Format number to Indonesian Rupiah currency with short format (K, M, B)
     *
     * @param float|int $amount
     * @return string
     */
    function formatRupiahShort($amount)
    {
        if ($amount >= 1000000000) {
            return 'Rp ' . number_format($amount / 1000000000, 1) . 'M';
        } elseif ($amount >= 1000000) {
            return 'Rp ' . number_format($amount / 1000000, 1) . 'jt';
        } elseif ($amount >= 1000) {
            return 'Rp ' . number_format($amount / 1000, 0) . 'rb';
        }
        
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
