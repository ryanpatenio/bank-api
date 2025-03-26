<?php
// app/Exceptions/Transactions/TransactionException.php
namespace App\Exceptions\Transactions;

use Exception;

class TransactionException extends Exception
{
    protected $code = 400; // Default HTTP status code
    protected $details = [];

    public function __construct(string $message, int $code = 400, array $details = [])
    {
        parent::__construct($message, $code);
        $this->details = $details;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}