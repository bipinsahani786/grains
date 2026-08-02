@extends('layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single { height: 38px; border: 1px solid #ced4da; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
    .input-group > .select2-container--default { width: auto !important; flex: 1 1 auto; }

    .section-divider { border-top: 2px dashed #dee2e6; margin: 1.5rem 0; }
    .section-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #6c757d; margin-bottom: 1rem; }

    /* Payment mode toggle */
    .payment-mode-card { border: 2px solid #dee2e6; border-radius: 10px; padding: 1rem; cursor: pointer; transition: all 0.2s; }
    .payment-mode-card:hover { border-color: var(--bs-primary); }
    .payment-mode-card.active { border-color: var(--bs-primary); background: rgba(var(--bs-primary-rgb), 0.05); }
    .payment-mode-card .mode-icon { font-size: 2rem; }

    /* Summary Card */
    .summary-card { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 10px; }
    .summary-row { display: flex; justify-content: space-between; padding: 0.35rem 0; }
    .summary-row.total { border-top: 2px solid #dee2e6; margin-top: 0.5rem; padding-top: 0.75rem; font-weight: 700; font-size: 1.1rem; }
    .summary-row.discount { color: #dc3545; }
    .summary-row.net { color: #198754; font-size: 1rem; font-weight: 600; }
    .summary-row.outstanding { color: #fd7e14; }

    /* FIFO Badge */
    .fifo-badge { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border-radius: 20px; padding: 3px 10px; font-size: 0.7rem; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">New Sale</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('business.sales.index') }}">Sales</a></li>
                <li class="breadcrumb-item">New Sale</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('business.sales.store') }}" method="POST" id="saleForm">
                            @csrf

                            {{-- SECTION 1: MASTER DETAILS --}}
                            <p class="section-label">1. Master Details</p>
                            <div class="row mb-2">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="date">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="sale_time">Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="sale_time" name="sale_time" value="{{ old('sale_time', date('H:i')) }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="party_id">Customer (Party) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select class="form-select select2-party" id="party_id" name="party_id" required>
                                            <option value="">Select Party</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" data-phone="{{ $customer->phone ?? '' }}" data-address="{{ $customer->address ?? '' }}" {{ old('party_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createPartyModal" title="Add New Party"><i class="feather-plus"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="broker_id">Broker <small class="text-muted">(Optional)</small></label>
                                    <div class="input-group">
                                        <select class="form-select select2-broker" id="broker_id" name="broker_id">
                                            <option value="">No Broker</option>
                                            @foreach($brokers as $broker)
                                                <option value="{{ $broker->id }}" data-phone="{{ $broker->phone ?? '' }}" data-address="{{ $broker->address ?? '' }}" {{ old('broker_id') == $broker->id ? 'selected' : '' }}>{{ $broker->name }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createBrokerModal" title="Add New Broker"><i class="feather-plus"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="section-divider"></div>

                            {{-- SECTION 2: GRAIN + LOT ALLOCATION --}}
                            <div class="d-flex align-items-center mb-3">
                                <p class="section-label mb-0">2. Grain & Lot Allocation</p>
                                <span class="fifo-badge ms-2">FIFO Auto-fill</span>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="grain_id">Grain <span class="text-danger">*</span></label>
                                    <select class="form-select" id="grain_id" name="grain_id" required onchange="fetchLotInfo()">
                                        <option value="">Select Grain</option>
                                        @foreach($grains as $grain)
                                            <option value="{{ $grain->id }}" {{ old('grain_id') == $grain->id ? 'selected' : '' }}>{{ $grain->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Total Quantity <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0.01" class="form-control" id="quantity" name="quantity" value="{{ old('quantity') }}" placeholder="Qty" required oninput="onMainQtyChange()">
                                        <select class="form-select" id="unit" name="unit" style="max-width: 120px;" required onchange="onQtyUnitChange()">
                                            <option value="Quintal" {{ old('unit','Quintal')=='Quintal'?'selected':'' }}>Quintal</option>
                                            <option value="Kg"      {{ old('unit')=='Kg'?'selected':'' }}>Kg</option>
                                            <option value="Ton"     {{ old('unit')=='Ton'?'selected':'' }}>Ton</option>
                                            <option value="Bags"    {{ old('unit')=='Bags'?'selected':'' }}>Bags</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="rate">Rate (per unit) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" min="0" class="form-control" id="rate" name="rate" value="{{ old('rate') }}" placeholder="0.00" required oninput="recalculate()">
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <div class="w-100 p-2 rounded text-center" style="background:rgba(102,126,234,0.08); border:1px dashed #667eea;">
                                        <div class="text-muted" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:1px;">Total Available Stock</div>
                                        <div class="fw-bold" style="color:#667eea; font-size:1.1rem;" id="totalStockDisplay">—</div>
                                    </div>
                                </div>
                            </div>

                            {{-- LOT ALLOCATION TABLE --}}
                            <div id="lotSectionWrapper" class="d-none mb-3">
                                <div class="card border-0" style="border-left: 4px solid #667eea !important; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; color:#667eea;"><i class="feather-layers me-1"></i> Available Lots — Enter Qty to Take</span>
                                            <button type="button" class="btn btn-sm" style="background:#667eea; color:#fff; font-size:0.75rem;" onclick="autoFifo()">⚡ Auto FIFO Fill</button>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-sm mb-1 align-middle" style="font-size:0.85rem;">
                                                <thead style="background:rgba(102,126,234,0.08);">
                                                    <tr>
                                                        <th style="color:#667eea;">Lot #</th>
                                                        <th style="color:#667eea;">Godown</th>
                                                        <th style="color:#667eea;">Purchase Date</th>
                                                        <th style="color:#667eea; text-align:right;">Available</th>
                                                        <th style="color:#667eea; text-align:right;">Avail. (<span class="global-unit-label">Base</span>)</th>
                                                        <th style="color:#667eea; text-align:right;">Purchase Rate</th>
                                                        <th style="color:#667eea; min-width:130px;">Take Qty (<span class="lot-unit-label">Unit</span>)</th>
                                                        <th style="color:#667eea; text-align:right;">Remaining After</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="lotTableBody">
                                                    {{-- JS fills rows --}}
                                                </tbody>
                                            </table>
                                        </div>

                                        {{-- Allocation Status Bar --}}
                                        <div class="mt-2 d-flex align-items-center gap-3 flex-wrap">
                                            <div>
                                                <span class="text-muted" style="font-size:0.75rem;">Total Allocated:</span>
                                                <strong id="totalAllocated" style="color:#667eea;">0.00</strong>
                                                <span class="lot-unit-label text-muted" style="font-size:0.75rem;">Quintal</span>
                                            </div>
                                            <div>
                                                <span class="text-muted" style="font-size:0.75rem;">Requested:</span>
                                                <strong id="totalRequested" style="color:#333;">0.00</strong>
                                                <span class="lot-unit-label text-muted" style="font-size:0.75rem;">Quintal</span>
                                            </div>
                                            <div id="allocationStatus"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="lotNoGrainMsg" class="alert alert-warning py-2 d-none">
                                <i class="feather-alert-circle me-1"></i> Please select a grain to see available lots.
                            </div>

                            <div class="section-divider"></div>

                            {{-- SECTION 3: ADD-ON CHARGES --}}
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <p class="section-label mb-0">3. Add-on Charges</p>
                                        <button type="button" class="btn btn-sm btn-light-info" onclick="addChargeRow()">+ Add Charge</button>
                                    </div>
                                    <table class="table table-sm table-bordered" id="chargesTable">
                                        <thead>
                                            <tr>
                                                <th>Charge Name</th>
                                                <th>Type</th>
                                                <th>Amount (₹)</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="chargesBody"></tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2" class="text-end">Charges Net:</th>
                                                <th id="chargesTotalDisplay">0.00</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                {{-- SECTION 4: PAYMENT MODE --}}
                                <div class="col-md-6">
                                    <p class="section-label mb-3">4. Recovery / Payment Mode</p>
                                    <input type="hidden" name="payment_mode" id="payment_mode" value="regular">
                                    <div class="row g-3 mb-3">
                                        <div class="col-6">
                                            <div class="payment-mode-card active" id="card_regular" onclick="setPaymentMode('regular')">
                                                <div class="mode-icon">📅</div>
                                                <div class="fw-bold mt-1">Regular Mode</div>
                                                <small class="text-muted">Recover later (40-50 days). Full amount goes to ledger.</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="payment-mode-card" id="card_cash_discount" onclick="setPaymentMode('cash_discount')">
                                                <div class="mode-icon">⚡</div>
                                                <div class="fw-bold mt-1">Cash Discount</div>
                                                <small class="text-muted">Instant recovery — cut % and settle now.</small>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Discount % (only for cash_discount) --}}
                                    <div id="discountSection" class="d-none mb-3">
                                        <label class="form-label">Discount % <span class="text-danger">*</span></label>
                                        <div class="input-group" style="max-width: 200px;">
                                            <input type="number" step="0.01" min="0" max="100" class="form-control" id="discount_percent" name="discount_percent" value="{{ old('discount_percent', 0) }}" placeholder="e.g. 2" oninput="recalculate()">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>

                                    {{-- Multi Split Payments --}}
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted fw-bold">Split Payments</small>
                                        <button type="button" class="btn btn-sm btn-light-success" onclick="addPaymentRow()">+ Add Payment</button>
                                    </div>
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr><th>Mode</th><th>Amount (₹)</th><th></th></tr>
                                        </thead>
                                        <tbody id="paymentsBody"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="section-divider"></div>

                            {{-- SECTION 5: NOTES + SUMMARY --}}
                            <div class="row mb-4">
                                <div class="col-md-7">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" name="notes" rows="3" placeholder="Optional notes...">{{ old('notes') }}</textarea>
                                </div>
                                <div class="col-md-5">
                                    <div class="summary-card p-3">
                                        <p class="section-label mb-2">Order Summary</p>
                                        <div class="summary-row">
                                            <span>Items Total:</span>
                                            <span id="sum_items">₹0.00</span>
                                        </div>
                                        <div class="summary-row">
                                            <span>Add-on Charges:</span>
                                            <span id="sum_charges">₹0.00</span>
                                        </div>
                                        <div class="summary-row total">
                                            <span>Grand Total:</span>
                                            <span id="sum_grand">₹0.00</span>
                                        </div>
                                        <div class="summary-row discount" id="sum_discount_row" style="display:none!important;">
                                            <span>Discount (<span id="sum_disc_pct">0</span>%):</span>
                                            <span id="sum_discount">-₹0.00</span>
                                        </div>
                                        <div class="summary-row net" id="sum_net_row" style="display:none!important;">
                                            <span>Net Receivable:</span>
                                            <span id="sum_net">₹0.00</span>
                                        </div>
                                        <div class="summary-row">
                                            <span>Amount Paid:</span>
                                            <span id="sum_paid" class="text-success">₹0.00</span>
                                        </div>
                                        <div class="summary-row outstanding">
                                            <span>Outstanding:</span>
                                            <span id="sum_outstanding">₹0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end align-items-center gap-3">
                                <a href="{{ route('business.sales.index') }}" class="btn btn-danger px-5 py-2 fw-bolder text-uppercase" style="min-width: 200px;">
                                    <i class="feather-x-circle me-2"></i> Cancel
                                </a>
                                <button type="button" class="btn btn-success px-5 py-2 fw-bolder text-uppercase" style="min-width: 200px;" onclick="submitSaleForm()">
                                    <i class="feather-check-circle me-2"></i> Save Sale
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
{{-- Create Party Modal --}}
<div class="modal fade" id="createPartyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Party</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="ajaxCreatePartyForm">
                @csrf
                <div class="modal-body">
                    <div id="modalPartyAlert" class="alert d-none"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Entity Type <span class="text-danger">*</span></label>
                            <select name="entity_type" class="form-select" required>
                                <option value="Individual">Individual</option>
                                <option value="Company">Company</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Party Type <span class="text-danger">*</span></label>
                            <select name="party_type_id" class="form-select" required>
                                <option value="">Select</option>
                                @foreach($partyTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Party Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Aadhar Number</label>
                            <input type="text" name="aadhar_no" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">GST Number</label>
                            <input type="text" name="gst_no" class="form-control">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Opening Balance</label>
                            <input type="number" name="opening_balance" class="form-control" value="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Balance Type</label>
                            <select name="opening_balance_type" class="form-select">
                                <option value="credit">Credit (Payable)</option>
                                <option value="debit">Debit (Receivable)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveParty">Save Party</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Create Broker Modal --}}
<div class="modal fade" id="createBrokerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="feather-user me-2"></i> Add New Broker</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="ajaxCreateBrokerForm">
                @csrf
                <div class="modal-body">
                    <div id="modalBrokerAlert" class="alert d-none"></div>
                    <h6 class="text-muted mb-3" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px;">Basic Details</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Broker Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <hr>
                    <h6 class="text-muted mb-3" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px;">Default Commission <small class="text-info">(Optional)</small></h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Commission Type</label>
                            <select name="commission_type" class="form-select">
                                <option value="">-- No Commission --</option>
                                <option value="per_quintal">Per Quintal (₹)</option>
                                <option value="per_kg">Per KG (₹)</option>
                                <option value="percentage">Percentage (%)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Rate</label>
                            <input type="number" name="rate" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Applies To</label>
                            <select name="applies_to" class="form-select">
                                <option value="both">Both</option>
                                <option value="purchase">Purchase Only</option>
                                <option value="sale">Sale Only</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveBroker"><i class="feather-save me-1"></i> Save Broker</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
let chargeIndex = 0;
let paymentIndex = 0;

// ---- Select2 ----
$(document).ready(function() {
    function formatUser(user) {
        if (!user.id) return user.text;
        var phone = $(user.element).data('phone') || 'N/A';
        var address = $(user.element).data('address') || 'N/A';
        return $('<div><strong>' + user.text + '</strong></div><div style="font-size:0.82em;color:#6c757d;">📱 ' + phone + ' | 📍 ' + address + '</div>');
    }
    
    function formatSelection(user) {
        if (!user.id) return user.text;
        var phone = $(user.element).data('phone') || '';
        return phone ? (user.text + ' - ' + phone) : user.text;
    }

    $('.select2-party').select2({ templateResult: formatUser, templateSelection: formatSelection });
    $('.select2-broker').select2({ templateResult: formatUser, templateSelection: formatSelection });

    // Party AJAX create
    $('#ajaxCreatePartyForm').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btnSaveParty').prop('disabled', true).text('Saving...');
        $.ajax({
            url: "{{ route('business.parties.store') }}", type: 'POST', data: $(this).serialize(),
            success: function(r) {
                if (r.success) {
                    var opt = new Option(r.party.name, r.party.id, true, true);
                    $(opt).attr('data-phone', r.party.phone || '').attr('data-address', r.party.address || '');
                    $('#party_id').append(opt).trigger('change');
                    $('#createPartyModal').modal('hide');
                    $('#ajaxCreatePartyForm')[0].reset();
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON && xhr.responseJSON.errors ? Object.values(xhr.responseJSON.errors).map(e => e.join(', ')).join('<br>') : 'Error creating party.';
                $('#modalPartyAlert').removeClass('d-none').addClass('alert-danger').html(errors);
            },
            complete: function() { $btn.prop('disabled', false).text('Save Party'); }
        });
    });

    // Broker AJAX create
    $('#ajaxCreateBrokerForm').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btnSaveBroker').prop('disabled', true).text('Saving...');
        $.ajax({
            url: "{{ route('business.financials.commissions.store-broker') }}", type: 'POST', data: $(this).serialize(),
            success: function(r) {
                if (r.success) {
                    var opt = new Option(r.broker.name, r.broker.id, true, true);
                    $(opt).attr('data-phone', r.broker.phone || '').attr('data-address', r.broker.address || '');
                    $('#broker_id').append(opt).trigger('change');
                    $('#createBrokerModal').modal('hide');
                    $('#ajaxCreateBrokerForm')[0].reset();
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON && xhr.responseJSON.errors ? Object.values(xhr.responseJSON.errors).map(e => e.join(', ')).join('<br>') : 'Error creating broker.';
                $('#modalBrokerAlert').removeClass('d-none').addClass('alert-danger').html(errors);
            },
            complete: function() { $btn.prop('disabled', false).html('<i class="feather-save me-1"></i> Save Broker'); }
        });
    });
});

// =============== LOT ALLOCATION SYSTEM ===============
var _lotsCache = [];
var BAG_WEIGHT_KG = 50;

// Convert from Quintal to selected unit
function qtlToUnit(qtl, unit) {
    if (unit === 'Kg')    return qtl * 100;
    if (unit === 'Ton')   return qtl / 10;
    if (unit === 'Bags')  return (qtl * 100) / BAG_WEIGHT_KG;
    return qtl; // Quintal
}

// Convert from selected unit to Quintal
function unitToQtl(qty, unit) {
    if (!qty) return 0;
    if (unit === 'Kg')    return qty / 100;
    if (unit === 'Ton')   return qty * 10;
    if (unit === 'Bags')  return (qty * BAG_WEIGHT_KG) / 100;
    return qty; // Quintal
}

function unitLabel(unit) {
    return unit || 'Quintal';
}

function fetchLotInfo() {
    var grainId = $('#grain_id').val();
    if (!grainId) {
        $('#lotSectionWrapper').addClass('d-none');
        $('#totalStockDisplay').text('—');
        _lotsCache = [];
        return;
    }
    $.ajax({
        url: "{{ url('/business/api/lots') }}",
        method: 'GET',
        data: { grain_id: grainId },
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(data) {
            if (data.error) {
                $('#totalStockDisplay').text('API Error');
                console.error('Lots API error:', data.error);
                return;
            }
            _lotsCache = data.lots || [];
            if (data.displayUnit) {
                $('.global-unit-label').text(data.displayUnit);
            }
            var totalQtl = _lotsCache.reduce(function(s, l) { return s + parseFloat(l.remaining_quantity); }, 0);
            var unit = $('#unit').val() || 'Quintal';
            $('#totalStockDisplay').text(qtlToUnit(totalQtl, unit).toFixed(2) + ' ' + unit);

            if (_lotsCache.length === 0) {
                $('#lotSectionWrapper').addClass('d-none');
                $('#totalStockDisplay').text('No Stock');
                return;
            }
            $('#lotSectionWrapper').removeClass('d-none');
            renderLotTable(false);
        },
        error: function(xhr) {
            $('#lotSectionWrapper').addClass('d-none');
            $('#totalStockDisplay').text('Error ' + xhr.status);
            console.error('Lots API fail:', xhr.status, xhr.responseText);
        }
    });
}

function onMainQtyChange() {
    recalculate();
    if (_lotsCache.length > 0) {
        autoFifo();
    }
}

function onQtyUnitChange() {
    recalculate();
    // Update total stock display in new unit
    if (_lotsCache.length > 0) {
        var unit = $('#unit').val() || 'Quintal';
        var totalQtl = _lotsCache.reduce(function(s, l) { return s + parseFloat(l.remaining_quantity); }, 0);
        $('#totalStockDisplay').text(qtlToUnit(totalQtl, unit).toFixed(2) + ' ' + unit);
        // Update column header unit labels
        $('.lot-unit-label').text(unit);
        // Re-render table with updated unit columns
        renderLotTable(null); // null = keep existing user inputs
    }
    updateAllocationStatus();
}

function renderLotTable(doAutoFifo) {
    var unit = $('#unit').val() || 'Quintal';
    $('.lot-unit-label').text(unit);

    var body = $('#lotTableBody');
    // Save existing user inputs before re-rendering
    var existingInputs = {};
    body.find('input.lot-take-qty').each(function() {
        existingInputs[$(this).data('lot-id')] = $(this).val();
    });

    body.empty();

    _lotsCache.forEach(function(lot, idx) {
        var qtlAvail    = parseFloat(lot.remaining_quantity);
        var unitAvail   = qtlToUnit(qtlAvail, unit);

        var existingVal = (doAutoFifo === null && existingInputs[lot.id] !== undefined) ? existingInputs[lot.id] : '';
        var remainingQtlAfter = qtlAvail; // will be updated by JS
        var unitAfter   = qtlToUnit(remainingQtlAfter, unit);

        var row = `<tr id="lot_row_${lot.id}" class="lot-row">
            <td><span class="badge" style="background:rgba(102,126,234,0.15); color:#667eea; font-size:0.8rem;">${lot.lot_no}</span></td>
            <td><small class="text-muted">${lot.godown}</small></td>
            <td><small class="text-muted">${lot.purchase_date || '—'}</small></td>
            <td class="text-end"><strong>${unitAvail.toFixed(2)}</strong> <small class="text-muted">${unit}</small></td>
            <td class="text-end"><span class="text-muted" style="font-size:0.8rem;">${lot.remaining_quantity_display}</span></td>
            <td class="text-end"><strong style="color:#28a745;">${lot.rate_display || '—'}</strong><br><small class="text-muted">Purchase Rate</small></td>
            <td>
                <input type="number" step="0.01" min="0" max="${unitAvail.toFixed(4)}"
                    class="form-control form-control-sm lot-take-qty"
                    data-lot-id="${lot.id}"
                    data-lot-qtl="${qtlAvail}"
                    data-lot-rate="${lot.rate || 0}"
                    placeholder="0.00"
                    value="${existingVal}"
                    oninput="onLotInputChange(this)"
                    name="lots[${idx}][quantity]">
                <input type="hidden" name="lots[${idx}][id]" value="${lot.id}">
            </td>
            <td class="text-end lot-remaining-after" id="lot_remaining_${lot.id}" style="font-size:0.82rem;">${unitAvail.toFixed(2)} ${unit}</td>
        </tr>`;
        body.append(row);
    });

    if (doAutoFifo === true) {
        autoFifo();
    } else {
        updateAllocationStatus();
    }
}

function onLotInputChange(input) {
    var lotId      = $(input).data('lot-id');
    var lotQtl     = parseFloat($(input).data('lot-qtl'));
    var unit       = $('#unit').val() || 'Quintal';
    var takeInUnit = parseFloat($(input).val()) || 0;
    var takeInQtl  = unitToQtl(takeInUnit, unit);

    // Cap at available
    if (takeInQtl > lotQtl) {
        var maxInUnit = qtlToUnit(lotQtl, unit);
        $(input).val(maxInUnit.toFixed(2));
        takeInQtl = lotQtl;
        takeInUnit = parseFloat($(input).val()) || 0;
    }

    // Update "Remaining After" cell
    var remainingQtl  = lotQtl - takeInQtl;
    var remainingUnit = qtlToUnit(remainingQtl, unit);
    $('#lot_remaining_' + lotId).text(remainingUnit.toFixed(2) + ' ' + unit);

    // ── Sync total lot qty → main quantity field ───────────────────────
    syncLotTotalToQty();
}

function syncLotTotalToQty() {
    var unit = $('#unit').val() || 'Quintal';
    var totalTakenUnit = 0;
    $('.lot-take-qty').each(function() {
        totalTakenUnit += parseFloat($(this).val()) || 0;
    });
    // Set the main quantity field to sum of lots (in current unit)
    $('#quantity').val(totalTakenUnit > 0 ? totalTakenUnit.toFixed(2) : '');
    updateAllocationStatus();
    recalculate();
}

function updateAllocationStatus() {
    var unit = $('#unit').val() || 'Quintal';
    var totalTakenQtl = 0;
    $('.lot-take-qty').each(function() {
        totalTakenQtl += unitToQtl(parseFloat($(this).val()) || 0, unit);
    });

    // "Requested" is now driven by what's in the main qty field
    var requestedQtl = unitToQtl(parseFloat($('#quantity').val()) || 0, unit);

    $('#totalAllocated').text(qtlToUnit(totalTakenQtl, unit).toFixed(2));
    $('#totalRequested').text(qtlToUnit(requestedQtl, unit).toFixed(2));

    var diff = Math.abs(requestedQtl - totalTakenQtl);
    var statusHtml = '';
    if (totalTakenQtl === 0) {
        statusHtml = '';
    } else if (diff < 0.001) {
        statusHtml = '<span class="badge bg-success"><i class="feather-check me-1"></i> Fully Allocated ✓</span>';
    } else if (totalTakenQtl < requestedQtl) {
        statusHtml = `<span class="badge bg-warning text-dark"><i class="feather-alert-triangle me-1"></i> Short by ${qtlToUnit(requestedQtl - totalTakenQtl, unit).toFixed(2)} ${unit}</span>`;
    } else {
        statusHtml = `<span class="badge bg-danger"><i class="feather-x me-1"></i> Over by ${qtlToUnit(totalTakenQtl - requestedQtl, unit).toFixed(2)} ${unit}</span>`;
    }
    $('#allocationStatus').html(statusHtml);
}

function autoFifo() {
    var unit = $('#unit').val() || 'Quintal';
    var requestedQtl = unitToQtl(parseFloat($('#quantity').val()) || 0, unit);
    var remaining = requestedQtl;

    $('.lot-take-qty').each(function() {
        var lotQtl = parseFloat($(this).data('lot-qtl'));
        var takeQtl = Math.min(lotQtl, remaining);
        var takeUnit = qtlToUnit(takeQtl, unit);
        $(this).val(takeUnit > 0 ? takeUnit.toFixed(2) : '');
        remaining -= takeQtl;

        // Update remaining-after
        var lotId = $(this).data('lot-id');
        var remainingAfterQtl = lotQtl - takeQtl;
        $('#lot_remaining_' + lotId).text(qtlToUnit(remainingAfterQtl, unit).toFixed(2) + ' ' + unit);
    });

    updateAllocationStatus();
}

// ---- Charges ----
function addChargeRow() {
    var i = chargeIndex++;
    var row = `<tr id="charge_row_${i}">
        <td><input type="text" name="charges[${i}][name]" class="form-control form-control-sm" placeholder="e.g. Loading" required></td>
        <td>
            <select name="charges[${i}][type]" class="form-select form-select-sm" onchange="recalculate()">
                <option value="add">Add (+)</option>
                <option value="deduct">Deduct (–)</option>
            </select>
        </td>
        <td><input type="number" step="0.01" name="charges[${i}][amount]" class="form-control form-control-sm charge-amount" placeholder="0.00" required oninput="recalculate()"></td>
        <td><button type="button" class="btn btn-sm btn-light-danger" onclick="removeRow('charge_row_${i}');recalculate()"><i class="feather-trash-2"></i></button></td>
    </tr>`;
    $('#chargesBody').append(row);
}

// ---- Payments ----
function addPaymentRow() {
    var i = paymentIndex++;
    var row = `<tr id="payment_row_${i}">
        <td>
            <select name="payments[${i}][mode]" class="form-select form-select-sm">
                <option value="cash">Cash</option>
                <option value="upi">UPI</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="cheque">Cheque</option>
            </select>
        </td>
        <td><input type="number" step="0.01" min="0" name="payments[${i}][amount]" class="form-control form-control-sm payment-amount" placeholder="0.00" required oninput="recalculate()"></td>
        <td><button type="button" class="btn btn-sm btn-light-danger" onclick="removeRow('payment_row_${i}');recalculate()"><i class="feather-trash-2"></i></button></td>
    </tr>`;
    $('#paymentsBody').append(row);
    recalculate();
}

function removeRow(id) { $('#' + id).remove(); recalculate(); }

// ---- Payment Mode Toggle ----
function setPaymentMode(mode) {
    $('#payment_mode').val(mode);
    $('#card_regular, #card_cash_discount').removeClass('active');
    $('#card_' + mode).addClass('active');
    if (mode === 'cash_discount') {
        $('#discountSection').removeClass('d-none');
    } else {
        $('#discountSection').addClass('d-none');
        $('#discount_percent').val(0);
    }
    recalculate();
}

// ---- Recalculate Summary ----
function recalculate() {
    var qty  = parseFloat($('#quantity').val()) || 0;
    var rate = parseFloat($('#rate').val()) || 0;
    var itemsTotal = qty * rate;

    var chargesNet = 0;
    $('.charge-amount').each(function() {
        var amt = parseFloat($(this).val()) || 0;
        var type = $(this).closest('tr').find('select').val();
        chargesNet += (type === 'deduct') ? -amt : amt;
    });

    var grandTotal = itemsTotal + chargesNet;
    var mode = $('#payment_mode').val();
    var discPct = (mode === 'cash_discount') ? (parseFloat($('#discount_percent').val()) || 0) : 0;
    var discAmt  = Math.round(grandTotal * discPct / 100 * 100) / 100;
    var netAmount = grandTotal - discAmt;

    var amountPaid = 0;
    $('.payment-amount').each(function() { amountPaid += parseFloat($(this).val()) || 0; });
    var outstanding = netAmount - amountPaid;

    $('#sum_items').text('₹' + itemsTotal.toFixed(2));
    $('#chargesTotalDisplay').text(chargesNet.toFixed(2));
    $('#sum_charges').text('₹' + chargesNet.toFixed(2));
    $('#sum_grand').text('₹' + grandTotal.toFixed(2));

    if (mode === 'cash_discount' && discPct > 0) {
        $('#sum_disc_pct').text(discPct);
        $('#sum_discount').text('-₹' + discAmt.toFixed(2));
        $('#sum_net').text('₹' + netAmount.toFixed(2));
        $('#sum_discount_row').css('display', '');
        $('#sum_net_row').css('display', '');
    } else {
        $('#sum_discount_row').css('display', 'none');
        $('#sum_net_row').css('display', 'none');
    }

    $('#sum_paid').text('₹' + amountPaid.toFixed(2));
    $('#sum_outstanding').text('₹' + outstanding.toFixed(2));

    updateAllocationStatus();
}

function submitSaleForm() {
    if (document.getElementById('saleForm').checkValidity()) {
        document.getElementById('saleForm').submit();
    } else {
        document.getElementById('saleForm').reportValidity();
    }
}
</script>
@endpush
