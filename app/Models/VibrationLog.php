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
}
