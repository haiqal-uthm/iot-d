<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'device_id',
        'status',
    ];

    protected $table = 'devices';

    public function orchard()
    {
        return $this->belongsTo(Orchard::class, 'orchard_id'); // This will correctly reference the orchard_id
    }
    
}
