<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'brand_name', 'address', 'type', 'email', 'phone', 'whatsapp_no', 'gstin', 'pan_no',
        'logo_path', 'favicon_path', 'signature_stamp_path', 'is_active', 'purchase_prefix', 'sale_prefix',
        'purchase_year_format', 'purchase_sequence_length', 'purchase_sequence_start',
        'sale_year_format', 'sale_sequence_length', 'sale_sequence_start', 'bag_weight_kg', 'display_unit',
        'purchase_header_path', 'purchase_footer_path', 'sale_header_path', 'sale_footer_path',
        'billing_terms_conditions', 'billing_bank_details', 'billing_authorised_signatory_text',
        'bank_name', 'account_holder', 'account_no', 'ifsc_code', 'branch_name', 'upi_id'
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

    public function getPanDisplayAttribute(): string
    {
        if (!empty($this->pan_no)) {
            return strtoupper($this->pan_no);
        }
        if (!empty($this->gstin) && strlen(trim($this->gstin)) >= 15) {
            return strtoupper(substr(trim($this->gstin), 2, 10));
        }
        return 'N/A';
    }

    public function getFormattedBankDetailsAttribute(): string
    {
        if (!empty($this->bank_name) || !empty($this->account_no)) {
            $lines = [];
            if ($this->bank_name) $lines[] = "Bank: " . $this->bank_name . ($this->branch_name ? " (" . $this->branch_name . ")" : "");
            if ($this->account_holder) $lines[] = "A/C Holder Name: " . $this->account_holder;
            if ($this->account_no) $lines[] = "A/C No: " . $this->account_no;
            if ($this->ifsc_code) $lines[] = "IFSC Code: " . strtoupper($this->ifsc_code);
            if ($this->upi_id) $lines[] = "UPI ID: " . $this->upi_id;
            return implode("\n", $lines);
        }

        if (!empty($this->billing_bank_details)) {
            return $this->billing_bank_details;
        }

        return "Bank: \nA/C Holder Name: \nA/C No: \nIFSC Code: ";
    }
}
