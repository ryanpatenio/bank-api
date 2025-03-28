<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\ApiKey;
use App\Models\Transactions;
use App\Models\Wallets;
use App\Utilities\TransactionThrower;
use Exception;

use Illuminate\Support\Facades\Log;

class TransactionServices {

    private $apiKeyServices;
    private $walletServices;
    public function __construct(ApiKeyServices $apiKeyServices, WalletServices $walletServices)
    {
        $this->apiKeyServices = $apiKeyServices;
        $this->walletServices = $walletServices;
    }
    /**
     * Process Credit Transaction
     * @return array response
     * @throws Exception | TransactionException
     */
    public function processCredit(array $data){
        $this->validateRequest($data);

        $userApiObj = $this->apiKeyServices->getUserApiKeyData($data['api_key']);
        if(!$userApiObj){
            throw new Exception('Invalid Api Key');
        }

        $this->isAccountNumberIsExist($data['account_number']);

        //Objects
        $currencyObj = $this->apiKeyServices->convertCurrencyCodeIntoId($data['currency']);
        $userWallet = $this->userWallet($userApiObj['user_id'],$currencyObj->id,$data['account_number']);
    
        $amount = $data['amount'];
       
        $newBalance =  $this->updateWalletBalance($userWallet->id,$amount); //update wallet Balance

        //transaction Data 
        $generatedCode = $this->walletServices->generateTransactionID();
        $type = TransactionType::CREDIT;
        $fee = '0';
        $status = 'success';
        $description = "Bank Transfer using External Api";
        $transactionData = [
            'wallet_id' => $userWallet->id,
            'currency_id'  => $currencyObj->id,
            'transaction_id' => $generatedCode,
            'type'          => $type->value,
            'client_ref_id'    => $data['client_ref'],
            'amount'        => $amount,
            'fee'           => $fee ?? 0,
            'status'        => $status,
            'description'   => $description,
            'api_key_id'    => $userApiObj['id']
        ];

        $this->validateTransactionData($transactionData);//validate Transaction Data returns exception
        $this->createTransaction($transactionData);

        //response data
        $response = [
            'transaction_id' => $generatedCode,
            'amount' => $amount,
            'currency_id' => $data['currency'],
            'old_balance' => $userWallet->balance,
            'new_balance' => $newBalance
        ];
        
        return json_message(EXIT_SUCCESS,'Credit transaction successful.',$response);
       
    }

    public function processDebit(array $data){

        $this->validateRequest($data);

        $userApiObj = $this->apiKeyServices->getUserApiKeyData($data['api_key']);
        if(!$userApiObj){
            throw new Exception('Invalid Api Key');
        }

        $this->isAccountNumberIsExist($data['account_number']);
        
        //Objects
        $currencyObj = $this->apiKeyServices->convertCurrencyCodeIntoId($data['currency']);
        $userWallet = $this->userWallet($userApiObj['user_id'],$currencyObj->id,$data['account_number']);
    
        $amount = $data['amount'];
        $balance = $userWallet->balance;

        TransactionThrower::insufficientFunds($balance,$amount);//validate funds    

        //this fee is static it must be change in future for dynamic
        $fee = $currencyObj->name === "PHP" ? "15" : ($currencyObj->name === "USD" ? "1" : '1');
        $newBalance =  $this->updateWalletBalance($userWallet->id,-($amount + $fee)); //update wallet Balance

        //transaction Data 
        $generatedCode = $this->walletServices->generateTransactionID();
        $type = TransactionType::DEBIT;       
        $status = 'success';
        $description = "Account Debit using External Api";
        $transactionData = [
            'wallet_id' => $userWallet->id,
            'currency_id'  => $currencyObj->id,
            'transaction_id' => $generatedCode,
            'type'          => $type->value,
            'client_ref_id'    => $data['client_ref'],
            'amount'        => $amount,
            'fee'           => $fee ?? 0,
            'status'        => $status,
            'description'   => $description,
            'api_key_id'    => $userApiObj['id'] //requires for using external api transaction
        ];
       
        $this->validateTransactionData($transactionData);

        $this->createTransaction($transactionData);

        //response data
        $response = [
            'transaction_id' => $generatedCode,
            'amount' => $amount,
            'fee'    => $fee.$currencyObj->symbol,
            'total_amount_deducted' => ($fee + $amount),
            'currency_id' => $data['currency'],
            'wallet_balance' => $newBalance
        ];
        
        return json_message(EXIT_SUCCESS,'Debit transaction successful.',$response);       

    }
   

