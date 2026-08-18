<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class SaleCharge extends Model
{
    protected $fillable = ['sale_id', 'name', 'type', 'amount'];
}
