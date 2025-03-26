<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
   protected $fillable = [
    'user_id',
    'phone',
    'birth_date',
    'gender',
    
    'address_line1',
    'address_line2',
    'city',
    'state',
    'postal_code',
    'country',

    'id_type',
    'id_number',
    'id_photo_path'

   ];
}
