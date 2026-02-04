<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProcessedData extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Job Card Data
        'job_card_job_order',
        'job_card_part_number',
        'job_card_timestamp',
        
        // SAP Data
        'sap_job_order',
        'sap_part_number',
        'sap_timestamp',
        
        // Actual Part Data
        'actual_part_job_order',
        'actual_part_number',
        'actual_part_timestamp',
        
        // Comparison Results
        'job_order_match_jobcard_sap',
        'part_number_match_jobcard_sap',
        'job_order_match_sap_actual',
        'part_number_match_sap_actual',
        
        // Status and Metadata
        'status',
        'error_message',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'job_card_timestamp' => 'datetime',
        'sap_timestamp' => 'datetime',
        'actual_part_timestamp' => 'datetime',
        'job_order_match_jobcard_sap' => 'boolean',
        'part_number_match_jobcard_sap' => 'boolean',
        'job_order_match_sap_actual' => 'boolean',
        'part_number_match_sap_actual' => 'boolean',
        'metadata' => 'array',
    ];
}