    /**
     * validate incoming request
     * @param array required ['currency,amount,client_ref']
     * @return void
     * @throws Exception
     */
    protected function validateRequest(array $data) :void {
        if(empty($data['currency'])){
            throw new Exception('currency is required');
        }
        if(empty($data['amount'])){
            throw new Exception('amount is required');
        }
        if(empty($data['client_ref'])){
            throw new Exception('client_ref is required');
        }
        if(empty($data['api_key'])){
            throw new Exception('api key is required');
        }
        if(empty($data['account_number'])){
            throw new Exception('account number is required');
        }
        

    }

    /**
     * Checks Transaction if Exist using Client Ref
     * @return boolean true false
     */

   /**
     * Check if a transaction exists for the given wallet ID and client reference.
     *
     * @param int $wallet_id
     * @param string $client_ref
     * @return Object | null
     * @throws Exception --catch
     */
    public function isTransactionExist(int $wallet_id, string $client_ref): ?object {
        // Validate input parameters
        if ($wallet_id <= 0 || empty(trim($client_ref))) {
            throw new Exception('Wallet ID and client reference are required and must be valid.');
        }

        return Transactions::where('wallet_id', $wallet_id)
            ->where('client_ref_id', $client_ref)
            ->first();
    }

    /**
     * Get User Wallet Data
     * 
     * @return object ['id,user_id,currency_id,account_number,balance']
     * @throws Exception
     */
    public function userWallet(int $user_id, int $currency_id, int $accountNumber) : object {
        $query = Wallets::where('user_id',$user_id)
                ->where('currency_id',$currency_id)
                ->where('account_number',$accountNumber)
                ->first();
        if(!$query){
           throw  new Exception('No Wallet Found or Invalid Account Number!');
        }
        return $query;
    }

     /**
     * Update wallet balance.
     *
     * @param int $walletId
     * @param float $amount
     */
    protected function updateWalletBalance(int $walletId, float $amount): float {
        // Get and update the wallet in one atomic operation
        $wallet = Wallets::where('id', $walletId)
        ->lockForUpdate() // Prevents race conditions
        ->firstOrFail();

        $wallet->balance += $amount;
        $wallet->save();

        return $wallet->balance;
    }
    /**
     * Create Transaction
     * @return void
     */
    private function createTransaction(array $data) :void {
        Transactions::create($data);
    }

    /**
     * Validates Transaction Data
     * @throws Exception
     */
    private function validateTransactionData(array $data) : void{

        $requiredFields = [
            'wallet_id' => 'Wallet ID is required',
            'currency_id' => 'Currency is required',
            'transaction_id' => 'Transaction code is required',
            'client_ref_id' => 'Client reference is required',
            'type' => 'Transaction type is required',
            'amount' => 'Amount is required',
            'fee'    => 'Fee is required',
            'status' => 'status is required'
        ];

        foreach ($requiredFields as $field => $message) {
            if (!isset($data[$field])) {  // Only checks if the key exists
                throw new Exception($message);
            }
        }

        if (!TransactionType::tryFrom($data['type'])) {
            throw new Exception("Transaction type must be 'debit' or 'credit'");
        }
        //check if transaction is already exist
        $isTransactionExist = $this->isTransactionExist($data['wallet_id'],$data['client_ref_id']);
        if($isTransactionExist){
            TransactionThrower::duplicate($isTransactionExist->transaction_id);
        }

    }

    /**
     * Checks User Wallet Account Number
     * @return void
     * @throws Exception
     */
    private function isAccountNumberIsExist(int $accountNumber) :void {
       $cleanAcctNumber = trim((string)$accountNumber);

       // Debug: Log some actual records from the database
        Log::debug("Sample wallet records:", [
            'first_record' => Wallets::first()->account_number ?? null,
            'random_record' => Wallets::inRandomOrder()->first()->account_number ?? null
        ]);
        // \Log::debug("BankAPI DB Check", [
        //     'env_db' => env('DB_DATABASE'),
        //     'queried_db' => DB::connection()->getDatabaseName(),
        //     'wallets' => DB::select('SELECT DATABASE() as current_db')
        // ]);
       
        $query = Wallets::where('account_number',$cleanAcctNumber)
            ->exists();
        if(!$query){
            throw new Exception('Account Number not found!');
        }
    } 
   
}

?>