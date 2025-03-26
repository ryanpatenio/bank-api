<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $table  ='api_keys';

    protected $fillable = [
        'user_id',
        'api_key',
        'callback_url',
        'status',
        'expires_at',
        'revoked_at'
    ];
}
