<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'image',
        'status',
    ];

    // Hapus casting boolean yang mungkin bermasalah
    // protected $casts = [
    //     'status' => 'boolean',
    // ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
