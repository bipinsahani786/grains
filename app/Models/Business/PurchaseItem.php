<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
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
}
