<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = ['storage', 'durian_type', 'quantity', 'status'];

    public function durian()
    {
        return $this->belongsTo(Durian::class, 'durian_type', 'name');
    }
}
