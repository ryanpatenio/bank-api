<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currencies extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'img_url'
    ];
}
