@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Party Ledger Report</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Reports</li>
                <li class="breadcrumb-item">Party Ledger</li>
            </ul>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('business.reports.party-ledger') }}" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Select Party <span class="text-danger">*</span></label>
                                <select name="party_id" class="form-select" required>
                                    <option value="">Select a Party</option>
                                    @foreach($parties as $party)
                                        <option value="{{ $party->id }}" {{ request('party_id') == $party->id ? 'selected' : '' }}>{{ $party->name }} ({{ $party->phone }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">View Ledger</button>
                            </div>
                        </form>
                    </div>
                </div>

                @if($selectedParty)
                <div class="card">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Ledger for: {{ $selectedParty->name }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Particulars</th>
                                        <th class="text-end">Debit (Dr)</th>
                                        <th class="text-end">Credit (Cr)</th>
                                        <th class="text-end">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalDebit = 0;
                                        $totalCredit = 0;
                                    @endphp
                                    @forelse($entries as $entry)
                                        @php
                                            $totalDebit += $entry->debit;
                                            $totalCredit += $entry->credit;
                                        @endphp
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($entry->entry_date)->format('d M, Y') }}</td>
                                            <td>
                                                <span class="badge bg-soft-primary text-primary text-capitalize">{{ $entry->entry_type }}</span>
                                            </td>
                                            <td>
                                                @if($entry->entry_type == 'purchase')
                                                    Purchase (Bill No: {{ $entry->reference->purchase_no ?? $entry->reference_id }})
                                                @elseif($entry->entry_type == 'sale')
                                                    Sale (Bill No: {{ $entry->reference->sale_no ?? $entry->reference_id }})
                                                @elseif($entry->entry_type == 'payment')
                                                    Payment ({{ $entry->reference->type ?? '' }})
                                                @else
                                                    {{ $entry->entry_type }}
                                                @endif
                                            </td>
                                            <td class="text-end text-danger">{{ $entry->debit > 0 ? '₹'.number_format($entry->debit, 2) : '-' }}</td>
                                            <td class="text-end text-success">{{ $entry->credit > 0 ? '₹'.number_format($entry->credit, 2) : '-' }}</td>
                                            <td class="text-end fw-bold">₹{{ number_format($entry->balance_after, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">No transactions found for this party</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="3" class="text-end">Totals:</th>
                                        <th class="text-end text-danger">₹{{ number_format($totalDebit, 2) }}</th>
                                        <th class="text-end text-success">₹{{ number_format($totalCredit, 2) }}</th>
                                        <th class="text-end text-primary">₹{{ number_format(abs($totalCredit - $totalDebit), 2) }} {{ ($totalCredit - $totalDebit) >= 0 ? 'Cr' : 'Dr' }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                @else
                <div class="card border-0 shadow-none bg-transparent">
                    <div class="card-body text-center py-5">
                        <i class="feather-file-text text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-muted">Select a party to view their ledger</h5>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
