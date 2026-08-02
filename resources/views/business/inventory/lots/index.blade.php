@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Lots (FIFO)</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Inventory</li>
                <li class="breadcrumb-item">Lots</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <form action="{{ route('business.inventory.lots.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                <div class="input-group input-group-sm" style="width: auto;">
                    <span class="input-group-text bg-light"><i class="feather-search"></i></span>
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Search lot no or grain..." value="{{ request('search') }}">
                </div>
                <div class="input-group input-group-sm" style="width: auto;">
                    <span class="input-group-text bg-light"><i class="feather-calendar"></i></span>
                    <input type="date" class="form-control form-control-sm" name="start_date" value="{{ request('start_date') }}">
                    <span class="input-group-text bg-light px-2 border-start-0 border-end-0">to</span>
                    <input type="date" class="form-control form-control-sm" name="end_date" value="{{ request('end_date') }}">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    @if(request('start_date') || request('end_date') || request('search'))
                        <a href="{{ route('business.inventory.lots.index') }}" class="btn btn-light btn-sm text-danger border"><i class="feather-x"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stretch stretch-full">
                    <div class="card-body p-3 hover-effect">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex gap-3 align-items-center w-100 overflow-hidden">
                                <div class="avatar-text bg-soft-primary text-primary">
                                    <i class="feather-layers"></i>
                                </div>
                                <div class="overflow-hidden w-100">
                                    <div class="fs-5 fw-bold text-dark text-truncate">{{ $totalLots }}</div>
                                    <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Total Lots</h3>
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
                                <div class="avatar-text bg-soft-success text-success">
                                    <i class="feather-check-circle"></i>
                                </div>
                                <div class="overflow-hidden w-100">
                                    <div class="fs-5 fw-bold text-dark text-truncate">{{ $openLots }}</div>
                                    <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Open Lots</h3>
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
                                    <i class="feather-shopping-bag"></i>
                                </div>
                                <div class="overflow-hidden w-100">
                                    <div class="fs-5 fw-bold text-dark text-truncate">@qtyRaw($totalInitialQty) @unitLabel</div>
                                    <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Total Initial Qty</h3>
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
                                <div class="avatar-text bg-soft-warning text-warning">
                                    <i class="feather-pie-chart"></i>
                                </div>
                                <div class="overflow-hidden w-100">
                                    <div class="fs-5 fw-bold text-dark text-truncate">@qtyRaw($totalRemainingQty) @unitLabel</div>
                                    <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Total Remaining Qty</h3>
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
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Status</th>
                                        <th>Lot No</th>
                                        <th>Grain</th>
                                        <th>Initial Qty (@unitLabel)</th>
                                        <th>Remaining Qty (@unitLabel)</th>
                                        <th>Purchase Rate</th>
                                        <th>Date Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lots as $lot)
                                        <tr class="{{ $lot->status == 'closed' ? 'bg-light text-muted' : '' }}">
                                            <td>
                                                @if($lot->status == 'open')
                                                    <span class="badge bg-soft-success text-success">Open</span>
                                                @else
                                                    <span class="badge bg-soft-secondary text-secondary">Closed</span>
                                                @endif
                                            </td>
                                            <td class="fw-bold">{{ $lot->lot_no }}</td>
                                            <td>{{ $lot->grain->name ?? 'N/A' }}</td>
                                            <td>@qtyRaw($lot->initial_quantity) @unitLabel</td>
                                            <td class="fw-bold {{ $lot->status == 'open' ? 'text-primary' : '' }}">@qtyRaw($lot->remaining_quantity) @unitLabel</td>
                                            <td>₹{{ number_format($lot->rate, 2) }}</td>
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
