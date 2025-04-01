<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
class WishlistController extends Controller
{
    /**
     * Add a product to the authenticated user's wishlist.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToWishlist($id)
    {
        if (!ctype_digit($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product ID.',
            ], 400);
        }


        $user = Auth::user();

        // Check if the product is already in the wishlist
        $existing = Wishlist::where('user_id', $user->id)
                            ->where('product_id', $id)
                            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in your wishlist.',
            ], 409); // 409 Conflict
        }

        // Add to wishlist
        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist successfully.',
        ], 201);
    }

    /**
     * Remove a product from the authenticated user's wishlist.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $product_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFromWishlist(Request $request, $product_id)
    {
        $user = Auth::user();

        // Find the wishlist item
        $wishlistItem = Wishlist::where('user_id', $user->id)
                                ->where('product_id', $product_id)
                                ->first();

        if (!$wishlistItem) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in your wishlist.',
            ], 404);
        }

        // Delete the wishlist item
        $wishlistItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist successfully.',
        ], 200);
    }

    /**
     * Get the authenticated user's wishlist.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWishlist()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }
    
        // Retrieve wishlist items with product details
        $wishlistItems = Wishlist::with('product')
            ->where('user_id', $user->id)
            ->get();
    
        if ($wishlistItems->isEmpty()) {
            return response()->json([
                'success' => true,
                'wishlist' => [],
                'message' => 'No items in the wishlist.'
            ], 200);
        }
    
        // Transform the data to include product details safely
        $wishlist = $wishlistItems->map(function ($item) {
            if (!$item->product) {
                return null; // Skip orphaned wishlist items
            }
    
            return [
                'id' => $item->product->id,
                'title' => $item->product->title,
                'ingredients' => $item->product->ingredients,
                'price' => $item->product->price,
                'unit_type' => $item->product->unit_type,
                'unit_value' => $item->product->unit_value,
                'image_url' => $item->product->img_url, // Adjusted field name
            ];
        })->filter(); // Removes null entries
    
        return response()->json([
            'success' => true,
            'wishlist' => $wishlist->values(), // Reset array keys
        ], 200);
    }
    
}
