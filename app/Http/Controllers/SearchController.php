<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    /**
     * Handle search requests.
     */
    public function search(Request $request)
    {
        // Validate input parameters
        $validator = Validator::make($request->all(), [
            'query' => 'nullable|string|max:255',
            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id',
            'sort_by' => 'nullable|in:price,title',
            'sort_order' => 'nullable|in:asc,desc',
            'unit_types' => 'nullable|array',
            'unit_types.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid search parameters.',
                'errors' => $validator->errors()
            ], 422);
        }
        // Retrieve query parameters
        $query = $request->input('query'); // Search keyword
        $categoryIds = $request->input('categories'); // Array of category IDs
        $sortBy = $request->input('sort_by', 'title'); // 'price' or 'title'
        $sortOrder = $request->input('sort_order', 'asc'); // 'asc' or 'desc'
        $unitTypes = $request->input('unit_types'); // Array of unit types

        // Generate a unique cache key based on query parameters
        $cacheKey = 'search_' . md5(json_encode([
            'query' => $query,
            'categories' => $categoryIds,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'unit_types' => $unitTypes,
        ]));

        // Attempt to retrieve from cache
        $products = Cache::remember($cacheKey, 60, function () use ($query, $categoryIds, $sortBy, $sortOrder, $unitTypes) {
            $productQuery = Product::with('category');

            // Search by query
            if ($query) {
                $productQuery->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                        ->orWhere('ingredients', 'LIKE', "%{$query}%");
                });
            }

            // Filter by categories
            if ($categoryIds && is_array($categoryIds)) {
                $productQuery->whereIn('category_id', $categoryIds);
            }

            // Filter by unit types
            if ($unitTypes && is_array($unitTypes)) {
                $productQuery->whereIn('unit_type', $unitTypes);
            }

            // Sorting
            if (in_array($sortBy, ['price', 'title']) && in_array($sortOrder, ['asc', 'desc'])) {
                $productQuery->orderBy($sortBy, $sortOrder);
            } else {
                // Default sorting
                $productQuery->orderBy('title', 'asc');
            }

            return $productQuery->paginate(20); // Adjust pagination as needed
        });

        return response()->json([
            'success' => true,
            'data' => $products
        ], 200);
    }
}
