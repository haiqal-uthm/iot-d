<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarvestLog extends Model
{
    protected $table = 'harvest_logs';

    protected $casts = [
        'harvest_date' => 'date:Y-m-d',
        'grade' => 'array',
        'condition' => 'array',
        'storage_location' => 'array',
    ];

    protected $fillable = [
        'orchard', 
        'durian_type', 
        'harvest_date', 
        'total_harvested', 
        'status',
        'estimated_weight',
        'grade',
        'condition',
        'storage_location',
        'remarks',
        'harvester_signature'
    ];

    public function orchard()
    {
        return $this->belongsTo(Orchard::class, 'orchard', 'code');
    }
}
