<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallId4 extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'callid_4';

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
        '7mushroom_1rivettail',
        'cleatangleloosehole_8nos',
        'cleatangleheighttotrackbase1_1',
        'cleatangleheighttotrackbase1_2',
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
        'spreaderposition',
        'side1loosehole_2nos_2',
        'distancelowerparttotrackbase_2',
        'rivettail_2nos_1',
        'rivettail_2nos_2',
        'doublerposition_1',
        'doublerposition_2',
        'anchornut_8nos',
        'trackstop_3nos_2',
        'failsafe_4nos_2',
        'loosehole_4nos',
        '6mushroom_1rivettail',
        'cleatangleloosehole_7nos',
        'cleatangleheighttotrackbase2_1',
        'cleatangleheighttotrackbase2_2',
        'packerside_2',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        // All fields are cast as strings as per the migration
        'quality_status' => 'string',
        'part_number' => 'string',
        'job_order' => 'string',
        'track' => 'string',
        'orientation' => 'string',
        'check_port_starboard' => 'string',
        'model_check' => 'string',
        'output_part_recognition' => 'string',
        'anchornut_10nos' => 'string',
        'trackstop_3nos_1' => 'string',
        'failsafe_4nos_1' => 'string',
        'loosehole_3nos' => 'string',
        '7mushroom_1rivettail' => 'string',
        'cleatangleloosehole_8nos' => 'string',
        'cleatangleheighttotrackbase1_1' => 'string',
        'cleatangleheighttotrackbase1_2' => 'string',
        'side2loosehole_2nos_1' => 'string',
        'distancefailsafetotrackbase_1' => 'string',
        'side1loosehole_2nos_1' => 'string',
        'distancefailsafetotrackbase_2' => 'string',
        'failsafeposition_1' => 'string',
        'packerside_1' => 'string',
        'failsafeposition_2' => 'string',
        'gapbetweenpart_1' => 'string',
        'gapbetweenpart_2' => 'string',
        'gapbetweenpart_3' => 'string',
        'side2loosehole_2nos_2' => 'string',
        'distancelowerparttotrackbase_1' => 'string',
        'spreaderposition' => 'string',
        'side1loosehole_2nos_2' => 'string',
        'distancelowerparttotrackbase_2' => 'string',
        'rivettail_2nos_1' => 'string',
        'rivettail_2nos_2' => 'string',
        'doublerposition_1' => 'string',
        'doublerposition_2' => 'string',
        'anchornut_8nos' => 'string',
        'trackstop_3nos_2' => 'string',
        'failsafe_4nos_2' => 'string',
        'loosehole_4nos' => 'string',
        '6mushroom_1rivettail' => 'string',
        'cleatangleloosehole_7nos' => 'string',
        'cleatangleheighttotrackbase2_1' => 'string',
        'cleatangleheighttotrackbase2_2' => 'string',
        'packerside_2' => 'string',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
}
