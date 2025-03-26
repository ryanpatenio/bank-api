<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\Transactions;
use Exception;

class TransactionServices {


    public function processCredit(array $data){
        $this->validateRequest($data);


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
     * @return bool
     * @throws \InvalidArgumentException --catch
     */
    public function isTransactionExist(int $wallet_id, string $client_ref): bool{
        // Validate input parameters
        if ($wallet_id <= 0 || empty(trim($client_ref))) {
            throw new \InvalidArgumentException('Wallet ID and client reference are required and must be valid.');
        }

        return Transactions::where('wallet_id', $wallet_id)
            ->where('client_ref_id', $client_ref)
            ->exists();
    }

}

?>