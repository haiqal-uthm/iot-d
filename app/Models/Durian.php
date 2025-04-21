<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Durian extends Model
{
    use HasFactory;

    protected $table = 'durians';
    protected $fillable = ['name', 'total'];

    // One Durian can have many Orchards
    public function orchards()
    {
        return $this->hasMany(Orchard::class);
    }
    
    // Relationship with inventory transactions
    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'durian_id');
    }
}
