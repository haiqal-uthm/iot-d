<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VibrationLog extends Model
{
    protected $table = 'vibration_logs';

    public $timestamps = false;

    protected $fillable = ['device_id', 'vibration_count', 'log_type', 'timestamp'];

    protected $casts = [
        'timestamp' => 'datetime', 
    ];

    public function orchard()
    {
        return $this->belongsTo(Orchard::class, 'device_id', 'device_id');
    }
    
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id'); // Changed to reference 'id' as owner key
    }
    
    // Add an accessor to get farm name through relationships
    public function getFarmNameAttribute()
    {
        if ($this->orchard && $this->orchard->farmer && $this->orchard->farmer->user) {
            return $this->orchard->farmer->user->name;
        }
        
        return 'Unknown Farm';
    }
    
    // Add an accessor for fall count (assuming vibration_count is the fall count)
    public function getFallCountAttribute()
    {
        return $this->vibration_count ?? 0;
    }
}
