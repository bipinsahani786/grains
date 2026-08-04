<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'expense_no', 'sequence_no', 'date', 'category_id',
        'description', 'amount', 'payment_mode', 'reference_no',
        'vendor_name', 'vendor_party_id',
        'is_recurring', 'recurring_frequency', 'recurring_next_date', 'recurring_parent_id',
        'notes', 'created_by',
    ];

    protected $casts = [
        'is_recurring'        => 'boolean',
        'date'                => 'date',
        'recurring_next_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function vendorParty()
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'vendor_party_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'created_by');
    }

    public function recurringParent()
    {
        return $this->belongsTo(Expense::class, 'recurring_parent_id');
    }

    public function recurringChildren()
    {
        return $this->hasMany(Expense::class, 'recurring_parent_id');
    }
}
