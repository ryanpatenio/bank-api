<?php

namespace App\Services;
use Illuminate\Support\Str;


class WalletServices {

    /**
     * Generate Transaction Code
     * @return String 
     */
    public function generateTransactionID(){
        return 'TXN-' . time() . '-' . Str::random(8);

    }

    /**
     * Generate Unique ID using uuid
     * @return String
     */
    public function genUUID(){

        return  Str::uuid();
    }

    /**
     * Generate Client Ref using user ID 
     * @return String
     */
    public function genClient_ref(int $user_id){
        return 'REF-' . $user_id . '-' . time();
    }
}