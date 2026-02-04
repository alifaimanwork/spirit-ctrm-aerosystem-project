<?php

namespace App\Http\Controllers;

use App\Models\JobCardData;
use App\Models\ProcessedData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JobCardController extends Controller
{
    /**
     * Get job card data by job order and part number
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByJobOrderAndPartNo(Request $request)
    {
        Log::info('JobCardController: Received request', $request->all());
        
        $request->validate([
            'job_order' => 'required|string',
            'part_no' => 'required|string',
        ]);

        try {
            Log::info('JobCardController: Validated request', [
                'job_order' => $request->job_order,
                'part_no' => $request->part_no
            ]);
            $jobCard = JobCardData::where('joborder', $request->job_order)
                ->where('partno', $request->part_no)
                ->first();

            if (!$jobCard) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job card not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $jobCard
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching job card data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch job card data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getNextJob()
    {
        try {
            // Get all processed job orders from the processed_data table
            $processedJobOrders = ProcessedData::whereNotNull('job_card_job_order')
                ->pluck('job_card_job_order')
                ->toArray();

            // Find the first job card that hasn't been processed yet
            $jobCard = JobCardData::whereNotIn('joborder', $processedJobOrders)
                                  ->orderBy('id', 'asc')
                                  ->first();

            if ($jobCard) {
                return response()->json([
                    'success' => true, 
                    'data' => $jobCard
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'No unprocessed job cards available.'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching next job card: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred while fetching the next job card.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
