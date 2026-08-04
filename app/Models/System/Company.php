<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'brand_name', 'address', 'type', 'email', 'phone', 'gstin', 
        'logo_path', 'favicon_path', 'signature_stamp_path', 'is_active', 'purchase_prefix', 'sale_prefix',
        'purchase_year_format', 'purchase_sequence_length', 'purchase_sequence_start',
        'sale_year_format', 'sale_sequence_length', 'sale_sequence_start', 'bag_weight_kg', 'display_unit',
        'purchase_header_path', 'purchase_footer_path', 'sale_header_path', 'sale_footer_path',
        'billing_terms_conditions', 'billing_bank_details', 'billing_authorised_signatory_text'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(\App\Models\System\Subscription::class);
    }

    public function users()
    {
        return $this->hasMany(\App\Models\Core\User::class);
    }
}
