<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InteractionLogger
{
    protected $filePath;
    protected $headers = [
        'user_id',
        'product_id',
        'category_id',
        'interaction_type',
        'date',
        'gender',
        'age',
    ];

    public function __construct()
    {
        $this->filePath = 'interactions/training_data.csv';
        
        // Use Storage facade consistently
        if (!Storage::exists($this->filePath)) {
            Storage::put($this->filePath, implode(',', $this->headers));
        }
        else {
            // Verify headers only if file exists
            if (!$this->hasHeaders()) {
                throw new \Exception('CSV file exists but headers are missing');
            }
        }
    }
    protected function hasHeaders()
    {
        try {
            $content = Storage::get($this->filePath);
            $lines = explode("\n", $content);
            return trim($lines[0]) === implode(',', $this->headers);
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Log an interaction to the CSV file.
     *
     * @param array $data
     * @return void
     */
    public function log(array $data)
    {
        // Ensure all required fields are present
        foreach ($this->headers as $header) {
            if (!array_key_exists($header, $data)) {
                // Handle missing fields as needed
                $data[$header] = null;
            }
        }

        // Format the data in the order of headers
        $row = [];
        foreach ($this->headers as $header) {
            $row[] = $data[$header];
        }

        // Write the row to the CSV
        $this->writeRow($row);
    }

    /**
     * Write a row to the CSV file.
     *
     * @param array $row
     * @return void
     */
    protected function writeRow(array $row)
    {
        try {
            // Use Storage facade for writing
            Storage::append($this->filePath, implode(',', $row));
        } catch (\Exception $e) {
            Log::error("CSV Write Error: " . $e->getMessage());
        }
    }
}
