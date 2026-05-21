<?php

namespace Src\Domain\OrderDomain\Exceptions;

use Exception;

class OrderAlreadyAssignedException extends Exception
{
    public function __construct(int $orderId, ?int $driverId = null)
    {
        $message = "Order with ID {$orderId} is already assigned";
        
        if ($driverId) {
            $message .= " to driver ID {$driverId}";
        }
        
        $message .= ".";
        
        parent::__construct($message);
    }
}
