<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class PurchaseCharge extends Model
{
    protected $fillable = ['purchase_id', 'type', 'amount'];
}
