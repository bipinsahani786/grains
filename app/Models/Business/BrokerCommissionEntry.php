<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class BrokerCommissionEntry extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'company_id', 'broker_id', 'reference_type', 'reference_id',
        'date', 'quantity', 'rate', 'commission_type', 'commission_rate', 'commission_amount',
        'payment_status', 'amount_paid', 'paid_at', 'paid_mode', 'payment_notes',
    ];

    protected $casts = [
        'date'             => 'date',
        'paid_at'          => 'date',
        'commission_amount'=> 'float',
        'amount_paid'      => 'float',
        'quantity'         => 'float',
        'rate'             => 'float',
        'commission_rate'  => 'float',
    ];

    public function broker()
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'broker_id');
    }

    public function reference()
    {
        return $this->morphTo();
    }

    /** Amount still owed for this commission entry */
    public function getPendingAmountAttribute(): float
    {
        return max(0, $this->commission_amount - $this->amount_paid);
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'paid';
    }
}
