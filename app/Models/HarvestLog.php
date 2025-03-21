<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarvestLog extends Model
{
    protected $table = 'harvest_logs';

    protected $casts = [
        'harvest_date' => 'date:Y-m-d', // More explicit format
    ];

    protected $fillable = [
        'orchard', 
        'durian_type', 
        'harvest_date', 
        'total_harvested', 
        'status'
    ];

    // Fixed relationship (assuming orchards table exists)
    public function orchard()
    {
        // If using orchard codes (A, B, C) as primary key in orchards table
        return $this->belongsTo(Orchard::class, 'orchard', 'code');
    }
}
