<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orchard extends Model
{
    use HasFactory;

    protected $fillable = ['orchardName', 'numTree', 'device_id'];

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
    public function durian()
    {
        return $this->belongsTo(Durian::class, 'durian_id');
    }
    public function vibrationLogs()
    {
        return $this->hasMany(VibrationLog::class, 'device_id', 'device_id');
    }

}
