@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Broker Profile</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('business.financials.commissions.index') }}">Broker Commissions</a></li>
                <li class="breadcrumb-item">Profile</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            {{-- Broker Details & Stats --}}
            <div class="col-xxl-3 col-xl-4">
                <div class="card stretch stretch-full text-center">
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="avatar-text avatar-xl bg-soft-primary text-primary mx-auto mb-3" style="font-size:2rem;">
                                {{ strtoupper(substr($brokerUser->name, 0, 1)) }}
                            </div>
                            <h4 class="mb-1">{{ $brokerUser->name }}</h4>
                            <p class="text-muted mb-0"><i class="feather-phone me-1"></i> {{ $brokerUser->phone ?? 'No Phone' }}</p>
                        </div>
                        
                        <div class="row g-2 text-start">
                            <div class="col-12">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-1">Total Earned</small>
                                    <h5 class="mb-0 text-dark">₹{{ number_format($stats['total_earned'], 2) }}</h5>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-soft-success rounded h-100">
                                    <small class="text-success d-block mb-1">Total Paid</small>
                                    <h6 class="mb-0 text-success">₹{{ number_format($stats['total_paid'], 2) }}</h6>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-soft-danger rounded h-100">
                                    <small class="text-danger d-block mb-1">Pending Owed</small>
                                    <h6 class="mb-0 text-danger">₹{{ number_format($stats['total_pending'], 2) }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Commission Ledger --}}
            <div class="col-xxl-9 col-xl-8">
                <div class="card stretch stretch-full">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Commission Ledger</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Transaction</th>
                                        <th>Qty & Rate</th>
                                        <th>Commission Amount</th>
                                        <th>Paid / Pending</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($entries as $entry)
                                        @php
                                            $pending = max(0, $entry->commission_amount - $entry->amount_paid);
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $entry->date->format('d M, Y') }}</div>
                                            </td>
                                            <td>
                                                @if($entry->reference_type === \App\Models\Business\Purchase::class)
                                                    <span class="badge bg-soft-info text-info mb-1">PURCHASE</span>
                                                @elseif($entry->reference_type === \App\Models\Business\Sale::class)
                                                    <span class="badge bg-soft-success text-success mb-1">SALE</span>
                                                @else
                                                    <span class="badge bg-light text-dark mb-1">OTHER</span>
                                                @endif
                                                <small class="text-muted d-block">ID: {{ $entry->reference_id }}</small>
                                            </td>
                                            <td>
                                                <div>@qtyRaw($entry->quantity) @unitLabel @ ₹{{ number_format(\App\Helpers\UnitHelper::rateFromQtl($entry->rate, $displayUnit, $bagWeightKg ?? 50), 2) }}</div>
                                                <small class="text-muted">{{ str_replace('_', ' ', $entry->commission_type) }} ({{ $entry->commission_rate }})</small>
                                            </td>
                                            <td>
                                                <h6 class="mb-0 text-dark">₹{{ number_format($entry->commission_amount, 2) }}</h6>
                                            </td>
                                            <td>
                                                <div class="text-success" style="font-size:0.85rem;">Paid: ₹{{ number_format($entry->amount_paid, 2) }}</div>
                                                @if($pending > 0)
                                                    <div class="text-danger fw-bold" style="font-size:0.85rem;">Pending: ₹{{ number_format($pending, 2) }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($entry->payment_status === 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($entry->payment_status === 'partial')
                                                    <span class="badge bg-warning text-dark">Partial</span>
                                                @else
                                                    <span class="badge bg-danger">Pending</span>
                                                @endif
                                                
                                                @if($entry->paid_at)
                                                    <small class="d-block text-muted mt-1">{{ $entry->paid_mode }}</small>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($pending > 0)
                                                    <button type="button" class="btn btn-sm btn-light-primary" 
                                                        onclick="openPayModal({{ $entry->id }}, {{ $pending }})">
                                                        <i class="feather-dollar-sign me-1"></i> Pay
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-light text-muted" disabled>
                                                        <i class="feather-check"></i> Cleared
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">No commission entries found for this broker.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pay Commission Modal --}}
<div class="modal fade" id="payModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pay Broker Commission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="payForm">
                    <input type="hidden" id="entry_id" name="entry_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Amount to Pay (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="pay_amount" name="amount" required>
                        <small class="text-muted">Max pending: ₹<span id="max_pending_display">0.00</span></small>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="paid_at" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                            <select class="form-select" name="paid_mode" required>
                                <option value="Cash">Cash</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Online">Online / Transfer</option>
                                <option value="NEFT">NEFT / RTGS</option>
                                <option value="UPI">UPI</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" name="payment_notes" rows="2" placeholder="Cheque number, transaction ID, etc."></textarea>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btnPaySubmit">Confirm Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentMaxPending = 0;

    function openPayModal(entryId, pendingAmt) {
        currentMaxPending = pendingAmt;
        document.getElementById('entry_id').value = entryId;
        document.getElementById('pay_amount').value = pendingAmt;
        document.getElementById('pay_amount').max = pendingAmt;
        document.getElementById('max_pending_display').innerText = pendingAmt.toFixed(2);
        
        var modalEl = document.getElementById('payModal');
        if (modalEl.parentNode !== document.body) {
            document.body.appendChild(modalEl);
        }
        var myModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        myModal.show();
    }

    document.getElementById('payForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let entryId = document.getElementById('entry_id').value;
        let amt = parseFloat(document.getElementById('pay_amount').value);
        
        if(amt > currentMaxPending) {
            toastr.error("Cannot pay more than the pending amount!");
            return;
        }

        let formData = new FormData(this);
        let btn = document.getElementById('btnPaySubmit');
        let originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        btn.disabled = true;

        fetch(`{{ url('business/financials/commissions') }}/${entryId}/pay`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
                setTimeout(() => window.location.reload(), 800);
            } else {
                toastr.error(data.message || 'Error recording payment.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            toastr.error("Server error. Please try again.");
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });
</script>
@endpush
