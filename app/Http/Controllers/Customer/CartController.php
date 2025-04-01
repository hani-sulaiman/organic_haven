<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Services\InteractionLogger;
class CartController extends Controller
{

    protected $interactionLogger;

    public function __construct(InteractionLogger $interactionLogger)
    {
        $this->interactionLogger = $interactionLogger;
    }
    /**
     * Display the customer's cart.
     */
    public function index()
    {
        $user = Auth::user();

        // Define cache key unique to the user
        $cacheKey = 'cart_user_' . $user->id;

        // Attempt to retrieve cart from cache
        $cart = Cache::remember($cacheKey, 60, function () use ($user) {
            return $user->cartItems()->with('product')->get();
        });

        return response()->json([
            'success' => true,
            'data' => $cart
        ], 200);
    }

    /**
     * Add a product to the cart.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validate input
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors.',
                'errors' => $validator->errors()
            ], 422);
        }

        $product_id = $request->product_id;
        $quantity = $request->quantity;
        $product = Product::find($product_id);
        // Check if the product is already in the cart
        $cartItem = CartItem::where('user_id', $user->id)
                            ->where('product_id', $product_id)
                            ->first();

        if ($cartItem) {
            // Update quantity
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Create new cart item
            $cartItem = CartItem::create([
                'user_id' => $user->id,
                'product_id' => $product_id,
                'quantity' => $quantity,
            ]);
        }

        // Invalidate cache
        $cacheKey = 'cart_user_' . $user->id;
        Cache::forget($cacheKey);

        // Retrieve updated cart
        $updatedCart = $user->cartItems()->with('product')->get();

        $this->interactionLogger->log([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'category_id' => $product->category_id,
            'interaction_type' => 'add_to_cart',
            'date' => now()->toDateTimeString(),
            'gender' => $user->gender ?? null,
                'age' => $user->birthdate
                    ?  abs(intval(now()->diffInYears($user->birthdate)))
                    : null,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully.',
            'data' => $updatedCart
        ], 200);
    }

    /**
     * Update the quantity of a cart item.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Validate input
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors.',
                'errors' => $validator->errors()
            ], 422);
        }

        $quantity = $request->quantity;

        // Find the cart item
        $cartItem = CartItem::where('id', $id)
                            ->where('user_id', $user->id)
                            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.'
            ], 404);
        }

        // Update quantity
        $cartItem->quantity = $quantity;
        $cartItem->save();

        // Invalidate cache
        $cacheKey = 'cart_user_' . $user->id;
        Cache::forget($cacheKey);

        // Retrieve updated cart
        $updatedCart = $user->cartItems()->with('product')->get();

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated successfully.',
            'data' => $updatedCart
        ], 200);
    }

    /**
     * Remove a cart item.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        // Find the cart item
        $cartItem = CartItem::where('id', $id)
                            ->where('user_id', $user->id)
                            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.'
            ], 404);
        }

        // Delete the cart item
        $cartItem->delete();

        // Invalidate cache
        $cacheKey = 'cart_user_' . $user->id;
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Cart item removed successfully.'
        ], 200);
    }

    /**
     * Clear the entire cart.
     */
    public function clearCart()
    {
        $user = Auth::user();

        // Delete all cart items for the user
        $user->cartItems()->delete();

        // Invalidate cache
        $cacheKey = 'cart_user_' . $user->id;
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully.'
        ], 200);
    }
}
