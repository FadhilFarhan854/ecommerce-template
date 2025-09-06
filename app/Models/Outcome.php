<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outcome extends Model
{
    protected $table = 'outcomes';

    protected $fillable = [
        'description',
        'amount',
    ];
}
