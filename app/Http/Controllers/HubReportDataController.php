<?php

namespace App\Http\Controllers;

use App\Models\HubReportData;
use App\Models\HubData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class HubReportDataController extends Controller
{
    /**
     * Get report data for the API
     */
    public function index(Request $request)
    {
        try {
            $query = HubReportData::query();

            // Apply date range filters if provided
            if ($request->has('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->has('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            $reportData = $query->orderBy('created_at', 'desc')->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'reportid' => $item->reportid,
                        'joborder' => $item->joborder,
                        'partno' => $item->partno,
                        'quality' => $item->quality,
                        'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $item->updated_at->format('Y-m-d H:i:s'),
                    ];
                });
            
            Log::info('Hub Report Data fetched:', [
                'count' => $reportData->count(),
                'date_range' => [
                    'start' => $request->start_date,
                    'end' => $request->end_date
                ]
            ]);

            return response()->json($reportData);
        } catch (\Exception $e) {
            Log::error('Error fetching hub report data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch report data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate production report data
     */
    public function generateProductionReport(Request $request)
    {
        try {
            $query = HubReportData::query();

            // Apply date range filters if provided
            if ($request->has('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->has('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            $reportData = $query->orderBy('created_at', 'desc')->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'reportid' => $item->reportid,
                        'joborder' => $item->joborder,
                        'partno' => $item->partno,
                        'quality' => $item->quality,
                        'created_at' => $item->created_at->toIso8601String(),
                    ];
                });

            return response()->json($reportData);
        } catch (\Exception $e) {
            Log::error('Error generating production report: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to generate production report',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
