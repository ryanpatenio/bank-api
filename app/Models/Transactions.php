<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $fillable = [
        'wallet_id',
        'receiver_wallet_id',
        'api_key_id',
        'transaction_id',
        'client_ref_id',
        'type',
        'amount',
        'status',
        'description',
        'fee',
        'currency_id'
    ];

    
    protected $casts = [
        'type' => TransactionType::class,
    ];

   
}
