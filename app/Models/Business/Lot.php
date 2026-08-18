<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Lot extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }
    protected $fillable = [
        'company_id', 'lot_no', 'grain_id', 'godown_id', 'purchase_id', 
        'initial_quantity', 'remaining_quantity', 'moisture', 'rate', 'status'
    ];

    public function grain()
    {
        return $this->belongsTo(Grain::class, 'grain_id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function godown()
    {
        return $this->belongsTo(Godown::class, 'godown_id');
    }

    public function saleAllocations()
    {
        return $this->hasMany(SaleLotAllocation::class, 'lot_id');
    }

    public function saleLotAllocations()
    {
        return $this->hasMany(SaleLotAllocation::class, 'lot_id');
    }
}
