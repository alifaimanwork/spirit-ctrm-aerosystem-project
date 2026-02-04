<?php

namespace App\Http\Controllers;

use App\Models\ProcessedData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProcessedDataController extends Controller
{
    /**
     * Get all processed data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $processedData = ProcessedData::latest()->paginate(50);
            
            return response()->json([
                'success' => true,
                'data' => $processedData
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching processed data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch processed data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Store processed data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Check if a job card has been processed
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_order' => 'required|string|max:255',
            'part_number' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $exists = ProcessedData::where('job_card_job_order', $request->job_order)
                ->where('job_card_part_number', $request->part_number)
                ->exists();

            return response()->json([
                'success' => true,
                'exists' => $exists
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking if job card was processed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to check if job card was processed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store processed data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Log the incoming request data
        Log::info('Received processed data request', [
            'request_data' => $request->all(),
            'headers' => $request->headers->all()
        ]);
        
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'job_card_job_order' => 'required|string|max:255',
                'job_card_part_number' => 'required|string|max:255',
                'job_card_timestamp' => 'required|date',
                'sap_job_order' => 'nullable|string|max:255',
                'sap_part_number' => 'nullable|string|max:255',
                'sap_timestamp' => 'nullable|date',
                'actual_part_job_order' => 'nullable|string|max:255',
                'actual_part_number' => 'nullable|string|max:255',
                'actual_part_timestamp' => 'nullable|date',
                'job_order_match_jobcard_sap' => 'required|boolean',
                'part_number_match_jobcard_sap' => 'required|boolean',
                'job_order_match_sap_actual' => 'required|boolean',
                'part_number_match_sap_actual' => 'required|boolean',
                'status' => 'required|string|in:pass,fail,incomplete',
                'error_message' => 'nullable|string',
                'metadata' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed for processed data', [
                    'errors' => $validator->errors(),
                    'input' => $request->all()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            Log::info('Creating processed data record', ['data' => $request->all()]);
            
            // Create the processed data record
            $processedData = ProcessedData::create([
                'job_card_job_order' => $request->job_card_job_order,
                'job_card_part_number' => $request->job_card_part_number,
                'job_card_timestamp' => $request->job_card_timestamp,
                'sap_job_order' => $request->sap_job_order,
                'sap_part_number' => $request->sap_part_number,
                'sap_timestamp' => $request->sap_timestamp,
                'actual_part_job_order' => $request->actual_part_job_order,
                'actual_part_number' => $request->actual_part_number,
                'actual_part_timestamp' => $request->actual_part_timestamp,
                'job_order_match_jobcard_sap' => $request->job_order_match_jobcard_sap,
                'part_number_match_jobcard_sap' => $request->part_number_match_jobcard_sap,
                'job_order_match_sap_actual' => $request->job_order_match_sap_actual,
                'part_number_match_sap_actual' => $request->part_number_match_sap_actual,
                'status' => $request->status,
                'error_message' => $request->error_message,
                'metadata' => $request->metadata,
            ]);

            // Add timestamp parsing and additional logging
            try {
                $data = $request->all();
                
                Log::debug('Processing data before create', [
                    'raw_data' => $data,
                    'job_card_timestamp_type' => gettype($data['job_card_timestamp']),
                    'actual_part_timestamp_type' => gettype($data['actual_part_timestamp']),
                    'sap_timestamp_type' => isset($data['sap_timestamp']) ? gettype($data['sap_timestamp']) : 'null'
                ]);
                
                // Convert timestamps to proper format if they're strings
                $data['job_card_timestamp'] = Carbon::parse($data['job_card_timestamp']);
                $data['actual_part_timestamp'] = Carbon::parse($data['actual_part_timestamp']);
                $data['sap_timestamp'] = $data['sap_timestamp'] ? Carbon::parse($data['sap_timestamp']) : null;
                
                // Update the processed data record with parsed timestamps
                $processedData->update([
                    'job_card_timestamp' => $data['job_card_timestamp'],
                    'actual_part_timestamp' => $data['actual_part_timestamp'],
                    'sap_timestamp' => $data['sap_timestamp']
                ]);
                
                Log::info('Successfully created and updated processed data record', [
                    'id' => $processedData->id,
                    'job_card_job_order' => $processedData->job_card_job_order,
                    'timestamps_parsed' => true
                ]);
                
            } catch (\Exception $e) {
                Log::error('Error processing timestamps: ' . $e->getMessage(), [
                    'error' => $e,
                    'job_card_timestamp' => $request->job_card_timestamp ?? null,
                    'actual_part_timestamp' => $request->actual_part_timestamp ?? null,
                    'sap_timestamp' => $request->sap_timestamp ?? null
                ]);
                // Continue even if timestamp parsing fails, as we've already created the record
            }

            return response()->json([
                'success' => true,
                'message' => 'Processed data saved successfully',
                'data' => $processedData->fresh() // Return fresh data from database
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving processed data: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save processed data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
