<?php

namespace App\Exceptions\Transactions;

class InsufficientFundsException extends TransactionException
{
    public function __construct(float $balance, float $amount)
    {
        parent::__construct(
            message: sprintf(
                'Insufficient funds. Tried to debit %.2f (available: %.2f)', 
                $amount, 
                $balance
            ),
            code: 422, // HTTP 422 Unprocessable Entity
            details: [
                'balance' => $balance,
                'required' => $amount,
            ]
        );
    }
}