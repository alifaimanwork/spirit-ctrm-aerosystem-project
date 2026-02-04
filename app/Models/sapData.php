<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sapData extends Model
{
    use HasFactory;

    protected $fillable = [
        'joborder',
        'partno'
    ];
}
