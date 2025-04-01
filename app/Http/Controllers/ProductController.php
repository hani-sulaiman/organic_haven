<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\InteractionLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected $interactionLogger;

    public function __construct(InteractionLogger $interactionLogger)
    {
        $this->interactionLogger = $interactionLogger;
    }

    /**
     * Display the specified product details by ID.
     */
    public function show($id)
    {
        if (!ctype_digit($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product ID.',
            ], 400);
        }

        $cacheKey = 'product_' . $id;
        $product = Cache::remember($cacheKey, 60, function () use ($id) {
            return Product::with('category')->find($id);
        });

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        // Initialize the in_wishlist flag to false.
        $inWishlist = false;
        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
            // Check if the product exists in the user's wishlist.
            $inWishlist = $user->wishlist()->where('product_id', $product->id)->exists();
        }
        // Append the flag to the product object.
        $product->in_wishlist = $inWishlist;

        // Log view interaction if desired...
        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
            $this->interactionLogger->log([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'category_id' => $product->category_id,
                'interaction_type' => 'view',
                'date' => now()->toDateTimeString(),
                'gender' => $user->gender ?? null,
                'age' => $user->birthdate ? abs(intval(now()->diffInYears($user->birthdate))) : null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
        ], 200);
    }
    public function get_categories(Request $request)
    {
        $categories = Category::all();
        return response()->json([
            'success' => true,
            'categories' => $categories
        ], 200);
    }
    public function productsByCategory(Request $request, $id)
    {
        // First, check that the category exists
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        // Retrieve optional parameters for sorting and pagination
        $sortBy = $request->input('sort_by', 'title'); // default: sort by title
        $sortOrder = $request->input('sort_order', 'asc'); // default: ascending
        $perPage = $request->input('per_page', 20); // default: 20 products per page
        $currentPage = $request->input('page', 1);

        // Generate a unique cache key based on category and query parameters
        $cacheKey = 'products_category_' . $id . '_sortby_' . $sortBy . '_order_' . $sortOrder . '_perPage_' . $perPage . '_page_' . $currentPage;

        // Retrieve the products from cache or query the database if not cached
        $products = Cache::remember($cacheKey, 60, function () use ($id, $sortBy, $sortOrder, $perPage) {
            return Product::where('category_id', $id)
                ->orderBy($sortBy, $sortOrder)
                ->paginate($perPage);
        });

        return response()->json([
            'success' => true,
            'data'    => $products,
        ], 200);
    }
}
