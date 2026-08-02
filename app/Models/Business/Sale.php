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
        'payment_mode', 'discount_percent', 'discount_amount', 'net_amount', 'amount_paid', 'outstanding_amount'
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

    /** Total collected so far via sale_collections */
    public function getTotalCollectedAttribute(): float
    {
        return (float) $this->collections->sum('amount');
    }

    /** Outstanding = net_amount - total_collected */
    public function getRemainingOutstandingAttribute(): float
    {
        $net = (float) ($this->net_amount ?? $this->total_amount ?? 0);
        return max(0, $net - $this->total_collected);
    }

    /** Is fully collected? */
    public function getIsFullyCollectedAttribute(): bool
    {
        return $this->remaining_outstanding < 0.01;
    }
}
