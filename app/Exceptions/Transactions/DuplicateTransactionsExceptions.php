<?php

namespace App\Exceptions\Transactions;

class DuplicateTransactionException extends TransactionException
{
    public function __construct(string $transactionId)
    {
        parent::__construct(
            message: sprintf('Duplicate transaction ID: %s', $transactionId),
            code: 409, // HTTP 409 Conflict
            details: ['transaction_id' => $transactionId]
        );
    }
}