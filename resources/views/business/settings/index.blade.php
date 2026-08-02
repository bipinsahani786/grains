@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Business Settings</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Settings</li>
            </ul>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Invoice / Bill Sequencing</h5>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('business.settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <h6 class="mb-3 text-primary border-bottom pb-2">Purchase Sequence Settings</h6>
                            <div class="row mb-4">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Purchase Prefix</label>
                                    <input type="text" name="purchase_prefix" id="purchase_prefix" class="form-control" value="{{ old('purchase_prefix', $company->purchase_prefix) }}" placeholder="e.g. PUR-">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Financial Year Format</label>
                                    <select name="purchase_year_format" id="purchase_year_format" class="form-control">
                                        <option value="" {{ old('purchase_year_format', $company->purchase_year_format) == '' ? 'selected' : '' }}>None</option>
                                        <option value="YY-YY" {{ old('purchase_year_format', $company->purchase_year_format) == 'YY-YY' ? 'selected' : '' }}>YY-YY (e.g. 26-27)</option>
                                        <option value="YYYY-YY" {{ old('purchase_year_format', $company->purchase_year_format) == 'YYYY-YY' ? 'selected' : '' }}>YYYY-YY (e.g. 2026-27)</option>
                                        <option value="YYYY" {{ old('purchase_year_format', $company->purchase_year_format) == 'YYYY' ? 'selected' : '' }}>YYYY (e.g. 2026)</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Sequence Length</label>
                                    <select name="purchase_sequence_length" id="purchase_sequence_length" class="form-control">
                                        @for($i = 2; $i <= 8; $i++)
                                            <option value="{{ $i }}" {{ old('purchase_sequence_length', $company->purchase_sequence_length) == $i ? 'selected' : '' }}>{{ $i }} Digits (e.g. {{ str_pad(1, $i, '0', STR_PAD_LEFT) }})</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Starting Sequence No.</label>
                                    <input type="number" name="purchase_sequence_start" id="purchase_sequence_start" class="form-control" value="{{ old('purchase_sequence_start', $company->purchase_sequence_start) }}" min="1">
                                </div>
                                <div class="col-12 mt-2">
                                    <div class="p-3 bg-light rounded d-flex align-items-center">
                                        <span class="me-3 fw-bold text-muted">Live Preview:</span>
                                        <span class="fs-5 fw-bolder text-primary" id="purchase_preview"></span>
                                    </div>
                                </div>
                            </div>

                            <h6 class="mb-3 text-success border-bottom pb-2">Sale Sequence Settings</h6>
                            <div class="row mb-4">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Sale Prefix</label>
                                    <input type="text" name="sale_prefix" id="sale_prefix" class="form-control" value="{{ old('sale_prefix', $company->sale_prefix) }}" placeholder="e.g. SAL-">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Financial Year Format</label>
                                    <select name="sale_year_format" id="sale_year_format" class="form-control">
                                        <option value="" {{ old('sale_year_format', $company->sale_year_format) == '' ? 'selected' : '' }}>None</option>
                                        <option value="YY-YY" {{ old('sale_year_format', $company->sale_year_format) == 'YY-YY' ? 'selected' : '' }}>YY-YY (e.g. 26-27)</option>
                                        <option value="YYYY-YY" {{ old('sale_year_format', $company->sale_year_format) == 'YYYY-YY' ? 'selected' : '' }}>YYYY-YY (e.g. 2026-27)</option>
                                        <option value="YYYY" {{ old('sale_year_format', $company->sale_year_format) == 'YYYY' ? 'selected' : '' }}>YYYY (e.g. 2026)</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Sequence Length</label>
                                    <select name="sale_sequence_length" id="sale_sequence_length" class="form-control">
                                        @for($i = 2; $i <= 8; $i++)
                                            <option value="{{ $i }}" {{ old('sale_sequence_length', $company->sale_sequence_length) == $i ? 'selected' : '' }}>{{ $i }} Digits (e.g. {{ str_pad(1, $i, '0', STR_PAD_LEFT) }})</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Starting Sequence No.</label>
                                    <input type="number" name="sale_sequence_start" id="sale_sequence_start" class="form-control" value="{{ old('sale_sequence_start', $company->sale_sequence_start) }}" min="1">
                                </div>
                                <div class="col-12 mt-2">
                                    <div class="p-3 bg-light rounded d-flex align-items-center">
                                        <span class="me-3 fw-bold text-muted">Live Preview:</span>
                                        <span class="fs-5 fw-bolder text-success" id="sale_preview"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h5 class="mb-3 text-success">Unit & Conversion Settings</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted fs-12 text-uppercase fw-bold">Standard Bag Weight (Kg)</label>
                                    <input type="number" name="bag_weight_kg" class="form-control" value="{{ old('bag_weight_kg', $company->bag_weight_kg ?? 50) }}" required min="1" step="0.01">
                                    <small class="text-muted fs-12">Used when "Bags" is selected as unit during purchase or sale.</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted fs-12 text-uppercase fw-bold">
                                        Display Unit <span class="text-danger">*</span>
                                    </label>
                                    <select name="display_unit" class="form-control" id="display_unit_select">
                                        @foreach(['Quintal' => 'Quintal (Qtl)', 'Kg' => 'Kilogram (Kg)', 'Ton' => 'Metric Ton (Ton)', 'Bags' => 'Bags'] as $val => $label)
                                            <option value="{{ $val }}" {{ old('display_unit', $company->display_unit ?? 'Quintal') == $val ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted fs-12">All stock, lot, report quantities will be shown in this unit.</small>
                                </div>
                                <div class="col-md-4 mb-3 d-flex align-items-end">
                                    <div class="w-100 p-3 rounded text-center" style="background:rgba(102,126,234,0.08); border:1px dashed #667eea;">
                                        <div class="text-muted" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:1px;">Live Example</div>
                                        <div class="fw-bold" style="color:#667eea; font-size:1.4rem;" id="unit_preview_val">100.00</div>
                                        <div class="text-muted" id="unit_preview_label" style="font-size:0.85rem;">Qtl</div>
                                        <div class="text-muted mt-1" style="font-size:0.72rem;">= 100 Quintal stored in DB</div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">

                            <h5 class="mb-3 text-info">Billing & Invoice Settings</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted fs-12 text-uppercase fw-bold">Purchase Bill Header Image</label>
                                    <input type="file" name="purchase_header" class="form-control" accept="image/*">
                                    @if(isset($company->purchase_header_path))
                                        <div class="mt-2"><img src="{{ Storage::url($company->purchase_header_path) }}" height="50" alt="Purchase Header" class="border"></div>
                                    @endif
                                    <small class="text-muted fs-12">Leave blank to use default text header.</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted fs-12 text-uppercase fw-bold">Sale Bill Header Image</label>
                                    <input type="file" name="sale_header" class="form-control" accept="image/*">
                                    @if(isset($company->sale_header_path))
                                        <div class="mt-2"><img src="{{ Storage::url($company->sale_header_path) }}" height="50" alt="Sale Header" class="border"></div>
                                    @endif
                                    <small class="text-muted fs-12">Leave blank to use default text header.</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted fs-12 text-uppercase fw-bold">Purchase Bill Footer Image</label>
                                    <input type="file" name="purchase_footer" class="form-control" accept="image/*">
                                    @if(isset($company->purchase_footer_path))
                                        <div class="mt-2"><img src="{{ Storage::url($company->purchase_footer_path) }}" height="50" alt="Purchase Footer" class="border"></div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted fs-12 text-uppercase fw-bold">Sale Bill Footer Image</label>
                                    <input type="file" name="sale_footer" class="form-control" accept="image/*">
                                    @if(isset($company->sale_footer_path))
                                        <div class="mt-2"><img src="{{ Storage::url($company->sale_footer_path) }}" height="50" alt="Sale Footer" class="border"></div>
                                    @endif
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label text-muted fs-12 text-uppercase fw-bold">Bank Details (For Bills)</label>
                                    <textarea name="billing_bank_details" class="form-control" rows="4" placeholder="Bank Name: SBI&#10;Account Holder: ...&#10;Account No: ...&#10;IFSC: ...">{{ old('billing_bank_details', $company->billing_bank_details) }}</textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label text-muted fs-12 text-uppercase fw-bold">Terms & Conditions</label>
                                    <textarea name="billing_terms_conditions" class="form-control" rows="4" placeholder="1. Goods once sold will not be taken back.">{{ old('billing_terms_conditions', $company->billing_terms_conditions) }}</textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted fs-12 text-uppercase fw-bold">Authorised Signatory Text</label>
                                    <input type="text" name="billing_authorised_signatory_text" class="form-control" value="{{ old('billing_authorised_signatory_text', $company->billing_authorised_signatory_text ?? 'Authorised Signatory') }}">
                                </div>
                            </div>


                            <button type="submit" class="btn btn-primary mt-3">Save Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    function generateYearString(format) {
        if (!format) return '';
        const now = new Date();
        const currentYear = now.getFullYear();
        const currentMonth = now.getMonth() + 1; // 1-12
        
        let startYear = currentYear;
        let endYear = currentYear + 1;
        
        // Assuming April to March financial year
        if (currentMonth < 4) {
            startYear = currentYear - 1;
            endYear = currentYear;
        }

        if (format === 'YY-YY') {
            return startYear.toString().slice(-2) + '-' + endYear.toString().slice(-2) + '-';
        } else if (format === 'YYYY-YY') {
            return startYear.toString() + '-' + endYear.toString().slice(-2) + '-';
        } else if (format === 'YYYY') {
            return startYear.toString() + '-';
        }
        return '';
    }

    function padNumber(num, size) {
        let s = String(num);
        while (s.length < size) s = "0" + s;
        return s;
    }

    function updatePreview(type) {
        const prefix = document.getElementById(type + '_prefix').value;
        const yearFormat = document.getElementById(type + '_year_format').value;
        const length = parseInt(document.getElementById(type + '_sequence_length').value);
        const start = parseInt(document.getElementById(type + '_sequence_start').value) || 1;
        
        const yearStr = generateYearString(yearFormat);
        const seqStr = padNumber(start, length);
        
        document.getElementById(type + '_preview').innerText = prefix + yearStr + seqStr;
    }

    ['purchase', 'sale'].forEach(type => {
        const ids = [type + '_prefix', type + '_year_format', type + '_sequence_length', type + '_sequence_start'];
        ids.forEach(id => {
            document.getElementById(id).addEventListener('input', () => updatePreview(type));
            document.getElementById(id).addEventListener('change', () => updatePreview(type));
        });
        updatePreview(type);
    });

    // ── Unit Display Preview ──────────────────────────────────────────────
    var bagWeightInput = document.querySelector('[name="bag_weight_kg"]');
    var unitSelect = document.getElementById('display_unit_select');
    var previewVal = document.getElementById('unit_preview_val');
    var previewLabel = document.getElementById('unit_preview_label');
    var BASE_QTL = 100;

    function updateUnitPreview() {
        var unit = unitSelect ? unitSelect.value : 'Quintal';
        var bagWeight = parseFloat(bagWeightInput ? bagWeightInput.value : 50) || 50;
        var val, label;
        if (unit === 'Kg')        { val = BASE_QTL * 100;                   label = 'Kg'; }
        else if (unit === 'Ton')  { val = BASE_QTL / 10;                    label = 'Ton'; }
        else if (unit === 'Bags') { val = (BASE_QTL * 100) / bagWeight;     label = 'Bags'; }
        else                      { val = BASE_QTL;                          label = 'Qtl'; }
        if (previewVal)   previewVal.textContent   = val.toFixed(2);
        if (previewLabel) previewLabel.textContent = label;
    }

    if (unitSelect)     unitSelect.addEventListener('change', updateUnitPreview);
    if (bagWeightInput) bagWeightInput.addEventListener('input', updateUnitPreview);
    updateUnitPreview();

});
</script>
@endpush
