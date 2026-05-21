<?php

namespace Src\Presentation\Cpanel\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Src\Domain\DriverDomain\Models\Driver;
use Src\Domain\OrderDomain\Enums\OrderStatus;
use Src\Presentation\Cpanel\Resources\OrderResource;

class DriverOrdersController extends Controller
{
    /**
     * Get all orders for a specific driver with optional filtering and pagination.
     *
     * @param Request $request
     * @param int $id The driver ID
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(Request $request, int $id): AnonymousResourceCollection|JsonResponse
    {
        // Find the driver
        $driver = Driver::find($id);

        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver not found.',
            ], 404);
        }

        // Start building the query
        $query = $driver->orders();

        // Apply status filter if provided
        if ($request->has('status')) {
            $status = $request->input('status');
            
            // Validate status value
            if (!in_array($status, OrderStatus::values())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status value.',
                    'valid_statuses' => OrderStatus::values(),
                ], 400);
            }

            $query->where('status', $status);
        }

        // Apply sorting (newest first by default)
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        
        $query->orderBy($sortBy, $sortOrder);

        // Get pagination parameters
        $perPage = min((int) $request->input('per_page', 15), 100); // Max 100 per page

        // Paginate results
        $orders = $query->paginate($perPage);

        // Return paginated resource collection
        return OrderResource::collection($orders);
    }

    /**
     * Get active orders for a specific driver (pending or assigned).
     *
     * @param Request $request
     * @param int $id The driver ID
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function active(Request $request, int $id): AnonymousResourceCollection|JsonResponse
    {
        $driver = Driver::find($id);

        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver not found.',
            ], 404);
        }

        // Get active orders using the scope
        $perPage = min((int) $request->input('per_page', 15), 100);
        
        $orders = $driver->orders()
            ->active()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return OrderResource::collection($orders);
    }

    /**
     * Get order statistics for a driver.
     *
     * @param int $id The driver ID
     * @return JsonResponse
     */
    public function statistics(int $id): JsonResponse
    {
        $driver = Driver::find($id);

        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver not found.',
            ], 404);
        }

        $statistics = [
            'driver_id' => $driver->id,
            'driver_name' => $driver->name,
            'total_orders' => $driver->orders()->count(),
            'pending_orders' => $driver->orders()->pending()->count(),
            'assigned_orders' => $driver->orders()->assigned()->count(),
            'completed_orders' => $driver->orders()->completed()->count(),
            'active_orders' => $driver->orders()->active()->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }
}
