<?php

namespace App\Helpers;

class ShipmentHelper
{
    /**
     * Check if shipment functionality is enabled
     */
    public static function isEnabled(): bool
    {
        return config('shipment.use_shipment', true);
    }

    /**
     * Get weight from product if shipment is enabled, otherwise return 0
     */
    public static function getProductWeight($product): float
    {
        if (!self::isEnabled()) {
            return 0;
        }

        return $product->weight ?? 0;
    }

    /**
     * Calculate total weight from cart items if shipment is enabled
     */
    public static function calculateTotalWeight($cartItems): ?float
    {
        if (!self::isEnabled()) {
            return null;
        }

        return $cartItems->sum(function ($item) {
            return self::getProductWeight($item->product) * $item->quantity;
        });
    }

    /**
     * Get shipping cost based on shipment settings
     */
    public static function getShippingCost($requestShippingCost = 0): int
    {
        if (!self::isEnabled()) {
            return 0;
        }

        return (int) $requestShippingCost;
    }
}
