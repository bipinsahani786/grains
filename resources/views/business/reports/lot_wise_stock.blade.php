@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Lot-wise Stock Report</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Reports</li>
                <li class="breadcrumb-item">Lot-wise Stock</li>
            </ul>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('business.reports.lot-wise-stock') }}" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Lots</option>
                                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open Lots Only</option>
                                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed Lots Only</option>
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
                                        <th>Lot No</th>
                                        <th>Grain</th>
                                        <th>Initial Qty</th>
                                        <th>Remaining Qty</th>
                                        <th>Purchase Rate</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lots as $lot)
                                        <tr>
                                            <td class="fw-bold text-dark">{{ $lot->lot_no }}</td>
                                            <td>{{ $lot->grain->name ?? 'N/A' }}</td>
                                            <td>@qtyRaw($lot->initial_quantity) @unitLabel</td>
                                            <td class="fw-bold {{ $lot->remaining_quantity > 0 ? 'text-success' : 'text-danger' }}">
                                                @qtyRaw($lot->remaining_quantity) @unitLabel
                                            </td>
                                            <td>₹@rateRaw($lot->rate) / @unitLabel</td>
                                            <td>
                                                @if($lot->status == 'open')
                                                    <span class="badge bg-soft-success text-success">Open</span>
                                                @else
                                                    <span class="badge bg-soft-danger text-danger">Closed</span>
                                                @endif
                                            </td>
                                            <td>{{ $lot->created_at->format('d M, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">No lots found</td>
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
