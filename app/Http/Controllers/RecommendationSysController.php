<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RecommendationSysController extends Controller
{
    /**
     * Handle the general recommendation request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function general(Request $request)
    {
        try {
            // Determine if the user is authenticated
            if (Auth::guard('sanctum')->check()) {
                $userId = Auth::guard('sanctum')->id();
                $aiEndpoint = '/recommend/user/' . $userId;
            } else {
                $aiEndpoint = '/recommend/general';
            }
            // Construct the full AI API URL
            $aiUrl = config('services.ai.base_url') . $aiEndpoint;

            // Make a GET request to the AI API
            $response = Http::timeout(60)->get($aiUrl);
          
            // Check if the AI API request was successful
            if ($response->successful()) {
                $recommendationIds = $response->json('recommendations', []);

                // Fetch product details based on the recommended IDs
                $products = Product::whereIn('id', $recommendationIds)->get();

                return response()->json([
                    'success' => true,
                    'recommendations' => $products,
                ], 200);
            } else {
                Log::error('AI API General Recommendation Failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve recommendations from AI system.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception in General Recommendation', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.',
            ], 500);
        }
    }

    /**
     * Handle the similar recommendation request.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $product_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function similar(Request $request, $product_id)
    {
        try {
            // Validate the product ID
            if (!is_numeric($product_id) || $product_id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid product ID.',
                ], 400);
            }

            // Determine if the user is authenticated
            if (Auth::guard('sanctum')->check()) {
                $userId = Auth::guard('sanctum')->id();
                $aiEndpoint = '/recommend/hybrid/' . $userId . '/' . $product_id;
            } else {
                $aiEndpoint = '/recommend/similar/' . $product_id;
            }

            // Construct the full AI API URL
            $aiUrl = config('services.ai.base_url') . $aiEndpoint;

            // Make a GET request to the AI API
            $response = Http::timeout(60)->get($aiUrl);

            // Check if the AI API request was successful
            if ($response->successful()) {
                $recommendationIds = $response->json('recommendations', []);

                // Fetch product details based on the recommended IDs
                $products = Product::whereIn('id', $recommendationIds)->get();

                return response()->json([
                    'success' => true,
                    'recommendations' => $products,
                ], 200);
            } else {
                Log::error('AI API Similar Recommendation Failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve recommendations from AI system.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception in Similar Recommendation', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.',
            ], 500);
        }
    }
}
