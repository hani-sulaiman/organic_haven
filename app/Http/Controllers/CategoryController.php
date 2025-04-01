<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories.
     */
    public function index()
    {
        $cacheKey = 'categories_all';
        $categories = Cache::remember($cacheKey, 60, function () {
            // Add withCount('products') to get the number of products per category
            return Category::withCount('products')->orderBy('title', 'asc')->get();
        });

        return response()->json([
            'success' => true,
            'data' => $categories,
        ], 200);
    }
}
