<?php


namespace App\Utilities;

use App\Exceptions\Transactions\{
    InsufficientFundsException,
    DuplicateTransactionException
};

class TransactionThrower
{
    public static function insufficientFunds(float $balance, float $amount): void
    {
        throw new InsufficientFundsException($balance, $amount);
    }

    public static function duplicate(string $transactionId): void
    {
        throw new DuplicateTransactionException($transactionId);
    }
}