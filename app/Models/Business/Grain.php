<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Grain extends Model
{
    protected $fillable = ['company_id', 'name', 'unit', 'opening_stock'];
}
