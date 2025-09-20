<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    // Define order status constants
    const STATUS_UNPAID = 'unpaid';
    const STATUS_PAID = 'paid';
    const STATUS_SENDING = 'sending';
    const STATUS_FINISHED = 'finished';
    const STATUS_CANCELLED = 'cancelled';
    
    // Define payment status constants
    const PAYMENT_STATUS_UNPAID = 'unpaid';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_FAILED = 'failed';
    
    protected $fillable = [
        'user_id',
        'status',
        'total_price',
        'total_weight',
        'shipping_address',
        'payment_method',
        'payment_status',
        'midtrans_order_id',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            
            // Generate unique midtrans order ID
            if (empty($model->midtrans_order_id)) {
                $model->midtrans_order_id = 'ORDER-' . time() . '-' . Str::random(8);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }
    
    /**
     * Check if order can be marked as sending by admin
     */
    public function canMarkAsSending()
    {
        return $this->status === self::STATUS_PAID;
    }
    
    /**
     * Check if payment can be continued
     */
    public function canContinuePayment()
    {
        return $this->status === self::STATUS_UNPAID;
    }
    
    /**
     * Check if order can be finished by user
     */
    public function canBeFinished()
    {
        return $this->status === self::STATUS_SENDING;
    }
    
    /**
     * Check if user can review products from this order
     */
    public function canReviewProducts()
    {
        return $this->status === self::STATUS_FINISHED;
    }
    
    /**
     * Get order status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            self::STATUS_UNPAID => 'Unpaid',
            self::STATUS_PAID => 'Paid',
            self::STATUS_SENDING => 'Sending',
            self::STATUS_FINISHED => 'Finished',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }
    
    /**
     * Get payment status label
     */
    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            self::PAYMENT_STATUS_UNPAID => 'Unpaid',
            self::PAYMENT_STATUS_PAID => 'Paid',
            self::PAYMENT_STATUS_FAILED => 'Failed',
        ];
        
        return $labels[$this->payment_status] ?? $this->payment_status;
    }
}
