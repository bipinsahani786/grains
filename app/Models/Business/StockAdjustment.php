<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class StockAdjustment extends Model
{
    protected $fillable = [
        'company_id', 'lot_id', 'grain_id', 'quantity_before', 
        'quantity_after', 'reason', 'notes', 'adjusted_by', 'date'
    ];

    public function grain()
    {
        return $this->belongsTo(Grain::class, 'grain_id');
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class, 'lot_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'adjusted_by');
    }
}
