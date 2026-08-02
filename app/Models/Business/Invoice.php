<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Invoice extends Model
{
    protected $fillable = [
        'company_id', 'invoiceable_type', 'invoiceable_id', 
        'invoice_number', 'pdf_path', 'signed'
    ];

    public function invoiceable()
    {
        return $this->morphTo();
    }
}
