<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\sapData;
use App\Models\ComparisonResults;
use App\Models\JobCardData;
use App\Models\ProcessedData;

class ComparisonController extends Controller
{
    public function checkMatch(Request $request)
    {
        $plcJobOrder = $request->input('jobOrder');
        $plcPartNo = $request->input('partNo');

        // Check for job order match
        $jobOrderMatch = sapData::where('joborder', $plcJobOrder)->exists();
        
        // Check for part no match
        $partNoMatch = sapData::where('partno', $plcPartNo)->exists();

        // Get the SAP data if both fields match
        $sapData = null;
        if ($jobOrderMatch && $partNoMatch) {
            $sapRecord = sapData::where('joborder', $plcJobOrder)
                               ->where('partno', $plcPartNo)
                               ->first();
            $sapData = [
                'jobOrder' => $sapRecord->joborder,
                'partNo' => $sapRecord->partno
            ];
        }

        return response()->json([
            'match' => $jobOrderMatch && $partNoMatch, // true only if both match
            'sapData' => $sapData,
            'fieldMatches' => [
                'jobOrder' => $jobOrderMatch,
                'partNo' => $partNoMatch
            ]
        ]);
    }

    public function store(Request $request)
    {
        ComparisonResults::create([
            'joborder' => $request->joborder,
            'partno' => $request->partno,
            'status' => $request->status
        ]);

        return response()->json(['message' => 'Comparison result stored']);
    }

    public function getResults()
    {
        $results = ComparisonResults::orderBy('created_at', 'desc')->get();
        return response()->json($results);
    }

    public function findSapMatch(Request $request)
    {
        $jobOrder = $request->query('joborder');
        $partNo = $request->query('partno');

        if (!$jobOrder || !$partNo) {
            return response()->json(['error' => 'Job order and part number are required.'], 400);
        }

        $sapData = sapData::where('joborder', $jobOrder)
                          ->where('partno', $partNo)
                          ->first();

        if ($sapData) {
            return response()->json(['success' => true, 'data' => $sapData]);
        } else {
            return response()->json(['success' => false, 'message' => 'No matching SAP data found.']);
        }
    }
} 