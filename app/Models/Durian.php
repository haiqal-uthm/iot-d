<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Durian extends Model
{
    use HasFactory;

    protected $table = 'durians';

    protected $fillable = ['orchard_id', 'name', 'total', 'orchard'];

    // Relationship with orchards (assuming an Orchard model exists)
    public function orchard()
    {
        return $this->hasMany(Orchard::class, 'durian_id');
    }
}
