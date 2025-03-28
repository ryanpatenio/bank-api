<?php

namespace App\Http\Controllers;

use App\Exceptions\Transactions\TransactionException;
use App\Services\ApiKeyServices;
use App\Services\TransactionServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiRequestController extends Controller
{  
   private $transactionServices,$apiKeyServices;

   public function __construct(TransactionServices $transactionServices,ApiKeyServices $apiKeyServices)
   {
      $this->transactionServices = $transactionServices;
      $this->apiKeyServices = $apiKeyServices;
   }

   public function credit(Request $r){

     $validator = Validator::make($r->all(),[
        'currency' => 'required|string',
        'amount' => 'required|numeric|min:0.01',
        'client_ref' => 'required|string'
     ]);

     $apiKey = $r->header('X-API-Key');
   
     if($validator->fails()){
         return json_message(EXIT_FORM_NULL,'Validation Errors',$validator->errors());
     }  
     
     //data
     $validateData = [
         'currency' => $r->input('currency'),
         'amount' => $r->input('amount'),
         'client_ref' => $r->input('client_ref'),
         'api_key'    => $apiKey
       ];

      try {
 
         $processCredit  = $this->transactionServices->processCredit($validateData);
         return $processCredit;

      }
      catch(\Exception $et){
         return json_message(EXIT_BE_ERROR,$et->getMessage());
      }
   }

   public function debit(Request $r){
      $apiKey = $r->header('X-API-Key');

      if(!$apiKey){
         return json_message(EXIT_401,'Invalid Api Key');
      }

      $validator = Validator::make($r->all(),[
         'currency' => 'required|string',
         'amount' => 'required|numeric|min:0.01',
         'client_ref' => 'required|string'
      ]);
      
      if($validator->fails()){
         return json_message(EXIT_FORM_NULL,'Validation Errors',$validator->errors());
      }
      
      //data
      $validateData = [
            'currency' => $r->input('currency'),
            'amount' => $r->input('amount'),
            'client_ref' => $r->input('client_ref'),
            'api_key'    => $apiKey
         ];
      try {

         $processDebit  = $this->transactionServices->processDebit($validateData);
         return $processDebit;

      } catch (TransactionException $e) {       
         return json_message($e->getCode(),$e->getMessage(),$e->getDetails());
      }catch(\Exception $et){
         return json_message(EXIT_BE_ERROR,$et->getMessage());
      }
     
   }
}
