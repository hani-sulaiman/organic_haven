<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RecommendationsModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Jobs\TrainRecommendationModel;
use Carbon\Carbon;

class RecommendationController extends Controller
{
    /**
     * Retrain the recommendation model.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function train(Request $request)
    {
        // Optional: Increase PHP's execution time and memory limit
        ini_set('max_execution_time', 0); // Unlimited execution time
        ini_set('memory_limit', '512M');  // Increase memory limit as needed

        try {
            // Check if training has already been done today
            $today = Carbon::today(); // Start of today based on app timezone
            $existingTraining = RecommendationsModel::whereDate('trained_at', $today)->first();

            if ($existingTraining) {
                return response()->json([
                    'success' => false,
                    'message' => 'Model has already been trained today.',
                    'trained_at' => $existingTraining->trained_at
                ], 429); // 429 Too Many Requests
            }

            // Path to the training_data.csv file
            $filePath = storage_path('app/private/interactions/training_data.csv');

            // Check if the file exists
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Training data file not found.',
                ], 404);
            }

            // Prepare the file for upload
            $fileContents = file_get_contents($filePath);
            $fileName = 'training_data.csv';

            // Send POST request to AI API with the CSV file
            $response = Http::timeout(0) // Remove timeout for the HTTP client
                ->attach(
                    'file', $fileContents, $fileName
                )
                ->post(config('services.ai.base_url') . '/train');

            // Check if the request was successful
            if ($response->successful()) {
                // Count the number of data lines (excluding header)
                $countDataTrained = $this->countCsvLines($filePath) - 1;

                // Record the training in the database
                RecommendationsModel::create([
                    'count_data_trained' => $countDataTrained,
                    'trained_at' => Carbon::now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Model retrained successfully.',
                    'data_trained' => $countDataTrained,
                ], 200);
            } else {
                Log::error('AI API Training Failed', [
                    'response_status' => $response->status(),
                    'response_body' => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'AI API Training failed.',
                    'details' => $response->body(),
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Exception during model training', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during model training.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Count the number of lines in a CSV file.
     *
     * @param string $filePath
     * @return int
     */
    protected function countCsvLines(string $filePath): int
    {
        $linecount = 0;
        $handle = fopen($filePath, "r");
        while(!feof($handle)){
            $line = fgets($handle);
            if ($line !== false){
                $linecount++;
            }
        }
        fclose($handle);
        return $linecount;
    }
    public function trainAsync(Request $request)
    {
        try {
            // Dispatch the training job
            TrainRecommendationModel::dispatch();

            return response()->json([
                'success' => true,
                'message' => 'Model retraining has been initiated.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to dispatch training job', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate model retraining.',
            ], 500);
        }
    }
}
