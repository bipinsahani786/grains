<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'company_id', 'plan_id', 'status', 'trial_ends_at', 
        'current_period_start', 'current_period_end', 'cancel_at_period_end', 
        'gateway', 'gateway_subscription_id'
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'cancel_at_period_end' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
