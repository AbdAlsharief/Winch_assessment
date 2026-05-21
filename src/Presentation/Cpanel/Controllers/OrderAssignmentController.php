<?php

namespace Src\Presentation\Cpanel\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Domain\OrderDomain\Contracts\OrderAssignmentContract;
use Src\Domain\OrderDomain\Exceptions\NoAvailableDriverException;
use Src\Domain\OrderDomain\Exceptions\OrderAlreadyAssignedException;
use Src\Domain\OrderDomain\Exceptions\OrderNotFoundException;
use Src\Presentation\Cpanel\Resources\OrderResource;

class OrderAssignmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param OrderAssignmentContract $orderAssignment
     */
    public function __construct(
        private readonly OrderAssignmentContract $orderAssignment
    ) {}

    /**
     * Assign an order to the best available driver.
     *
     * @param int $id The order ID
     * @return OrderResource|JsonResponse
     */
    public function assign(int $id): OrderResource|JsonResponse
    {
        try {
            $order = $this->orderAssignment->assignToBestDriver($id);

            return new OrderResource($order);

        } catch (OrderNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
                'error' => $e->getMessage(),
            ], 404);

        } catch (OrderAlreadyAssignedException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order is already assigned.',
                'error' => $e->getMessage(),
            ], 409);

        } catch (NoAvailableDriverException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No available driver found.',
                'error' => $e->getMessage(),
            ], 503);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Unassign an order from its driver.
     *
     * @param int $id The order ID
     * @return OrderResource|JsonResponse
     */
    public function unassign(int $id): OrderResource|JsonResponse
    {
        try {
            $order = $this->orderAssignment->unassignOrder($id);

            return new OrderResource($order);

        } catch (OrderNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
                'error' => $e->getMessage(),
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}
