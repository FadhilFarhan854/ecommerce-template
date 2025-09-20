<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'review',
        'rating',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Check if user can review a product based on finished orders
     */
    public static function canUserReviewProduct($userId, $productId)
    {
        return Order::where('user_id', $userId)
            ->where('status', Order::STATUS_FINISHED)
            ->whereHas('items', function($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->exists();
    }
}
