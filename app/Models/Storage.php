<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    protected $table = 'storage';
    
    protected $fillable = [
        'name',
        'description',
        'capacity',
        'temperature_control',
        'status'
    ];

    // Add relationship for inventory transactions
    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'storage_location');
    }
    
    // Helper method to get all active storage locations
    public static function getLocations()
    {
        return self::where('status', 'active')->pluck('name', 'id');
    }

    public function getCurrentStockAttribute()
    {
        return $this->inventoryTransactions()->sum('quantity');
    }

    public function getCapacityPercentageAttribute()
    {
        if ($this->capacity <= 0) return 0;
        return min(100, ($this->current_stock / $this->capacity) * 100);
    }
}