<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Earnings extends Model
{
    protected $fillable = [
        'transaction_id',
        'user_id',
        'amount',
        'earn_at'
    ];
}
