<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'nama_depan',
        'nama_belakang',
        'alamat',
        'kode_pos',
        'kecamatan',
        'provinsi',
        'hp',
        'kelurahan',
        'kota',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
