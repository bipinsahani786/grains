<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class PartyType extends Model
{
    protected $fillable = ['company_id', 'name', 'slug'];

    public function company()
    {
        return $this->belongsTo(\App\Models\System\Company::class);
    }
}
