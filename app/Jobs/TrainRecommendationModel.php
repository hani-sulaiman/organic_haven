<?php

namespace App\Jobs;

use App\Models\RecommendationsModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TrainRecommendationModel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Handle the job.
     */
    public function handle()
    {
        try {
            // Path to the training_data.csv file
            $filePath = storage_path('app/private/interactions/training_data.csv');

            // Check if the file exists
            if (!file_exists($filePath)) {
                Log::error('Training data file not found.');
                return;
            }

            // Send POST request to AI API with the CSV file
            $response = Http::attach(
                'file', file_get_contents($filePath), 'training_data.csv'
            )->post(config('services.ai.base_url') . '/train');

            // Check if the request was successful
            if ($response->successful()) {
                // Count the number of data lines (excluding header)
                $countDataTrained = $this->countCsvLines($filePath) - 1;

                // Record the training in the database
                RecommendationsModel::create([
                    'count_data_trained' => $countDataTrained,
                    'trained_at' => Carbon::now(),
                ]);

                Log::info('Model retrained successfully.', [
                    'data_trained' => $countDataTrained,
                ]);
            } else {
                Log::error('AI API Training Failed', [
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception during model training', [
                'error' => $e->getMessage(),
            ]);
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
}
