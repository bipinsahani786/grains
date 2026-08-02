<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    protected $fillable = ['sale_id', 'mode', 'amount'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
