@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Total Grain Stocks</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Inventory</li>
                <li class="breadcrumb-item">Stocks</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <form action="{{ route('business.inventory.stocks.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="feather-calendar"></i></span>
                    <input type="date" class="form-control form-control-sm" name="start_date" value="{{ request('start_date') }}">
                    <span class="input-group-text bg-light px-2 border-start-0 border-end-0">to</span>
                    <input type="date" class="form-control form-control-sm" name="end_date" value="{{ request('end_date') }}">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    @if(request('start_date') || request('end_date'))
                        <a href="{{ route('business.inventory.stocks.index') }}" class="btn btn-light btn-sm text-danger border"><i class="feather-x"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-soft-primary mb-4">
                    <i class="feather-info me-2"></i> <strong>Tip:</strong> Click on any grain card below to see the complete history of where the stock was purchased from.
                </div>
            </div>

            @forelse($stocks as $stock)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card stretch stretch-full border-0 shadow-sm rounded-4 overflow-hidden" style="cursor: pointer; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);" data-bs-toggle="collapse" data-bs-target="#history-{{ $stock->id }}">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="avatar avatar-md bg-primary text-white shadow-sm rounded-circle d-flex align-items-center justify-content-center">
                                <i class="feather-box fs-5"></i>
                            </div>
                            <span class="badge bg-soft-primary text-primary rounded-pill">View History</span>
                        </div>
                        <h3 class="fw-bolder text-dark mb-1">@qtyRaw($stock->quantity)</h3>
                        <p class="text-muted fw-medium mb-0">{{ $stock->grain->name ?? 'Unknown Grain' }} <small class="text-muted">(@unitLabel)</small></p>
                    </div>
                </div>
            </div>
            
            <!-- History Collapse for this stock -->
            <div class="col-12 mb-4">
                <div class="collapse" id="history-{{ $stock->id }}">
                    <div class="card card-body border-0 shadow-sm rounded-4 p-4" style="background-color: #f8f9fa;">
                        <h6 class="mb-4 fw-bold text-primary">
                            <i class="feather-clock me-2"></i>Purchase History for {{ $stock->grain->name ?? 'Unknown Grain' }}
                        </h6>
                        @php
                            $lots = $lotsByGrain->get($stock->grain_id, collect());
                        @endphp
                        
                        @if($lots->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0 bg-white">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Party / Supplier</th>
                                            <th>Lot No</th>
                                            <th>Purchased Qty (@unitLabel)</th>
                                            <th>Remaining Qty (@unitLabel)</th>
                                            <th>Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lots as $lot)
                                            <tr>
                                                <td>{{ $lot->created_at->format('d M, Y') }}</td>
                                                <td>{{ $lot->purchase->party->name ?? 'N/A' }}</td>
                                                <td class="fw-bold">{{ $lot->lot_no }}</td>
                                                <td>{{ $lot->initial_quantity }} Qtl &rarr; <strong>@qtyRaw($lot->initial_quantity) @unitLabel</strong></td>
                                                <td class="{{ $lot->status == 'open' ? 'text-primary fw-bold' : 'text-muted' }}">@qtyRaw($lot->remaining_quantity) @unitLabel</td>
                                                <td>₹@rateRaw($lot->rate) / @unitLabel</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-muted">No purchase history found for this grain.</div>
                        @endif
                    </div>
                </div>
            </div>
            
            @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No stock available. Stocks will appear here once purchases are made.
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
