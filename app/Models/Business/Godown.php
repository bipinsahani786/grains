<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class Godown extends Model
{
    protected $guarded = [];

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }
}
