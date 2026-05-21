<?php

namespace Src\Domain\OrderDomain\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\Domain\OrderDomain\Contracts\OrderAssignmentContract;
use Src\Domain\OrderDomain\Enums\OrderStatus;
use Src\Domain\OrderDomain\Exceptions\NoAvailableDriverException;
use Src\Domain\OrderDomain\Exceptions\OrderAlreadyAssignedException;
use Src\Domain\OrderDomain\Exceptions\OrderNotFoundException;
use Src\Domain\OrderDomain\Models\Order;
use Src\Domain\DriverDomain\Contracts\DriverLocationContract;
use Src\Domain\DriverDomain\Enums\DriverStatus;
use Src\Domain\DriverDomain\Models\Driver;

class OrderAssignmentService implements OrderAssignmentContract
{
    public function __construct(
        private readonly DriverLocationContract $driverLocationService
    ) {}

    /**
     * Assign an order to the best available driver.
     *
     * Uses pessimistic locking to prevent race conditions in high-concurrency scenarios.
     * The entire operation is wrapped in a database transaction to ensure atomicity.
     *
     * @param int $orderId The ID of the order to assign
     * @return Order The assigned order with driver relationship loaded
     * @throws OrderNotFoundException If the order doesn't exist
     * @throws OrderAlreadyAssignedException If the order is already assigned
     * @throws NoAvailableDriverException If no available driver is found
     */
    public function assignToBestDriver(int $orderId): Order
    {
        return DB::transaction(function () use ($orderId) {
            // Step 1: Fetch the order with pessimistic lock to prevent concurrent modifications
            $order = Order::lockForUpdate()->find($orderId);

            if (!$order) {
                throw new OrderNotFoundException($orderId);
            }

            // Step 2: Validate order can be assigned
            if (!$order->isPending()) {
                throw new OrderAlreadyAssignedException($orderId, $order->driver_id);
            }

            // Step 3: Find the nearest available driver
            $driver = $this->driverLocationService->findNearestAvailableDriver(
                latitude: (float) $order->pickup_latitude,
                longitude: (float) $order->pickup_longitude,
                maxDistanceKm: null // No radius limit, can be configured
            );

            if (!$driver) {
                Log::warning("No available driver found for order", [
                    'order_id' => $orderId,
                    'pickup_latitude' => $order->pickup_latitude,
                    'pickup_longitude' => $order->pickup_longitude,
                ]);
                
                throw new NoAvailableDriverException($orderId);
            }

            // Step 4: Lock the driver record to prevent concurrent assignments
            $driver = Driver::lockForUpdate()->find($driver->id);

            // Step 5: Double-check driver is still available (race condition protection)
            if (!$driver || !$driver->isAvailable()) {
                Log::warning("Driver became unavailable during assignment", [
                    'order_id' => $orderId,
                    'driver_id' => $driver?->id,
                ]);
                
                throw new NoAvailableDriverException($orderId);
            }

            // Step 6: Perform the assignment atomically
            $order->status = OrderStatus::Assigned;
            $order->driver_id = $driver->id;
            $order->save();

            $driver->status = DriverStatus::Busy;
            $driver->save();

            // Step 7: Log successful assignment
            Log::info("Order assigned successfully", [
                'order_id' => $orderId,
                'driver_id' => $driver->id,
                'driver_name' => $driver->name,
                'distance' => $driver->distance ?? null,
            ]);

            // Step 8: Load the driver relationship and return
            return $order->load('driver');
        });
    }

    /**
     * Assign multiple orders to best available drivers in batch.
     *
     * @param array<int> $orderIds Array of order IDs to assign
     * @return array{assigned: array<Order>, failed: array{order_id: int, reason: string}}
     */
    public function assignMultipleOrders(array $orderIds): array
    {
        $assigned = [];
        $failed = [];

        foreach ($orderIds as $orderId) {
            try {
                $assigned[] = $this->assignToBestDriver($orderId);
            } catch (OrderNotFoundException $e) {
                $failed[] = [
                    'order_id' => $orderId,
                    'reason' => 'Order not found',
                ];
            } catch (OrderAlreadyAssignedException $e) {
                $failed[] = [
                    'order_id' => $orderId,
                    'reason' => 'Order already assigned',
                ];
            } catch (NoAvailableDriverException $e) {
                $failed[] = [
                    'order_id' => $orderId,
                    'reason' => 'No available driver',
                ];
            }
        }

        return [
            'assigned' => $assigned,
            'failed' => $failed,
        ];
    }

    /**
     * Unassign an order from its driver.
     *
     * @param int $orderId The ID of the order to unassign
     * @return Order The unassigned order
     * @throws OrderNotFoundException If the order doesn't exist
     */
    public function unassignOrder(int $orderId): Order
    {
        return DB::transaction(function () use ($orderId) {
            $order = Order::lockForUpdate()->find($orderId);

            if (!$order) {
                throw new OrderNotFoundException($orderId);
            }

            if ($order->isPending() || $order->isCompleted()) {
                return $order;
            }

            $driverId = $order->driver_id;

            // Unassign the order
            $order->status = OrderStatus::Pending;
            $order->driver_id = null;
            $order->save();

            // Mark driver as available if they have no other active orders
            if ($driverId) {
                $driver = Driver::lockForUpdate()->find($driverId);
                
                if ($driver) {
                    $hasActiveOrders = $driver->orders()
                        ->whereIn('status', [OrderStatus::Pending, OrderStatus::Assigned])
                        ->exists();

                    if (!$hasActiveOrders) {
                        $driver->status = DriverStatus::Available;
                        $driver->save();
                    }
                }
            }

            Log::info("Order unassigned successfully", [
                'order_id' => $orderId,
                'driver_id' => $driverId,
            ]);

            return $order->fresh();
        });
    }
}
