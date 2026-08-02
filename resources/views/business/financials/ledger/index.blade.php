@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Ledger Entries</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Financials</li>
                <li class="breadcrumb-item">Ledger</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <form action="{{ route('business.financials.ledger.index') }}" method="GET" class="d-flex align-items-center gap-2">
                    <div class="input-group input-group-sm" style="width: auto;">
                        <span class="input-group-text bg-light"><i class="feather-search"></i></span>
                        <input type="text" class="form-control form-control-sm" name="search" placeholder="Search party or type..." value="{{ request('search') }}">
                    </div>
                    <div class="input-group input-group-sm" style="width: auto;">
                        <span class="input-group-text bg-light"><i class="feather-calendar"></i></span>
                        <input type="date" class="form-control form-control-sm" name="start_date" value="{{ request('start_date') }}">
                        <span class="input-group-text bg-light px-2 border-start-0 border-end-0">to</span>
                        <input type="date" class="form-control form-control-sm" name="end_date" value="{{ request('end_date') }}">
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        @if(request('start_date') || request('end_date') || request('search'))
                            <a href="{{ route('business.financials.ledger.index') }}" class="btn btn-light btn-sm text-danger border"><i class="feather-x"></i></a>
                        @endif
                    </div>
                </form>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="feather-download me-2"></i> Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}"><i class="feather-file-text me-2"></i> CSV</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}"><i class="feather-printer me-2"></i> Print / PDF</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stretch stretch-full">
                    <div class="card-body p-3 hover-effect">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex gap-3 align-items-center w-100 overflow-hidden">
                                <div class="avatar-text bg-soft-success text-success">
                                    <i class="feather-arrow-down-left"></i>
                                </div>
                                <div class="overflow-hidden w-100">
                                    <div class="fs-5 fw-bold text-dark text-truncate">₹{{ number_format($totalDebit, 2) }}</div>
                                    <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Total Debit</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stretch stretch-full">
                    <div class="card-body p-3 hover-effect">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex gap-3 align-items-center w-100 overflow-hidden">
                                <div class="avatar-text bg-soft-danger text-danger">
                                    <i class="feather-arrow-up-right"></i>
                                </div>
                                <div class="overflow-hidden w-100">
                                    <div class="fs-5 fw-bold text-dark text-truncate">₹{{ number_format($totalCredit, 2) }}</div>
                                    <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Total Credit</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stretch stretch-full">
                    <div class="card-body p-3 hover-effect">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex gap-3 align-items-center w-100 overflow-hidden">
                                <div class="avatar-text bg-soft-primary text-primary">
                                    <i class="feather-pie-chart"></i>
                                </div>
                                <div class="overflow-hidden w-100">
                                    <div class="fs-5 fw-bold text-dark text-truncate">₹{{ number_format($netBalance, 2) }}</div>
                                    <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Net Balance</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stretch stretch-full">
                    <div class="card-body p-3 hover-effect">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex gap-3 align-items-center w-100 overflow-hidden">
                                <div class="avatar-text bg-soft-info text-info">
                                    <i class="feather-activity"></i>
                                </div>
                                <div class="overflow-hidden w-100">
                                    <div class="fs-5 fw-bold text-dark text-truncate">{{ $totalTransactions }}</div>
                                    <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Total Transactions</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="ledgerTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Party</th>
                                        <th>Type</th>
                                        <th class="text-end">Debit (₹)</th>
                                        <th class="text-end">Credit (₹)</th>
                                        <th class="text-end">Balance (₹)</th>
                                        <th class="text-end">Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($entries as $entry)
                                        <tr data-bs-toggle="collapse" data-bs-target="#details-{{ $entry->id }}" style="cursor: pointer;">
                                            <td>{{ \Carbon\Carbon::parse($entry->entry_date)->format('d M, Y') }}</td>
                                            <td><h6 class="mb-0">{{ $entry->party->name ?? 'N/A' }}</h6></td>
                                            <td>
                                                <span class="badge bg-soft-{{ $entry->entry_type == 'sale' ? 'success text-success' : ($entry->entry_type == 'purchase' ? 'danger text-danger' : 'primary text-primary') }} text-capitalize">
                                                    {{ $entry->entry_type }}
                                                </span>
                                            </td>
                                            <td class="text-end {{ $entry->debit > 0 ? 'text-success fw-bold' : 'text-muted' }}">
                                                {{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}
                                            </td>
                                            <td class="text-end {{ $entry->credit > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                                {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}
                                            </td>
                                            <td class="text-end fw-bold">
                                                {{ number_format($entry->balance_after, 2) }}
                                            </td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-icon btn-light-primary">
                                                    <i class="feather-chevron-down"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="p-0 border-0">
                                                <div class="collapse" id="details-{{ $entry->id }}">
                                                    <div class="p-4 bg-light border-bottom">
                                                        <h6 class="mb-3"><i class="feather-info me-2 text-primary"></i> Transaction Details</h6>
                                                        @if($entry->entry_type == 'purchase' && $entry->reference)
                                                            <div class="table-responsive mt-3">
                                                                <table class="table table-sm table-bordered bg-white">
                                                                    <thead class="bg-light">
                                                                        <tr>
                                                                            <th>Grain</th>
                                                                            <th>Quantity</th>
                                                                            <th>Rate</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($entry->reference->items ?? [] as $item)
                                                                            <tr>
                                                                                <td class="fw-bold">{{ $item->grain->name ?? 'N/A' }}</td>
                                                                                <td>{{ $item->quantity }} {{ $item->unit }}</td>
                                                                                <td>₹{{ number_format($item->rate, 2) }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                                @if($entry->reference->lots && $entry->reference->lots->count() > 0)
                                                                    <div class="mt-2">
                                                                        <small class="text-muted d-block mb-1">Lots Generated:</small>
                                                                        @foreach($entry->reference->lots as $lot)
                                                                            <span class="badge bg-secondary">{{ $lot->lot_no }}</span>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @elseif($entry->entry_type == 'sale' && $entry->reference)
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <small class="text-muted d-block">Grain</small>
                                                                    <span class="fw-bold">{{ $entry->reference->grain->name ?? 'N/A' }}</span>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <small class="text-muted d-block">Quantity Sold</small>
                                                                    <span class="fw-bold">{{ $entry->reference->quantity }} {{ $entry->reference->unit }}</span>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <small class="text-muted d-block">Sale Rate</small>
                                                                    <span class="fw-bold">₹{{ number_format($entry->reference->rate, 2) }}</span>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <small class="text-muted d-block">Consumed Lot</small>
                                                                    @foreach($entry->reference->saleLotAllocations ?? [] as $allocation)
                                                                        <span class="badge bg-secondary">{{ $allocation->lot->lot_no ?? 'N/A' }}</span>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="text-muted">No additional details available for this manual entry or payment.</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">No ledger entries found</td>
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
@endsection
