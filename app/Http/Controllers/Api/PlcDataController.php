<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\HubData;
use App\Models\CallId1;
use App\Models\CallId2;
use App\Models\CallId3;
use App\Models\CallId4;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PlcDataController extends Controller
{
    /**
     * Store PLC data from the PLC device
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid data format',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Parse the input data
            $data = $request->input('data');
            $values = array_map('trim', explode(',', $data));
            
            // Store the hub data
            $hubData = HubData::create([
                'all_str' => $data,
                'joborder' => $values[0] ?? null,
                'partno' => $values[1] ?? null,
                'is_processed' => false,
            ]);

            // Process the data immediately
            $hubData->process();

            return response()->json([
                'status' => 'success',
                'message' => 'Hub data received and processed successfully',
                'id' => $hubData->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error storing hub data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process hub data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get production report data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Get the latest job card data from hub_data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestJobCard(Request $request)
    {
        try {
            $latestData = HubData::whereNotNull('joborder')
                ->whereNotNull('partno')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$latestData) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No job card data found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'job_order' => $latestData->joborder,
                    'part_number' => $latestData->partno,
                    'created_at' => $latestData->created_at->toDateTimeString(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting latest job card: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get latest job card',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the next uncompared record from hub_data for comparison
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNextUnprocessed(Request $request)
    {
        try {
            // Start a database transaction
            return DB::transaction(function () {
                // Log the query being executed for debugging
                Log::debug('Fetching next uncompared record from hub_data');
                
                // Get the oldest uncompared record that has been processed but not compared yet
                $unprocessedData = HubData::where('is_processed', true)
                    ->where('is_compared', false)
                    ->where(function($query) {
                        $query->whereNotNull('joborder')
                              ->where('joborder', '!=', '');
                    })
                    ->where(function($query) {
                        $query->whereNotNull('partno')
                              ->where('partno', '!=', '');
                    })
                    ->orderBy('created_at', 'asc')
                    ->lockForUpdate()
                    ->first();

                Log::debug('Uncompared data query result:', $unprocessedData ? $unprocessedData->toArray() : ['message' => 'No uncompared records found']);

                if (!$unprocessedData) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'No uncompared data found',
                        'data' => null
                    ], 404);
                }

                // Mark the record as compared
                $unprocessedData->update([
                    'is_compared' => true,
                    'compared_at' => now()
                ]);
                
                Log::debug('Marked record as compared:', [
                    'id' => $unprocessedData->id,
                    'joborder' => $unprocessedData->joborder,
                    'partno' => $unprocessedData->partno
                ]);

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'id' => $unprocessedData->id,
                        'job_order' => $unprocessedData->joborder,
                        'part_number' => $unprocessedData->partno,
                        'created_at' => $unprocessedData->created_at->toDateTimeString(),
                    ]
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Error getting next unprocessed data: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get next unprocessed data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the latest actual part data from callid_4
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Get actual part data by ID and track
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCallIdDataById($id, Request $request)
    {
        try {
            // Default to track 3 since that's where the data is being processed
            $trackId = (int)$request->query('track_id', 3);
            
            Log::debug('getCallIdDataById called with:', [
                'id' => $id,
                'track_id' => $trackId,
                'request_params' => $request->all()
            ]);

            $models = [
                1 => CallId1::class,
                2 => CallId2::class,
                3 => CallId3::class,
                4 => CallId4::class,
            ];
 
            // First try with the requested track ID
            $modelClass = $models[$trackId] ?? $models[3];
            
            // Try to find by ID first
            $model = $modelClass::find($id);
            
            // If not found by ID, try to find by job order or part number
            if (!$model) {
                $query = $modelClass::query();
                
                // Check if ID might be a job order or part number
                $query->where('job_order', $id)
                      ->orWhere('part_number', $id);
                
                // If we have additional search criteria, use them
                if ($request->has('job_order')) {
                    $query->where('job_order', $request->job_order);
                }
                if ($request->has('part_number')) {
                    $query->where('part_number', $request->part_number);
                }
                
                $model = $query->orderBy('created_at', 'desc')->first();
            }

            // If still not found, try all tracks
            if (!$model) {
                Log::debug('Trying all tracks to find matching record');
                
                foreach ($models as $trackId => $modelClass) {
                    $query = $modelClass::query()
                        ->where('job_order', $request->job_order ?: $id)
                        ->orWhere('part_number', $request->part_number ?: $id);
                    
                    $model = $query->orderBy('created_at', 'desc')->first();
                    if ($model) {
                        Log::debug('Found record in track ' . $trackId);
                        break;
                    }
                }
            }

            if (!$model) {
                Log::warning('No actual part data found in any track', [
                    'id' => $id,
                    'track_id' => $trackId,
                    'job_order' => $request->job_order ?? null,
                    'part_number' => $request->part_number ?? null
                ]);
                
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'No actual part data found in any track',
                    'track_id' => $trackId,
                    'criteria' => [
                        'id' => $id,
                        'job_order' => $request->job_order ?? null,
                        'part_number' => $request->part_number ?? null
                    ]
                ], 404);
            }

            // Get the actual track ID where the model was found
            $foundTrackId = array_search(get_class($model), $models);
            
            Log::debug('Found matching record:', [
                'id' => $model->id,
                'track_id' => $foundTrackId,
                'track_identification' => $model->track_identification ?? 'N/A',
                'track' => $model->track ?? 'N/A',
                'job_order' => $model->job_order ?? 'N/A',
                'part_number' => $model->part_number ?? 'N/A',
                'created_at' => $model->created_at ?? 'N/A',
                'model_class' => get_class($model)
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $model,
                'track_id' => $foundTrackId
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching callid data by ID: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch actual part data',
                'error' => $e->getMessage(),
                'track_id' => $trackId ?? null
            ], 500);
        }
    }

    /**
     * Get the latest actual part data from the specified track
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestCallIdData(Request $request)
    {
        try {
            $trackId = (int)$request->query('track_id', 4); // Default to track 4 for backward compatibility
            $hubDataId = $request->query('hub_data_id');
            
            Log::debug('getLatestCallIdData called with:', [
                'track_id' => $trackId,
                'hub_data_id' => $hubDataId,
                'job_order' => $request->job_order ?? null,
                'part_number' => $request->part_number ?? null
            ]);
            
            // Get the appropriate model based on track ID
            $model = null;
            switch ($trackId) {
                case 1:
                    $model = new CallId1();
                    break;
                case 2:
                    $model = new CallId2();
                    break;
                case 3:
                    $model = new CallId3();
                    break;
                case 4:
                default:
                    $model = new CallId4();
                    break;
            }
            
            $query = $model->query();
            
            // Always include hub_data_id in the query if provided
            if ($hubDataId) {
                $query->where('hub_data_id', $hubDataId);
            } 
            
            // If no hub_data_id or no match found, try to match by job order and part number
            if ((!$hubDataId || $query->count() === 0) && 
                $request->has('job_order') && !empty($request->job_order) &&
                $request->has('part_number') && !empty($request->part_number)) {
                
                // Create a new query to search by job order and part number
                $jobOrder = $request->job_order;
                $partNumber = $request->part_number;
                
                // Try to get the original part number from the request if available
                $originalPartNumber = $request->original_part_number ?? $partNumber;
                
                Log::debug('Searching by job order and part number', [
                    'job_order' => $jobOrder,
                    'part_number' => $partNumber,
                    'original_part_number' => $originalPartNumber
                ]);
                
                // First, normalize the part numbers for comparison
                $normalizedPartNumber = strtoupper(preg_replace('/[^A-Z0-9]/', '', $partNumber));
                $normalizedOriginalPartNumber = strtoupper(preg_replace('/[^A-Z0-9]/', '', $originalPartNumber));
                
                Log::debug('Normalized part numbers for search', [
                    'normalized_part_number' => $normalizedPartNumber,
                    'normalized_original_part_number' => $normalizedOriginalPartNumber
                ]);
                
                // Try to find a match using LIKE queries for more flexibility
                $exactMatch = $model->where(function($query) use ($jobOrder, $normalizedPartNumber, $normalizedOriginalPartNumber) {
                    // First, try to match job order exactly and part number with LIKE
                    $query->where('job_order', 'LIKE', "%{$jobOrder}%")
                          ->where(function($q) use ($normalizedPartNumber, $normalizedOriginalPartNumber) {
                              $q->whereRaw("UPPER(REPLACE(REPLACE(part_number, '-', ''), ' ', '')) LIKE ?", ["%{$normalizedPartNumber}%"])
                                ->orWhereRaw("UPPER(REPLACE(REPLACE(part_number, '-', ''), ' ', '')) LIKE ?", ["%{$normalizedOriginalPartNumber}%"]);
                          });
                    
                    // If no match found, try with just the numeric part of the job order
                    if (preg_match('/\d+/', $jobOrder, $matches)) {
                        $numericJobOrder = $matches[0];
                        $query->orWhere(function($q) use ($numericJobOrder, $normalizedPartNumber, $normalizedOriginalPartNumber) {
                            $q->where('job_order', 'LIKE', "%{$numericJobOrder}%")
                              ->where(function($q2) use ($normalizedPartNumber, $normalizedOriginalPartNumber) {
                                  $q2->whereRaw("UPPER(REPLACE(REPLACE(part_number, '-', ''), ' ', '')) LIKE ?", ["%{$normalizedPartNumber}%"])
                                     ->orWhereRaw("UPPER(REPLACE(REPLACE(part_number, '-', ''), ' ', '')) LIKE ?", ["%{$normalizedOriginalPartNumber}%"]);
                              });
                        });
                    }
                })
                ->orderBy('created_at', 'desc')
                ->first();
                
                if ($exactMatch) {
                    Log::debug('Found match using flexible search', [
                        'match_type' => 'flexible',
                        'job_order' => $exactMatch->job_order,
                        'part_number' => $exactMatch->part_number,
                        'created_at' => $exactMatch->created_at
                    ]);
                }
                
                if ($exactMatch) {
                    Log::debug('Found exact match by job order and part number', [
                        'match_type' => 'exact',
                        'part_number' => $exactMatch->part_number
                    ]);
                    return response()->json([
                        'status' => 'success',
                        'data' => $exactMatch,
                        'match_type' => 'exact'
                    ]);
                }
                
                // If no exact match, try case-insensitive search
                $insensitiveMatch = $model->whereRaw('LOWER(job_order) = ?', [strtolower($jobOrder)])
                                       ->whereRaw('LOWER(part_number) = ?', [strtolower($partNumber)])
                                       ->orderBy('created_at', 'desc')
                                       ->first();
                
                if ($insensitiveMatch) {
                    Log::debug('Found case-insensitive match by job order and part number');
                    return response()->json([
                        'status' => 'success',
                        'data' => $insensitiveMatch,
                        'match_type' => 'case_insensitive'
                    ]);
                }
                
                // If still no match, try searching in the normalized fields if they exist
                if (Schema::hasColumn($model->getTable(), 'normalized_job_order') && 
                    Schema::hasColumn($model->getTable(), 'normalized_part_number')) {
                    
                    $normalizedJobOrder = strtoupper(preg_replace('/[^A-Z0-9]/', '', $jobOrder));
                    $normalizedPartNumber = strtoupper(preg_replace('/[^A-Z0-9]/', '', $partNumber));
                    
                    $normalizedMatch = $model->where('normalized_job_order', $normalizedJobOrder)
                                          ->where('normalized_part_number', $normalizedPartNumber)
                                          ->orderBy('created_at', 'desc')
                                          ->first();
                    
                    if ($normalizedMatch) {
                        Log::debug('Found normalized match by job order and part number');
                        return response()->json([
                            'status' => 'success',
                            'data' => $normalizedMatch,
                            'match_type' => 'normalized'
                        ]);
                    }
                }
                
                // If we get here, no match was found
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'No matching actual part data found for the specified job order and part number',
                    'criteria' => [
                        'job_order' => $jobOrder,
                        'part_number' => $partNumber
                    ]
                ], 404);
            }
            
            // Log the query being built
            $sql = $query->toSql();
            $bindings = $query->getBindings();
            Log::debug('Query being executed:', [
                'sql' => $sql,
                'bindings' => $bindings,
                'track_id' => $trackId,
                'hub_data_id' => $hubDataId,
                'job_order' => $request->job_order ?? null,
                'part_number' => $request->part_number ?? null
            ]);
            
            // Get the most recent matching record
            $latestData = $query->orderBy('created_at', 'desc')
                               ->first();
            
            if (!$latestData) {
                Log::warning('No matching actual part data found', [
                    'track_id' => $trackId,
                    'hub_data_id' => $hubDataId,
                    'job_order' => $request->job_order ?? null,
                    'part_number' => $request->part_number ?? null
                ]);
                
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'No matching actual part data found for the specified criteria',
                    'track_id' => $trackId,
                    'hub_data_id' => $hubDataId,
                    'criteria' => [
                        'job_order' => $request->job_order ?? null,
                        'part_number' => $request->part_number ?? null
                    ]
                ], 404);
            }

            Log::debug('Found matching record:', [
                'id' => $latestData->id,
                'track_identification' => $latestData->track_identification ?? 'N/A',
                'job_order' => $latestData->job_order ?? 'N/A',
                'part_number' => $latestData->part_number ?? 'N/A',
                'created_at' => $latestData->created_at ?? 'N/A'
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $latestData,
                'track_id' => $trackId
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getLatestCallIdData: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request' => [
                    'track_id' => $trackId ?? null,
                    'job_order' => $request->job_order ?? null,
                    'part_number' => $request->part_number ?? null
                ]
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch latest actual part data',
                'error' => $e->getMessage(),
                'track_id' => $trackId ?? null
            ], 500);
        }
    }

    /**
     * Get report data for all tracks
     */
    public function getReportData(Request $request)
    {
        try {
            // Get all records from each callid table, ordered by creation date (newest first)
            $callId1Records = CallId1::orderBy('created_at', 'desc')->get();
            $callId2Records = CallId2::orderBy('created_at', 'desc')->get();
            $callId3Records = CallId3::orderBy('created_at', 'desc')->get();
            $callId4Records = CallId4::orderBy('created_at', 'desc')->get();

            // Get the latest job card data
            $jobCard = HubData::whereNotNull('joborder')
                ->whereNotNull('partno')
                ->orderBy('created_at', 'desc')
                ->first();

            $jobCardDetails = [];
            if ($jobCard) {
                $jobCardDetails = [
                    'job_order' => $jobCard->joborder,
                    'part_number' => $jobCard->partno,
                    'recorded_at' => $jobCard->created_at->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s')
                ];
            }

            // Process records from each track
            $reports = collect();
            
            if ($callId1Records->isNotEmpty()) {
                $reports = $reports->merge($this->processTrackRecords($callId1Records, $jobCard, 1));
            }
            if ($callId2Records->isNotEmpty()) {
                $reports = $reports->merge($this->processTrackRecords($callId2Records, $jobCard, 2));
            }
            if ($callId3Records->isNotEmpty()) {
                $reports = $reports->merge($this->processTrackRecords($callId3Records, $jobCard, 3));
            }
            if ($callId4Records->isNotEmpty()) {
                $reports = $reports->merge($this->processTrackRecords($callId4Records, $jobCard, 4));
            }

            // Sort all reports by created_at in descending order to show latest first
            $reports = $reports->sortByDesc(function($item) {
                return $item['partRecognition'][0]['output']; // This is the timestamp from the record
            })->values();

            return response()->json([
                'status' => 'success',
                'data' => $reports,
                'jobCardDetails' => $jobCardDetails
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching production report data: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch production report data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process records for a specific track
     */
    protected function processTrackRecords($records, $jobCard, $trackId)
    {
        return $records->map(function ($record) use ($jobCard, $trackId) {
            $jobOrder = $jobCard->joborder ?? 'N/A';
            $partNo = $jobCard->partno ?? 'N/A';
            $recordedAt = $jobCard->created_at 
                ? $jobCard->created_at->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s')
                : 'N/A';

            $baseData = [
                'id' => $record->id,
                'track_id' => $trackId,
                'jobOrderFromJobCard' => $jobOrder,
                'partNoFromJobCard' => $partNo,
                'jobOrderFromPart' => $record->job_order ?? 'N/A',
                'partNoFromPart' => $record->part_number ?? 'N/A',
                'partRecognition' => [
                    ['no' => 1, 'inspection' => 'Timestamp', 'output' => $record->created_at ? \Carbon\Carbon::parse($record->created_at)->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s') : 'N/A'],
                    ['no' => 2, 'inspection' => 'Track Identification', 'output' => $record->track_identification],
                    ['no' => 3, 'inspection' => 'Job Order (From Part)', 'output' => $record->job_order],
                    ['no' => 4, 'inspection' => 'Part Number (From Part)', 'output' => $record->part_number],
                    ['no' => 5, 'inspection' => 'Job Order (From Job Card)', 'output' => $jobOrder],
                    ['no' => 6, 'inspection' => 'Part Number (From Job Card)', 'output' => $partNo],
                    ['no' => 7, 'inspection' => 'Track & Orientation', 'output' => $this->getTrackAndOrientation($record)],
                    ['no' => 8, 'inspection' => 'Side (Port/Starboard)', 'output' => $this->getStatusOutput($record->check_port_starboard)],
                    ['no' => 9, 'inspection' => 'Model (A321/A320)', 'output' => $record->model_check],
                    ['no' => 10, 'inspection' => 'Output Part Recognition', 'output' => $this->getStatusOutput($record->output_part_recognition)],
                ],
                'qualityInspection' => []
            ];

            // Common inspections for all tracks (first 11 items)
            $commonInspections = [
                ['no' => 11, 'inspection' => 'Anchor Nut - 10 Nos', 'field' => 'anchornut_10nos'],
                ['no' => 12, 'inspection' => 'Thread Protrusion Track Stop - 3 Nos', 'field' => 'trackstop_3nos_1'],
                ['no' => 13, 'inspection' => 'Thread Protrusion Failsafe - 4 Nos', 'field' => 'failsafe_4nos_1'],
                ['no' => 14, 'inspection' => 'Loose Hole - 3 Nos', 'field' => 'loosehole_3nos'],
            ];

            // Track-specific inspections
            $trackInspections = [
                1 => [
                    // Track 1 specific inspections
                    ['no' => 15, 'inspection' => 'Cleat Angle Height to Track Base (1)', 'field' => 'cleatangleheighttotrackbase1_1', 'type' => 'measurement'],
                    ['no' => 16, 'inspection' => 'Cleat Angle Height to Track Base (2)', 'field' => 'cleatangleheighttotrackbase1_2', 'type' => 'measurement'],
                    ['no' => 17, 'inspection' => '7 Mushroom 1 Rivet Tail', 'field' => '7mushroom_1rivettail'],
                    ['no' => 18, 'inspection' => 'Cleat Angle Loose Hole - 8 Nos', 'field' => 'cleatangleloosehole_8nos'],
                    ['no' => 19, 'inspection' => 'Loose Hole Side 2 - 2 Nos (1)', 'field' => 'side2loosehole_2nos_1'],
                    ['no' => 20, 'inspection' => 'Distance of Failsafe to Track Base (1)', 'field' => 'distancefailsafetotrackbase_1', 'type' => 'measurement'],
                    ['no' => 21, 'inspection' => 'Loose Hole Side 1 - 2 Nos (1)', 'field' => 'side1loosehole_2nos_1'],
                    ['no' => 22, 'inspection' => 'Distance of Failsafe to Track Base (2)', 'field' => 'distancefailsafetotrackbase_2', 'type' => 'measurement'],
                    ['no' => 23, 'inspection' => 'Failsafe Position (1)', 'field' => 'failsafeposition_1', 'type' => 'measurement'],
                    ['no' => 24, 'inspection' => 'Packer Side 1', 'field' => 'packerside_1', 'type' => 'measurement'],
                    ['no' => 25, 'inspection' => 'Failsafe Position (2)', 'field' => 'failsafeposition_2', 'type' => 'measurement'],
                    ['no' => 26, 'inspection' => 'Gap Between Part', 'field' => 'gapbetweenpart_1', 'type' => 'measurement'],
                    ['no' => 27, 'inspection' => 'Loose Hole Side 2 - 2 Nos (2)', 'field' => 'side2loosehole_2nos_2'],
                    ['no' => 28, 'inspection' => 'Distance Lower Part to Track Base (1)', 'field' => 'distancelowerparttotrackbase_1', 'type' => 'measurement'],
                    ['no' => 29, 'inspection' => 'Loose Hole Side 1 - 2 Nos (2)', 'field' => 'side1loosehole_2nos_2'],
                    ['no' => 30, 'inspection' => 'Distance Lower Part to Track Base (2)', 'field' => 'distancelowerparttotrackbase_2', 'type' => 'measurement'],
                    ['no' => 31, 'inspection' => 'Failsafe Position (3)', 'field' => 'failsafeposition_3', 'type' => 'measurement'],
                    ['no' => 32, 'inspection' => 'Doubler Position (1)', 'field' => 'doublerposition_1', 'type' => 'measurement'],
                    ['no' => 33, 'inspection' => 'Doubler Position (2)', 'field' => 'doublerposition_2', 'type' => 'measurement'],
                    ['no' => 34, 'inspection' => 'Anchor Nut - 8 Nos', 'field' => 'anchornut_8nos'],
                    ['no' => 35, 'inspection' => 'Thread Protrusion Track Stop - 3 Nos (2)', 'field' => 'trackstop_3nos_2'],
                    ['no' => 36, 'inspection' => 'Thread Protrusion Failsafe - 4 Nos (2)', 'field' => 'failsafe_4nos_2'],
                    ['no' => 37, 'inspection' => 'Loose Hole - 4 Nos', 'field' => 'loosehole_4nos'],
                    ['no' => 38, 'inspection' => 'Cleat Angle Height to Track Base (3)', 'field' => 'cleatangleheighttotrackbase2_1', 'type' => 'measurement'],
                    ['no' => 39, 'inspection' => 'Cleat Angle Height to Track Base (4)', 'field' => 'cleatangleheighttotrackbase2_2', 'type' => 'measurement'],
                    ['no' => 40, 'inspection' => '6 Mushroom 1 Rivet Tail', 'field' => '6mushroom_1rivettail'],
                    ['no' => 41, 'inspection' => 'Cleat Angle Loose Hole - 7 Nos', 'field' => 'cleatangleloosehole_7nos'],
                    ['no' => 42, 'inspection' => 'Packer Side 2', 'field' => 'packerside_2', 'type' => 'measurement'],
                    ['no' => 43, 'inspection' => 'Overall Part Inspection', 'field' => 'quality_status', 'isStatus' => true]
                ],
                2 => [
                    // Track 2 specific inspections
                    ['no' => 15, 'inspection' => 'Cleat Angle Height to Track Base (1)', 'field' => 'cleatangleheighttotrackbase1_1', 'type' => 'measurement'],
                    ['no' => 16, 'inspection' => 'Cleat Angle Height to Track Base (2)', 'field' => 'cleatangleheighttotrackbase1_2', 'type' => 'measurement'],
                    ['no' => 17, 'inspection' => '7 Mushroom 1 Rivet Tail', 'field' => '7mushroom_1rivettail'],
                    ['no' => 18, 'inspection' => 'Cleat Angle Loose Hole - 8 Nos', 'field' => 'cleatangleloosehole_8nos'],
                    ['no' => 19, 'inspection' => 'Loose Hole Side 2 - 2 Nos (1)', 'field' => 'side2loosehole_2nos_1'],
                    ['no' => 20, 'inspection' => 'Distance of Failsafe to Track Base (1)', 'field' => 'distancefailsafetotrackbase_1', 'type' => 'measurement'],
                    ['no' => 21, 'inspection' => 'Loose Hole Side 1 - 2 Nos (1)', 'field' => 'side1loosehole_2nos_1'],
                    ['no' => 22, 'inspection' => 'Distance of Failsafe to Track Base (2)', 'field' => 'distancefailsafetotrackbase_2', 'type' => 'measurement'],
                    ['no' => 23, 'inspection' => 'Failsafe Position (1)', 'field' => 'failsafeposition_1', 'type' => 'measurement'],
                    ['no' => 24, 'inspection' => 'Packer Side 1', 'field' => 'packerside_1', 'type' => 'measurement'],
                    ['no' => 25, 'inspection' => 'Failsafe Position (2)', 'field' => 'failsafeposition_2', 'type' => 'measurement'],
                    ['no' => 26, 'inspection' => 'Gap Between Part (1)', 'field' => 'gapbetweenpart_1', 'type' => 'measurement'],
                    ['no' => 27, 'inspection' => 'Gap Between Part (2)', 'field' => 'gapbetweenpart_2', 'type' => 'measurement'],
                    ['no' => 28, 'inspection' => 'Gap Between Part (3)', 'field' => 'gapbetweenpart_3', 'type' => 'measurement'],
                    ['no' => 29, 'inspection' => 'Loose Hole Side 2 - 2 Nos (2)', 'field' => 'side2loosehole_2nos_2'],
                    ['no' => 30, 'inspection' => 'Distance Lower Part to Track Base (1)', 'field' => 'distancelowerparttotrackbase_1', 'type' => 'measurement'],
                    ['no' => 31, 'inspection' => 'Loose Hole Side 1 - 2 Nos (2)', 'field' => 'side1loosehole_2nos_2'],
                    ['no' => 32, 'inspection' => 'Distance Lower Part to Track Base (2)', 'field' => 'distancelowerparttotrackbase_2', 'type' => 'measurement'],
                    ['no' => 33, 'inspection' => 'Failsafe Position (3)', 'field' => 'failsafeposition_3', 'type' => 'measurement'],
                    ['no' => 34, 'inspection' => 'Doubler Position (1)', 'field' => 'doublerposition_1', 'type' => 'measurement'],
                    ['no' => 35, 'inspection' => 'Doubler Position (2)', 'field' => 'doublerposition_2', 'type' => 'measurement'],
                    ['no' => 36, 'inspection' => 'Anchor Nut - 8 Nos', 'field' => 'anchornut_8nos'],
                    ['no' => 37, 'inspection' => 'Thread Protrusion Track Stop - 3 Nos (2)', 'field' => 'trackstop_3nos_2'],
                    ['no' => 38, 'inspection' => 'Thread Protrusion Failsafe - 4 Nos (2)', 'field' => 'failsafe_4nos_2'],
                    ['no' => 39, 'inspection' => 'Loose Hole - 4 Nos', 'field' => 'loosehole_4nos'],
                    ['no' => 40, 'inspection' => 'Cleat Angle Height to Track Base (3)', 'field' => 'cleatangleheighttotrackbase2_1', 'type' => 'measurement'],
                    ['no' => 41, 'inspection' => 'Cleat Angle Height to Track Base (4)', 'field' => 'cleatangleheighttotrackbase2_2', 'type' => 'measurement'],
                    ['no' => 42, 'inspection' => '6 Mushroom 1 Rivet Tail', 'field' => '6mushroom_1rivettail'],
                    ['no' => 43, 'inspection' => 'Cleat Angle Loose Hole - 7 Nos', 'field' => 'cleatangleloosehole_7nos'],
                    ['no' => 44, 'inspection' => 'Packer Side 2', 'field' => 'packerside_2', 'type' => 'measurement'],
                    ['no' => 45, 'inspection' => 'Overall Part Inspection', 'field' => 'quality_status', 'isStatus' => true]
                ],
                3 => [
                    // Track 3 specific inspections
                    ['no' => 15, 'inspection' => '7 Mushroom 1 Rivet Tail', 'field' => '7mushroom_1rivettail'],
                    ['no' => 16, 'inspection' => 'Cleat Angle Loose Hole - 8 Nos', 'field' => 'cleatangleloosehole_8nos'],
                    ['no' => 17, 'inspection' => 'Cleat Angle Height to Track Base (1)', 'field' => 'cleatangleheighttotrackbase1_1', 'type' => 'measurement'],
                    ['no' => 18, 'inspection' => 'Cleat Angle Height to Track Base (2)', 'field' => 'cleatangleheighttotrackbase1_2', 'type' => 'measurement'],
                    ['no' => 19, 'inspection' => 'Loose Hole Side 2 - 2 Nos (1)', 'field' => 'side2loosehole_2nos_1'],
                    ['no' => 20, 'inspection' => 'Distance of Failsafe to Track Base (1)', 'field' => 'distancefailsafetotrackbase_1', 'type' => 'measurement'],
                    ['no' => 21, 'inspection' => 'Loose Hole Side 1 - 2 Nos (1)', 'field' => 'side1loosehole_2nos_1'],
                    ['no' => 22, 'inspection' => 'Distance of Failsafe to Track Base (2)', 'field' => 'distancefailsafetotrackbase_2', 'type' => 'measurement'],
                    ['no' => 23, 'inspection' => 'Failsafe Position (1)', 'field' => 'failsafeposition_1', 'type' => 'measurement'],
                    ['no' => 24, 'inspection' => 'Packer Side 1', 'field' => 'packerside_1', 'type' => 'measurement'],
                    ['no' => 25, 'inspection' => 'Failsafe Position (2)', 'field' => 'failsafeposition_2', 'type' => 'measurement'],
                    ['no' => 26, 'inspection' => 'Gap Between Part (1)', 'field' => 'gapbetweenpart_1', 'type' => 'measurement'],
                    ['no' => 27, 'inspection' => 'Gap Between Part (2)', 'field' => 'gapbetweenpart_2', 'type' => 'measurement'],
                    ['no' => 28, 'inspection' => 'Gap Between Part (3)', 'field' => 'gapbetweenpart_3', 'type' => 'measurement'],
                    ['no' => 29, 'inspection' => 'Loose Hole Side 2 - 2 Nos (2)', 'field' => 'side2loosehole_2nos_2'],
                    ['no' => 30, 'inspection' => 'Distance Lower Part to Track Base (1)', 'field' => 'distancelowerparttotrackbase_1', 'type' => 'measurement'],
                    ['no' => 31, 'inspection' => 'Loose Hole Side 1 - 2 Nos (2)', 'field' => 'side1loosehole_2nos_2'],
                    ['no' => 32, 'inspection' => 'Distance Lower Part to Track Base (2)', 'field' => 'distancelowerparttotrackbase_2', 'type' => 'measurement'],
                    ['no' => 33, 'inspection' => 'Failsafe Position (3)', 'field' => 'failsafeposition_3', 'type' => 'measurement'],
                    ['no' => 34, 'inspection' => 'Doubler Position (1)', 'field' => 'doublerposition_1', 'type' => 'measurement'],
                    ['no' => 35, 'inspection' => 'Doubler Position (2)', 'field' => 'doublerposition_2', 'type' => 'measurement'],
                    ['no' => 36, 'inspection' => 'Anchor Nut - 8 Nos', 'field' => 'anchornut_8nos'],
                    ['no' => 37, 'inspection' => 'Thread Protrusion Track Stop - 3 Nos (2)', 'field' => 'trackstop_3nos_2'],
                    ['no' => 38, 'inspection' => 'Thread Protrusion Failsafe - 4 Nos (2)', 'field' => 'failsafe_4nos_2'],
                    ['no' => 39, 'inspection' => 'Loose Hole - 4 Nos', 'field' => 'loosehole_4nos'],
                    ['no' => 40, 'inspection' => '6 Mushroom 1 Rivet Tail', 'field' => '6mushroom_1rivettail'],
                    ['no' => 41, 'inspection' => 'Cleat Angle Loose Hole - 7 Nos', 'field' => 'cleatangleloosehole_7nos'],
                    ['no' => 42, 'inspection' => 'Cleat Angle Height to Track Base (3)', 'field' => 'cleatangleheighttotrackbase2_1', 'type' => 'measurement'],
                    ['no' => 43, 'inspection' => 'Cleat Angle Height to Track Base (4)', 'field' => 'cleatangleheighttotrackbase2_2', 'type' => 'measurement'],
                    ['no' => 44, 'inspection' => 'Packer Side 2', 'field' => 'packerside_2', 'type' => 'measurement'],
                    ['no' => 45, 'inspection' => 'Overall Part Inspection', 'field' => 'quality_status', 'isStatus' => true]
                ],
                4 => [
                    // Track 4 specific inspections
                    ['no' => 15, 'inspection' => '7 Mushroom 1 Rivet Tail', 'field' => '7mushroom_1rivettail'],
                    ['no' => 16, 'inspection' => 'Cleat Angle Loose Hole - 8 Nos', 'field' => 'cleatangleloosehole_8nos'],
                    ['no' => 17, 'inspection' => 'Cleat Angle Height to Track Base (1)', 'field' => 'cleatangleheighttotrackbase1_1', 'type' => 'measurement'],
                    ['no' => 18, 'inspection' => 'Cleat Angle Height to Track Base (2)', 'field' => 'cleatangleheighttotrackbase1_2', 'type' => 'measurement'],
                    ['no' => 19, 'inspection' => 'Loose Hole Side 2 - 2 Nos (1)', 'field' => 'side2loosehole_2nos_1'],
                    ['no' => 20, 'inspection' => 'Distance of Failsafe to Track Base (1)', 'field' => 'distancefailsafetotrackbase_1', 'type' => 'measurement'],
                    ['no' => 21, 'inspection' => 'Loose Hole Side 1 - 2 Nos (1)', 'field' => 'side1loosehole_2nos_1'],
                    ['no' => 22, 'inspection' => 'Distance of Failsafe to Track Base (2)', 'field' => 'distancefailsafetotrackbase_2', 'type' => 'measurement'],
                    ['no' => 23, 'inspection' => 'Failsafe Position (1)', 'field' => 'failsafeposition_1', 'type' => 'measurement'],
                    ['no' => 24, 'inspection' => 'Packer Side 1', 'field' => 'packerside_1', 'type' => 'measurement'],
                    ['no' => 25, 'inspection' => 'Failsafe Position (2)', 'field' => 'failsafeposition_2', 'type' => 'measurement'],
                    ['no' => 26, 'inspection' => 'Gap Between Part (1)', 'field' => 'gapbetweenpart_1', 'type' => 'measurement'],
                    ['no' => 27, 'inspection' => 'Gap Between Part (2)', 'field' => 'gapbetweenpart_2', 'type' => 'measurement'],
                    ['no' => 28, 'inspection' => 'Gap Between Part (3)', 'field' => 'gapbetweenpart_3', 'type' => 'measurement'],
                    ['no' => 29, 'inspection' => 'Loose Hole Side 2 - 2 Nos (2)', 'field' => 'side2loosehole_2nos_2'],
                    ['no' => 30, 'inspection' => 'Distance Lower Part to Track Base (1)', 'field' => 'distancelowerparttotrackbase_1', 'type' => 'measurement'],
                    ['no' => 31, 'inspection' => 'Spreader Position', 'field' => 'spreaderposition', 'type' => 'measurement'],
                    ['no' => 32, 'inspection' => 'Loose Hole Side 1 - 2 Nos (2)', 'field' => 'side1loosehole_2nos_2'],
                    ['no' => 33, 'inspection' => 'Distance Lower Part to Track Base (2)', 'field' => 'distancelowerparttotrackbase_2', 'type' => 'measurement'],
                    ['no' => 34, 'inspection' => 'Rivet Tail - 2 Nos (1)', 'field' => 'rivettail_2nos_1'],
                    ['no' => 35, 'inspection' => 'Rivet Tail - 2 Nos (2)', 'field' => 'rivettail_2nos_2'],
                    ['no' => 36, 'inspection' => 'Doubler Position (1)', 'field' => 'doublerposition_1', 'type' => 'measurement'],
                    ['no' => 37, 'inspection' => 'Doubler Position (2)', 'field' => 'doublerposition_2', 'type' => 'measurement'],
                    ['no' => 38, 'inspection' => 'Anchor Nut - 8 Nos', 'field' => 'anchornut_8nos'],
                    ['no' => 39, 'inspection' => 'Thread Protrusion Track Stop - 3 Nos (2)', 'field' => 'trackstop_3nos_2'],
                    ['no' => 40, 'inspection' => 'Thread Protrusion Failsafe - 4 Nos (2)', 'field' => 'failsafe_4nos_2'],
                    ['no' => 41, 'inspection' => 'Loose Hole - 4 Nos', 'field' => 'loosehole_4nos'],
                    ['no' => 42, 'inspection' => '6 Mushroom 1 Rivet Tail', 'field' => '6mushroom_1rivettail'],
                    ['no' => 43, 'inspection' => 'Cleat Angle Loose Hole - 7 Nos', 'field' => 'cleatangleloosehole_7nos'],
                    ['no' => 44, 'inspection' => 'Cleat Angle Height to Track Base (3)', 'field' => 'cleatangleheighttotrackbase2_1', 'type' => 'measurement'],
                    ['no' => 45, 'inspection' => 'Cleat Angle Height to Track Base (4)', 'field' => 'cleatangleheighttotrackbase2_2', 'type' => 'measurement'],
                    ['no' => 46, 'inspection' => 'Packer Side 2', 'field' => 'packerside_2', 'type' => 'measurement'],
                    ['no' => 47, 'inspection' => 'Overall Part Inspection', 'field' => 'quality_status', 'isStatus' => true]
                ]
            ];

            // Add common inspections
            foreach ($commonInspections as $inspection) {
                $baseData['qualityInspection'][] = [
                    'no' => $inspection['no'],
                    'inspection' => $inspection['inspection'],
                    'output' => $this->getStatusOutput($record->{$inspection['field']} ?? null)
                ];
            }

            // Add track-specific inspections
            if (isset($trackInspections[$trackId])) {
                foreach ($trackInspections[$trackId] as $inspection) {
                    $value = $record->{$inspection['field']} ?? null;
                    $output = $this->getStatusOutput($value);
                    
                    // Handle measurement type fields
                    if (($inspection['type'] ?? '') === 'measurement' && $value !== null) {
                        $output = "{$value}mm";
                    }
                    
                    $baseData['qualityInspection'][] = [
                        'no' => $inspection['no'],
                        'inspection' => $inspection['inspection'],
                        'output' => $output
                    ];
                }
            }

            return $baseData;
        });
    }

    /**
     * Get status output (OK/NG/N/A)
     */
    protected function getStatusOutput($value, $okValue = 'OK', $ngValue = 'NG')
    {
        if ($value === 0 || $value === '0') {
            return $okValue;
        } elseif ($value === 1 || $value === '1') {
            return $ngValue;
        }
        return 'N/A';
    }

    /**
     * Get track and orientation status
     */
    protected function getTrackAndOrientation($record)
    {
        $track = $this->getStatusOutput($record->track ?? null, 'OK', 'NG');
        $orientation = $this->getStatusOutput($record->orientation ?? null, 'OK', 'NG');
        return "{$track} / {$orientation}";
    }

    /**
     * Process unprocessed actual part data records
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Process unprocessed hub data records
     * This method only processes the all_str field and marks records as processed
     * without affecting their comparison status
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processUnprocessed(Request $request)
    {
        try {
            // Get the count of unprocessed records (only checking is_processed, not is_compared)
            $unprocessedCount = HubData::where('is_processed', false)
                ->whereNotNull('joborder')
                ->where('joborder', '!=', '')
                ->whereNotNull('partno')
                ->where('partno', '!=', '')
                ->count();
            
            if ($unprocessedCount === 0) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No unprocessed records found',
                    'processed' => 0,
                    'has_more' => false
                ]);
            }

            $processed = 0;
            $errors = [];

            // Get the oldest unprocessed record (only checking is_processed, not is_compared)
            $unprocessedRecord = HubData::where('is_processed', false)
                ->whereNotNull('joborder')
                ->where('joborder', '!=', '')
                ->whereNotNull('partno')
                ->where('partno', '!=', '')
                ->orderBy('created_at', 'asc')
                ->first();

            if ($unprocessedRecord) {
                try {
                    // Process the record
                    $unprocessedRecord->process();
                    $processed = 1;
                    
                    // Mark as processed but don't touch the comparison status
                    $unprocessedRecord->update([
                        'is_processed' => true,
                        'processed_at' => now(),
                        // Keep is_compared as is (false by default for new records)
                    ]);
                    
                    Log::debug('Processed record:', [
                        'id' => $unprocessedRecord->id,
                        'joborder' => $unprocessedRecord->joborder,
                        'partno' => $unprocessedRecord->partno
                    ]);
                } catch (\Exception $e) {
                    $errors[] = [
                        'id' => $unprocessedRecord->id,
                        'error' => $e->getMessage()
                    ];
                    Log::error("Error processing record {$unprocessedRecord->id}: " . $e->getMessage());
                }
            }

            // Check if there are more unprocessed records (only checking is_processed)
            $hasMore = HubData::where('is_processed', false)
                ->whereNotNull('joborder')
                ->where('joborder', '!=', '')
                ->whereNotNull('partno')
                ->where('partno', '!=', '')
                ->exists();

            return response()->json([
                'status' => 'success',
                'message' => $processed > 0 ? 'Record processed successfully' : 'No records to process',
                'processed' => $processed,
                'has_more' => $hasMore,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Error in processUnprocessed: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process records',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
