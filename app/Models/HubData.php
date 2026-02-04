<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CallId1;
use App\Models\CallId2;
use App\Models\CallId3;
use App\Models\CallId4;


class HubData extends Model
{
    use HasFactory;

    protected $table = 'hub_data';
    
    protected $fillable = [
        'joborder',
        'partno',
        'all_str',
        'is_processed',
        'is_compared',
        'processed_at',
        'compared_at',
        'error_message'
    ];

    protected $casts = [
        'is_processed' => 'boolean',
        'is_compared' => 'boolean',
        'processed_at' => 'datetime',
        'compared_at' => 'datetime',
    ];

    public function process()
    {
        DB::beginTransaction();

        try {
            // Split the raw data by commas and clean up the values
            $values = array_map('trim', explode(',', $this->all_str));
            
            // The first value is the track ID (1, 2, 3, or 4)
            $trackId = (int)($values[0] ?? 0);
            
            // Debug log the values array
            Log::debug('Processing values array:', $values);
            Log::debug('Track ID detected:', ['track_id' => $trackId]);
            
            // Initialize callIdData with track_identification and hub_data_id
            $callIdData = [
                'track_identification' => $values[0] ?? null,
                'hub_data_id' => $this->id, // Add the hub_data_id to link back to the original record
            ];

            // Map values based on track ID
            switch ($trackId) {
                case 1:
                    $callIdData = array_merge($callIdData, [
                        'quality_status' => $values[1] ?? null,
                        'part_number' => $values[2] ?? null,
                        'job_order' => $values[3] ?? null,
                        'track' => $values[4] ?? null,
                        'orientation' => $values[5] ?? null,
                        'check_port_starboard' => $values[6] ?? null,
                        'model_check' => $values[7] ?? null,
                        'output_part_recognition' => $values[8] ?? null,
                        'anchornut_10nos' => $values[9] ?? null,
                        'trackstop_3nos_1' => $values[10] ?? null,
                        'failsafe_4nos_1' => $values[11] ?? null,
                        'loosehole_3nos' => $values[12] ?? null,
                        'cleatangleheighttotrackbase1_1' => $values[13] ?? null,
                        'cleatangleheighttotrackbase1_2' => $values[14] ?? null,
                        '7mushroom_1rivettail' => $values[15] ?? null,
                        'cleatangleloosehole_8nos' => $values[16] ?? null,
                        'side2loosehole_2nos_1' => $values[17] ?? null,
                        'distancefailsafetotrackbase_1' => $values[18] ?? null,
                        'side1loosehole_2nos_1' => $values[19] ?? null,
                        'distancefailsafetotrackbase_2' => $values[20] ?? null,
                        'failsafeposition_1' => $values[21] ?? null,
                        'packerside_1' => $values[22] ?? null,
                        'failsafeposition_2' => $values[23] ?? null,
                        'gapbetweenpart_1' => $values[24] ?? null,
                        'side2loosehole_2nos_2' => $values[25] ?? null,
                        'distancelowerparttotrackbase_1' => $values[26] ?? null,
                        'side1loosehole_2nos_2' => $values[27] ?? null,
                        'distancelowerparttotrackbase_2' => $values[28] ?? null,
                        'failsafeposition_3' => $values[29] ?? null,
                        'doublerposition_1' => $values[30] ?? null,
                        'doublerposition_2' => $values[31] ?? null,
                        'anchornut_8nos' => $values[32] ?? null,
                        'trackstop_3nos_2' => $values[33] ?? null,
                        'failsafe_4nos_2' => $values[34] ?? null,
                        'loosehole_4nos' => $values[35] ?? null,
                        'cleatangleheighttotrackbase2_1' => $values[36] ?? null,
                        'cleatangleheighttotrackbase2_2' => $values[37] ?? null,
                        '6mushroom_1rivettail' => $values[38] ?? null,
                        'cleatangleloosehole_7nos' => $values[39] ?? null,
                        'packerside_2' => $values[40] ?? null,
                    ]);
                    break;

                case 2:
                    $callIdData = array_merge($callIdData, [
                        'quality_status' => $values[1] ?? null,
                        'part_number' => $values[2] ?? null,
                        'job_order' => $values[3] ?? null,
                        'track' => $values[4] ?? null,
                        'orientation' => $values[5] ?? null,
                        'check_port_starboard' => $values[6] ?? null,
                        'model_check' => $values[7] ?? null,
                        'output_part_recognition' => $values[8] ?? null,
                        'anchornut_10nos' => $values[9] ?? null,
                        'trackstop_3nos_1' => $values[10] ?? null,
                        'failsafe_4nos_1' => $values[11] ?? null,
                        'loosehole_3nos' => $values[12] ?? null,
                        'cleatangleheighttotrackbase1_1' => $values[13] ?? null,
                        'cleatangleheighttotrackbase1_2' => $values[14] ?? null,
                        '7mushroom_1rivettail' => $values[15] ?? null,
                        'cleatangleloosehole_8nos' => $values[16] ?? null,
                        'side2loosehole_2nos_1' => $values[17] ?? null,
                        'distancefailsafetotrackbase_1' => $values[18] ?? null,
                        'side1loosehole_2nos_1' => $values[19] ?? null,
                        'distancefailsafetotrackbase_2' => $values[20] ?? null,
                        'failsafeposition_1' => $values[21] ?? null,
                        'packerside_1' => $values[22] ?? null,
                        'failsafeposition_2' => $values[23] ?? null,
                        'gapbetweenpart_1' => $values[24] ?? null,
                        'gapbetweenpart_2' => $values[25] ?? null,
                        'gapbetweenpart_3' => $values[26] ?? null,
                        'side2loosehole_2nos_2' => $values[27] ?? null,
                        'distancelowerparttotrackbase_1' => $values[28] ?? null,
                        'side1loosehole_2nos_2' => $values[29] ?? null,
                        'distancelowerparttotrackbase_2' => $values[30] ?? null,
                        'failsafeposition_3' => $values[31] ?? null,
                        'doublerposition_1' => $values[32] ?? null,
                        'doublerposition_2' => $values[33] ?? null,
                        'anchornut_8nos' => $values[34] ?? null,
                        'trackstop_3nos_2' => $values[35] ?? null,
                        'failsafe_4nos_2' => $values[36] ?? null,
                        'loosehole_4nos' => $values[37] ?? null,
                        'cleatangleheighttotrackbase2_1' => $values[38] ?? null,
                        'cleatangleheighttotrackbase2_2' => $values[39] ?? null,
                        '6mushroom_1rivettail' => $values[40] ?? null,
                        'cleatangleloosehole_7nos' => $values[41] ?? null,
                        'packerside_2' => $values[42] ?? null,
                    ]);
                    break;

                case 3:
                    $callIdData = array_merge($callIdData, [
                        'quality_status' => $values[1] ?? null,
                        'part_number' => $values[2] ?? null,
                        'job_order' => $values[3] ?? null,
                        'track' => $values[4] ?? null,
                        'orientation' => $values[5] ?? null,
                        'check_port_starboard' => $values[6] ?? null,
                        'model_check' => $values[7] ?? null,
                        'output_part_recognition' => $values[8] ?? null,
                        'anchornut_10nos' => $values[9] ?? null,
                        'trackstop_3nos_1' => $values[10] ?? null,
                        'failsafe_4nos_1' => $values[11] ?? null,
                        'loosehole_3nos' => $values[12] ?? null,
                        '7mushroom_1rivettail' => $values[13] ?? null,
                        'cleatangleloosehole_8nos' => $values[14] ?? null,
                        'cleatangleheighttotrackbase1_1' => $values[15] ?? null,
                        'cleatangleheighttotrackbase1_2' => $values[16] ?? null,
                        'side2loosehole_2nos_1' => $values[17] ?? null,
                        'distancefailsafetotrackbase_1' => $values[18] ?? null,
                        'side1loosehole_2nos_1' => $values[19] ?? null,
                        'distancefailsafetotrackbase_2' => $values[20] ?? null,
                        'failsafeposition_1' => $values[21] ?? null,
                        'packerside_1' => $values[22] ?? null,
                        'failsafeposition_2' => $values[23] ?? null,
                        'gapbetweenpart_1' => $values[24] ?? null,
                        'gapbetweenpart_2' => $values[25] ?? null,
                        'gapbetweenpart_3' => $values[26] ?? null,
                        'side2loosehole_2nos_2' => $values[27] ?? null,
                        'distancelowerparttotrackbase_1' => $values[28] ?? null,
                        'side1loosehole_2nos_2' => $values[29] ?? null,
                        'distancelowerparttotrackbase_2' => $values[30] ?? null,
                        'failsafeposition_3' => $values[31] ?? null,
                        'doublerposition_1' => $values[32] ?? null,
                        'doublerposition_2' => $values[33] ?? null,
                        'anchornut_8nos' => $values[34] ?? null,
                        'trackstop_3nos_2' => $values[35] ?? null,
                        'failsafe_4nos_2' => $values[36] ?? null,
                        'loosehole_4nos' => $values[37] ?? null,
                        '6mushroom_1rivettail' => $values[38] ?? null,
                        'cleatangleloosehole_7nos' => $values[39] ?? null,
                        'cleatangleheighttotrackbase2_1' => $values[40] ?? null,
                        'cleatangleheighttotrackbase2_2' => $values[41] ?? null,
                        'packerside_2' => $values[42] ?? null,
                    ]);
                    break;

                case 4:
                    $callIdData = array_merge($callIdData, [
                        'quality_status' => $values[1] ?? null,
                        'part_number' => $values[2] ?? null,
                        'job_order' => $values[3] ?? null,
                        'track' => $values[4] ?? null,
                        'orientation' => $values[5] ?? null,
                        'check_port_starboard' => $values[6] ?? null,
                        'model_check' => $values[7] ?? null,
                        'output_part_recognition' => $values[8] ?? null,
                        'anchornut_10nos' => $values[9] ?? null,
                        'trackstop_3nos_1' => $values[10] ?? null,
                        'failsafe_4nos_1' => $values[11] ?? null,
                        'loosehole_3nos' => $values[12] ?? null,
                        '7mushroom_1rivettail' => $values[13] ?? null,
                        'cleatangleloosehole_8nos' => $values[14] ?? null,
                        'cleatangleheighttotrackbase1_1' => $values[15] ?? null,
                        'cleatangleheighttotrackbase1_2' => $values[16] ?? null,
                        'side2loosehole_2nos_1' => $values[17] ?? null,
                        'distancefailsafetotrackbase_1' => $values[18] ?? null,
                        'side1loosehole_2nos_1' => $values[19] ?? null,
                        'distancefailsafetotrackbase_2' => $values[20] ?? null,
                        'failsafeposition_1' => $values[21] ?? null,
                        'packerside_1' => $values[22] ?? null,
                        'failsafeposition_2' => $values[23] ?? null,
                        'gapbetweenpart_1' => $values[24] ?? null,
                        'gapbetweenpart_2' => $values[25] ?? null,
                        'gapbetweenpart_3' => $values[26] ?? null,
                        'side2loosehole_2nos_2' => $values[27] ?? null,
                        'distancelowerparttotrackbase_1' => $values[28] ?? null,
                        'spreaderposition' => $values[29] ?? null,
                        'side1loosehole_2nos_2' => $values[30] ?? null,
                        'distancelowerparttotrackbase_2' => $values[31] ?? null,
                        'rivettail_2nos_1' => $values[32] ?? null,
                        'rivettail_2nos_2' => $values[33] ?? null,
                        'doublerposition_1' => $values[34] ?? null,
                        'doublerposition_2' => $values[35] ?? null,
                        'anchornut_8nos' => $values[36] ?? null,
                        'trackstop_3nos_2' => $values[37] ?? null,
                        'failsafe_4nos_2' => $values[38] ?? null,
                        'loosehole_4nos' => $values[39] ?? null,
                        '6mushroom_1rivettail' => $values[40] ?? null,
                        'cleatangleloosehole_7nos' => $values[41] ?? null,
                        'cleatangleheighttotrackbase2_1' => $values[42] ?? null,
                        'cleatangleheighttotrackbase2_2' => $values[43] ?? null,
                        'packerside_2' => $values[44] ?? null,
                    ]);
                    break;

                default:
                    throw new \RuntimeException('Invalid track ID: ' . $trackId);
            }

            // Determine which model to use based on track ID
            $model = match($trackId) {
                1 => CallId1::class,
                2 => CallId2::class,
                3 => CallId3::class,
                4 => CallId4::class,
                default => throw new \RuntimeException('Invalid track ID: ' . $trackId)
            };

            // Log the data being inserted for debugging
            Log::debug("Creating {$model} record with data:", $callIdData);
            
            // Create the record using the appropriate model
            $record = $model::create($callIdData);

            if (!$record) {
                throw new \RuntimeException("Failed to create {$model} record");
            }

            // Update the hub data record
            $this->update([
                'is_processed' => true,
                'processed_at' => now(),
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->update([
                'error_message' => $e->getMessage(),
                'is_processed' => false,
            ]);
            
            Log::error('Error processing hub data', [
                'hub_data_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input_data' => $this->all_str,
                'parsed_values' => $values ?? null,
            ]);
            
            return false;
        }
    }
}
