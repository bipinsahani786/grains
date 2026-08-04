<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company;
        return view('business.settings.index', compact('company'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'brand_name' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:1024',
            
            'purchase_prefix' => 'nullable|string|max:20',
            'purchase_year_format' => 'nullable|string|max:20',
            'purchase_sequence_length' => 'required|integer|min:1|max:10',
            'purchase_sequence_start' => 'required|integer|min:1',

            'sale_prefix' => 'nullable|string|max:20',
            'sale_year_format' => 'nullable|string|in:YY-YY,YYYY-YY,YYYY',
            'sale_sequence_length' => 'nullable|integer|min:2|max:8',
            'sale_sequence_start' => 'nullable|integer|min:1',
            'bag_weight_kg' => 'required|numeric|min:1|max:1000',
            'display_unit' => 'required|string|in:Quintal,Kg,Ton,Bags',
            
            'purchase_header' => 'nullable|image|max:2048',
            'purchase_footer' => 'nullable|image|max:2048',
            'sale_header' => 'nullable|image|max:2048',
            'sale_footer' => 'nullable|image|max:2048',
            'billing_terms_conditions' => 'nullable|string',
            'billing_bank_details' => 'nullable|string',
            'billing_authorised_signatory_text' => 'nullable|string|max:100',
        ]);

        $company = Auth::user()->company;
        $company->brand_name = $request->brand_name;
        $company->gstin = $request->gstin;
        $company->phone = $request->phone;
        $company->address = $request->address;
        
        $company->purchase_prefix = $request->purchase_prefix ?? '';
        $company->purchase_year_format = $request->purchase_year_format;
        $company->purchase_sequence_length = $request->purchase_sequence_length;
        $company->purchase_sequence_start = $request->purchase_sequence_start;
        
        $company->sale_prefix = $request->sale_prefix ?? '';
        $company->sale_year_format = $request->sale_year_format;
        $company->sale_sequence_length = $request->sale_sequence_length;
        $company->sale_sequence_start = $request->sale_sequence_start;
        $company->bag_weight_kg = $request->bag_weight_kg;
        $company->display_unit  = $request->display_unit;
        
        $company->billing_terms_conditions = $request->billing_terms_conditions;
        $company->billing_bank_details = $request->billing_bank_details;
        $company->billing_authorised_signatory_text = $request->billing_authorised_signatory_text ?? 'Authorised Signatory';

        if ($request->hasFile('logo')) {
            $company->logo_path = $request->file('logo')->store('company_billing', 'public');
        }
        if ($request->hasFile('favicon')) {
            $company->favicon_path = $request->file('favicon')->store('company_billing', 'public');
        }
        if ($request->hasFile('purchase_header')) {
            $company->purchase_header_path = $request->file('purchase_header')->store('company_billing', 'public');
        }
        if ($request->hasFile('purchase_footer')) {
            $company->purchase_footer_path = $request->file('purchase_footer')->store('company_billing', 'public');
        }
        if ($request->hasFile('sale_header')) {
            $company->sale_header_path = $request->file('sale_header')->store('company_billing', 'public');
        }
        if ($request->hasFile('sale_footer')) {
            $company->sale_footer_path = $request->file('sale_footer')->store('company_billing', 'public');
        }

        $company->save();

        return redirect()->route('business.settings.index')->with('success', 'Settings updated successfully.');
    }
}
