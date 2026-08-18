@extends('layouts.app')

@push('styles')
<style>
    .kpi-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    .filter-card {
        border: 1px solid #edf2f9;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .table-sales th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #6c757d;
        background-color: #f8f9fa;
        border-bottom: 2px solid #edf2f9;
        padding: 12px 16px;
    }
    .table-sales td {
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f7;
    }
    .badge-paid {
        background-color: #d1e7dd;
        color: #0f5132;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 6px;
    }
    .badge-partial {
        background-color: #fff3cd;
        color: #664d03;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 6px;
    }
    .badge-unpaid {
        background-color: #f8d7da;
        color: #842029;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 6px;
    }
    .details-box {
        background: #f8fafc;
        border-left: 4px solid #667eea;
        border-radius: 0 8px 8px 0;
        padding: 18px;
    }
</style>
@endpush

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Sales Invoices</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Sales List</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="{{ route('business.sales.create') }}" class="btn btn-primary px-3">
                        <i class="feather-plus me-1"></i>
                        <span>New Sale</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="main-content">
        {{-- KPI STATS CARDS --}}
        <div class="row g-3 mb-4">
            <div class="col-xxl-3 col-md-6">
                <div class="card kpi-card bg-white h-100">
                    <div class="card-body d-flex align-items-center justify-content-between p-3">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Sales</span>
                            <h3 class="fw-bold text-dark mt-1 mb-0">₹{{ number_format($stats['total_amount'] ?? 0, 2) }}</h3>
                            <small class="text-muted">{{ $stats['total_count'] ?? 0 }} Total Invoices</small>
                        </div>
                        <div class="kpi-icon bg-soft-primary text-primary">
                            <i class="feather-shopping-bag"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-md-6">
                <div class="card kpi-card bg-white h-100">
                    <div class="card-body d-flex align-items-center justify-content-between p-3">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Received</span>
                            <h3 class="fw-bold text-success mt-1 mb-0">₹{{ number_format($stats['total_paid'] ?? 0, 2) }}</h3>
                            <small class="text-success"><i class="feather-check-circle me-1"></i>{{ $stats['paid_count'] ?? 0 }} Fully Paid</small>
                        </div>
                        <div class="kpi-icon bg-soft-success text-success">
                            <i class="feather-check-square"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-md-6">
                <div class="card kpi-card bg-white h-100">
                    <div class="card-body d-flex align-items-center justify-content-between p-3">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Balance Due (Pending)</span>
                            <h3 class="fw-bold text-danger mt-1 mb-0">₹{{ number_format($stats['total_due'] ?? 0, 2) }}</h3>
                            <small class="text-danger"><i class="feather-alert-triangle me-1"></i>{{ ($stats['partial_count'] ?? 0) + ($stats['unpaid_count'] ?? 0) }} Invoices Due</small>
                        </div>
                        <div class="kpi-icon bg-soft-danger text-danger">
                            <i class="feather-alert-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-md-6">
                <div class="card kpi-card bg-white h-100">
                    <div class="card-body d-flex align-items-center justify-content-between p-3">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Partial Payments</span>
                            <h3 class="fw-bold text-warning mt-1 mb-0">{{ $stats['partial_count'] ?? 0 }}</h3>
                            <small class="text-muted">{{ $stats['unpaid_count'] ?? 0 }} Unpaid Bills</small>
                        </div>
                        <div class="kpi-icon bg-soft-warning text-warning">
                            <i class="feather-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="card filter-card mb-4">
            <div class="card-body p-3">
                <form action="{{ route('business.sales.index') }}" method="GET" id="salesFilterForm">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label text-muted small mb-1 fw-bold">Search</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="feather-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" name="search" value="{{ request('search') }}" placeholder="Sale #, Customer, PO, Truck...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small mb-1 fw-bold">Customer</label>
                            <select name="party_id" class="form-select form-select-sm">
                                <option value="">All Customers</option>
                                @foreach($parties as $party)
                                    <option value="{{ $party->id }}" {{ request('party_id') == $party->id ? 'selected' : '' }}>{{ $party->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small mb-1 fw-bold">Grain</label>
                            <select name="grain_id" class="form-select form-select-sm">
                                <option value="">All Grains</option>
                                @foreach($grains as $grain)
                                    <option value="{{ $grain->id }}" {{ request('grain_id') == $grain->id ? 'selected' : '' }}>{{ $grain->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small mb-1 fw-bold">Payment Status</label>
                            <select name="payment_status" class="form-select form-select-sm">
                                <option value="">All Status</option>
                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Fully Paid</option>
                                <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial Paid</option>
                                <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid / Due</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="row g-1">
                                <div class="col-6">
                                    <label class="form-label text-muted small mb-1 fw-bold">From Date</label>
                                    <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small mb-1 fw-bold">To Date</label>
                                    <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 text-end mt-2 pt-1 border-top">
                            @if(request()->hasAny(['search', 'party_id', 'grain_id', 'payment_status', 'from_date', 'to_date']))
                                <a href="{{ route('business.sales.index') }}" class="btn btn-sm btn-light-secondary me-2">
                                    <i class="feather-x me-1"></i> Clear Filters
                                </a>
                            @endif
                            <button type="submit" class="btn btn-sm btn-primary px-3">
                                <i class="feather-filter me-1"></i> Apply Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- SALES TABLE --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                @if (session('success'))
                    <div class="alert alert-success m-3">
                        <i class="feather-check-circle me-1"></i> {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger m-3">
                        <i class="feather-alert-circle me-1"></i> {{ session('error') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover table-sales mb-0">
                        <thead>
                            <tr>
                                <th>Sale No</th>
                                <th>Date & Time</th>
                                <th>Customer</th>
                                <th>Grain</th>
                                <th>Quantity & Rate</th>
                                <th>Bill Total</th>
                                <th>Payment & Due Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $item)
                                @php
                                    $netAmt = (float)($item->net_amount ?? $item->total_amount ?? 0);
                                    $paidAmt = $item->total_paid;
                                    $dueAmt = $item->remaining_outstanding;
                                    $status = $item->payment_status;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold text-primary">{{ $item->sale_no ?? 'N/A' }}</div>
                                        @if($item->po_no)
                                            <span class="badge bg-light text-muted border mt-1" style="font-size:0.65rem;">PO: {{ $item->po_no }}</span>
                                        @endif
                                        @if($item->truck_no)
                                            <span class="badge bg-light text-muted border mt-1" style="font-size:0.65rem;"><i class="feather-truck me-1"></i>{{ $item->truck_no }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($item->date)->format('d M, Y') }}</div>
                                        <small class="text-muted">{{ $item->sale_time ? \Carbon\Carbon::parse($item->sale_time)->format('h:i A') : '' }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->party->name ?? 'N/A' }}</div>
                                        @if($item->party && $item->party->phone)
                                            <small class="text-muted d-block"><i class="feather-phone me-1"></i>{{ $item->party->phone }}</small>
                                        @endif
                                        @if($item->broker)
                                            <small class="text-info d-block"><i class="feather-user me-1"></i>Broker: {{ $item->broker->name }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-primary text-primary px-2 py-1">{{ $item->grain->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            @qtyRaw($item->quantity) @unitLabel
                                            @if($item->bags_count)
                                                <small class="text-muted">({{ $item->bags_count }} Bags)</small>
                                            @endif
                                        </div>
                                        <small class="text-muted">₹@rateRaw($item->rate) / @unitLabel</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark fs-14">₹{{ number_format($netAmt, 2) }}</span>
                                        @if($item->discount_amount > 0)
                                            <small class="text-danger d-block">-₹{{ number_format($item->discount_amount, 2) }} ({{ $item->discount_percent }}% Disc)</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($status === 'paid')
                                            <span class="badge badge-paid">
                                                <i class="feather-check-circle me-1"></i> Fully Paid
                                            </span>
                                            <div class="small text-muted mt-1">Paid: <strong class="text-success">₹{{ number_format($paidAmt, 2) }}</strong></div>
                                        @elseif($status === 'partial')
                                            <span class="badge badge-partial">
                                                <i class="feather-clock me-1"></i> Partial Paid
                                            </span>
                                            <div class="small mt-1">
                                                <span class="text-success">Paid: ₹{{ number_format($paidAmt, 2) }}</span><br>
                                                <span class="text-danger fw-bold">Due: ₹{{ number_format($dueAmt, 2) }}</span>
                                            </div>
                                        @else
                                            <span class="badge badge-unpaid">
                                                <i class="feather-alert-circle me-1"></i> Unpaid
                                            </span>
                                            <div class="small text-danger fw-bold mt-1">Due: ₹{{ number_format($dueAmt, 2) }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            <a href="{{ route('business.sales.print', $item->id) }}" class="btn btn-sm btn-icon btn-light-info" target="_blank" title="Print Bill">
                                                <i class="feather-printer"></i>
                                            </a>

                                            @if($dueAmt > 0.01)
                                                <button class="btn btn-sm btn-icon btn-light-success" title="Collect Payment" onclick="openCollectModal({{ $item->id }}, '{{ $item->sale_no }}', '{{ $item->party->name ?? 'Customer' }}', {{ $dueAmt }})">
                                                    <i class="feather-dollar-sign"></i>
                                                </button>
                                            @endif

                                            <a href="{{ route('business.sales.edit', $item->id) }}" class="btn btn-sm btn-icon btn-light-secondary" title="Edit Sale">
                                                <i class="feather-edit-2"></i>
                                            </a>

                                            <button class="btn btn-sm btn-icon btn-light-primary" type="button" data-bs-toggle="collapse" data-bs-target="#details-{{ $item->id }}" aria-expanded="false" title="View Details">
                                                <i class="feather-chevron-down"></i>
                                            </button>

                                            <form action="{{ route('business.sales.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel sale #{{ $item->sale_no }}? This will restore allocated stock and delete ledger records.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Delete / Cancel">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8" class="p-0 border-0">
                                        <div class="collapse" id="details-{{ $item->id }}">
                                            <div class="details-box m-3">
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size:0.7rem;">Lot Allocations</small>
                                                        @if($item->saleLotAllocations->isNotEmpty())
                                                            @foreach($item->saleLotAllocations as $alloc)
                                                                <div class="fw-bold text-dark mb-1">
                                                                    <span class="badge bg-soft-info text-info">{{ $alloc->lot->lot_no ?? ('Lot #' . $alloc->lot_id) }}</span>
                                                                    {{ number_format($alloc->quantity_taken, 2) }} Qtl
                                                                    <small class="text-muted">(@ ₹{{ number_format($alloc->cost_rate ?? 0, 2) }})</small>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="text-muted small">No lot allocated</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-3">
                                                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size:0.7rem;">Gross Profit Margin</small>
                                                        @php
                                                            $totalCost = $item->saleLotAllocations->sum(function($a) { return $a->quantity_taken * ($a->cost_rate ?? 0); });
                                                            $totalRevenue = (float)$item->quantity * (float)$item->rate;
                                                            $profit = $totalRevenue - $totalCost;
                                                        @endphp
                                                        <div class="fw-bold {{ $profit >= 0 ? 'text-success' : 'text-danger' }} fs-14">
                                                            ₹{{ number_format($profit, 2) }}
                                                        </div>
                                                        <small class="text-muted">Revenue: ₹{{ number_format($totalRevenue, 2) }} | Cost: ₹{{ number_format($totalCost, 2) }}</small>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size:0.7rem;">Transport & Despatch</small>
                                                        @if($item->truck_no || $item->driver_name)
                                                            <div class="text-dark small"><strong>Truck:</strong> {{ $item->truck_no ?? '—' }}</div>
                                                            <div class="text-dark small"><strong>Driver:</strong> {{ $item->driver_name ?? '—' }} {{ $item->driver_phone ? '('.$item->driver_phone.')' : '' }}</div>
                                                            @if($item->truck_fare > 0)
                                                                <div class="text-dark small"><strong>Fare:</strong> ₹{{ number_format($item->truck_fare, 2) }} (Adv: ₹{{ number_format($item->freight_advance ?? 0, 2) }})</div>
                                                            @endif
                                                        @else
                                                            <div class="text-muted small">No transport info</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-3">
                                                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size:0.7rem;">Add-on Charges</small>
                                                        @if($item->charges && $item->charges->count() > 0)
                                                            @foreach($item->charges as $ch)
                                                                <div class="small">
                                                                    {{ $ch->name }}: 
                                                                    <strong class="{{ $ch->type == 'deduct' ? 'text-danger' : 'text-success' }}">
                                                                        {{ $ch->type == 'deduct' ? '-' : '+' }}₹{{ number_format($ch->amount, 2) }}
                                                                    </strong>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="text-muted small">No extra charges</div>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if($item->notes)
                                                    <div class="mt-2 pt-2 border-top">
                                                        <small class="text-muted d-block"><strong>Notes:</strong> {{ $item->notes }}</small>
                                                    </div>
                                                @endif

                                                {{-- Payment History --}}
                                                <div class="mt-3 pt-2 border-top">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="mb-0 text-dark fw-bold" style="font-size:0.85rem;"><i class="feather-dollar-sign me-1 text-success"></i> Payment History & Collections</h6>
                                                        @if($dueAmt > 0.01)
                                                            <button class="btn btn-xs btn-success py-1 px-2" onclick="openCollectModal({{ $item->id }}, '{{ $item->sale_no }}', '{{ $item->party->name ?? 'Customer' }}', {{ $dueAmt }})">
                                                                <i class="feather-plus me-1"></i> Collect Payment
                                                            </button>
                                                        @endif
                                                    </div>
                                                    
                                                    @php
                                                        $hasPayments = ($item->payments && $item->payments->count() > 0) || ($item->collections && $item->collections->count() > 0);
                                                    @endphp

                                                    @if($hasPayments)
                                                        <table class="table table-sm table-bordered bg-white mb-0" style="font-size:0.8rem;">
                                                            <thead class="bg-light">
                                                                <tr>
                                                                    <th>Type</th>
                                                                    <th>Date</th>
                                                                    <th>Amount</th>
                                                                    <th>Mode</th>
                                                                    <th>Ref / Notes</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if($item->payments)
                                                                    @foreach($item->payments as $p)
                                                                        <tr>
                                                                            <td><span class="badge bg-soft-secondary text-secondary">Initial Payment</span></td>
                                                                            <td>{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</td>
                                                                            <td class="fw-bold text-success">₹{{ number_format($p->amount, 2) }}</td>
                                                                            <td>{{ strtoupper($p->mode) }}</td>
                                                                            <td>—</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif
                                                                @if($item->collections)
                                                                    @foreach($item->collections as $col)
                                                                        <tr>
                                                                            <td><span class="badge bg-soft-success text-success">Recovery Collection</span></td>
                                                                            <td>{{ $col->collected_at ? \Carbon\Carbon::parse($col->collected_at)->format('d M Y') : '—' }}</td>
                                                                            <td class="fw-bold text-success">₹{{ number_format($col->amount, 2) }}</td>
                                                                            <td>{{ $col->mode ?? 'Cash' }}</td>
                                                                            <td>{{ $col->reference_no ? 'Ref: ' . $col->reference_no : ($col->notes ?? '—') }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    @else
                                                        <div class="text-muted small">No payment recorded yet. Total Due: <strong>₹{{ number_format($dueAmt, 2) }}</strong></div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="feather-inbox fs-1 d-block mb-2 text-muted"></i>
                                        No sales records found matching your filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Collect Payment Modal --}}
<div class="modal fade" id="collectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="feather-dollar-sign text-success me-1"></i> Record Sale Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light border mb-3 py-2 px-3">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted d-block">Invoice No:</small>
                            <strong id="modal_sale_no" class="text-primary">—</strong>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted d-block">Customer:</small>
                            <strong id="modal_party_name" class="text-dark">—</strong>
                        </div>
                    </div>
                </div>

                <form id="collectForm">
                    <input type="hidden" id="sale_id" name="sale_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount Received (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" class="form-control form-control-lg fw-bold text-success" id="collect_amount" name="amount" required>
                        <small class="text-muted">Max pending due: <strong>₹<span id="max_collect_display">0.00</span></strong></small>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="collected_at" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Payment Mode <span class="text-danger">*</span></label>
                            <select class="form-select" name="mode" required>
                                <option value="Cash">Cash</option>
                                <option value="UPI">UPI</option>
                                <option value="Bank Transfer">Bank Transfer / NEFT</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Reference / UTR / Cheque No.</label>
                        <input type="text" class="form-control" name="reference_no" placeholder="e.g. UTR123456789 or Cheque #4589">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes (Optional)</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Payment received notes..."></textarea>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4" id="btnCollectSubmit">
                            <i class="feather-check-circle me-1"></i> Save Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let maxCollect = 0;

    function openCollectModal(saleId, saleNo, partyName, pendingAmt) {
        maxCollect = pendingAmt;
        document.getElementById('sale_id').value = saleId;
        document.getElementById('modal_sale_no').innerText = saleNo;
        document.getElementById('modal_party_name').innerText = partyName;
        document.getElementById('collect_amount').value = pendingAmt.toFixed(2);
        document.getElementById('collect_amount').max = pendingAmt;
        document.getElementById('max_collect_display').innerText = pendingAmt.toFixed(2);
        
        var modalEl = document.getElementById('collectModal');
        if (modalEl.parentNode !== document.body) {
            document.body.appendChild(modalEl);
        }
        var myModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        myModal.show();
    }

    document.getElementById('collectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let saleId = document.getElementById('sale_id').value;
        let amt = parseFloat(document.getElementById('collect_amount').value);
        
        if (amt > maxCollect + 0.01) {
            alert("Cannot collect more than the outstanding amount of ₹" + maxCollect.toFixed(2));
            return;
        }

        let formData = new FormData(this);
        let btn = document.getElementById('btnCollectSubmit');
        let originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
        btn.disabled = true;

        fetch(`{{ url('business/sales') }}/${saleId}/collect`, {
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
                window.location.reload();
            } else {
                alert(data.message || 'Error saving collection.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert("Server error. Please try again.");
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });
</script>
@endpush
