<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Purchase extends Model
{
    use SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }
    protected $fillable = [
        'company_id', 'purchase_no', 'sequence_no', 'date', 'purchase_time', 'party_id', 'broker_id', 'grain_id',
        'quantity', 'unit', 'moisture', 'rate', 'total_unit', 
        'total_amount', 'notes', 'created_by'
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

    public function lot()
    {
        return $this->hasOne(Lot::class, 'purchase_id');
    }

    public function lots()
    {
        return $this->hasMany(Lot::class, 'purchase_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id');
    }

    public function charges()
    {
        return $this->hasMany(PurchaseCharge::class, 'purchase_id');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'related');
    }

    /** Total paid = sum of payments */
    public function getTotalPaidAttribute(): float
    {
        return (float) ($this->relationLoaded('payments') ? $this->payments->sum('amount') : $this->payments()->sum('amount'));
    }

    /** Outstanding Payable Due = total_amount - total_paid */
    public function getRemainingOutstandingAttribute(): float
    {
        $total = (float) ($this->total_amount ?? 0);
        return max(0, round($total - $this->total_paid, 2));
    }

    /** Payment Status: 'paid', 'partial', 'unpaid' */
    public function getPaymentStatusAttribute(): string
    {
        $total = (float) ($this->total_amount ?? 0);
        $paid = $this->total_paid;
        if ($total > 0 && $paid >= $total - 0.01) {
            return 'paid';
        } elseif ($paid > 0.01) {
            return 'partial';
        } else {
            return 'unpaid';
        }
    }
}
