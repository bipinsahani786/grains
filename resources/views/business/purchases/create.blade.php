@extends('layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .input-group > .select2-container--default {
        width: auto !important;
        flex: 1 1 auto;
    }
</style>
@endpush

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">New Purchase Bill</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('business.purchases.index') }}">Purchases</a></li>
                <li class="breadcrumb-item">New Bill</li>
            </ul>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('business.purchases.store') }}" method="POST" id="purchaseForm">
                            @csrf
                            
                            <h5 class="mb-4">1. Master Details</h5>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="date">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="purchase_time">Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="purchase_time" name="purchase_time" value="{{ old('purchase_time', date('H:i')) }}" required>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="party_id">Party (Seller) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select class="form-select select2-party" id="party_id" name="party_id" required>
                                            <option value="">Select Party</option>
                                            @foreach($parties as $party)
                                                <option value="{{ $party->id }}" data-phone="{{ $party->phone ?? '' }}" data-address="{{ $party->address ?? '' }}">{{ $party->name }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createPartyModal" title="Add New Party"><i class="feather-plus"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="broker_id">Broker</label>
                                    <div class="input-group">
                                        <select class="form-select select2-broker" id="broker_id" name="broker_id">
                                            <option value="">No Broker</option>
                                            @foreach($brokers as $broker)
                                                <option value="{{ $broker->id }}" data-phone="{{ $broker->phone ?? '' }}" data-address="{{ $broker->address ?? '' }}">{{ $broker->name }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createBrokerModal" title="Add New Broker"><i class="feather-plus"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4" id="detailsRow" style="display: none;">
                                <div class="col-md-6" id="partyDetailsCol" style="display: none;">
                                    <div class="card bg-light border-0 shadow-sm h-100">
                                        <div class="card-body py-2 px-3 text-muted" style="font-size: 0.9em;">
                                            <i class="feather-map-pin text-primary"></i> <span id="partyDetailsAddress"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="brokerDetailsCol" style="display: none;">
                                    <div class="card bg-light border-0 shadow-sm h-100">
                                        <div class="card-body py-2 px-3 text-muted" style="font-size: 0.9em;">
                                            <i class="feather-map-pin text-info"></i> <span id="brokerDetailsAddress"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
                                <h5 class="mb-0">2. Grain Items</h5>
                                <button type="button" class="btn btn-sm btn-light-primary" onclick="addItemRow()">+ Add Item</button>
                            </div>
                            
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered" id="itemsTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="min-width: 180px;">Grain <span class="text-danger">*</span></th>
                                            <th style="min-width: 180px;">Godown <span class="text-danger">*</span></th>
                                            <th style="width: 100px;">Moisture %</th>
                                            <th style="min-width: 120px;">Quantity <span class="text-danger">*</span></th>
                                            <th style="min-width: 120px;">Unit <span class="text-danger">*</span></th>
                                            <th style="min-width: 120px;">Rate <span class="text-danger">*</span></th>
                                            <th style="min-width: 120px;">Amount</th>
                                            <th style="width: 50px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsBody">
                                        <!-- Rows will be injected here via JS -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="6" class="text-end">Items Total:</th>
                                            <th id="itemsTotalDisplay">0.00</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <hr>

                            <div class="row mb-4 mt-4">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">3. Add-on Charges</h5>
                                        <button type="button" class="btn btn-sm btn-light-info" onclick="addChargeRow()">+ Add Charge</button>
                                    </div>
                                    <table class="table table-sm table-bordered" id="chargesTable">
                                        <thead>
                                            <tr>
                                                <th>Charge Name</th>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="chargesBody">
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2" class="text-end">Charges Net:</th>
                                                <th id="chargesTotalDisplay">0.00</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">4. Payment (Optional)</h5>
                                        <button type="button" class="btn btn-sm btn-light-success" onclick="addPaymentRow()">+ Split Payment</button>
                                    </div>
                                    <table class="table table-sm table-bordered" id="paymentsTable">
                                        <thead>
                                            <tr>
                                                <th>Mode</th>
                                                <th>Amount Paid</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="paymentsBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <hr class="my-4 border-dashed">
                            
                            <!-- SUMMARY SECTION -->
                            <div class="row mb-4 justify-content-end">
                                <div class="col-md-5">
                                    <div class="card bg-light border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fs-6 text-muted">Grand Total:</span>
                                                <span class="fs-4 fw-bolder text-primary" id="grandTotalDisplay">₹0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fs-6 text-muted">Outstanding Balance:</span>
                                                <span class="fs-5 fw-bold text-warning" id="outstandingDisplay">₹0.00</span>
                                            </div>
                                            <div class="mt-2 text-end">
                                                <small class="text-muted">Outstanding will be added to the party's ledger.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end align-items-center gap-3">
                                <a href="{{ route('business.purchases.index') }}" class="btn btn-danger px-5 py-2 fw-bolder text-uppercase" style="min-width: 200px;">
                                    <i class="feather-x-circle me-2"></i> Cancel
                                </a>
                                <button type="button" class="btn btn-success px-5 py-2 fw-bolder text-uppercase" style="min-width: 200px;" onclick="submitForm()">
                                    <i class="feather-check-circle me-2"></i> Save Complete Bill
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
<!-- Create Party Modal -->
<div class="modal fade" id="createPartyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Party</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="ajaxCreatePartyForm">
                @csrf
                <div class="modal-body">
                    <div id="modalAlert" class="alert d-none"></div>
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
                                <option value="">Select Party Type</option>
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

<!-- Create Broker Modal -->
<div class="modal fade" id="createBrokerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="feather-user me-2"></i> Add New Broker</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="ajaxCreateBrokerForm">
                @csrf
                <div class="modal-body">
                    <div id="modalBrokerAlert" class="alert d-none"></div>
                    
                    <h6 class="text-muted mb-3 text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing:1px;">Basic Details</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Broker Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. Ramesh Kumar">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="9999999999">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Broker's address"></textarea>
                        </div>
                    </div>

                    <hr>
                    <h6 class="text-muted mb-3 text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing:1px;">Default Commission Rate <small class="text-info">(Optional)</small></h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Commission Type</label>
                            <select name="commission_type" class="form-select">
                                <option value="">-- No Commission --</option>
                                <option value="per_quintal">Per Quintal (Fixed ₹)</option>
                                <option value="per_kg">Per KG (Fixed ₹)</option>
                                <option value="percentage">Percentage (%)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Rate</label>
                            <input type="number" name="rate" class="form-control" step="0.01" min="0" placeholder="e.g. 5">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Applies To</label>
                            <select name="applies_to" class="form-select">
                                <option value="both">Both (Purchase & Sale)</option>
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
    let itemIndex = 0;
    let chargeIndex = 0;
    let paymentIndex = 0;

    const grains = @json($grains);
    const godowns = @json($godowns);

    function addItemRow() {
        let grainOptions = '<option value="">Select</option>';
        grains.forEach(g => grainOptions += `<option value="${g.id}">${g.name}</option>`);

        let godownOptions = '<option value="">Select</option>';
        godowns.forEach(g => godownOptions += `<option value="${g.id}">${g.name}</option>`);

        let tr = `
            <tr id="itemRow_${itemIndex}" class="align-middle">
                <td class="p-2"><select name="items[${itemIndex}][grain_id]" class="form-select" required>${grainOptions}</select></td>
                <td class="p-2"><select name="items[${itemIndex}][godown_id]" class="form-select" required>${godownOptions}</select></td>
                <td class="p-2"><input type="number" step="0.01" name="items[${itemIndex}][moisture]" class="form-control"></td>
                <td class="p-2"><input type="number" step="0.01" name="items[${itemIndex}][quantity]" class="form-control qty-input" onkeyup="calculateTotals()" required></td>
                <td class="p-2">
                    <select name="items[${itemIndex}][unit]" class="form-select" required>
                        <option value="Quintal">Quintal</option>
                        <option value="Kg">Kg</option>
                        <option value="Ton">Ton</option>
                    </select>
                </td>
                <td class="p-2"><input type="number" step="0.01" name="items[${itemIndex}][rate]" class="form-control rate-input" onkeyup="calculateTotals()" required></td>
                <td class="item-amount text-end fw-bold p-2">0.00</td>
                <td class="p-2 text-center"><button type="button" class="btn btn-icon btn-sm btn-light-danger" onclick="removeRow('itemRow_${itemIndex}')"><i class="feather-trash"></i></button></td>
            </tr>
        `;
        document.getElementById('itemsBody').insertAdjacentHTML('beforeend', tr);
        itemIndex++;
    }

    function addChargeRow() {
        let tr = `
            <tr id="chargeRow_${chargeIndex}">
                <td><input type="text" name="charges[${chargeIndex}][name]" class="form-control" placeholder="e.g. Labour" required></td>
                <td>
                    <select name="charges[${chargeIndex}][type]" class="form-select" onchange="calculateTotals()">
                        <option value="add">Add (+)</option>
                        <option value="deduct">Deduct (-)</option>
                    </select>
                </td>
                <td><input type="number" step="0.01" name="charges[${chargeIndex}][amount]" class="form-control charge-amount" onkeyup="calculateTotals()" required></td>
                <td><button type="button" class="btn btn-icon btn-light-danger" onclick="removeRow('chargeRow_${chargeIndex}')"><i class="feather-trash"></i></button></td>
            </tr>
        `;
        document.getElementById('chargesBody').insertAdjacentHTML('beforeend', tr);
        chargeIndex++;
    }

    function addPaymentRow() {
        let tr = `
            <tr id="paymentRow_${paymentIndex}">
                <td>
                    <select name="payments[${paymentIndex}][mode]" class="form-select" required>
                        <option value="cash">Cash</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="upi">UPI</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </td>
                <td><input type="number" step="0.01" name="payments[${paymentIndex}][amount]" class="form-control payment-amount" onkeyup="calculateTotals()" required></td>
                <td><button type="button" class="btn btn-icon btn-light-danger" onclick="removeRow('paymentRow_${paymentIndex}')"><i class="feather-trash"></i></button></td>
            </tr>
        `;
        document.getElementById('paymentsBody').insertAdjacentHTML('beforeend', tr);
        paymentIndex++;
    }

    function removeRow(id) {
        document.getElementById(id).remove();
        calculateTotals();
    }

    function calculateTotals() {
        let itemsTotal = 0;
        let rows = document.querySelectorAll('#itemsBody tr');
        rows.forEach(row => {
            let qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            let rate = parseFloat(row.querySelector('.rate-input').value) || 0;
            let amt = qty * rate;
            row.querySelector('.item-amount').innerText = amt.toFixed(2);
            itemsTotal += amt;
        });
        document.getElementById('itemsTotalDisplay').innerText = itemsTotal.toFixed(2);

        let chargesNet = 0;
        let chargeRows = document.querySelectorAll('#chargesBody tr');
        chargeRows.forEach(row => {
            let amt = parseFloat(row.querySelector('.charge-amount').value) || 0;
            let type = row.querySelector('select[name$="[type]"]').value;
            if (type === 'add') {
                chargesNet += amt;
            } else {
                chargesNet -= amt;
            }
        });
        document.getElementById('chargesTotalDisplay').innerText = chargesNet.toFixed(2);

        let grandTotal = itemsTotal + chargesNet;
        document.getElementById('grandTotalDisplay').innerText = '₹' + grandTotal.toFixed(2);

        let paidTotal = 0;
        let paymentRows = document.querySelectorAll('#paymentsBody tr');
        paymentRows.forEach(row => {
            paidTotal += parseFloat(row.querySelector('.payment-amount').value) || 0;
        });

        let outstanding = grandTotal - paidTotal;
        document.getElementById('outstandingDisplay').innerText = '₹' + outstanding.toFixed(2);
    }

    function submitForm() {
        if(document.getElementById('purchaseForm').checkValidity()) {
            document.getElementById('purchaseForm').submit();
        } else {
            document.getElementById('purchaseForm').reportValidity();
        }
    }

    // Party Select2 with Custom Formatting and AJAX Creation
    $(document).ready(function() {
        function formatParty(party) {
            if (!party.id) {
                return party.text;
            }
            var name = party.text;
            var phone = $(party.element).data('phone') || 'N/A';
            var address = $(party.element).data('address') || 'N/A';
            
            var $party = $(
                '<div><strong>' + name + '</strong></div>' +
                '<div style="font-size: 0.85em; color: #6c757d;">' +
                '<i class="feather-phone"></i> ' + phone + ' | <i class="feather-map-pin"></i> ' + address +
                '</div>'
            );
            return $party;
        };

        function formatPartySelection(party) {
            if (!party.id) return party.text;
            var phone = $(party.element).data('phone') || '';
            return phone ? (party.text + ' - ' + phone) : party.text;
        }

        function updateDetailsRow() {
            var partyVis = $('#partyDetailsCol').is(':visible');
            var brokerVis = $('#brokerDetailsCol').is(':visible');
            if (partyVis || brokerVis) {
                $('#detailsRow').show();
            } else {
                $('#detailsRow').hide();
            }
        }

        $('.select2-party').select2({
            templateResult: formatParty,
            templateSelection: formatPartySelection
        }).on('select2:select', function (e) {
            var data = e.params.data;
            if (data.id) {
                var address = $(data.element).data('address');
                if (address && address.trim() !== '' && address.trim() !== 'N/A') {
                    $('#partyDetailsAddress').text(address);
                    $('#partyDetailsCol').show();
                } else {
                    $('#partyDetailsCol').hide();
                }
            } else {
                $('#partyDetailsCol').hide();
            }
            updateDetailsRow();
        });
        
        $('.select2-broker').select2({
            templateResult: formatParty,
            templateSelection: formatPartySelection
        }).on('select2:select', function (e) {
            var data = e.params.data;
            if (data.id) {
                var address = $(data.element).data('address');
                if (address && address.trim() !== '' && address.trim() !== 'N/A') {
                    $('#brokerDetailsAddress').text(address);
                    $('#brokerDetailsCol').show();
                } else {
                    $('#brokerDetailsCol').hide();
                }
            } else {
                $('#brokerDetailsCol').hide();
            }
            updateDetailsRow();
        });

        $('#ajaxCreatePartyForm').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $('#btnSaveParty');
            $btn.prop('disabled', true).text('Saving...');
            
            $.ajax({
                url: "{{ route('business.parties.store') }}",
                type: 'POST',
                data: $form.serialize(),
                headers: { 'Accept': 'application/json' },
                success: function(response) {
                    if(response.success) {
                        var party = response.party;
                        var newOption = new Option(party.name, party.id, true, true);
                        $(newOption).attr('data-phone', party.phone || '');
                        $(newOption).attr('data-address', party.address || '');
                        $('#party_id').append(newOption).trigger('change');
                        
                        $('#createPartyModal').modal('hide');
                        $form.trigger('reset');
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                    var errorMsg = 'Failed to create party.';
                    if(errors) {
                        errorMsg = Object.values(errors).map(e => e.join(', ')).join('<br>');
                    }
                    $('#modalAlert').removeClass('d-none alert-success').addClass('alert-danger').html(errorMsg);
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Save Party');
                }
            });
        });

        $('#ajaxCreateBrokerForm').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $('#btnSaveBroker');
            $btn.prop('disabled', true).text('Saving...');
            
            $.ajax({
                url: "{{ route('business.financials.commissions.store-broker') }}",
                type: 'POST',
                data: $form.serialize(),
                headers: { 'Accept': 'application/json' },
                success: function(response) {
                    if(response.success) {
                        var broker = response.broker;
                        var newOption = new Option(broker.name, broker.id, true, true);
                        $(newOption).attr('data-phone', broker.phone || '');
                        $(newOption).attr('data-address', broker.address || '');
                        $('#broker_id').append(newOption).trigger('change');
                        
                        $('#createBrokerModal').modal('hide');
                        $form.trigger('reset');
                        $('#modalBrokerAlert').addClass('d-none');
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                    var errorMsg = 'Failed to create broker.';
                    if(errors) {
                        errorMsg = Object.values(errors).map(e => e.join(', ')).join('<br>');
                    }
                    $('#modalBrokerAlert').removeClass('d-none alert-success').addClass('alert-danger').html(errorMsg);
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="feather-save me-1"></i> Save Broker');
                }
            });
        });
    });

    // Initialize with one item row
    window.onload = function() {
        addItemRow();
    }
</script>
@endpush
@endsection
