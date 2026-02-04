<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HubReportData extends Model
{
    protected $table = 'hub_reportdata';
    
    protected $fillable = [
        'reportid',
        'part_name',
        'part_number',
        'job_order',
        'quality_check'
    ];
    
    protected $primaryKey = 'reportid';
    public $incrementing = false;
}
