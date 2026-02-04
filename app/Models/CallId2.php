<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallId2 extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'callid_2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hub_data_id',
        'track_identification',
        'quality_status',
        'part_number',
        'job_order',
        'track',
        'orientation',
        'check_port_starboard',
        'model_check',
        'output_part_recognition',
        'anchornut_10nos',
        'trackstop_3nos_1',
        'failsafe_4nos_1',
        'loosehole_3nos',
        'cleatangleheighttotrackbase1_1',
        'cleatangleheighttotrackbase1_2',
        '7mushroom_1rivettail',
        'cleatangleloosehole_8nos',
        'side2loosehole_2nos_1',
        'distancefailsafetotrackbase_1',
        'side1loosehole_2nos_1',
        'distancefailsafetotrackbase_2',
        'failsafeposition_1',
        'packerside_1',
        'failsafeposition_2',
        'gapbetweenpart_1',
        'gapbetweenpart_2',
        'gapbetweenpart_3',
        'side2loosehole_2nos_2',
        'distancelowerparttotrackbase_1',
        'side1loosehole_2nos_2',
        'distancelowerparttotrackbase_2',
        'failsafeposition_3',
        'doublerposition_1',
        'doublerposition_2',
        'anchornut_8nos',
        'trackstop_3nos_2',
        'failsafe_4nos_2',
        'loosehole_4nos',
        'cleatangleheighttotrackbase2_1',
        'cleatangleheighttotrackbase2_2',
        '6mushroom_1rivettail',
        'cleatangleloosehole_7nos',
        'packerside_2',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the data record associated with the callid_2.
     */
    public function dataRecord()
    {
        return $this->morphOne(DataRecord::class, 'callable');
    }
}
