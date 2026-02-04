<?php

namespace App\Http\Controllers;

use App\Models\DataRecord;
use Illuminate\Http\Request;
use Carbon\Carbon; // Ensure to import Carbon for date manipulation

class DataRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = DataRecord::query();

        // Filtering logic
        if ($request->filled('flatness_status')) {
            $query->where('flatness_status', $request->flatness_status);
        }
        
        if ($request->filled('barcodes_1')) {
            $query->where('barcodes_1', 'like', '%' . $request->barcodes_1 . '%');
        }
        
        if ($request->filled('created_at')) {
            // Ensure that the date filter works with the correct format
            $date = Carbon::createFromFormat('Y-m-d', $request->created_at);
            $query->whereDate('created_at', $date);
        }

        // Return the filtered results as JSON
        return response()->json($query->get());
    }
}
