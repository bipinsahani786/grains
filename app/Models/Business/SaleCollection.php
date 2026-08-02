<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class SaleCollection extends Model
{
    protected $fillable = [
        'company_id', 'sale_id', 'amount', 'mode',
        'collected_at', 'reference_no', 'notes', 'created_by',
    ];

    protected $casts = [
        'collected_at' => 'date',
        'amount'       => 'float',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
