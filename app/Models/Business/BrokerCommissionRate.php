<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class BrokerCommissionRate extends Model
{
    protected $fillable = ['company_id', 'broker_id', 'grain_id', 'commission_type', 'rate', 'applies_to'];

    public function broker()
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'broker_id');
    }

    public function grain()
    {
        return $this->belongsTo(Grain::class, 'grain_id');
    }
}
