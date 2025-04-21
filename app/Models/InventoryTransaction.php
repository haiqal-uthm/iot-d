<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['farmer_id', 'durian_id', 'storage_location', 'quantity', 'type', 'remarks'];

    // Relationship with Farmer
    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    // Relationship with Durian
    public function durian()
    {
        return $this->belongsTo(Durian::class);
    }

    // Scope to get transactions for a specific farmer
    public function scopeForFarmer($query, $farmerId)
    {
        return $query->where('farmer_id', $farmerId);
    }

    // Get current stock level for a specific storage location
    public static function getCurrentStock($farmerId, $storageLocation)
    {
        return self::forFarmer($farmerId)->where('storage_location', $storageLocation)->sum('quantity');
    }

    // Get current stock level for a specific durian type and storage location
    public static function getDurianStock($farmerId, $durianId, $storageLocation = null)
    {
        $query = self::forFarmer($farmerId)->where('durian_id', $durianId);

        if ($storageLocation) {
            $query->where('storage_location', $storageLocation);
        }

        return $query->sum('quantity');
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_location');
    }
}
