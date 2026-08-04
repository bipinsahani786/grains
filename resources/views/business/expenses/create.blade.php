@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Add Expense</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('business.expenses.index') }}">Expenses</a></li>
                <li class="breadcrumb-item">Add</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('business.expenses.store') }}" method="POST">
            @csrf
            <div class="row">
                {{-- Main Form --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold">Expense Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" min="0" name="amount" class="form-control" placeholder="0.00" value="{{ old('amount') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select name="category_id" class="form-select" required>
                                            <option value="">-- Select Category --</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <a href="{{ route('business.expenses.categories.index') }}" class="btn btn-outline-secondary" target="_blank" title="Manage Categories">
                                            <i class="feather-settings"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                                    <select name="payment_mode" class="form-select" required>
                                        <option value="cash" {{ old('payment_mode') == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="bank" {{ old('payment_mode') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="upi" {{ old('payment_mode') == 'upi' ? 'selected' : '' }}>UPI</option>
                                        <option value="cheque" {{ old('payment_mode') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <input type="text" name="description" class="form-control" placeholder="What was this expense for?" value="{{ old('description') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Reference No <small class="text-muted">(Cheque/UTR)</small></label>
                                    <input type="text" name="reference_no" class="form-control" placeholder="Optional" value="{{ old('reference_no') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Vendor Section --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold">Vendor / Payee <small class="text-muted fw-normal">(Optional)</small></h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Vendor Name (Free Text)</label>
                                    <input type="text" name="vendor_name" class="form-control" placeholder="e.g. Sharma Transport Co." value="{{ old('vendor_name') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Or Link to Existing Party</label>
                                    <select name="vendor_party_id" class="form-select select2-party">
                                        <option value="">-- Select Party (optional) --</option>
                                        @foreach($parties as $party)
                                            <option value="{{ $party->id }}" {{ old('vendor_party_id') == $party->id ? 'selected' : '' }}>
                                                {{ $party->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <label class="form-label">Notes <small class="text-muted">(Optional)</small></label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    {{-- Recurring --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold"><i class="feather-refresh-cw me-2 text-primary"></i>Recurring Expense</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="isRecurring" name="is_recurring" value="1"
                                    {{ old('is_recurring') ? 'checked' : '' }} onchange="toggleRecurring(this)">
                                <label class="form-check-label" for="isRecurring">Mark as Recurring</label>
                            </div>
                            <div id="recurringFields" style="{{ old('is_recurring') ? '' : 'display:none;' }}">
                                <div class="mb-3">
                                    <label class="form-label">Frequency</label>
                                    <select name="recurring_frequency" class="form-select">
                                        <option value="monthly" {{ old('recurring_frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="weekly" {{ old('recurring_frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="yearly" {{ old('recurring_frequency') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Next Due Date</label>
                                    <input type="date" name="recurring_next_date" class="form-control" value="{{ old('recurring_next_date') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="feather-save me-2"></i>Save Expense
                        </button>
                        <a href="{{ route('business.expenses.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRecurring(el) {
    document.getElementById('recurringFields').style.display = el.checked ? '' : 'none';
}
</script>
@endsection
