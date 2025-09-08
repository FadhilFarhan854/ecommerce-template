<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'percentage',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    /**
     * Get the product that owns the discount
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if discount is active
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Get discounted price for a given original price
     */
    public function getDiscountedPrice($originalPrice)
    {
        if (!$this->isActive() || $this->percentage <= 0) {
            return $originalPrice;
        }

        $discountAmount = ($originalPrice * $this->percentage) / 100;
        return $originalPrice - $discountAmount;
    }

    /**
     * Get discount amount for a given original price
     */
    public function getDiscountAmount($originalPrice)
    {
        if (!$this->isActive() || $this->percentage <= 0) {
            return 0;
        }

        return ($originalPrice * $this->percentage) / 100;
    }
}
