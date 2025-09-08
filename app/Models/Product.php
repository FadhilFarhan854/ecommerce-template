<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'weight',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class)->withDefault([
            'name' => 'Uncategorized'
        ]);
    }


    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function discount()
    {
        return $this->hasOne(Discount::class);
    }

    // Helper method untuk menghitung rata-rata rating
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    // Helper method untuk menghitung jumlah review
    public function getReviewCountAttribute()
    {
        return $this->reviews()->count();
    }

    // Helper method untuk mendapatkan harga setelah diskon
    public function getDiscountedPriceAttribute()
    {
        if ($this->discount && $this->discount->isActive()) {
            return $this->discount->getDiscountedPrice($this->price);
        }
        return $this->price;
    }

    // Helper method untuk mendapatkan persentase diskon
    public function getDiscountPercentageAttribute()
    {
        if ($this->discount && $this->discount->isActive()) {
            return $this->discount->percentage;
        }
        return 0;
    }

    // Helper method untuk cek apakah ada diskon aktif
    public function hasActiveDiscountAttribute()
    {
        return $this->discount && $this->discount->isActive() && $this->discount->percentage > 0;
    }

    // Helper method untuk mendapatkan jumlah penghematan
    public function getSavingsAmountAttribute()
    {
        if ($this->hasActiveDiscount) {
            return $this->discount->getDiscountAmount($this->price);
        }
        return 0;
    }
}
