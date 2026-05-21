<?php

namespace Src\Domain\OrderDomain\Contracts;

use Src\Domain\OrderDomain\Models\Order;

interface OrderAssignmentContract
{
    /**
     * Assign an order to the best available driver.
     *
     * @param int $orderId The ID of the order to assign
     * @return Order The assigned order with driver relationship loaded
     * @throws \Src\Domain\OrderDomain\Exceptions\OrderNotFoundException
     * @throws \Src\Domain\OrderDomain\Exceptions\OrderAlreadyAssignedException
     * @throws \Src\Domain\OrderDomain\Exceptions\NoAvailableDriverException
     */
    public function assignToBestDriver(int $orderId): Order;
}
