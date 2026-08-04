@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Edit Expense — {{ $expense->expense_no }}</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('business.expenses.index') }}">Expenses</a></li>
                <li class="breadcrumb-item">Edit</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('business.expenses.update', $expense->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold">Expense Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control" value="{{ old('date', $expense->date->format('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" min="0" name="amount" class="form-control" value="{{ old('amount', $expense->amount) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ old('category_id', $expense->category_id) == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                                    <select name="payment_mode" class="form-select" required>
                                        <option value="cash" {{ old('payment_mode', $expense->payment_mode) == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="bank" {{ old('payment_mode', $expense->payment_mode) == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="upi" {{ old('payment_mode', $expense->payment_mode) == 'upi' ? 'selected' : '' }}>UPI</option>
                                        <option value="cheque" {{ old('payment_mode', $expense->payment_mode) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <input type="text" name="description" class="form-control" value="{{ old('description', $expense->description) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Reference No</label>
                                    <input type="text" name="reference_no" class="form-control" value="{{ old('reference_no', $expense->reference_no) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold">Vendor / Payee</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Vendor Name (Free Text)</label>
                                    <input type="text" name="vendor_name" class="form-control" value="{{ old('vendor_name', $expense->vendor_name) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Link to Existing Party</label>
                                    <select name="vendor_party_id" class="form-select">
                                        <option value="">-- None --</option>
                                        @foreach($parties as $party)
                                            <option value="{{ $party->id }}" {{ old('vendor_party_id', $expense->vendor_party_id) == $party->id ? 'selected' : '' }}>
                                                {{ $party->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $expense->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold"><i class="feather-refresh-cw me-2 text-primary"></i>Recurring</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="isRecurring" name="is_recurring" value="1"
                                    {{ old('is_recurring', $expense->is_recurring) ? 'checked' : '' }} onchange="toggleRecurring(this)">
                                <label class="form-check-label" for="isRecurring">Is Recurring</label>
                            </div>
                            <div id="recurringFields" style="{{ old('is_recurring', $expense->is_recurring) ? '' : 'display:none;' }}">
                                <div class="mb-3">
                                    <label class="form-label">Frequency</label>
                                    <select name="recurring_frequency" class="form-select">
                                        <option value="monthly" {{ old('recurring_frequency', $expense->recurring_frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="weekly" {{ old('recurring_frequency', $expense->recurring_frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="yearly" {{ old('recurring_frequency', $expense->recurring_frequency) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Next Due Date</label>
                                    <input type="date" name="recurring_next_date" class="form-control"
                                        value="{{ old('recurring_next_date', $expense->recurring_next_date ? $expense->recurring_next_date->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="feather-save me-2"></i>Update Expense
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
