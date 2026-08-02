<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class BankAccount extends Model
{
    protected $fillable = ['company_id', 'name', 'account_no', 'bank_name', 'opening_balance'];
}
