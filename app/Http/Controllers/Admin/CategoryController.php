<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'data' => $categories
        ], 200);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|unique:categories,title|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create category
        $category = Category::create([
            'title' => $request->title,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified category.
     */
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category
        ], 200);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|unique:categories,title,' . $id . '|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update category
        $category->update([
            'title' => $request->title,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'data' => $category
        ], 200);
    }

    /**
     * Remove the specified category.
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.'
        ], 200);
    }
}
