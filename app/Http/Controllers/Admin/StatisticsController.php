<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\RecommendationsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StatisticsController extends Controller
{
    /**
     * Display a listing of the statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // 1. Count of Users
            $userCount = User::count();

            // 2. Count of Products
            $productCount = Product::count();

            // 3. Count of Categories
            $categoryCount = Category::count();

            // 4. Count of Orders This Month
            $ordersThisMonth = Order::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

            // 5. Count of Orders This Year
            $ordersThisYear = Order::whereYear('created_at', now()->year)->count();

            // 6. Total Count of Orders
            $ordersAll = Order::count();

            // 7. Count of Interactions in training_data.csv
            $interactionsCount = $this->countInteractions();

            // 8. Count of Data Trained at Last Training
            $lastTraining = RecommendationsModel::orderBy('trained_at', 'desc')->first();
            $lastDataTrainedCount = $lastTraining ? $lastTraining->count_data_trained : 0;
            $lastTrainedAt = $lastTraining ? $lastTraining->trained_at : null;

            return response()->json([
                'success' => true,
                'data' => [
                    'user_count' => $userCount,
                    'product_count' => $productCount,
                    'category_count' => $categoryCount,
                    'orders_this_month' => $ordersThisMonth,
                    'orders_this_year' => $ordersThisYear,
                    'orders_all' => $ordersAll,
                    'interactions_count' => $interactionsCount,
                    'last_data_trained_count' => $lastDataTrainedCount,
                    'last_trained_at' => $lastTrainedAt,
                ]
            ], 200);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Failed to retrieve statistics: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics. Please try again later.',
            ], 500);
        }
    }

    /**
     * Count the number of interactions in training_data.csv.
     *
     * @return int
     */
    protected function countInteractions()
    {
        $filePath = 'interactions/training_data.csv';
        $count = 0;

        if (Storage::exists($filePath)) {
            // Read the file line by line to handle large files efficiently
            $handle = Storage::readStream($filePath);
            if ($handle) {
                $lineNumber = 0;
                while (($line = fgets($handle)) !== false) {
                    $lineNumber++;
                    // Skip the header row
                    if ($lineNumber > 1) {
                        $count++;
                    }
                }
                fclose($handle);
            } else {
                Log::error("Unable to open the file: {$filePath}");
            }
        } else {
            Log::warning("Interactions file does not exist: {$filePath}");
        }

        return $count;
    }
}
