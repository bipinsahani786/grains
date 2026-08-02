<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $guarded = [];

    public function grain()
    {
        return $this->belongsTo(Grain::class);
    }

    public function godown()
    {
        return $this->belongsTo(Godown::class);
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'created_by');
    }
}
