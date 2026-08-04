@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Sales Report</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Reports</li>
                <li class="breadcrumb-item">Sales Report</li>
            </ul>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('business.reports.sales') }}" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Party</label>
                                <select name="party_id" class="form-select">
                                    <option value="">All Parties</option>
                                    @foreach($parties as $party)
                                        <option value="{{ $party->id }}" {{ request('party_id') == $party->id ? 'selected' : '' }}>{{ $party->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Grain</label>
                                <select name="grain_id" class="form-select">
                                    <option value="">All Grains</option>
                                    @foreach($grains as $grain)
                                        <option value="{{ $grain->id }}" {{ request('grain_id') == $grain->id ? 'selected' : '' }}>{{ $grain->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Party</th>
                                        <th>Broker</th>
                                        <th>Grain</th>
                                        <th>Qty & Unit</th>
                                        <th>Rate</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalAmount = 0; @endphp
                                    @forelse($sales as $sale)
                                        @php $totalAmount += $sale->total_amount; @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ \Carbon\Carbon::parse($sale->date)->format('d M, Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($sale->sale_time)->format('h:i A') }}</small>
                                            </td>
                                            <td>{{ $sale->party->name ?? 'N/A' }}</td>
                                            <td>{{ $sale->broker->name ?? 'N/A' }}</td>
                                            <td>{{ $sale->grain->name ?? 'N/A' }}</td>
                                            <td>@qtyRaw($sale->quantity) @unitLabel</td>
                                            <td>₹@rateRaw($sale->rate) / @unitLabel</td>
                                            <td class="fw-bold text-dark">₹{{ number_format($sale->total_amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">No sales found for the selected criteria</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="6" class="text-end">Total Amount:</th>
                                        <th class="text-primary fs-6">₹{{ number_format($totalAmount, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
