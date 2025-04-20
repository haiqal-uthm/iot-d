<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'storage_location',
        'quantity',
        'type',
        'remarks'
    ];

    // Relationship with Farmer
    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    // Scope to get transactions for a specific farmer
    public function scopeForFarmer($query, $farmerId)
    {
        return $query->where('farmer_id', $farmerId);
    }

    // Get current stock level for a specific storage location
    public static function getCurrentStock($farmerId, $storageLocation)
    {
        return self::forFarmer($farmerId)
            ->where('storage_location', $storageLocation)
            ->sum('quantity');
    }
}
