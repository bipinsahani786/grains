<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Payment extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }
    protected $fillable = [
        'company_id', 'party_id', 'direction', 'amount', 'mode', 
        'bank_account_id', 'reference_no', 'related_type', 'related_id', 
        'cash_discount_pct', 'notes', 'created_by', 'date'
    ];

    public function related()
    {
        return $this->morphTo();
    }
}
