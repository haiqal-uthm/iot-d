<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Orchard;
use App\Models\VibrationLog;
use App\Models\HarvestLog;


class Device extends Model
{
    use HasFactory;

    protected $primaryKey = 'device_id';
    protected $fillable = [
        'name',
        'device_id',
        'status',
    ];

    protected $table = 'devices';

    public function orchards()
    {
        return $this->hasMany(Orchard::class, 'device_id', 'device_id');
    }

    public function vibrationLogs()
    {
        return $this->hasMany(VibrationLog::class, 'device_id', 'device_id');
    }
    
    // Override the delete method to handle related records
    public function delete()
    {
        // Clear device reference in related orchards first
        $this->orchards()->update(['device_id' => null]);
        
        // Delete all related records
        $this->vibrationLogs()->delete();
        $this->harvestLogs()->delete();
        
        return parent::delete();
    }

    // Add harvestLogs relationship
    public function harvestLogs()
    {
        return $this->hasManyThrough(
            HarvestLog::class,
            Orchard::class,
            'device_id',  // Foreign key on orchards table
            'orchard_id', // Foreign key on harvest_logs table
            'device_id',  // Local key on devices table
            'id'          // Local key on orchards table
        );
    }
}
