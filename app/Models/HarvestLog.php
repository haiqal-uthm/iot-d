<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Farmer;
use App\Models\Orchard;
use App\Models\Durian;

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
        'farmer_id',
        'orchard_id',
        'durian_id',
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

    // Relationships
    public function orchard()
    {
        return $this->belongsTo(Orchard::class);
    }

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }
    
    public function durian()
    {
        return $this->belongsTo(Durian::class, 'durian_id');
    }

    public function scopeForFarmer($query, $userId)
    {
        return $query->whereHas('farmer', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with(['orchard', 'durian']);
    }

    // Accessors for JSON fields - fixed ternary operators
    public function getGradeAttribute($value)
    {
        return is_array($value) ? $value : (json_decode($value, true) ?: []);
    }
    
    public function getConditionAttribute($value)
    {
        return is_array($value) ? $value : (json_decode($value, true) ?: []);
    }
    
    public function getStorageLocationAttribute($value)
    {
        return is_array($value) ? $value : (json_decode($value, true) ?: []);
    }
}
