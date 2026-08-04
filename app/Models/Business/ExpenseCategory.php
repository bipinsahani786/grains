<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = ['company_id', 'name', 'color', 'icon', 'is_system'];

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id');
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Core\Company::class);
    }
}
