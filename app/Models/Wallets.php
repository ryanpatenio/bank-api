<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallets extends Model
{
   
   protected $connection = 'mysql';

   protected $fillable = [
    'user_id',
    'currency_id',
    'account_number',
    'balance'
   ];
}
