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
     * Check if order can be paid/retried
     */
    public function canRetryPayment()
    {
        return $this->status === self::STATUS_UNPAID;
    }
    
    /**
     * Check if order can be marked as sending by admin
     */
    public function canMarkAsSending()
    {
        return $this->status === self::STATUS_PAID;
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
            self::STATUS_UNPAID => 'Belum Dibayar',
            self::STATUS_PAID => 'Sudah Dibayar',
            self::STATUS_SENDING => 'Sedang Dikirim',
            self::STATUS_FINISHED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }
    
    /**
     * Get payment status label
     */
    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            self::PAYMENT_STATUS_UNPAID => 'Belum Dibayar',
            self::PAYMENT_STATUS_PAID => 'Sudah Dibayar',
            self::PAYMENT_STATUS_FAILED => 'Gagal',
        ];
        
        return $labels[$this->payment_status] ?? $this->payment_status;
    }
}
