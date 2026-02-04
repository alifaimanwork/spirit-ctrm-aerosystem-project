<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComparisonResults extends Model
{
    use HasFactory;
    
    protected $table = 'comparison_results';
    
    protected $fillable = [
        'joborder',
        'partno',
        'status'
    ];
}
