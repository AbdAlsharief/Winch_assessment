<?php

namespace Src\Domain\OrderDomain\Exceptions;

use Exception;

class NoAvailableDriverException extends Exception
{
    public function __construct(int $orderId, ?float $searchRadius = null)
    {
        $message = "No available driver found for order ID {$orderId}";
        
        if ($searchRadius) {
            $message .= " within {$searchRadius}km radius";
        }
        
        $message .= ".";
        
        parent::__construct($message);
    }
}
