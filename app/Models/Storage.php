<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    protected $table = 'storage';
    
    protected $fillable = [
        'storage_location',
        'durian_type',
        'quantity',
        'harvest_log_id'
    ];

    public function harvestLog()
    {
        return $this->belongsTo(HarvestLog::class);
    }
}