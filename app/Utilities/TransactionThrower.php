<?php


namespace App\Utilities;

use App\Exceptions\Transactions\{
    InsufficientFundsException,
    DuplicateTransactionsExceptions
};

class TransactionThrower
{
    public static function insufficientFunds(float $balance, float $amount): void
    {
        if($balance < $amount){
            throw new InsufficientFundsException($balance, $amount);
        }
        
    }

    public static function duplicate(string $transactionId): void
    {
        throw new DuplicateTransactionsExceptions($transactionId);
    }
}