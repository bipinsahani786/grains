<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class SaleLotAllocation extends Model
{
    protected $fillable = [
        'sale_id', 'lot_id', 'quantity_taken', 'cost_rate'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }
}
