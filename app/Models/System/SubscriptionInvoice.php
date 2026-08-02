<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class SubscriptionInvoice extends Model
{
    protected $fillable = [
        'company_id', 'subscription_id', 'amount', 'status', 
        'invoice_number', 'due_at', 'paid_at'
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(\App\Models\System\Company::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
