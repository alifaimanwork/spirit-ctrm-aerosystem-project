<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\sapData;

class FileUploadController extends Controller
{
    public function uploadSap(Request $request)
    {
        // Check if we're receiving direct data or a file
        if ($request->has('data') && is_array($request->data)) {
            // Clear existing data
            sapData::truncate();
            
            // Process the filtered data
            $imported = 0;
            foreach ($request->data as $item) {
                if (!empty($item['joborder']) && !empty($item['partno'])) {
                    sapData::updateOrCreate(
                        [
                            'joborder' => trim($item['joborder']),
                            'partno' => trim($item['partno'])
                        ]
                    );
                    $imported++;
                }
            }
            
            return response()->json([
                'message' => "Successfully imported {$imported} records from filtered data.",
                'imported' => $imported
            ], 200);
        }
        
        // Fallback to file upload for backward compatibility
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        // Get the file
        $file = $request->file('file');

        // Parse the CSV
        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            // Clear existing data
            sapData::truncate();
            
            $header = fgetcsv($handle); // Read the first row as header
            
            // Convert headers to lowercase for case-insensitive comparison
            $header = array_map('strtolower', $header);
            
            // Find the correct column indices
            $jobOrderIndex = array_search('joborder', $header);
            $partNoIndex = array_search('partno', $header);
            
            // If columns not found, try alternative names
            if ($jobOrderIndex === false) {
                $jobOrderIndex = array_search('job order', $header);
            }
            if ($partNoIndex === false) {
                $partNoIndex = array_search('part no', $header);
            }
            
            // If still not found, use first two columns as fallback
            if ($jobOrderIndex === false) $jobOrderIndex = 0;
            if ($partNoIndex === false) $partNoIndex = 1;
            
            $imported = 0;
            while (($row = fgetcsv($handle)) !== false) {
                // Skip empty rows
                if (empty(array_filter($row))) continue;
                
                // Get values by index to ensure correct mapping
                $jobOrder = trim($row[$jobOrderIndex] ?? '');
                $partNo = trim($row[$partNoIndex] ?? '');
                
                // Only save if we have valid data
                if (!empty($jobOrder) && !empty($partNo)) {
                    sapData::create([
                        'joborder' => $jobOrder,
                        'partno' => $partNo,
                    ]);
                    $imported++;
                }
            }
            fclose($handle);
            
            return response()->json([
                'message' => "Successfully imported {$imported} records from CSV file.",
                'imported' => $imported
            ], 200);
        }

        return response()->json(['message' => 'CSV data uploaded and saved successfully!'], 200);
    }

    public function getsapData()
    {
        $sapData = sapData::all();
        return response()->json(['data' => $sapData], 200);
    }
}

