@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Party Profile</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('business.parties.index') }}">Parties</a></li>
                <li class="breadcrumb-item">{{ $party->name }}</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#collectPaymentModal">
                    <i class="feather-dollar-sign me-2"></i> Collect Payment / Advance
                </button>
            </div>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-xl-4">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="avatar avatar-xl bg-soft-primary text-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                                <span class="fs-4">{{ substr($party->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h4 class="mb-1">{{ $party->name }}</h4>
                                <p class="text-muted mb-0">{{ $party->phone }} | {{ $party->partyType->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex justify-content-between mb-2">
                                <span class="text-muted flex-shrink-0">Email:</span>
                                <span class="fw-bold text-end ms-3" style="word-break: break-all;" title="{{ $party->email }}">{{ $party->email ?? 'N/A' }}</span>
                            </li>
                            <li class="d-flex justify-content-between mb-2">
                                <span class="text-muted flex-shrink-0">Aadhar:</span>
                                <span class="fw-bold text-end ms-3" style="word-break: break-all;" title="{{ $party->aadhar_no }}">{{ $party->aadhar_no ?? 'N/A' }}</span>
                            </li>
                            <li class="d-flex justify-content-between mb-2">
                                <span class="text-muted flex-shrink-0">GST:</span>
                                <span class="fw-bold text-end ms-3" style="word-break: break-all;" title="{{ $party->gst_no }}">{{ $party->gst_no ?? 'N/A' }}</span>
                            </li>
                            <li class="d-flex justify-content-between mb-2">
                                <span class="text-muted flex-shrink-0">Opening Balance:</span>
                                <span class="fw-bold text-end ms-3">₹{{ number_format($party->opening_balance, 2) }} {{ $party->opening_balance_type ? '(' . ucfirst($party->opening_balance_type) . ')' : '' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card stretch stretch-full bg-soft-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h5 class="mb-0 text-warning">Current Outstanding</h5>
                            <i class="feather-alert-circle text-warning fs-3"></i>
                        </div>
                        <h2 class="text-warning mb-1">₹{{ number_format($currentBalance, 2) }}</h2>
                        <p class="text-warning-subtle mb-0">Total amount pending to be settled.</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card stretch stretch-full bg-soft-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h5 class="mb-0 text-primary">Total Trade Value</h5>
                            <i class="feather-trending-up text-primary fs-3"></i>
                        </div>
                        <h2 class="text-primary mb-1">₹{{ number_format($totalPurchases, 2) }}</h2>
                        <p class="text-primary-subtle mb-0">Lifetime purchases from this party.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <ul class="nav nav-tabs card-header-tabs mb-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#ledgerTab" role="tab">Ledger History</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#purchasesTab" role="tab">Purchase Bills</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#paymentsTab" role="tab">Payment History</a>
                            </li>
                        </ul>
                        <form action="{{ route('business.parties.show', $party->id) }}" method="GET" class="d-flex align-items-center mb-0">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="feather-calendar"></i></span>
                                <input type="date" class="form-control form-control-sm" name="start_date" value="{{ request('start_date') }}">
                                <span class="input-group-text bg-light px-2 border-start-0 border-end-0">to</span>
                                <input type="date" class="form-control form-control-sm" name="end_date" value="{{ request('end_date') }}">
                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                @if(request('start_date') || request('end_date'))
                                    <a href="{{ route('business.parties.show', $party->id) }}" class="btn btn-light btn-sm text-danger border"><i class="feather-x"></i></a>
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        <div class="tab-content">
                            <!-- Ledger Tab -->
                            <div class="tab-pane fade show active" id="ledgerTab" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Debit (Dr)</th>
                                                <th>Credit (Cr)</th>
                                                <th>Balance</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($ledgerEntries as $entry)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($entry->entry_date)->format('d M Y') }}</td>
                                                <td><span class="badge bg-soft-secondary text-secondary text-capitalize">{{ $entry->entry_type }}</span></td>
                                                <td class="text-danger">{{ $entry->debit > 0 ? '₹'.number_format($entry->debit, 2) : '-' }}</td>
                                                <td class="text-success">{{ $entry->credit > 0 ? '₹'.number_format($entry->credit, 2) : '-' }}</td>
                                                <td class="fw-bold">₹{{ number_format($entry->balance_after, 2) }}</td>
                                                <td>
                                                    @if($entry->reference_type === \App\Models\Business\Payment::class && $entry->reference)
                                                        <span class="text-muted small">{{ $entry->reference->notes ?? '-' }}</span>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Purchases Tab -->
                            <div class="tab-pane fade" id="purchasesTab" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Items Summary</th>
                                                <th>Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($purchases as $purchase)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($purchase->date)->format('d M Y') }}</td>
                                                <td>
                                                    @foreach($purchase->items as $item)
                                                        <span class="badge bg-light text-dark border">{{ $item->grain->name ?? 'Grain' }} ({{ $item->quantity }} {{ $item->unit }})</span>
                                                    @endforeach
                                                </td>
                                                <td class="fw-bold">₹{{ number_format($purchase->total_amount, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Payments Tab -->
                            <div class="tab-pane fade" id="paymentsTab" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Mode</th>
                                                <th>Direction</th>
                                                <th>Amount</th>
                                                <th>Notes/Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($payments as $payment)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($payment->date)->format('d M Y') }}</td>
                                                <td class="text-uppercase">{{ $payment->mode }}</td>
                                                <td>
                                                    @if($payment->direction == 'in')
                                                        <span class="text-success"><i class="feather-arrow-down"></i> Received</span>
                                                    @else
                                                        <span class="text-danger"><i class="feather-arrow-up"></i> Paid</span>
                                                    @endif
                                                </td>
                                                <td class="fw-bold">₹{{ number_format($payment->amount, 2) }}</td>
                                                <td><span class="text-muted small">{{ $payment->notes ?? '-' }}</span></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
<!-- Collect Payment/Advance Modal -->
<div class="modal fade" id="collectPaymentModal" tabindex="-1" aria-labelledby="collectPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('business.parties.payments.store', $party->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="collectPaymentModalLabel">Collect Payment / Give Advance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Transaction Type</label>
                            <select class="form-select" name="type" required>
                                <option value="payment">Regular Payment</option>
                                <option value="advance">Advance</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Direction</label>
                            <select class="form-select" name="direction" required>
                                <option value="in">Received (Money In)</option>
                                <option value="out">Paid (Money Out)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount (₹)</label>
                            <input type="number" step="0.01" min="0.01" class="form-control" name="amount" required placeholder="0.00">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mode of Payment</label>
                            <select class="form-select" name="mode" required>
                                <option value="cash">Cash</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="upi">UPI</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" name="date" required value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes / Reason (e.g. Kheti ke liye)</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Enter reason for advance or any notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Record Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush
@endsection
