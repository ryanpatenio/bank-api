<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\Currencies;
use Exception;
use Illuminate\Support\Str;

class ApiKeyServices {

    /**
     * Generate Api Key
     * @return Key
     */
    public function generateApiKey(){

        $apiKey = Str::random(50);
    
        // Api_keys::updateOrCreate([
        //     'user_id' => $user_id,
        //     'api_key' => hash('sha256',$apiKey)
        // ]);
        return $apiKey;
    }

    /**
     * Convert Currency ['PHP'] into ID
     * @return Object  ['id,code,name,symbol,img_url']
     * @throws Exception
     */
    public function convertCurrencyCodeIntoId( string $currency_code){
        if(empty($currency_code)){
            throw new Exception('currency id is required');
        }
    
        return Currencies::where('code',$currency_code)
            ->first();
     }

     /**
     * Get User Api Key Data
     * @return array ['user_id,'api_key,status,callback_url'] | false
     * 
     */
    public function getUserApiKeyData(string $apiKey){
        if(empty($apiKey)){
            return false;
        }
        
        $hash_api = hash('sha256', $apiKey);
        $data = ApiKey::where('api_key',$hash_api)
        ->where('status','active')
        ->first();

        if(!$data){
            return false;
        }

        return $data;
        
    }
}