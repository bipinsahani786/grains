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
}
