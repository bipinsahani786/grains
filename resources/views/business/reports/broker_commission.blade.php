@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Broker Commission Ledger</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Reports</li>
                <li class="breadcrumb-item">Broker Commission</li>
            </ul>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('business.reports.broker-commission') }}" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Select Broker <span class="text-danger">*</span></label>
                                <select name="broker_id" class="form-select" required>
                                    <option value="">Select a Broker</option>
                                    @foreach($brokers as $broker)
                                        <option value="{{ $broker->id }}" {{ request('broker_id') == $broker->id ? 'selected' : '' }}>{{ $broker->name }}</option>
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
                                <button type="submit" class="btn btn-primary w-100">View Commission</button>
                            </div>
                        </form>
                    </div>
                </div>

                @if($selectedBroker)
                <div class="card">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Commission Ledger for: {{ $selectedBroker->name }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Deal Type</th>
                                        <th>Qty</th>
                                        <th>Deal Rate</th>
                                        <th>Commission Applied</th>
                                        <th class="text-end">Amount Earned</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalEarned = 0;
                                    @endphp
                                    @forelse($entries as $entry)
                                        @php
                                            $totalEarned += $entry->commission_amount;
                                        @endphp
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($entry->date)->format('d M, Y') }}</td>
                                            <td>
                                                @if(str_contains($entry->reference_type, 'Purchase'))
                                                    <span class="badge bg-soft-info text-info">Purchase</span>
                                                @else
                                                    <span class="badge bg-soft-primary text-primary">Sale</span>
                                                @endif
                                                <small class="text-muted d-block">ID: {{ $entry->reference_id }}</small>
                                            </td>
                                            <td>@qtyRaw($entry->quantity) @unitLabel</td>
                                            <td>₹{{ number_format($entry->rate, 2) }}</td>
                                            <td>
                                                <span class="text-capitalize">{{ str_replace('_', ' ', $entry->commission_type) }}</span>: 
                                                @if($entry->commission_type == 'percentage')
                                                    {{ $entry->commission_rate }}%
                                                @else
                                                    ₹{{ number_format($entry->commission_rate, 2) }}
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold text-success">₹{{ number_format($entry->commission_amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">No commission records found for this broker</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="5" class="text-end">Total Commission Earned:</th>
                                        <th class="text-end text-primary fs-6">₹{{ number_format($totalEarned, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                @else
                <div class="card border-0 shadow-none bg-transparent">
                    <div class="card-body text-center py-5">
                        <i class="feather-award text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-muted">Select a broker to view their commission ledger</h5>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
