@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Purchase List</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Purchase List</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="{{ route('business.purchases.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>New Purchase</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body p-0">
                        @if (session('success'))
                            <div class="alert alert-success m-3">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Purchase No</th>
                                        <th>Date & Time</th>
                                        <th>Party</th>
                                        <th>Grain</th>
                                        <th>Quantity</th>
                                        <th>Rate</th>
                                        <th>Total Amount</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchases as $item)
                                        <tr data-bs-toggle="collapse" data-bs-target="#details-{{ $item->id }}" style="cursor: pointer;">
                                            <td>
                                                <div class="fw-bold text-primary">{{ $item->purchase_no ?? 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ \Carbon\Carbon::parse($item->date)->format('d M, Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($item->purchase_time)->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                <h6 class="mb-0">{{ $item->party->name ?? 'N/A' }}</h6>
                                            </td>
                                            <td><span class="badge bg-soft-primary text-primary">{{ $item->items->count() }} Items</span></td>
                                            <td>
                                                @qtyRaw($item->items->sum('quantity')) @unitLabel
                                            </td>
                                            <td>-</td>
                                            <td><span class="text-success fw-bold">₹{{ number_format($item->total_amount, 2) }}</span></td>
                                            <td class="text-end" onclick="event.stopPropagation();">
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <a href="{{ route('business.purchases.print', $item->id) }}" class="btn btn-sm btn-icon btn-light-info" target="_blank" title="Print Bill">
                                                        <i class="feather-printer"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-icon btn-light-primary" data-bs-toggle="collapse" data-bs-target="#details-{{ $item->id }}">
                                                        <i class="feather-chevron-down"></i>
                                                    </button>
                                                    <form action="{{ route('business.purchases.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this purchase? This will revert all stock and ledgers.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Cancel Purchase">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" class="p-0 border-0">
                                                <div class="collapse" id="details-{{ $item->id }}">
                                                    <div class="p-4 bg-light">
                                                        <h6 class="mb-3 fw-bold text-dark">Purchased Items Details</h6>
                                                        <div class="table-responsive mb-3">
                                                            <table class="table table-sm table-bordered mb-0 bg-white">
                                                                <thead class="bg-light">
                                                                    <tr>
                                                                        <th>Grain</th>
                                                                        <th>Godown</th>
                                                                        <th>Moisture</th>
                                                                        <th>Quantity</th>
                                                                        <th>Rate</th>
                                                                        <th>Amount</th>
                                                                        <th>Lot No.</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($item->items as $purchaseItem)
                                                                        @php
                                                                            $lot = $item->lots->where('grain_id', $purchaseItem->grain_id)->where('godown_id', $purchaseItem->godown_id)->first();
                                                                        @endphp
                                                                        <tr>
                                                                            <td class="fw-bold">{{ $purchaseItem->grain->name ?? 'N/A' }}</td>
                                                                            <td>{{ $purchaseItem->godown->name ?? 'N/A' }}</td>
                                                                            <td>{{ $purchaseItem->moisture ? $purchaseItem->moisture . '%' : '-' }}</td>
                                                                            <td>@qtyRaw($purchaseItem->quantity) @unitLabel</td>
                                                                            <td>₹{{ number_format($purchaseItem->rate, 2) }}</td>
                                                                            <td class="fw-bold text-dark">₹{{ number_format($purchaseItem->quantity * $purchaseItem->rate, 2) }}</td>
                                                                            <td><span class="badge bg-soft-secondary text-dark">{{ $lot->lot_no ?? 'N/A' }}</span></td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <small class="text-muted d-block mb-1">Ledger Update</small>
                                                                <div class="fw-bold text-success"><i class="feather-check-circle me-1"></i> Credited to Party</div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <small class="text-muted d-block mb-1">Stock Update</small>
                                                                <div class="fw-bold text-success"><i class="feather-check-circle me-1"></i> Stock Increased</div>
                                                            </div>
                                                        </div>
                                                        @if($item->notes)
                                                        <div class="row mt-3">
                                                            <div class="col-12">
                                                                <small class="text-muted d-block mb-1">Notes</small>
                                                                <div>{{ $item->notes }}</div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">No purchases found</td>
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
