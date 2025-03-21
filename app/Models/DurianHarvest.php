<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DurianHarvest extends Model
{
    use HasFactory;

    protected $fillable = ['harvester_id', 'count'];
}
