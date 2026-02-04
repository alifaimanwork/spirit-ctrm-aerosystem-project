<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActualPartData extends Model
{
    protected $fillable = [
        'raw_data',
        'is_processed',
        'processed_at',
        'error_message'
    ];

    protected $casts = [
        'is_processed' => 'boolean',
        'processed_at' => 'datetime',
    ];

    /**
     * Process the actual part data and store it in the CallId4 table
     * Moved to HubData model
     */
    // public function process()
    // {
    //     DB::beginTransaction();

    //     try {
    //         // Split the raw data by commas and clean up the values
    //         $values = array_map('trim', explode(',', $this->raw_data));
    //         $index = 0;

    //         // Map the values to the CallId4 model fields based on the migration structure
    //         $callIdData = [
    //             'job_order' => $values[$index++] ?? null,
    //             'part_number' => $values[$index++] ?? null,
    //             'track' => $values[$index++] ?? null,
    //             'orientation' => $values[$index++] ?? null,
    //             'check_port_starboard' => $values[$index++] ?? null,
    //             'model_check' => $values[$index++] ?? null,
    //             'output_part_recognition' => $values[$index++] ?? null,
    //             'anchornut_10nos' => $values[$index++] ?? null,
    //             'trackstop_3nos_1' => $values[$index++] ?? null,
    //             'failsafe_4nos_1' => $values[$index++] ?? null,
    //             'loosehole_3nos' => $values[$index++] ?? null,
    //             '7mushroom_1rivettail' => $values[$index++] ?? null,
    //             'cleatangleloosehole_8nos' => $values[$index++] ?? null,
    //             'cleatangleheighttotrackbase1_1' => $values[$index++] ?? null,
    //             'cleatangleheighttotrackbase1_2' => $values[$index++] ?? null,
    //             'side2loosehole_2nos_1' => $values[$index++] ?? null,
    //             'distancefailsafetotrackbase_1' => $values[$index++] ?? null,
    //             'side1loosehole_2nos_1' => $values[$index++] ?? null,
    //             'distancefailsafetotrackbase_2' => $values[$index++] ?? null,
    //             'failsafeposition_1' => $values[$index++] ?? null,
    //             'packerside_1' => $values[$index++] ?? null,
    //             'failsafeposition_2' => $values[$index++] ?? null,
    //             'gapbetweenpart_1' => $values[$index++] ?? null,
    //             'gapbetweenpart_2' => $values[$index++] ?? null,
    //             'gapbetweenpart_3' => $values[$index++] ?? null,
    //             'side2loosehole_2nos_2' => $values[$index++] ?? null,
    //             'distancelowerparttotrackbase_1' => $values[$index++] ?? null,
    //             'spreaderposition' => $values[$index++] ?? null,
    //             'side1loosehole_2nos_2' => $values[$index++] ?? null,
    //             'distancelowerparttotrackbase_2' => $values[$index++] ?? null,
    //             'rivettail_2nos_1' => $values[$index++] ?? null,
    //             'rivettail_2nos_2' => $values[$index++] ?? null,
    //             'doublerposition_1' => $values[$index++] ?? null,
    //             'doublerposition_2' => $values[$index++] ?? null,
    //             'anchornut_8nos' => $values[$index++] ?? null,
    //             'trackstop_3nos_2' => $values[$index++] ?? null,
    //             'failsafe_4nos_2' => $values[$index++] ?? null,
    //             'loosehole_4nos' => $values[$index++] ?? null,
    //             '6mushroom_1rivettail' => $values[$index++] ?? null,
    //             'cleatangleloosehole_7nos' => $values[$index++] ?? null,
    //             'cleatangleheighttotrackbase2_1' => $values[$index++] ?? null,
    //             'cleatangleheighttotrackbase2_2' => $values[$index++] ?? null,
    //             'packerside_2' => $values[$index] ?? null,// Last field, no need to increment index after this
    //         ];

    //         // Create the CallId4 record
    //         CallId4::create($callIdData);

    //         // Update the actual part data record
    //         $this->update([
    //             'is_processed' => true,
    //             'processed_at' => now(),
    //         ]);

    //         DB::commit();
    //         return true;

    //     } catch (\Exception $e) {
    //         DB::rollBack();
            
    //         $this->update([
    //             'error_message' => $e->getMessage(),
    //         ]);
            
    //         Log::error('Error processing actual part data', [
    //             'actual_part_data_id' => $this->id,
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
            
    //         return false;
    //     }
    // }
}
