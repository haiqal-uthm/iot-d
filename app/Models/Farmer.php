<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ic_number',
        'farm_name',
        'address',
        'profile_image',
        'notes',
        'assigned_orchards'
    ];

    protected $casts = [
        'assigned_orchards' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orchards()
    {
        return $this->belongsToMany(Orchard::class, 'farmer_orchard', 'farmer_id', 'orchard_id');
    }
}