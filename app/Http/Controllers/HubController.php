<?php

namespace App\Http\Controllers;

use App\Models\HubData;
use App\Models\ProcessedData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class HubController extends Controller
{
    public function getNextActualPart(Request $request)
    {
        try {
            $processedHubDataIds = ProcessedData::pluck('hub_data_id')->toArray();

            $hubData = HubData::whereNotIn('id', $processedHubDataIds)
                                ->orderBy('id', 'asc')
                                ->first();

            if ($hubData) {
                return response()->json(['success' => true, 'data' => $hubData]);
            } else {
                return response()->json(['success' => false, 'message' => 'No new actual part available.']);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching next actual part: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching the next actual part.'], 500);
        }
    }
}
