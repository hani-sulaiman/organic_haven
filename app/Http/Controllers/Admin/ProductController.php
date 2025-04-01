<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index()
    {
        // Eager load category
        $products = Product::with('category')->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ], 200);
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'ingredients' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'unit_type'   => 'required|in:lb,oz,ea,ct',
            'unit_value'  => 'required|numeric|min:0.01',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors.',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Handle Image Upload
        $img_url = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            // Generate a unique filename
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            // Define the destination path in the public folder
            $destinationPath = public_path('uploads/products');
            // Move the file to the destination path
            $image->move($destinationPath, $filename);
            // Build the URL relative to your public directory
            $img_url = '/uploads/products/' . $filename;
        }

        // Create product
        $product = Product::create([
            'title'       => $request->title,
            'ingredients' => $request->ingredients,
            'category_id' => $request->category_id,
            'price'       => $request->price,
            'unit_type'   => $request->unit_type,
            'unit_value'  => $request->unit_value,
            'img_url'     => $img_url,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data'    => $product
        ], 201);
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $product
        ], 200);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'title'       => 'nullable|string|max:255',
            'ingredients' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'price'       => 'nullable|numeric|min:0',
            'unit_type'   => 'nullable|in:lb,oz,ea,ct',
            'unit_value'  => 'nullable|numeric|min:0.01',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors.',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Handle Image Upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->img_url && file_exists(public_path($product->img_url))) {
                unlink(public_path($product->img_url));
            }

            $image = $request->file('image');
            // Generate a unique filename
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/products');
            $image->move($destinationPath, $filename);
            $img_url = '/uploads/products/' . $filename;

            $product->img_url = $img_url;
        }

        // Update only the provided fields
        $updateData = array_filter($request->only([
            'title',
            'ingredients',
            'category_id',
            'price',
            'unit_type',
            'unit_value'
        ]), function ($value) {
            return !is_null($value);
        });

        $product->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data'    => $product
        ], 200);
    }

    /**
     * Remove the specified product.
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        // Delete image if exists
        if ($product->img_url && file_exists(public_path($product->img_url))) {
            unlink(public_path($product->img_url));
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ], 200);
    }
}
