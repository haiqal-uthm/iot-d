<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DurianHarvest extends Model
{
    use HasFactory;

    protected $table = 'harvest_logs';

    protected $fillable = [
        'farmer_id',
        'orchard_id',
        'durian_id',
        'durian_type',
        'harvest_date',
        'total_harvested',
        'estimated_weight',
        'remarks'
    ];

    protected $casts = [
        'harvest_date' => 'date',
    ];

    // Relationships
    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function orchard()
    {
        return $this->belongsTo(Orchard::class);
    }

    public function durian()
    {
        return $this->belongsTo(Durian::class);
    }

    // Scopes for filtering
    public function scopeFilterByDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('harvest_date', [$startDate, $endDate]);
        }
        return $query;
    }

    public function scopeFilterByFarmer($query, $farmerId)
    {
        if ($farmerId) {
            return $query->where('farmer_id', $farmerId);
        }
        return $query;
    }

    public function scopeFilterByDurian($query, $durianId)
    {
        if ($durianId) {
            return $query->where('durian_id', $durianId);
        }
        return $query;
    }

    public function scopeFilterByOrchard($query, $orchardId)
    {
        if ($orchardId) {
            return $query->where('orchard_id', $orchardId);
        }
        return $query;
    }
}
