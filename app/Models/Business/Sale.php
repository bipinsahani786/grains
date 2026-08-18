<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Sale extends Model
{
    use SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }
    protected $fillable = [
        'company_id', 'sale_no', 'sequence_no', 'date', 'sale_time', 'party_id', 'broker_id', 'grain_id',
        'quantity', 'unit', 'rate', 'total_amount', 'notes', 'created_by',
        'payment_mode', 'discount_percent', 'discount_amount', 'net_amount', 'amount_paid', 'outstanding_amount',
        'po_no', 'bags_count', 'truck_no', 'driver_name', 'driver_phone', 'truck_fare', 'freight_advance', 'freight_balance'
    ];

    public function party()
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'party_id');
    }

    public function broker()
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'broker_id');
    }

    public function grain()
    {
        return $this->belongsTo(Grain::class, 'grain_id');
    }

    public function saleLotAllocations()
    {
        return $this->hasMany(SaleLotAllocation::class, 'sale_id');
    }

    public function charges()
    {
        return $this->hasMany(SaleCharge::class, 'sale_id');
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class, 'sale_id');
    }

    public function collections()
    {
        return $this->hasMany(SaleCollection::class, 'sale_id');
    }

    public function brokerCommission()
    {
        return $this->morphOne(BrokerCommissionEntry::class, 'reference');
    }

    /** Total paid = initial payments + later recovery collections */
    public function getTotalPaidAttribute(): float
    {
        $initialPaid = (float) ($this->relationLoaded('payments') ? $this->payments->sum('amount') : $this->payments()->sum('amount'));
        $laterCollections = (float) ($this->relationLoaded('collections') ? $this->collections->sum('amount') : $this->collections()->sum('amount'));
        
        if ($initialPaid == 0 && $laterCollections == 0) {
            return (float) ($this->amount_paid ?? 0);
        }
        
        return $initialPaid + $laterCollections;
    }

    /** Outstanding = net_amount - total_paid */
    public function getRemainingOutstandingAttribute(): float
    {
        $net = (float) ($this->net_amount ?? $this->total_amount ?? 0);
        return max(0, round($net - $this->total_paid, 2));
    }

    /** Payment Status: 'paid', 'partial', 'unpaid' */
    public function getPaymentStatusAttribute(): string
    {
        $net = (float) ($this->net_amount ?? $this->total_amount ?? 0);
        $paid = $this->total_paid;
        if ($net > 0 && $paid >= $net - 0.01) {
            return 'paid';
        } elseif ($paid > 0.01) {
            return 'partial';
        } else {
            return 'unpaid';
        }
    }

    /** Is fully collected? */
    public function getIsFullyCollectedAttribute(): bool
    {
        return $this->remaining_outstanding < 0.01;
    }
}
