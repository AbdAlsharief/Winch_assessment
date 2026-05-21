<?php

namespace Src\Domain\OrderDomain\Exceptions;

use Exception;

class OrderNotFoundException extends Exception
{
    public function __construct(int $orderId)
    {
        parent::__construct("Order with ID {$orderId} not found.");
    }
}
