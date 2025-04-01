<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\InteractionLogger;
class OrderController extends Controller
{
    protected $interactionLogger;

    public function __construct(InteractionLogger $interactionLogger)
    {
        $this->interactionLogger = $interactionLogger;
    }
    /**
     * Create an order from the user's cart.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Retrieve cart items
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty.',
            ], 400);
        }

        // Calculate subtotal
        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        // Calculate tax (5%)
        $tax = round($subtotal * 0.05, 2);

        // Calculate total
        $total = round($subtotal + $tax ,2);

        // Start Transaction
        DB::beginTransaction();

        try {
            // Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);

            // Create Order Items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product->id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                    'total' => $cartItem->product->price * $cartItem->quantity,
                ]);
                $this->interactionLogger->log([
                    'user_id' => $user->id,
                    'product_id' => $cartItem->product->id,
                    'category_id' => $cartItem->product->category_id,
                    'interaction_type' => 'ordered',
                    'date' => now()->toDateTimeString(),
                    'gender' => $user->gender ?? null,
                    'age' => $user->birthdate
                    ?  abs(intval(now()->diffInYears($user->birthdate)))
                    : null,
                ]);
            }

            // Clear Cart
            $user->cartItems()->delete();

            // Invalidate Cart Cache
            $cacheKey = 'cart_user_' . $user->id;
            Cache::forget($cacheKey);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully.',
                'data' => $order->load('orderItems.product'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            // Optionally log the error
            // \Log::error('Order creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order. Please try again.',
            ], 500);
        }
    }
    public function pendingOrder(Request $request)
    {
        $user = Auth::user();
        // Retrieve the latest order with status 'pending' and include order items and products.
        $order = $user->orders()
                      ->where('status', 'pending')
                      ->with('orderItems.product')
                      ->latest()
                      ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'No pending order found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $order
        ], 200);
    }
    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        $user = Auth::user();

        // Attempt to retrieve orders from cache
        $cacheKey = 'orders_user_' . $user->id;
        $orders = Cache::remember($cacheKey, 60, function () use ($user) {
            return $user->orders()->with('orderItems.product')->orderBy('created_at', 'desc')->get();
        });

        return response()->json([
            'success' => true,
            'data' => $orders,
        ], 200);
    }

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        $user = Auth::user();

        // Attempt to retrieve order from cache
        $cacheKey = 'order_user_' . $user->id . '_order_' . $id;
        $order = Cache::remember($cacheKey, 60, function () use ($user, $id) {
            return $user->orders()->with('orderItems.product')->find($id);
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

    public function updateStatus(Request $request, $id)
    {
        // Optional: Implement if customers can update order status.
    }
    public function destroy($id)
    {
        // Optional: Implement if necessary.
    }
}
