<?php

namespace Src\Presentation\Cpanel\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Src\Domain\OrderDomain\Models\Order;
use Src\Domain\OrderDomain\Enums\OrderStatus;
use Src\Presentation\Cpanel\Resources\OrderResource;

class OrdersController extends Controller
{
    /**
     * Get all orders with optional filtering and pagination.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Order::with('driver');

        // Apply status filter if provided
        if ($request->has('status')) {
            $status = $request->input('status');
            
            if (in_array($status, OrderStatus::values())) {
                $query->where('status', $status);
            }
        }

        // Apply sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        
        $query->orderBy($sortBy, $sortOrder);

        // Get pagination parameters
        $perPage = min((int) $request->input('per_page', 15), 100);

        // Paginate results
        $orders = $query->paginate($perPage);

        return OrderResource::collection($orders);
    }

    /**
     * Get a single order by ID.
     *
     * @param int $id
     * @return OrderResource|\Illuminate\Http\JsonResponse
     */
    public function show(int $id): OrderResource|\Illuminate\Http\JsonResponse
    {
        $order = Order::with('driver')->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        return new OrderResource($order);
    }
}
