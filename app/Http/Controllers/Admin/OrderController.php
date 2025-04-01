<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a paginated list of all orders.
     */
    public function index(Request $request)
    {
        // Retrieve query parameters for pagination and filtering if needed
        $perPage = $request->input('per_page', 20); // Default to 20 per page

        // Generate a unique cache key based on query parameters
        $cacheKey = 'admin_orders_page_' . $request->input('page', 1) . '_per_page_' . $perPage;

        // Attempt to retrieve from cache
        $orders = Cache::remember($cacheKey, 60, function () use ($perPage) {
            return Order::with('user', 'orderItems.product')
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage);
        });

        return response()->json([
            'success' => true,
            'data' => $orders
        ], 200);
    }

    /**
     * Display the specified order details.
     */
    public function show($id)
    {
        // Validate that $id is a positive integer
        if (!ctype_digit($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order ID.',
            ], 400);
        }

        // Generate cache key
        $cacheKey = 'admin_order_' . $id;

        // Attempt to retrieve from cache
        $order = Cache::remember($cacheKey, 60, function () use ($id) {
            return Order::with('user', 'orderItems.product')->find($id);
        });

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
        ], 200);
    }

    /**
     * Update the status of the specified order.
     */
    public function update(Request $request, $id)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,paid,shipped,delivered,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status value.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate that $id is a positive integer
        if (!ctype_digit($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order ID.',
            ], 400);
        }

        // Find the order
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        // Update the status
        $order->status = $request->input('status');
        $order->save();

        // Invalidate cache
        Cache::forget('admin_order_' . $id);
        Cache::forget('admin_orders_page_' . $request->input('page', 1) . '_per_page_' . $request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.',
            'data' => $order,
        ], 200);
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy($id)
    {
        // Validate that $id is a positive integer
        if (!ctype_digit($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order ID.',
            ], 400);
        }

        // Find the order
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        // Delete the order
        $order->delete();

        // Invalidate cache
        Cache::forget('admin_order_' . $id);
        // Optionally, clear all orders cache or implement a more granular invalidation
        // For simplicity, we'll clear all admin orders cache
        Cache::flush(); // Use with caution in production

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully.',
        ], 200);
    }
    public function generateBill($id)
    {
        // Validate that $id is a positive integer
        if (!ctype_digit($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order ID.',
            ], 400);
        }

        // Generate cache key
        $cacheKey = 'admin_order_' . $id;

        // Attempt to retrieve from cache
        $order = Cache::remember($cacheKey, 60, function () use ($id) {
            return Order::with('user', 'orderItems.product')->find($id);
        });

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        // Pass the order data to the bill.blade.php view
        return view('admin.orders.bill', compact('order'));
    }
}
