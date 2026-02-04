<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SapData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SapDataController extends Controller
{
    public function getLatest()
    {
        try {
            $latestSap = SapData::latest('created_at')->first();
            
            if (!$latestSap) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No SAP data found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'job_order' => $latestSap->joborder,
                    'part_number' => $latestSap->partno,
                    'created_at' => $latestSap->created_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching latest SAP data: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch SAP data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function findMatch(Request $request)
    {
        try {
            $request->validate([
                'job_order' => 'required|string',
                'part_number' => 'required|string'
            ]);

            // Normalize part numbers by removing hyphens and converting to uppercase for comparison
            $normalizedPartNumber = strtoupper(str_replace('-', '', $request->part_number));
            
            // Get all records with matching job order first
            $matches = SapData::where('joborder', $request->job_order)
                ->latest('created_at')
                ->get();
                
            // Find the first record where the normalized part numbers match
            $match = $matches->first(function ($record) use ($normalizedPartNumber) {
                $recordPartNumber = strtoupper(str_replace('-', '', $record->partno));
                return $recordPartNumber === $normalizedPartNumber;
            });

            if (!$match) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'No matching SAP data found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'job_order' => $match->joborder,
                    'part_number' => $match->partno,
                    'created_at' => $match->created_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error finding SAP match: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to find SAP match',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
